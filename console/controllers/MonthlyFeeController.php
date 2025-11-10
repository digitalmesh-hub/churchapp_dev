<?php
namespace console\controllers;

use yii;
use yii\console\Controller;
use common\models\extendedmodels\ExtendedBills;
use common\models\extendedmodels\ExtendedMember;
use common\models\extendedmodels\ExtendedMonthlySubscriptionFee;
use common\models\extendedmodels\ExtendedInstitution;
use common\models\basemodels\Usercredentials;
use yii\console\Exception;
use yii\helpers\Console;


/**
 * Monthly Fee Controller
 * This controller will insert monthly subscription fee for member  
 */
class MonthlyFeeController extends Controller
{

    const ACTIONTYPE = 'create';
    const PENDING = 0 ;
    const PROCESSING = 1 ;
    const PROCESSED = 2 ;
    const MEMBERTYPE = 0;

    /**
     * This is the index action for console/scheduler.
     * @return [type] [description]
     */
    public function actionIndex()
    {
        $subscriptionFee = ExtendedMonthlySubscriptionFee::findOne(['status'=> self::PENDING]);
        if (!empty($subscriptionFee)) {
            $this->processSubscription($subscriptionFee);
        } else {
            Yii::$app->end();
        }
    }
    
    protected function processSubscription($data)
    {
        $result = [];
        $month = date('m');
        $year = date('Y');
        $allMember = ExtendedMember::findAll(['institutionid' => $data->institutionId, 'membertype' => self::MEMBERTYPE]);
        if (!empty($allMember)) {
            $amount = (string)$data->amount;
            $transactionDate = date('d.m.Y', strtotimeNew($data->transactionDate));
            //change status to processing
            $data->status = self::PROCESSING;
            $data->update();
            foreach ($allMember as $key => $member) {
                try {
                    // Creates opening balance entry if not exist
                    $status = ExtendedBills::createOpeningBalance(
                        $member->memberid,
                        $month,
                        $year,
                        $member->memberno,
                        explode("/", $member->memberno, 2)[0],
                        $data->userId,
                        $data->institutionId
                    );
                } catch (Exception $e) {
                    Yii::error(
                        'Error while calculating opening balance , '.
                        var_export([
                            'error' => $e->getMessage(),
                            'class' => get_class()
                        ],true));
                }
                if ($status) { 
                    $model = new ExtendedBills();
                    $model->memberid = $member->memberid;
                    $model->transactiondate = $transactionDate;
                    $model->description = $data->description;
                    $model->debit = $amount;
                    $model->institutionid = $data->institutionId;
                    $model->type = yii::$app->params['otherTransactions']['type'];
                    $model->month =$month;
                    $model->year =$year;
                    $model->userid =$data->userId;
                    $model->memberNo =$member->memberno;
                    $arr = explode("/", $member->memberno, 2);
                    $model->newmembernum =$arr[0];
                    if ($model->save()) {
                        $result['success'][] = $model->memberNo;
                        $billId = $model->billid;
                        $mailSelect ="select mb.member_email from member mb left join bills bi on mb.memberid = bi.memberid where bi.billId= '$billId' ";
                        $emailAddress= Yii::$app->db->createCommand($mailSelect)->queryOne();
                        $billData = new \stdClass();
                        $billData->billId = $billId;
                        $billData->emailAddress = $emailAddress['member_email'];
                        if ($model['debit']!='' || $model['debit']!= null) {
                            if (filter_var($billData->emailAddress, FILTER_VALIDATE_EMAIL)) {
                                $this->emailOnTransaction($billData, $data);
                            }
                        }
                    } else {
                        Yii::error(
                            'Error while saving bill model in subscription controller, '.
                            var_export(
                                [
                                    'error' => $model->getErrors(),
                                    'class' => get_class()
                                ],true
                            )
                        );
                        $result['error'][] = $member->memberno;
                    }
                } else {
                    $result['error'][] = $member->memberno;
                }
            }
            
            $isSuccess = isset($result['success']) ? count($result['success']) : 0;
            $isError = isset($result['error']) ? count($result['error']) : 0;
            if (!empty($result)) {
                $txnData['description'] = $data->description;
                $txnData['debit'] = $data->amount;
                $txnData['date'] = date(
                    yii::$app->params['dateFormat']['viewDateFormat'],
                    strtotimeNew($data->transactionDate)
                );
                $txnData['isSuccessCount'] = $isSuccess;
                $txnData['isErrorCount'] = $isError;
                $txnData['failedMembers'] = isset($result['error']) ? $result['error'] : null;
                $this->emailOnBillChange(self::ACTIONTYPE, $txnData, $data->institutionId);
            }
        }
        $data->status = self::PROCESSED;//change status to processed
        $data->update();
        return true;
    }

