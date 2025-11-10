<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use backend\controllers\BaseController;
use common\models\extendedmodels\ExtendedUserprofile;
use common\models\extendedmodels\ExtendedBills;
use common\models\extendedmodels\ExtendedMember;
use common\models\extendedmodels\ExtendedCheque;
use common\models\extendedmodels\ExtendedInstitution;
use common\models\extendedmodels\ExtendedBillReceipt;
use common\models\searchmodels\MemberSearch;
use common\models\basemodels\Cheque;
use common\models\extendedmodels\ExtendedNeft;
use common\models\basemodels\Member;
use yii\web\NotFoundHttpException;
use kartik\mpdf\Pdf;

class BillManagerController extends BaseController
{
	
    function beforeAction($action)
    {   
        //if manage billing privilage is set user has access.
        if (!Yii::$app->user->can('145ddf4b-1e84-4027-8026-f3de456c6400')) {
            throw new \yii\web\ForbiddenHttpException('You are not allowed to perform the requested action');
        }
        return parent::beforeAction($action);
    }
	   
	/**
	 * Lists all ExtendedUserprofile models with 
	 * same institution except the logged in user
	 * @return mixed
	 */
	
	public function actionHome()
	{
		$searchModel = new MemberSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
				return $this->render('bill-home', [
				'searchModel' => $searchModel,
				'dataProvider' => $dataProvider,
		]);
	}
	
	/**
	 * render index page
	 * @return mixed
	 */
	public function actionIndex($id)
	{
		$memberid= $id;
		$memberDetails = ExtendedMember::find()->select(array ('memberid'))->where('institutionid = :InstitutionId', [':InstitutionId' => yii::$app->user->identity->institutionid])->orderBy(['firstName'=>SORT_ASC, 'middleName' => SORT_ASC,'lastName' => SORT_ASC])->all();
		$data = yii\helpers\ArrayHelper::toArray($memberDetails);
		$member = ExtendedMember::find()->where('memberid = :memberid', [':memberid' => $id])->one();
		
		//to form month dropdown list
		$months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
		
		//to form year dropdown list
		$years = [];
		for($i = date('Y')-5 ; $i <= date('Y'); $i++) {
			$years[$i] = $i;
		}
		
		return $this->render('index',[
				'months' => $months,
				'years' => $years,
				'member' => $member,
				'data'	=> $data,	
				'memberid' => $memberid
		]);
	}
	
