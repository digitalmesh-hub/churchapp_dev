<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Bills;
use Exception;

/**
 * This is the extended model class for table "bills".
 *
 * @property int $billid
 * @property string $transactiondate
 * @property string $transactiontype
 * @property string $description
 * @property string $debit
 * @property string $credit
 * @property string $amount
 * @property string $voucher
 * @property string $voucherType
 * @property int $type
 * @property string $memberNo
 * @property int $year
 * @property int $userid
 * @property int $month
 * @property int $institutionid
 * @property int $memberid
 * @property string $newmembernum
 *
 * @property BillReceipt[] $billReceipts
 * @property Institution $institution
 * @property Member $member
 * @property Usercredentials $user
 * @property Cheque[] $cheques
 */
class ExtendedBills extends Bills
{
	/**
	 * To get the bill seen count
	 * @param $institutionId int
	 * @param $memberId int
	 * @param $userType string
	 * @return $billSeenCount array
	 */
	public static function getBillSeenCount($institutionId,$memberId,$userType)
	{  
		try {
			$billSeenCount = Yii::$app->db->createCommand(
					"CALL getbillsseencount(:institutionid,:memberid,:usertype)")
					->bindValue(':institutionid', $institutionId)
					->bindValue(':memberid', $memberId)
					->bindValue(':usertype', $userType)
					->queryOne();
			return $billSeenCount;	
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * To get the bill info for the given month
	 * @param unknown $month
	 * @param unknown $year
	 * @param unknown $memberId
	 * @param unknown $institutionId
	 * @return \yii\db\false|boolean
	 */
	public static function getBillsInfo($month,$year,$memberId,$institutionId)
	{
		try {
			$billsInfo = Yii::$app->db->createCommand(
					"CALL getbillinfo(:memberid,:month,:year,:institutionid)")
					->bindValue(':memberid', $memberId)
					->bindValue(':month', $month)
					->bindValue(':year', $year)
					->bindValue(':institutionid', $institutionId)
					->queryOne();
			return $billsInfo;
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * To get bill list by member
	 * @param unknown $month
	 * @param unknown $year
	 * @param unknown $institutionId
	 * @param unknown $memberId
	 * @return boolean
	 */
	public static function getBillListByMember($month,$year,$institutionId,$memberId)
	{
		try {
			$billListByMember = Yii::$app->db->createCommand(
					"CALL getbills(:memberid,:month,:year,:institutionid)") // getbills is the actual sp
					->bindValue(':memberid', $memberId)
					->bindValue(':month', $month)
					->bindValue(':year', $year)
					->bindValue(':institutionid', $institutionId)
					->queryAll();
			return $billListByMember;
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * To get the bills
	 * details for
	 * synchronize
	 * @param $institutionId int
	 * @param $memberid int
	 * @param $currentDate dateTime
	 */
	public static function getBillsForSync($currentDate,$institutionId,$memberId)
	{
		try {
			$billsData = Yii::$app->db->createCommand(
					"CALL getbillsforsync(:currentdate,:institutionid,:memberid)") 
					->bindValue(':currentdate', $currentDate)
					->bindValue(':institutionid', $institutionId)
					->bindValue(':memberid', $memberId)
					->queryAll();
			return $billsData;
			
		} catch (Exception $e) {
			return false;
		}
	}
	/**
     * gets the balance for the given month.
     * Open balance is calculated by subtracting the 
     * sum of credits from sum of debits ie, sum(Dr) - sum(Cr)
     * @param  integer $memberid Member Id
     * @param  integer $month    Bill month
     * @param  integer $year     Bill year
     * @return float             Balance
     */
    public static function getBillBalance($memberid, $month, $year)
    {   
        $balanceQuery = 'CALL usp_Bill_BalanceCalculation(
            :MemberID, 
            :BillMonth, 
            :BillYear,
            :BillType, 
            @bal
        )';
        
        $connection = \Yii::$app->db;
        $command = $connection->createCommand($balanceQuery);
        $command->bindParam(':MemberID', $memberid, \PDO::PARAM_INT);
        $command->bindParam(':BillMonth', $month, \PDO::PARAM_INT);
        $command->bindParam(':BillYear', $year, \PDO::PARAM_INT);
        $command->bindParam(
            ':BillType', 
            Yii::$app->params['openBalance']['type'], 
            \PDO::PARAM_INT
        );
        $command->execute();

        $balance = $connection->createCommand(
            'select @bal'
        )->queryScalar();

        // Validating query result
        if (   isset($balance) 
            && is_numeric($balance)
        ) {
            // Note: when $balance = 0, then 'type' will be debit
            return [
                'balance' => abs(round($balance,2)),
                'type' => ($balance < 0) ? 'credit' : 'debit'
            ];
        } else {
            Yii::error(
                'Balance $balance either is empty or false '.
                var_export($balance, true)
            );
            throw new \yii\web\HttpException(
                404, 
                'Failed to find the opening balance'
            );
        }
    }

    /**
     * Creates opening entry if not exist
     * @param  integer $memberid    Member Id
     * @param  integer $month       Bill Month
     * @param  integer $year        Bill Year
     * @param  integer $memberNo    Member number
     * @param  integer $newMemberNo New member number
     * @return none
     */
    public static function createOpeningBalance(
        $memberid, $month, $year, $memberNo, $newMemberNo, $userId = null, $institutionId = null
    ) {
        $type = Yii::$app->params['openBalance']['type'];

        // To check opening balance entry exist
        $openingBalanceData = self::getOpeningBalance(
            $memberid, 
            $month, 
            $year
        );

        // If no entry for opening balance, then calculate the 
        // opening balance and make entry in debits
        if(empty($openingBalanceData)){
            $openingBalanceData = self::getBillBalance(
                $memberid, 
                $month, 
                $year
            );

            $openingBalance = strval($openingBalanceData['balance']);
            $openingBalanceType = $openingBalanceData['type'];
            $openingBalanceModel = new Bills();
            
            //setting values into model
            $openingBalanceModel->memberid = $memberid;
            $openingBalanceModel->year = $year;
            $openingBalanceModel->month = $month;
                if (!$userId && !$institutionId) {
                    $openingBalanceModel->userid = Yii::$app->user->id; 
                    $openingBalanceModel->institutionid = Yii::$app->user->identity->institutionid;
                 } else {
            $openingBalanceModel->userid = $userId ;
            $openingBalanceModel->institutionid = $institutionId;

            }
            $openingBalanceModel->memberNo = $memberNo;
            $openingBalanceModel->newmembernum = explode("/", $newMemberNo, 2)[0];

            // Saving new opening balance amount, 
            // according to the opening balance type
            if ($openingBalanceType === 'debit') {
                $openingBalanceModel->debit = $openingBalance;
                $openingBalanceModel->credit = NULL;
            } else {
                $openingBalanceModel->credit = $openingBalance;
                $openingBalanceModel->debit = NULL;
            }

            //creating transaction date as 01.mm.yyyy
            $openingBalanceModel->transactiondate = '01';
            $openingBalanceModel->transactiondate .= ($month<10) ? '.0' .$month : '.' . $month;
            $openingBalanceModel->transactiondate .= '.' . $year;
            
            $openingBalanceModel->description = yii::$app->params['openBalance']['description'];
            $openingBalanceModel->type = $type;
            
            $openingBalanceModel->transactiontype = '';
            $openingBalanceModel->amount = '';
            $openingBalanceModel->voucher = '';
            $openingBalanceModel->voucherType = '';
            
            //insert opening balance
            if (!$openingBalanceModel->save()) {
                Yii::error(
                    'Error while saving opening balance, '.
                    var_export([
                        'error' => $openingBalanceModel->getErrors(),
                        'class' => get_class()
                    ], true)
                );
                return false;
            } else {
                return true;
            }
        } else {
            // Returns true since opening balance in available in database
            return true; 
        }
    }

    /**
     * Corrects the opening balance calculation
     * @param  integer $memberid    Member Id
     * @param  integer $month       Bill Month
     * @param  integer $year        Bill Year
     * @return none
     */
    public static function updateSubsequentOpeningBalances(
        $memberid, $month, $year
    ) { 
        $correctOpenBalQuery = 'CALL usp_Bill_OpeningBalanceCorrection(
            :MemberID, 
            :BillMonth, 
            :BillYear,
            :BillType
        )';
        $connection = \Yii::$app->db;
        $command = $connection->createCommand($correctOpenBalQuery);
        $command->bindParam(':MemberID', $memberid, \PDO::PARAM_INT);
        $command->bindParam(':BillMonth', $month, \PDO::PARAM_INT);
        $command->bindParam(':BillYear', $year, \PDO::PARAM_INT);
        $command->bindParam(
            ':BillType', 
            Yii::$app->params['openBalance']['type'], 
            \PDO::PARAM_INT
        );
        $command->execute();

        return true;
    }

    /**
     * Gets the opening balance
     * @param  integer $memberid Member Id
     * @param  integer $month    Bill Month
     * @param  integer $year     Bill Year
     * @return integer
     */
    public static function getOpeningBalance($memberid, $month, $year)
    {   
        $openBalanceType = yii::$app->params['openBalance']['type'];

        //getting opening balance
        $openingBalanceData = ExtendedBills::find()
                                ->select(['debit', 'credit'])
                                ->where('type = :Type AND memberid = :MemberId AND month = :Month AND year = :Year',[
                                    ':Type' => $openBalanceType, 
                                    ':MemberId' => $memberid, 
                                    ':Month' => $month, 
                                    ':Year' => $year
                                ])->one();
        // Gets the opening balance
        if (!empty($openingBalanceData)) {
            // If opening balance is a debit amount, return it
            if (   is_numeric($openingBalanceData->debit)
                && (   !is_numeric($openingBalanceData->credit) 
                    || $openingBalanceData->credit == 0)
            ) {
                return [
                    'balance' => round($openingBalanceData->debit,2),
                    'type' => 'debit'
                ];
            } else if (   is_numeric($openingBalanceData->credit)  
                       && (   !is_numeric($openingBalanceData->debit)
                           || $openingBalanceData->debit == 0)
            ) { // If opening balance is a credit amount. return the -ve of the value
                // return round($openingBalanceData->credit,2);
                return [
                    'balance' => round($openingBalanceData->credit,2),
                    'type' => 'credit'
                ];
            } else { // throws error when both debit and credit are non-numeric
                Yii::error(
                    'debit and credit amount is present at $openingBalanceData. Either'.
                    ' one is allowed at a time '.var_export($openingBalanceData, true)
                );
                throw new \yii\web\HttpException(
                    404, 
                    'Failed to find the opening balance'
                );
            }
        } else {
            // Return 0 when opening balance row is empty
            return false;
        }

    }
    public function getMember()
    {
    	return $this->hasOne(Member::className(), ['memberid' => 'memberid']);
    }
    public function getCheque()
    {
    	return $this->hasMany(Cheque::className(), ['BillId' => 'billid']);
    }
    /**
     * To get bills info for sync
     * @param unknown $currentMonth
     * @param unknown $currentYear
     * @param unknown $memberId
     * @param unknown $institutionId
     * @return \yii\db\false|boolean
     */
    public static function getBillsInfoForSync($currentMonth, $currentYear, $memberId, $institutionId)
    {
    	try {
    		$billInfo = Yii::$app->db->createCommand("
    						CALL getbillinfoforsync(:memberid,:month,:year,:institutionid)")
    		    				->bindValue(':memberid', $memberId)
    		    				->bindValue(':month', $currentMonth)
    		    				->bindValue(':year', $currentYear)
    		    				->bindValue(':institutionid', $institutionId)
    		    				->queryOne();
    		return $billInfo;
    		
    	} catch (Exception $e) {
    		return false;
    	}
    }
	
}