    /**
     * Sends Email on bill create, update or delete action
     * to administrator
     * @param  string  $action    Action type. ie, created/update/delete
     * @param  integer $memberid  Member Id
     * @param  integer $month     Bill month
     * @param  integer $year      Bill year
     * @return none
     */
    private function emailOnBillChange($action, $txnData, $institutionId)
    {
        // Send email only when the 'emailAdminOnBillChange' flag is true
        if (Yii::$app->params['emailAdminOnBillChange']) {
            // Gets the institution model
            $institutionData = ExtendedInstitution::find()->where(['id' => $institutionId])->one();
    
            // ['email' => 'name']
            $from = [yii::$app->params['re-memberEmail'] => $institutionData->name];
            $to  = !empty($institutionData) ? $institutionData->email : null;
            //institution logo
            $logo = !empty($institutionData->institutionlogo)
            ? yii::$app->params['instituteLogoFrom'] . $institutionData->institutionlogo
            : null;
    
            // Gets the CC email if 'isBillChangeCc' flag is true
            $cc = Yii::$app->params['isBillChangeCc'] ? Yii::$app->params['billChangeCcEmail'] : null;
            
            $isSuccess = !empty($txnData['isSuccessCount']) ? $txnData['isSuccessCount'] : 0;
            $subject = 'Monthly subscription fee update';
            $message ='Dear Admin,<br><br>';           
            $message .='This is to inform you that the monthly subscription fee has been added for '.$isSuccess.' members.
                 <br><br> ';
            /*$message = 'Dear Admin,<br><br>';
            $message .= 'This is to inform you that '.
                    'monthly subscription fee has been ';*/
    
            $date = !empty($txnData['date']) ? $txnData['date'] : '';
            $description = !empty($txnData['description']) ? $txnData['description'] : '';
            $debit = !empty($txnData['debit']) ? $txnData['debit'] : '';
            $isError = !empty($txnData['isErrorCount']) ? $txnData['isErrorCount'] : 0;
            $failedMembers = !empty($txnData['failedMembers']) ? $txnData['failedMembers'] : '';
            $txnDetails ='
                <table cellpadding="5" cellspacing="0" 
                border="1" 
                font-family: Tahoma, Geneva, sans-serif;
                font-size: 13px;
                color: #000; 
                width="800px">
                    <caption>Summary</caption>
                    <tr>
                        <th>Transaction Date</th>
                        <th>Description</th>
                        <th align="right">Debit (Dr)</th>
                    </tr>
                    <tr>
                        <td>'.$date.'</td>
                        <td>'.$description.'</td>
                        <td align="right">
                        '.yii::$app->MoneyFormat->decimalWithComma($debit).'
                        </td>
                    </tr>
                </table>
                ';

            if ($isError > 0) {
                $txnDetails .='
                <table cellpadding="5" cellspacing="0"
                border="1"
                font-family: Tahoma, Geneva, sans-serif;
                font-size: 13px;
                color: #000; 
                width="750px">
                    <caption>Failed Members</caption>
                    <tr>
                        <th>Member No</th>
                        <th>Status</th>
                    </tr>';
                foreach ($failedMembers as $memberno) {
                       $txnDetails .='
                       <tr>
                            <td>'.$memberno.'</td>
                            <td>Failed</td>
                        </tr>';
                }
                    $txnDetails .='</table>';
            }
           
            $mailContent['content'] = $message.$txnDetails;
            $mailContent['template'] = "bill-action-email";
            //Sending mail.
            $emailStatus = yii::$app->EmailHandler->sendEmail(
                $from,
                $to,
                $cc,
                $subject,
                $mailContent,
                null,
                $logo
            );
            return $emailStatus;
        } else {
            return true;
        }
    }