	/**
	 * action to save or update bill details
	 * @return JSON
	 */
	public function actionUploadBill()
	{
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//returning array
		$result = [];
		$raw = Yii::$app->request->post('json');
		$jsonData = json_decode($raw);
		if($jsonData){
			//set result status as success
			$result = ['status' => 'success'];
			//get parameters
			$memberid = isset($jsonData->memberid) ? $jsonData->memberid: '';
			$month = isset($jsonData->month) ? $jsonData->month + 1 : '';
			$year = isset($jsonData->year) ? $jsonData->year : '';
			$billId = isset($jsonData->billId) ? $jsonData->billId : '';
			$transactionDate = isset($jsonData->transactionDate) ? $jsonData->transactionDate : '';
			$description = isset($jsonData->description) ? $jsonData->description : '';
			$debit = (isset($jsonData->debit) && $jsonData->debit > 0)? number_format((float)$jsonData->debit, 2, '.', '') : '';
			$credit = (isset($jsonData->credit) && $jsonData->credit > 0)? number_format((float)$jsonData->credit, 2, '.', '') : '';
			$paymentType = isset($jsonData->paymentType) ? $jsonData->paymentType : '';
			//echo $paymentType;
			$memberNo = ExtendedMember::find()->select('memberno')->where('memberid = :MemberId', [':MemberId' => $memberid])->scalar();
			// Gets action type. ie, create or update
			$actionType = $jsonData->actionType;
			
			//transaction date stored in db as dd.mm.yyyy format
			$transactionDate = explode('-', $transactionDate);
			$transactionDate = $transactionDate[2] . '.' . $transactionDate[1] . '.' .$transactionDate[0];
			//check if new record or already existing record
			$billModel = !empty($billId) ? ExtendedBills::findOne($billId) : new ExtendedBills();
			$billsid= ExtendedBills::find()->where(['memberid'=>$memberid])->one();
			//setting values to model
			$billModel->memberid = $memberid;
			$billModel->year = $year;
			$billModel->month = $month;
			$billModel->userid = yii::$app->user->id;
			$billModel->institutionid = yii::$app->user->identity->institutionid;
			$billModel->memberNo = $memberNo;
			$billModel->newmembernum = explode("/", $billModel->memberNo, 2)[0];
			$billModel->transactiondate = date(yii::$app->params['dateFormat']['sqlDateFromatWithDotSep'],strtotimeNew($transactionDate));
			$billModel->description = $description;
			$billModel->debit = $debit;
			$billModel->credit = $credit;
			$billModel->type = yii::$app->params['otherTransactions']['type'];
			$billModel->transactiontype = '';
			$billModel->amount = '';
			$billModel->voucher = '';
			$billModel->voucherType = '';

			// Creates opening balance entry if not exist
			$status = ExtendedBills::createOpeningBalance(
				$memberid,
				$month,
				$year,
				$memberNo,
				$billModel->memberNo
			);
			if (!$status) {
				$result['upload'] = false;
			}

			//insert bill entry
			if ($status && $billModel->save()) {
				// When user edits an older bill, then correct the opening balance
				// for the later bills
				if (   $year < date('Y') 
					|| ($year == date('Y') && $month < date('m'))
				) {
					ExtendedBills::updateSubsequentOpeningBalances(
						$memberid,
						$month,
						$year
					);
				}
				
				//if credit amount transacted through cheque
				if($paymentType == 'cheque'){
					
					//getting cheque details
					$chequeNo = isset($jsonData->ChequeNo) ? $jsonData->ChequeNo : '';
					$chequeDate = isset($jsonData->ChequeDate) ? date(yii::$app->params['dateFormat']['sqlDateFormat'], strtotimeNew($jsonData->ChequeDate)) : '';
					$bank = isset($jsonData->Bank) ? $jsonData->Bank : '';
					$branch = isset($jsonData->Branch) ? $jsonData->Branch : '';
					$chequeModel = ExtendedCheque::find()->where('BillId = :BillId', [':BillId' => $billId])->one();
					
					//if record doesnot exist create new record
					if(!$chequeModel){
						$chequeModel = new ExtendedCheque();
					}
					
					//set values into model
					$chequeModel->ChequeNo = $chequeNo;
					$chequeModel->Date = $chequeDate;
					$chequeModel->Bank = $bank;
					$chequeModel->Branch =$branch;
					$chequeModel->BillId = $billModel->billid;
					$chequeModel->paymentType = $paymentType;
					
					//insert record
					if($chequeModel->save()){
						$result['upload'] = true;
					} else {
						$billModel->delete();
						$result['upload'] = false;
					}
				} else if ( $paymentType == 'neft') {
				  
				    $neftNo = isset($jsonData->NeftNo) ? $jsonData->NeftNo : '';
				    $Neftbank = isset($jsonData->NeftBank) ? $jsonData->NeftBank : '';
				    $Neftbranch = isset($jsonData->NeftBranch) ? $jsonData->NeftBranch : '';
				    $NeftDate = !empty($jsonData->NeftDate) ? date(yii::$app->params['dateFormat']['sqlDateFormat'], strtotimeNew($jsonData->NeftDate)) : null;
				    $chequeModel = ExtendedCheque::find()->where('BillId = :BillId', [':BillId' => $billId])->one();
				    
				    //if record doesnot exist create new record
				    if(!$chequeModel){
				        $chequeModel = new ExtendedCheque();
				    }
				    
				    $chequeModel->BillId = $billModel->billid;
				    $chequeModel->NeftNo = $neftNo;
				    $chequeModel->paymentType = $paymentType;
				    $chequeModel->Bank = $Neftbank;
				    $chequeModel->Branch =$Neftbranch;
				    $chequeModel->Date = $NeftDate;
				   
				    if($chequeModel->save()){
				        $result['upload'] = true;
				    } else {
				        $billModel->delete();
				        $result['upload'] = false;
				    }
				    
				} else if ( $paymentType == 'upi') {
				  
				    //getting cheque details
					$UpiId = isset($jsonData->UpiId) ? $jsonData->UpiId : '';
					$UpiDate = isset($jsonData->UpiDate) ? date(yii::$app->params['dateFormat']['sqlDateFormat'], strtotimeNew($jsonData->UpiDate)) : '';
					$TxnId = isset($jsonData->TxnId) ? $jsonData->TxnId : '';
					
					$chequeModel = ExtendedCheque::find()->where('BillId = :BillId', [':BillId' => $billId])->one();
					
					//if record doesnot exist create new record
					if(!$chequeModel){
						$chequeModel = new ExtendedCheque();
					}
					
					//set values into model
					$chequeModel->ChequeNo = $TxnId;
					$chequeModel->Date = $UpiDate;
					$chequeModel->UpiId = $UpiId;
					$chequeModel->BillId = $billModel->billid;
					$chequeModel->paymentType = $paymentType;
					
					//insert record
					if($chequeModel->save()){
						$result['upload'] = true;
					} else {
						$billModel->delete();
						$result['upload'] = false;
					}
				    
				} else if ( $paymentType == 'card') {
					
					$cardNo = isset($jsonData->CardNo) ? $jsonData->CardNo : '';
					$Cardbank = isset($jsonData->CardBank) ? $jsonData->CardBank : null;
					$chequeModel = ExtendedCheque::find()->where('BillId = :BillId', [':BillId' => $billId])->one();
					
					//if record doesnot exist create new record
					if(!$chequeModel){
						$chequeModel = new ExtendedCheque();
					}
					
					$chequeModel->BillId = $billModel->billid;
					$chequeModel->ChequeNo = $cardNo;
					$chequeModel->paymentType = $paymentType;
					$chequeModel->Bank = $Cardbank;
					
						
					if($chequeModel->save()){
						$result['upload'] = true;
					} else {
						$billModel->delete();
						$result['upload'] = false;
					}
				} else if ( $paymentType == 'cash' ) {
					//if payment done through cash, and there is an existing entry as cheque, then delete that entry
				 	$chequeModel = ExtendedCheque::find()->where('BillId = :BillId', [':BillId' => $billId])->one();
				 	
					if(!$chequeModel){
						$chequeModel = new ExtendedCheque();
					}
					$chequeModel->BillId = $billModel->billid;
					$chequeModel->paymentType = $paymentType;
					if($chequeModel->save()){
						$result['upload'] = true;
					}else {
						$billModel->delete();
						$result['upload'] = false;
					}
				}
				$result['upload'] = true;
			} else {
				$result['upload'] = false;
			}

			// Sends Email to administrator on delete action
			$txnData = [];
			$txnData['memberNo'] = $billModel['memberNo'];
			$txnData['date'] = date(
				yii::$app->params['dateFormat']['viewDateFormat'], 
				strtotimeNew($billModel['transactiondate'])
			);
			$txnData['description'] = !empty($billModel['description']) 
									 ? $billModel['description'] : '';
			$txnData['debit'] = !empty($billModel['debit']) 
							   ? $billModel['debit'] : '-';
			$txnData['credit'] = !empty($billModel['credit']) 
							    ? $billModel['credit'] : '-';
			$txnData['paymentType'] = (is_numeric($billModel['credit']) 
									  && !empty($paymentType))
								   ? $paymentType : 'cash';
			$this->emailOnBillChange($actionType, $txnData);
			
			$billId = $billModel->billid;
			$mailSelect ="select mb.member_email from member mb left join bills bi on mb.memberid = bi.memberid where bi.billId= '$billId' ";
			$emailAddress= Yii::$app->db->createCommand ($mailSelect)->queryOne();
			
			$data = new \stdClass();
			$data->billId = $billId;
			$data->emailAddress = $emailAddress['member_email'];
			if($billModel['credit']!='' || $billModel['debit']!=''){
				$this->emailOnTransaction($data);
			}  
			//getting current records to render table
			$result['bills'] = $this->RenderBills($memberid, $month, $year);
		} else {
			//set result status as success
			$result = ['status' => 'failure'];
		}
		
		return $result;
	}
	
	/**
	 * action to delete a bill entry
	 * @return JSON
	 */
	public function actionDeleteBill()
	{
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//returning array
		$result = [];
		$raw = Yii::$app->request->post('json');
		$jsonData = json_decode($raw);
		
		if ($jsonData) {
			//set result status as success
			$result = ['status' => 'success', 'deleted' => true];
			
			//get parameters
			$memberid = isset($jsonData->memberid) ? $jsonData->memberid: '';
			$month = isset($jsonData->month) ? $jsonData->month + 1 : '';
			$year = isset($jsonData->year) ? $jsonData->year : '';
			$billId = isset($jsonData->billId) ? $jsonData->billId : '';

			//if a cheque details exist, delete that record too
			$chequeModel = ExtendedCheque::find()->where('BillId = :BillId', [':BillId' => $billId])->one();
			if($chequeModel){
				$paymentType = true;
				$chequeModel->delete();
			}
			$receiptModel = ExtendedBillReceipt::find()->where('billId = :billId', [':billId' => $billId])->one();
			if($receiptModel){
				$receiptModel->delete();
			}
			
			//find bill model and delete
			$billModel = ExtendedBills::findOne($billId);
			$billId = $billModel->billid;
			$emailAddress = (new \yii\db\Query())
				->select('mb.member_email')
				->from('member mb')
				->leftJoin('bills bi', 'mb.memberid = bi.memberid')
				->where(['bi.billId' => $billId])
				->one(); 

			$data = new \stdClass();
			$data->billId = $billId;
			$data->emailAddress = $emailAddress['member_email'] ?? '';

			if(!$billModel->delete()){
				$result['deleted'] = false;
			}

			// Correcting the opening balances on old bill entry deletion
			if (   $year < date('Y') 
				|| ($year == date('Y') && $month < date('m')) 
			) {
				ExtendedBills::updateSubsequentOpeningBalances(
					$memberid,
					$month,
					$year
				);
			}
			
			// Sends Email to administrator on delete action
			$txnData = [];
			$txnData['memberNo'] = $billModel['memberNo'];
			$txnData['date'] = date(
				yii::$app->params['dateFormat']['viewDateFormat'], 
				strtotimeNew($billModel['transactiondate'])
			);
			$txnData['description'] = $billModel['description'];
			$txnData['debit'] = !empty($billModel['debit']) 
							   ? $billModel['debit'] : '-';
			$txnData['credit'] = !empty($billModel['credit']) 
							   ? $billModel['credit'] : '-';
			$txnData['paymentType'] = (is_numeric($billModel['credit']) 
									  && !empty($paymentType)) 
							   		 ? 'cheque': 'cash';
			$this->emailOnBillChange('delete', $txnData);
			//getting current records to render table
			$result['bills'] = $this->RenderBills($memberid, $month, $year);
		} else {
			//set result status as failure
			$result = ['status' => 'failure'];
		}
		
		return $result;
	}
			