    /**
     * sends Email on bill creation,update or delete action
     * to member
     * @param $jsonData
     */
    
    private function emailOnTransaction($txnData, $data)
    {
        $result = [];
        if ($txnData) {
            //set result status as success
            $result = ['status' => 'success', 'mailed' => true];
            //get parameters
            $billId = isset($txnData->billId) ? $txnData->billId : '';
            $emailAddress = isset($txnData->emailAddress) ? $txnData->emailAddress : '';
            $bill = ExtendedBills::findOne($billId);
            $member = ExtendedMember::find()->where('memberid = :memberid', [':memberid' => $bill->memberid])->one();
            $institution = ExtendedInstitution::find()->where('id = :institutionId', [':institutionId' => $bill->institutionid])->one();
            $Adminprofile = Usercredentials::findOne(['id' =>$data->userId]);
            $memberid=$member['memberid'];
            //echo $memberid;die;
            $billid =$bill['billid'];
            $debit=$bill['debit'];
            //institution logo
            $logo = !empty($institution->institutionlogo)
            ? yii::$app->params['instituteLogoFrom'] . $institution->institutionlogo
            : null;
            $institutionName = $institution->name;
            $email  = $emailAddress;
            $userID = $memberid;
            $title = 'Monthly subscription fee';
            $from = $Adminprofile->emailid;
            //if the amount is debited then a mail with account statement will be sent
            if ($debit!='' || $debit!= null) {
                $title = "Bill Statement";
                $MessageDb = 'Dear ' . ucwords($member->firstName) .' '. ucwords($member->middleName) .' '. ucwords($member->lastName) . ' ,<br><br>';
                $MessageDb .= 'Your account has been updated on ' . date(yii::$app->params['dateFormat']['viewDateFormat'])
                .'. Please see the statement below for the details. You can contact the undersigned if you require any clarification. '. '<br><br>';
                $txnDetails = $this->mailStatement($billId);
                $mailContent['name'] = $institutionName;
                $mailContent['content'] = $MessageDb.$txnDetails;
                $mailContent['template'] = "emailmessage";
                $temp = yii::$app->EmailHandler->sendEmail(
                    $from,
                    $email,
                    null,
                    $title,
                    $mailContent,
                    null,
                    $logo
                );
            } else {
                //set result status as success
                $result = ['status' => 'failure'];
            }
            return $result;
        }
    }