	/**
	 * action to upload opening balance bill entry
	 * @return JSON
	 */
	public function actionUploadOpenbalance()
	{
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//returning array
		$result = [];
		$raw = Yii::$app->request->post('json');
		$jsonData = json_decode($raw);
		
		if($jsonData){
			//set result status as success
			$result = ['status' => 'success', 'upload' => true];
			
			//get parameters
			$memberid = isset($jsonData->memberid) ? $jsonData->memberid: '';
			$month = isset($jsonData->month) ? $jsonData->month + 1 : '';
			$year = isset($jsonData->year) ? $jsonData->year : '';
			$debit = isset($jsonData->debit) ? $jsonData->debit : '';
			$memberNo = ExtendedMember::find()->select('memberno')->where('memberid = :MemberId', [':MemberId' => $memberid])->scalar();
			
			//find entry for opening balance
			$openingBalanceModel= ExtendedBills::find()->where('type = :Type AND memberid = :MemberId AND month = :Month AND year = :Year',
					[':Type' => yii::$app->params['openBalance']['type'], ':MemberId' => $memberid, ':Month' => $month, ':Year' => $year])->one();
			
			//if no entry exist then create new
			if(!$openingBalanceModel){
				$openingBalanceModel= new ExtendedBills();
				
				//setting values
				$openingBalanceModel->memberid = $memberid;
				$openingBalanceModel->year = $year;
				$openingBalanceModel->month = $month;
				$openingBalanceModel->description = yii::$app->params['openBalance']['description'];
				$openingBalanceModel->debit = $debit;
				$openingBalanceModel->userid = yii::$app->user->id;
				$openingBalanceModel->institutionid = yii::$app->user->identity->institutionid;
				$openingBalanceModel->memberNo = $memberNo;
				$openingBalanceModel->newmembernum = explode("/", $openingBalanceModel->memberNo, 2)[0];
				$openingBalanceModel->type = yii::$app->params['openBalance']['type'];
				
				$openingBalanceModel->transactiondate = '01';
				$openingBalanceModel->transactiondate .= ($month<10) ? '.0' .$month : '.' . $month;
				$openingBalanceModel->transactiondate .= '.' . $year;
				
				$openingBalanceModel->credit = '';
				$openingBalanceModel->transactiontype = '';
				$openingBalanceModel->amount = '';
				$openingBalanceModel->voucher = '';
				$openingBalanceModel->voucherType = '';
				
			} else {
				//if entry exist in table, update debit value
				$openingBalanceModel->debit = $debit;
			}
			
			//insert record
			if(!$openingBalanceModel->save()) {
				$result['upload'] = false;
			}
			
			//getting current records to render table
			$result['bills'] = $this->RenderBills($memberid, $month, $year);
		
		} else {
			//set result status as success
			$result = ['status' => 'failure'];
		}
		
		echo json_encode($result);
	}
	/**
	 * get all bill entries of the user for given month and year
	 * service ajax call from index page
	 * @return JSON
	 */
	public function actionMonthlyBill()
	{
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//returning array
		$result = [];
		$raw = Yii::$app->request->post('json');
		$jsonData = json_decode($raw);
		if($jsonData){
			//set result status as success
			$result = ['status' => 'success'];
			
			//get parameters
			$memberid = isset($jsonData->memberid) ? $jsonData->memberid: '';
			$month = isset($jsonData->month) ? $jsonData->month + 1 : '';
			$year = isset($jsonData->year) ? $jsonData->year : '';
			$result['bills'] = $this->RenderBills($memberid, $month, $year);
		} else {
			//set result status as success
			$result = ['status' => 'failure'];
		}
		
		return $result;
	}
	
	
	/**
	 * get payment details of a bill
	 * @return JSON
	 */
	public function actionPaymentDetails()
	{
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//returning array
		$result = [];
		$raw = Yii::$app->request->post('json');
		$jsonData = json_decode($raw);
		if($jsonData){
			//set result status as success
			$result = ['status' => 'success'];
			
			//get parameter
			$billId = isset($jsonData->billId) ? $jsonData->billId : '';
			
			//find if cheque details exist
			$chequeModel = !empty($billId) ? ExtendedCheque::find()->where('BillId = :BillId', [':BillId' => $billId])->one() : null;
			//setting values for response JSON
			if($chequeModel){
			    if ($chequeModel->paymentType == "cheque" ) {
				$result['paymentType'] = "cheque";
			 	$result['chequeNo'] = $chequeModel->ChequeNo;
			 	$result['chequeDate'] = $chequeModel->Date;
			 	$result['bank'] = $chequeModel->Bank;
			 	$result['branch'] = $chequeModel->Branch;
			    } 
			    
			    else if($chequeModel->paymentType == "neft" ) {
			        $result['paymentType'] = "neft";
			        $result['neftNo'] = $chequeModel->NeftNo;
			        $result['neftDate'] = $chequeModel->Date;
			        $result['neftBank'] = $chequeModel->Bank;
			        $result['neftBranch'] = $chequeModel->Branch;
			        	
			    }
			    else if($chequeModel->paymentType == "card") {
			    	
			    	$result['paymentType'] = "card";
			    	$result['CardNo'] = $chequeModel->ChequeNo;
			    	$result['CardBank'] = $chequeModel->Bank;
			    	
			    } else if($chequeModel->paymentType == "upi") {
			    	
			    	$result['paymentType'] = "upi";
			    	$result['UpiId'] = $chequeModel->UpiId;
			    	$result['UpiDate'] = $chequeModel->Date;
					$result['TxnId'] = $chequeModel->ChequeNo;
			    	
			    }
			 } else {
			 	$result['paymentType'] = "cash";
			 }
		
		} else {
			//set result status as success
			$result = ['status' => 'failure'];
		}
		
		return $result;
	}
	
	/**
	 * function to render bill data into table
	 * @return String
	 */	
	protected function RenderBills($memberid, $month, $year){
		// Getting opening balance, present in `bills` table
		$openingBalanceData = ExtendedBills::getOpeningBalance(
			$memberid, 
			$month, 
			$year
		);
		
		// Calculate opening balance if not exists in the database
		if (empty($openingBalanceData)) {
			$openingBalanceData = ExtendedBills::getBillBalance(
				$memberid, 
				$month, 
				$year
			);
		}
			$openingBalance = $openingBalanceData['balance'];
			$openingBalanceType = $openingBalanceData['type'];
		/* } else {
			$openingBalance = $openingBalanceData['balance'];
			$openingBalanceType = $openingBalanceData['type'];
		} */

		// Rounding the opening balance amount
		if (is_numeric($openingBalance)) {
			$openingBalance = round($openingBalance, 2);
		}

		//getting bill entries
		$billsData = ExtendedBills::find()->where('(type != :Type OR type is null) AND (memberid = :MemberId AND month = :Month AND year = :Year)',
				[':Type' => yii::$app->params['openBalance']['type'], ':MemberId' => $memberid, ':Month' => $month, ':Year' => $year])
				->orderBy('transactiondate ASC')->all();
			
		//render table
		$bills = $this->renderPartial('bill-view', [
			'billsData' => $billsData,
			'openingBalance' =>$openingBalance,
			'openingBalanceType' =>$openingBalanceType
		]);
		return $bills;
	}
	
	/**
	 * action to create pdf reciept. 
	 * $billId - primary key of bill record,
	 * $action - specify the action('D' - download, 'F' - save to file)
	 * $filename - path to save pdf. 
	 * @param integer $billId
	 * @param String $action
	 * @param String $filename
	 * @return boolean
	 */
	protected function createPdf($billId, $action, $filename)
	{
		$pdf = new Pdf();
		
		$receiptNo = ExtendedBillReceipt::getReceiptNo($billId);

		if ($receiptNo === false) {
			throw new \yii\web\HttpException(
				404, 
				'Sorry!, Failed to find the receipt no'
			);
		}

		$bill = ExtendedBills::findOne($billId);	
		$cheque = ExtendedCheque::find()->where('BillId = :BillId', [':BillId' => $billId])->one();
		$member = ExtendedMember::find()->where('memberid = :memberid', [':memberid' => $bill->memberid])->one();
		$institution = ExtendedInstitution::find()->where('id = :institutionId', [':institutionId' => $bill->institutionid])->one();
		$reciept = $this->renderPartial('reciept',[
				'receiptNo' => $receiptNo,
				'bill' => $bill,
				'cheque' => $cheque,
				'member' => $member,
				'institution' => $institution
		]);
        $mpdf = $pdf->api;
		$mpdf->WriteHTML($reciept);
		$mpdf->Output($filename, $action);
		return true;
	}
	
	/**
	 * action to download receipt as pdf
	 * @param integer $id
	 */
	public function actionDownloadReciept($id){
		$filename = 'rec_' . $id. '.pdf';
		$this->createPdf($id, 'D', $filename);
	}
	