    /**
     * 
     * Returns statement about account
     * transactions
     * @param $billId
     */
    private function mailStatement($billId)
    {
        $bill = ExtendedBills::findOne($billId);
        $memberid = $bill['memberid'];
        $month = $bill['month'];
        $year = $bill['year'];
        $details = "select bi.*,ch.paymentType from bills bi
                    left join member mb on mb.memberid=bi.memberid left join cheque ch on ch.BillId=bi.billid where 
                    year ='$year' 
                    AND month ='$month' AND mb.memberid='$memberid' 
                    and bi.type <> 1 order by bi.transactiondate asc";
        $detailStatement = Yii::$app->db->createCommand($details)->queryAll();
        $openingBalance = "SELECT transactiondate,debit,credit,type FROM bills bi left join 
                            member mb on bi.memberid=mb.memberid where mb.memberid='$memberid' and type=1 and
                            year ='$year' and month ='$month'";
        $openingBalanceData = Yii::$app->db->createCommand($openingBalance)->queryAll();
        foreach ($openingBalanceData as $value) {
            $OpeningBalanceType = $value['type'];
            $credit = round($value['credit'], 2);
            $debit = round($value['debit'], 2);
        }
        $txnDetails ='
            <table cellpadding="5" cellspacing="0"
            font-family: Tahoma, Geneva, sans-serif;
            font-size: 13px;
            color: #000;
            border="1" 
            width="800px">
                <tr>
                    <th>Member No</th>
                    <th>Transaction Date</th>
                    <th>Description</th>
                    <th align="right">Debit(Dr)</th>
                    <th align="right">Credit(Cr)</th>
                </tr>
            ';
		if ($credit!='' || $debit!='') {
			$txnDetails .='<tr>';
		}
        if ($credit!='') {
            $txnDetails .='
                        <td colspan="3"><strong>Opening Balance</strong></td>
                        <td align="right">&nbsp;</td>
                        <td align="right"><strong>'.yii::$app->MoneyFormat->decimalWithComma($credit).'</strong>
                        </td>
                        ';          
        } elseif ($debit!='') {
            $txnDetails .='
                        <td colspan="3"><strong>Opening Balance</strong></td>
                        <td align="right"><strong>'.yii::$app->MoneyFormat->decimalWithComma($debit).'</strong>
                        </td>
                        <td align="right">&nbsp;</td>
                        ';
        }
        if ($credit!='' || $debit!='') {
			$txnDetails .='</tr>';
		}
        $balance = 0;
        $totalDebit = 0;
        $totalCredit = 0;   
        foreach ($detailStatement as $row) {
            $d = (float)$row['debit'];
            $c = (float)$row['credit'];
            $totalDebit += $d;
            $totalCredit += $c;
            $totalDebits  = $totalDebit + $debit;
            $totalCredits = $totalCredit + $credit;
            $trdDate = $row['transactiondate'];
            $date = str_replace('.', '-', $trdDate);
            $txnDetails .= '<tr>
                <td>'.$row['memberNo'].'</td>
                <td>'.date('d-m-Y', strtotimeNew($date)).'</td>
                <td>'.$row['description'].'</td>
                <td align="right">'.yii::$app->MoneyFormat->decimalWithComma($row['debit']).'
                </td>
                <td align="right">'.yii::$app->MoneyFormat->decimalWithComma($row['credit']).'</td>
                </tr>
            ';
        }
        $txnDetails .='<tr>
            <td><strong>Total</strong></td>
            <td colspan="2">&nbsp;</td>       
            <td align="right"><strong>'.yii::$app->MoneyFormat->decimalWithComma($totalDebits).'</strong></td>
            <td align="right"><strong>'.yii::$app->MoneyFormat->decimalWithComma($totalCredits).'
                </strong></td>
            </tr>
        ';
        $balance = $totalDebits - $totalCredits;
        $txnDetails.='<tr>';
        if ($balance >= 0) {
            $txnDetails.='
                <td colspan="3"><strong>Balance : (total debits - total credits)</strong></td>
                <td align="right"><strong>
                '."(".yii::$app->MoneyFormat->decimalWithComma($balance).")".'
                </strong></td>
                <td align="right">&nbsp;</td>';
        }
        if ($balance < 0) {
            $txnDetails.='
            <td colspan="3"><strong>Balance : (total debits - total credits)</strong></td>
            <td align="right">&nbsp;</td>
            <td align="right"><strong>'."(".yii::$app->MoneyFormat->decimalWithComma(-1 * $balance).")".'</strong></td>';
        }
        $txnDetails.='</tr></table>';
        return $txnDetails;
    }
}