	/**
	 * action to sent reciept by mail. Call through ajax
	 * @return JSON
	 */
	public function actionMailReciept()
	{
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//returning array
		$result = [];
		$raw = Yii::$app->request->post('json');
		$jsonData = json_decode($raw);
		$result = $this->emailOnTransaction($jsonData);
		return $result;
		
	}
	/**
	 * 
	 * Returns statement about account
	 * transactions
	 * @param $billId
	 */
	private function mailStatement($billId){
		
		$bill = ExtendedBills::findOne($billId);
		
		$cheque = ExtendedCheque::find()->where(['BillId'=>$billId])->one() ;
		$memberid = $bill['memberid'];
		$month = $bill['month'];
		$year = $bill['year'];
		
		$details = "select bi.*,ch.paymentType from bills bi
					left join member mb on mb.memberid=bi.memberid left join cheque ch on ch.BillId=bi.billid where 
					year ='$year' 
					AND month ='$month' AND mb.memberid='$memberid' 
					and bi.type <> 1 order by bi.transactiondate asc";
		$detailStatement = Yii::$app->db->createCommand ($details)->queryAll();
		$openingBalance = "SELECT transactiondate,debit,credit,type FROM bills bi left join 
							member mb on bi.memberid=mb.memberid where mb.memberid='$memberid' and type=1 and
							year ='$year' and month ='$month'";
		$openingBalanceData = Yii::$app->db->createCommand ($openingBalance)->queryAll();
		
		foreach ($openingBalanceData as $value){
			$OpeningBalanceType = $value['type'];
			$credit = round($value['credit'],2);
			$debit = round($value['debit'],2);
		}
		$txnDetails = '
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
                    <th align="left">Payment Type</th>
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
                        <td>&nbsp;</td>
                        ';
                       
        } elseif ($debit!='') {
            $txnDetails .='
                        <td colspan="3"><strong>Opening Balance</strong></td>
                        <td align="right"><strong>'.yii::$app->MoneyFormat->decimalWithComma($debit).'</strong>
                        </td>
                        <td align="right">&nbsp;</td>
                        <td>&nbsp;</td>
                        ';
        }
			if ($credit!='' || $debit!='') {
			$txnDetails .='</tr>';
			}
			$balance = 0;
			$totalDebit = 0;
			$totalCredit = 0;

        foreach ($detailStatement as $row) {
                    $totalDebit += (float)$row['debit'];
                    $totalCredit += (float)$row['credit'];
                    $totalDebits = $totalDebit+$debit;
                    $totalCredits = $totalCredit+$credit;
                    $trdDate = $row['transactiondate'];
                    $date = str_replace('.', '-', $trdDate);
                    $txnDetails .= '<tr>
                                        <td>'.$row['memberNo'].'</td>
                                        <td>'.date('d-m-Y', strtotimeNew($date)).'</td>
                                        <td>'.$row['description'].'</td>
                                        <td align="right">'.yii::$app->MoneyFormat->decimalWithComma($row['debit']).'
                                        </td>
                                        <td align="right">'.yii::$app->MoneyFormat->decimalWithComma($row['credit']).'
                                        </td>
                                    ';
            if ($row['paymentType']!='') {
                $txnDetails.='<td>'.strtoupper($row['paymentType']).'</td>';
            } elseif ($row['credit'] != '') {
                $txnDetails.='<td>Cash</td>';
            } else {
                $txnDetails.='<td>&nbsp;</td>';
            }

                $txnDetails.= '</tr>';
        }

                    $txnDetails .='<tr>
                            <td><strong>Total</strong></td>
                            <td colspan="2">&nbsp;</td>       
                            <td align="right"><strong>
                            '.yii::$app->MoneyFormat->decimalWithComma($totalDebits).'
                            </strong></td>
                            <td align="right"><strong>
                            '.yii::$app->MoneyFormat->decimalWithComma($totalCredits).'
                           </strong></td>
                           <td>&nbsp;</td>
                        </tr>
                        ';
				$balance = $totalDebits - $totalCredits;
				if($balance >= 0){
                    $txnDetails .= '<tr>
                    <td colspan="3"><strong>Balance : (total debits - total credits)</strong></td>
                    <td align="right"><strong>
                    '."(".yii::$app->MoneyFormat->decimalWithComma($balance).")".'</strong></td>
                    <td align="right">&nbsp;</td>
                    <td>&nbsp;</td>  
                    ';
				}
				if($balance < 0){
				 	$txnDetails .='
                            <td colspan="3"><strong>Balance : (total debits - total credits)</strong></td>
                            <td align="right">&nbsp;</td>
                            <td align="right"><strong>
                            '."(".yii::$app->MoneyFormat->decimalWithComma(-1 * $balance).")".'
                            </strong></td>
                            <td>&nbsp;</td>';
				}
				$txnDetails.='
					</tr>
					</table>';

			return $txnDetails;
		}
	
	/**
	 * sends Email on bill creation,update or delete action
	 * to member
	 * @param $jsonData
	 */
	
	private function emailOnTransaction($jsonData)
	{
		$result = [];
		if($jsonData){
			//set result status as success
			$result = ['status' => 'success', 'mailed' => true];
			//get parameters
			$billId = isset($jsonData->billId) ? $jsonData->billId : '';
			$emailAddress = isset($jsonData->emailAddress) ? $jsonData->emailAddress : '';
			$bill = ExtendedBills::findOne($billId);
			$member = ExtendedMember::find()->where('memberid = :memberid', [':memberid' => $bill->memberid])->one();		
			$institution = ExtendedInstitution::find()->where('id = :institutionId', [':institutionId' => $bill->institutionid])->one();
			$memberid=$member['memberid'];
			$billid =$bill['billid'];
			$debit=$bill['debit'];
			$credit=$bill['credit'];
			$logo = !empty($institution->institutionlogo)
			? yii::$app->params['instituteLogoFrom'] . $institution->institutionlogo
			: null;
			$institutionName = $institution->name;
			$email  = $emailAddress;
			$userID = $memberid;
			$title = 'Receipt for your payment';
			$from = yii::$app->user->identity->emailid;
			$filename = ['rec_' . $billId .'.pdf'];
			$atp = Yii::getAlias('@backend'.Yii::$app->params['email']['attachmentPath']);
			if (!is_dir($atp)) {
            	mkdir($atp, 0777, true); //Make directory with year and month.
        	}
			$path = $atp.$filename[0];
			//if the amount is credited then a mail with attachment will be sent
			if($credit!=''){	
				$Message = 'Dear ' . ucwords($member->firstName) .' '. ucwords($member->middleName) .' '. ucwords($member->lastName) . ' ,<br><br>';
				$Message .= 'Please find the attached receipt for the payment that you made on '
						. date(yii::$app->params['dateFormat']['viewDateFormat'], strtotimeNew($bill->transactiondate))
						. '. You can contact the undersigned if you require any clarification.'
								. '<br><br>';
				//create receipt pdf file and save into file
             
				if($emailAddress && $this->createPdf($billId, 'F', $path)){
								$txnDetails = $this->mailStatement($billId);
								$mailContent['name'] = $institutionName;
								$mailContent['content'] = $Message.$txnDetails;
								$mailContent['template'] = "emailmessage";

								//Sending mail
								$temp = yii::$app->EmailHandler->sendEmail($from,$email,null,$title,$mailContent,$filename, $logo);
		
								if(file_exists($path)){
									unlink($path);
								}
								if(!$temp == 1){
									$result['mailed'] = false;
								}
			} else {
				$result['mailed'] = false;
			}
			}
			//if the amount is debited then a mail with account statement will be sent
			elseif ($debit!=''){
				$title = "Bill Statement";
				$MessageDb = 'Dear ' . ucwords($member->firstName) .' '. ucwords($member->middleName) .' '. ucwords($member->lastName) . ' ,<br><br>';
				$MessageDb .= 'Your account has been updated on ' . date(yii::$app->params['dateFormat']['viewDateFormat'])
				.'.Please see the statement below for the details. You can contact the undersigned if you require any clarification.'. '<br><br>';
				$txnDetails=$this->mailStatement($billId);
				$mailContent['name'] = $institutionName;
				$mailContent['content'] = $MessageDb.$txnDetails;
				$mailContent['template'] = "emailmessage";
				$temp = yii::$app->EmailHandler->sendEmail($from,$email,null,$title,$mailContent,null, $logo);
				
			}
			 
		} else {
			//set result status as success
			$result = ['status' => 'failure'];
		}
		//delete file after sending mail
		if(file_exists($path)) {
			unlink($path);
		}
		return $result;
	}
	
	
	/**
	 * Sends Email on bill create, update or delete action
	 * to administrator
	 * @param  string  $action    Action type. ie, created/update/delete
	 * @param  integer $memberid  Member Id
	 * @param  integer $month  	  Bill month
	 * @param  integer $year  	  Bill year
	 * @return none
	 */
	private function emailOnBillChange($action, $txnData) {
		// Send email only when the 'emailAdminOnBillChange' flag is true
		if (Yii::$app->params['emailAdminOnBillChange']) {
			// Gets the institution model
			$institutionData = ExtendedInstitution::find()->where([
					'id' => Yii::$app->user->identity->institutionid
			])->one();
	
			// ['email' => 'name']
			$from = [
					yii::$app->params['re-memberEmail'] => $institutionData->name
			];
	
			if (!YII_DEBUG && YII_ENV == 'prod') {
				$to  = !empty($institutionData) ? $institutionData->email : null;
	
				//institution logo
				$logo = !empty($institutionData->institutionlogo)
				? yii::$app->params['instituteLogoFrom'] . $institutionData->institutionlogo
				: null;
	
				// Gets the CC email if 'isBillChangeCc' flag is true
				$cc = Yii::$app->params['isBillChangeCc']
				? Yii::$app->params['billChangeCcEmail'] : null;
	
			} else {
				$to = Yii::$app->params['adminEmail'];
				$cc = Yii::$app->params['isBillChangeCc']
				? Yii::$app->params['adminEmail'] : null;
				$logo = null;
	
			}
	
			$subject = 'Bill modification alert';
			$message = 'Dear Admin,<br><br>';
			$message .= 'This is to inform you that, the following '.
					'transaction has been ';
	
			// Composes mail message
			switch ($action) {
				case 'delete':
					$message .= 'deleted.';
					break;
				case 'create':
					$message .= 'added.';
					break;
				case 'update':
					$message .= 'edited.';
					break;
				default:
					// Intentionally left blank
					break;
			}
			$message .= '<br/><br/>';
	
			$memberNo = !empty($txnData['memberNo'])
			? $txnData['memberNo'] : '';
	
			$date = !empty($txnData['date'])
			? $txnData['date'] : '';
	
			$description = !empty($txnData['description'])
			? $txnData['description'] : '';
	
			$debit = !empty($txnData['debit'])
			? $txnData['debit'] : '';
	
			$credit = !empty($txnData['credit'])
			? $txnData['credit'] : '';
	
			$paymentType = (is_numeric($credit) && !empty($txnData['paymentType']))
			? $txnData['paymentType'] : '-';
	
			$txnDetails = '
				 <table cellpadding="5" cellspacing="0"
                        font-family: Tahoma, Geneva, sans-serif;
                        font-size: 13px;
                        color: #000;
                        border="1" 
                        width="800px">
				    <tr>
                        <th align="left">Member No</th>
                        <th align="left">Transaction Date</th>
                        <th align="left">Description</th>
                        <th align="right">Debit(Dr)</th>
                        <th align="right">Credit(Cr)</th>
                        <th align="left">Payment Type</th>
                    </tr>
                    <tr>
                        <td>'.$memberNo.'</td>
                        <td>'.$date.'</td>
                        <td>'.$description.'</td>
                        <td align="right">'.$debit.'</td>
                        <td align="right">'.$credit.'</td>
                        <td>'.$paymentType.'</td>
                    </tr>
				</table>';
				
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
	 * Gets the last cheque details of the given memberid
	 * @param  integer $memberid Member ID
	 * @return string
	 */
	public function actionLastChequeData($memberid)
	{	
		$chequeDataQuery 
			= 'SELECT 
				`cheque`.`Bank`, 
				`cheque`.`Branch`,
				`cheque`.`paymentType`
			FROM
			    `cheque`
			   
			INNER JOIN
			    `bills` ON `cheque`.`BillId` = `bills`.`billid`
			WHERE
			    `memberid` = :MemberID AND `paymentType` = :paymentType 
			ORDER BY 
				`cheque`.`Id` DESC';
		$chequeData = \Yii::$app->db->createCommand($chequeDataQuery)
            ->bindValue(':MemberID', $memberid)
            ->bindValue(':paymentType', 'cheque')
            ->queryOne();
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $chequeData;  
        
	}
	
	/**
	 * Gets the last neft details of the given memberid
	 * @param  integer $memberid Member ID
	 * @return string
	 */
	public function actionLastNeftData($memberid)
	{
	    $chequeDataQuery
	    = 'SELECT
	    `cheque`.`Bank`,
	    `cheque`.`Branch`,
	    `cheque`.`paymentType`
	    FROM
	    `cheque`
	
	    INNER JOIN
	    `bills` ON `cheque`.`BillId` = `bills`.`billid`
	    WHERE
	    `memberid` = :MemberID AND `paymentType` = :paymentType
	    ORDER BY
	    `cheque`.`Id` DESC';
	    $chequeData = \Yii::$app->db->createCommand($chequeDataQuery)
	    ->bindValue(':MemberID', $memberid)
	    ->bindValue(':paymentType', 'neft')
	    ->queryOne();
	    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
	    return $chequeData;
	
	}

	/**
	 * Gets the last upi details of the given memberid
	 * @param  integer $memberid Member ID
	 * @return string
	 */
	public function actionLastUpiData($memberid)
	{
	    $upiDataQuery
	    = 'SELECT
	    `cheque`.`UpiId`,
	    `cheque`.`paymentType`
	    FROM
	    `cheque`
	
	    INNER JOIN
	    `bills` ON `cheque`.`BillId` = `bills`.`billid`
	    WHERE
	    `memberid` = :MemberID AND `paymentType` = :paymentType
	    ORDER BY
	    `cheque`.`Id` DESC';
	    $upiData = \Yii::$app->db->createCommand($upiDataQuery)
	    ->bindValue(':MemberID', $memberid)
	    ->bindValue(':paymentType', 'upi')
	    ->queryOne();
	    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
	    return $upiData;
	
	}
	/**
	 * Displays the account statement
	 * @return $dataSet
	 */
	public function actionStatement()
	{
		$memberid = Yii::$app->request->post ( "memberid" );
		$dataSelect = "select mb.firstName,mb.middleName,mb.lastName,mb.memberno,bi.transactiondate,bi.description,
						bi.debit,bi.credit,ch.ChequeNo,ch.Date,ch.Bank,ch.Branch,ch.paymentType,ch.NeftNo
						from member mb LEFT JOIN bills bi ON mb.memberid = bi.memberid
						LEFT JOIN cheque ch ON ch.billid = bi.billid where mb.memberid = '$memberid'
						order by str_to_date(bi.transactiondate,'%d.%m.%Y') desc";
		$queryResult = Yii::$app->db->createCommand ($dataSelect)->queryAll();
		$dataSet = $this->renderPartial ( 'statement', [
				'queryResult' => $queryResult,
	
		],false, true );
		return $dataSet;
	}
	/**
	 * Finds the ExtendedUserprofile model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $id
	 * @return ExtendedUserprofile the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id)
	{
		if (($model = ExtendedMember::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}
}