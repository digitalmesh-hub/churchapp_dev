<?php
namespace api\modules\v3\controllers;

use Yii;
use api\modules\v3\controllers\BaseController;
use api\modules\v3\models\responses\ApiResponse;
use yii\helpers\ArrayHelper;
use common\models\extendedmodels\ExtendedUserMember;
use common\models\extendedmodels\ExtendedMember;
use common\models\extendedmodels\ExtendedBills;
use common\models\extendedmodels\ExtendedBillseendetails;
use common\components\EmailHandlerComponent;
use common\models\extendedmodels\ExtendedInstitution;


class BillsController extends BaseController
{
	public $statusCode;
	public $message = "";
	public $data;
	public $code;
	
	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return ArrayHelper::merge(
				parent::behaviors(),
				[
					'verbs' => [
						'class' => \yii\filters\VerbFilter::className(),
							'actions' => [
								'get-transactions' => ['GET'],
								'export-bills' => ['POST'],
						]
					],
				]
				);
	}
	/**
	 * Index action.
	 * @return $statusCode int
	 */
	public function actionIndex()
	{
		return new ApiResponse(404, new \stdClass(), "Method not found");
	}

	
	/**
	 * To retrieve user's 
	 * current transaction details
	 * @param $memberId string
	 * @param $month string
	 * @param $year string
	 * @param $billType int
	 */
	public function actionGetTransactions()
	{   
		$request = Yii::$app->request;
		$month = $request->get('month');
		$year = $request->get('year');
		$billType = $request->get('billType');
		$month = filter_var($month, FILTER_SANITIZE_NUMBER_INT);
		$year = filter_var($year, FILTER_SANITIZE_NUMBER_INT);
		$institutionId = Yii::$app->user->identity->institutionid;
		$userId = Yii::$app->user->identity->id;
		$userType = Yii::$app->user->identity->usertype;
		$memberDetails = ExtendedMember::getMemberId($userId, $institutionId, $userType);
		$memberId = $memberDetails['memberid'];
		$memberEmail = ExtendedUserMember::getMemberEmail($institutionId, $userId, $userType);
		$memberEmail = $memberEmail['email'];  
		$bills = [];
		if($userId) {
				$billInfoResponse = ExtendedBills::getBillsInfo($month, $year, $memberId, $institutionId);
				if ($billInfoResponse) {
					$nextMonth = $billInfoResponse['@nextmonth'];
					$nextYear = $billInfoResponse['@nextyear'];
					$previousMonth = (!empty($billInfoResponse['@previousmonth'])) ? $billInfoResponse['@previousmonth'] : $month ;
					$previousYear = (!empty($billInfoResponse['@previousyear'])) ? $billInfoResponse['@previousyear'] : $year;
					
					$response = $this->getBillDetails($billType, $nextMonth, $nextYear, $previousMonth, $previousYear, $month, $year, $institutionId, $memberId);
					usort($response, function ($a, $b)
					{
					    $t1 = strtotimeNew($a['transactiondate']);
					    $t2 = strtotimeNew($b['transactiondate']);
					    return $t1 - $t2;
					} );

					$currentMonth = date('m');
					$currentYear = date('Y');
					$openingBalance = 0.0;
					$openingBalanceType = "--";
					$closingBalance = 0;
					if(!empty($response) && count($response) > 0) {
						$firstElement = $response[0];
						$currentMonth = $firstElement['month'];
						$currentYear = $firstElement['year'];
						$billInfoResponse2 = ExtendedBills::getBillsInfo($currentMonth, $currentYear, $memberId, $institutionId);
						
						$normalBillList = [];
						$openingBalanceList = [];
						$normalBillList = array_values(array_filter($response,array($this, 'normalBill')));
						$openingBalanceList = array_values(array_filter($response,array($this, 'openingBill')));
						if(count($normalBillList) < 1 && count($openingBalanceList) == 1) {
						
							$openingDebitAmount = 0;
							$openingCreditAmount = 0;
							$openingBalance = 0.0;
							$openingBalanceType = "--";
							if ($openingBalanceList) {
								$openingDebitAmount = (string)$openingBalanceList[0]['debit'];
								$openingCreditAmount = (string)$openingBalanceList[0]['credit'];
								$openingBalance = ($openingDebitAmount == null ||
										$openingDebitAmount == 0.0 ||
										$openingDebitAmount == 0 ||
										$openingDebitAmount == '') ? (string)$openingCreditAmount : (string)$openingDebitAmount;
								$openingBalanceType = ($openingDebitAmount == null ||
										$openingDebitAmount == 0.0 ||
										$openingDebitAmount == 0 ||
										$openingDebitAmount == '') ? "Cr" : "Dr";
							}
							$closingBalance = 0;
							$data = $this->setData($currentMonth, $currentYear, $openingBalance, $openingBalanceType, $closingBalance, $billInfoResponse2, $memberEmail);
							$data->bills = $bills;
							$billSeenDetails = $this->saveBillSeenDetails($memberId,$userType,$currentMonth,$currentYear,$institutionId);
							$billSeenCount = ExtendedBillseendetails::getBillSeenCount($billSeenDetails);
							$billSeenCount = $billSeenCount['count'];
							if($billSeenCount == 0) {
								$billSeenDetails->save();
							}
						} else {
							$openingDebitAmount = 0;
							$openingCreditAmount = 0;
							$openingBalance = 0.0;
							$openingBalanceType = "--";
							if ($openingBalanceList) {
								$openingDebitAmount = (string)$openingBalanceList[0]['debit'];
								$openingCreditAmount = (string)$openingBalanceList[0]['credit'];
								$openingBalance = ($openingDebitAmount == null ||
										$openingDebitAmount == 0.0 ||
										$openingDebitAmount == 0 ||
										$openingDebitAmount == '') ? (string)$openingCreditAmount : (string)$openingDebitAmount;
										$openingBalanceType = ($openingDebitAmount == null ||
												$openingDebitAmount == 0.0 ||
												$openingDebitAmount == 0 ||
												$openingDebitAmount == '') ? "Cr" : "Dr";
							}
							$closingBalance = 0;
							$dateArray = [];
							foreach ($normalBillList as $data) {
								$date = date_create($data['transactiondate']);
								$groupDate = date_format($date,"d F Y");
								if (!isset($dateArray[$groupDate])){
									$dateArray[$groupDate] = array();
								}
								array_push($dateArray[$groupDate], $data);
							}
						    
						    //array to hold whole transaction records
							$billDatas = [];
							$j = 0;
							foreach ($dateArray as $a => $b) {
								$result = [];
								foreach ($b as $c => $d) {
									$debit = (string)$d['debit'] ;
									$credit = (string)$d['credit'];
									$result['transactionDate'] = date('d F Y',strtotimeNew($a));
									$result['transactions'][$c]['transactionId'] = 0;
									$result['transactions'][$c]['transactionType'] = ($credit)  ?
														"Cr" : "Dr" ;
									$result['transactions'][$c]['transactionDetails'] = (!empty($d['description'])) ? $d['description'] : '';
									$result['transactions'][$c]['transactionAmount'] = ($credit) ?
											$credit : $debit;
								}
								$billDatas[$j] = $result;
								$j++;
							}
							if (count($billDatas) > 0) {
								$billSeenDetails = $this->saveBillSeenDetails($memberId,$userType,$currentMonth,$currentYear,$institutionId);
								$billSeenCount = ExtendedBillseendetails::getBillSeenCount($billSeenDetails);
								$billSeenCount = $billSeenCount['count'];
								if ($billSeenCount == 0) {
									$billSeenDetails->save();
								}
							}
							$data = $this->setData($currentMonth, $currentYear, $openingBalance, $openingBalanceType, $closingBalance, $billInfoResponse2, $memberEmail);
							$data->bills = $billDatas;
						}
					} else {
						$data = $this->setNewData($currentMonth, $currentYear, $openingBalance, $openingBalanceType, $closingBalance, $billInfoResponse, $memberEmail);
						$data->bills = $bills;
						if (count($bills) > 0) {
							$billSeenDetails = $this->saveBillSeenDetails($memberId,$userType,$currentMonth,$currentYear,$institutionId);
							$billSeenCount = ExtendedBillseendetails::getBillSeenCount($billSeenDetails);
							$billSeenCount = $billSeenCount['count'];
							if ($billSeenCount == 0) {
								$billSeenDetails->save();
							}
						}
					}
				} else {
					$data = new \stdClass();
					$data->month = (string)$currentMonth;
					$data->year = (string)$currentYear;
					$data->openingBalance = 0;
					$data->openingBalanceType = "--";
					$data->closingBalance = 0;
					$data->isPreviousMonthDataAvailable = false ;
					$data->isNextMonthDataAvailable = false ;
					$data->memberEmail = ($memberEmail) ? $memberEmail : "";
					$data->bills=$bills; 
				}
				$this->statusCode = 200;
				$this->message = '';
				$this->data = $data;
				return new ApiResponse($this->statusCode,$this->data,$this->message);
			} else {
				$this->statusCode = 500;
				$this->message = 'An error occurred while processing the request1';
				$this->data = new \stdClass();
				return new ApiResponse($this->statusCode,$this->data,$this->message);
			}
	}
	
	/**
	 * To export bills
	 * @param $memberId string
	 * @param $emailAddress string
	 * @param $month string
	 * @param $year string
	 */
	public function actionExportBills()
	{
		$request = Yii::$app->request;
		$memberId = $request->post('memberId');
		$emailAddress = $request->post('emailAddress');
		$month = $request->post('month');
		$year = $request->post('year');
		if($memberId && $emailAddress && $month && $year )
		{
			$memberId = filter_var($memberId, FILTER_SANITIZE_NUMBER_INT);
			$month = filter_var($month, FILTER_SANITIZE_NUMBER_INT);
			$year = filter_var($year, FILTER_SANITIZE_NUMBER_INT);
			$institutionId = ExtendedUserMember::getInstitutionId($memberId);
			$institutionId = $institutionId['institutionid'];
			$userType = Yii::$app->user->identity->usertype;
			$userId = ExtendedUserMember::getUserId($memberId,$userType);
			$userId = $userId['userid'];
			if ($userId) {
				$responseBills = ExtendedBills::getBillListByMember($month, $year, $institutionId, $memberId);
				if ($responseBills) {
					$response = $this->sendBillToMember($responseBills,$memberId,$month,$year,$emailAddress,$institutionId);
					if($response == true) {
						$this->statusCode = 200;
						$this->message = 'The bill details has been sent to the specified email address.';
						$this->data = new \stdClass();
						return new ApiResponse($this->statusCode,$this->data,$this->message);
					} else {
						$this->statusCode = 500;
						$this->message = 'An error occurred while processing the request';
						$this->data = new \stdClass();
						return new ApiResponse($this->statusCode,$this->data,$this->message);
					}
				}
			}else{
				$this->statusCode = 500;
				$this->message = 'An error occurred while processing the request';
				$this->data = new \stdClass();
				return new ApiResponse($this->statusCode,$this->data,$this->message);
			}
		} else {
			$this->statusCode = 500;
			$this->message = 'An error occurred while processing the request';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		}
		
		
	}

	/**
	 * To get the bill
	 * details based on bill type
	 * @param $billType int
	 */
	protected function getBillDetails($billType,$nextMonth,$nextYear,$previousMonth,$previousYear,$month,$year,$institutionId,$memberId)
	{
		if($billType == 1) //navigating to next bill
		{
			$response = ExtendedBills::getBillListByMember($nextMonth, $nextYear, $institutionId, $memberId);
		}
		elseif ($billType == -1) //navigating to previous bill
		{
			$response = ExtendedBills::getBillListByMember($previousMonth, $previousYear, $institutionId, $memberId);
		}
		else
		{
			$response = ExtendedBills::getBillListByMember($month, $year, $institutionId, $memberId);
			if(count($response) == 0 )
			{
				$response = ExtendedBills::getBillListByMember($previousMonth, $previousYear, $institutionId, $memberId);
			}
			
		}
		return $response;
	}
	
	/**
	 * To set the data
	 * for response
	 */
	protected function setData($currentMonth,$currentYear,$openingBalance,
			$openingBalanceType,$closingBalance,$billInfoResponse2,$memberEmail)
	{
		try {
			$data = new \stdClass();
			$data->month = (string)$currentMonth;
			$data->year = (string)$currentYear;
			$data->openingBalance = (!empty($openingBalance))? $openingBalance : 0;
			$data->openingBalanceType = $openingBalanceType;
			$data->closingBalance = (!empty($closingBalance))? $closingBalance : 0;
			$data->isPreviousMonthDataAvailable = ($billInfoResponse2['@previousmonth'] > 0 &&
					$billInfoResponse2['@previousyear'] > 0) ? true : false ;
			$data->isNextMonthDataAvailable = ($billInfoResponse2['@nextmonth'] > 0 &&
							$billInfoResponse2['@nextyear'] > 0) ? true : false ;
			$data->memberEmail = ($memberEmail) ? $memberEmail : "";
		return $data;
								
		} catch (Exception $e) {
			return false;
		}
	
	}
	/**
	 * To add bill seen details
	 */
	protected function saveBillSeenDetails($memberId,$userType,$currentMonth,$currentYear,$institutionId)
	{ 
		try {
			$billSeenDetails = new ExtendedBillseendetails();
			$billSeenDetails->memberid = (int)$memberId;
			$billSeenDetails->usertype = (string)$userType;
			$billSeenDetails->month = (int)$currentMonth;
			$billSeenDetails->year = (int)$currentYear;
			$billSeenDetails->institutionid = (int)$institutionId;
			$billSeenDetails->createddatetime  = date('Y-m-d h:i:s');
			return $billSeenDetails;

		} catch (Exception $e) {
			return false;
		}
	}
	
	/**
	 * to set new
	 * transaction data
	 */
	protected function setNewData($currentMonth,$currentYear,$openingBalance,
									$openingBalanceType,$closingBalance,$billInfoResponse,$memberEmail)
	{ 
		try {
			$data = new \stdClass();
			$data->month = (string)$currentMonth;
			$data->year = (string)$currentYear;
			$data->openingBalance = 0;
			$data->openingBalanceType = "--";
			$data->closingBalance = 0;
			$data->isPreviousMonthDataAvailable = ($billInfoResponse['@previousmonth'] > 0 &&
					$billInfoResponse['@previousyear'] > 0) ? true : false ;
			$data->isNextMonthDataAvailable = ($billInfoResponse['@nextmonth'] > 0 &&
							$billInfoResponse['@nextyear'] > 0) ? true : false ;
			$data->memberEmail = ($memberEmail) ? $memberEmail : "";
			return $data;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/**
	 * To send the mail 
	 * to the member
	 */
	protected function sendBillToMember($responseData, $memberId, $month, $year, $emailAddress, $institutionId)
	{   
		$amount = 0;
		$amountCredit = 0;
		$amountDebit = 0;
		$transactionDate = '';
		$grandTotalDebit = 0;
		$grandTotalCredit = 0;
		$bills = [];
		$billList = [];
		$normalBillList = [];
		$openingBalanceList = [];
		$openingDebit = "";
		$day = "";
		$openingCredit = "";
		$memberName = ExtendedMember::getMemberName($memberId);
		$memberName = $memberName['firstName'];
		$institutionName = ExtendedInstitution::getInstitutionDetails($institutionId);
		$institutionName = $institutionName['name'];
		$monthNum  = $month;
		$monthName = date('F', mktime(0, 0, 0, $monthNum, 10));
		if (count($responseData) > 0) {
			$normalBillList = array_values(array_filter($responseData,array($this, 'normalBill')));
			$openingBalanceList = array_values(array_filter($responseData,array($this, 'openingBill')));
			if(count($normalBillList) < 1 && count($openingBalanceList) == 1) {
				if ($openingBalanceList) {
					$openingDebit = "";
					$openingCredit = "";
					if ($openingBalanceList[0]['debit']) {
						$openingDebit = ($openingBalanceList[0]['debit'] > 0) ? $openingBalanceList[0]['debit'] : '';
					}
					if ($openingBalanceList[0]['credit']) {

						$openingCredit = ($openingBalanceList[0]['credit'] > 0) ? $openingBalanceList[0]['credit'] : '';
					}
					if ($openingBalanceList[0]['debit']) {
						$grandTotalDebit += $openingBalanceList[0]['debit'];
					}
					if ($openingBalanceList[0]['credit']) {
						$grandTotalCredit = $grandTotalCredit + $openingBalanceList[0]['credit'];
					}	
				}
			} else {
				$dateArray = [];
				if (!empty($normalBillList)) {
					foreach ($normalBillList as $data) {
						$date = date_create($data['transactiondate']);
						$groupDate = date_format($date,"Y-m-d");
						if (!isset($dateArray[$groupDate])) {
							$dateArray[$groupDate] = array();
						}
						array_push($dateArray[$groupDate], $data);
					}
				}
				if ($openingBalanceList) {
					$openingDebit = "";
					$openingCredit = "";
					if ($openingBalanceList[0]['debit']) {
						$openingDebit = ($openingBalanceList[0]['debit'] > 0) ? $openingBalanceList[0]['debit'] : '';
					}
					if($openingBalanceList[0]['credit']) {
						$openingCredit = ($openingBalanceList[0]['credit'] > 0) ? $openingBalanceList[0]['credit'] : '';
					}
					if($openingBalanceList[0]['debit']) {
						$grandTotalDebit = $grandTotalDebit + $openingBalanceList[0]['debit'];
					}
					if($openingBalanceList[0]['credit']) {
						$grandTotalCredit = $grandTotalCredit + $openingBalanceList[0]['credit'];
					}
				}

				if (!empty($dateArray)) { 
					foreach ($dateArray as $date => $billData) {
						$bill =[];
						$bill['transactiondate'] = $date;
						$billlist =[];
						if($date) {
							$transactions = [];
						}
						foreach ($billData as $key => $value) {
							$result = [];
							if($date != $value['transactiondate']) {
								$transactionDate = $value['transactiondate'];
								$convert_date = strtotimeNew($transactionDate);
								$day = date('j',$convert_date);
							}
							$description = (!empty($value['description'])) ? $value['description'] : '';
							$result['description'] = $description;
							if(!empty($value['debit'])) {
								$debit = (($value['debit']) > 0) ? ($value['debit']) : '';
								$result['debit'] = $debit;
								$result['credit'] = null;
							}
							if(!empty($value['credit'])) {
								$credit = (($value['credit']) > 0) ? ($value['credit']) : '';
								$result['credit'] = $credit;
								$result['debit'] = null;
							}
							if(!empty($value['debit'])) {
								$grandTotalDebit += $value['debit'];
							}
							if(!empty($value['credit'])){
								$grandTotalCredit += $value['credit'];
							}
							array_push($billlist,$result);
						}
						$bill['billlist'] = $billlist;
						array_push($bills, $bill);
					}
				}
			}
			/*if(round($grandTotalDebit) > round($grandTotalCredit)) {
				$amount = round($grandTotalDebit) - round($grandTotalCredit);
			} elseif (round($grandTotalDebit) < round($grandTotalCredit)) {
				$amount = round($grandTotalCredit) - round($grandTotalDebit);
			} else {
				$amount = round($grandTotalDebit) - round($grandTotalCredit);
			}
			//balance
			if(round($grandTotalDebit) < round($grandTotalCredit) ) {
				$amountCredit = $amount;
			} else {
				//$amount will be displayed in debit column
				$amountDebit = $amount;
			}*/

			//As requested by Qa that not to round amount.
			if(($grandTotalDebit) > ($grandTotalCredit)) {
				$amount = ($grandTotalDebit) - ($grandTotalCredit);
			} elseif (($grandTotalDebit) < ($grandTotalCredit)) {
				$amount = ($grandTotalCredit) - ($grandTotalDebit); 
			} else {
				$amount = ($grandTotalDebit) - ($grandTotalCredit);
			}
			//balance
			if(($grandTotalDebit) < ($grandTotalCredit) ) {
				$amountCredit = $amount;
			} else {
				//$amount will be displayed in debit column
				$amountDebit = $amount;
			}
			$mailobj = new EmailHandlerComponent();
			$from = Yii::$app->params['tempEmail'];
			$email = $emailAddress;
			$title ='';
			$subject = $institutionName .' - Bill for the month '.$monthName;
			$institutionLogo = Yii::$app->user->identity->institution->institutionlogo;
			$mailContent['template'] = 'bill-email';
			$mailContent['name'] = $memberName;
			$mailContent['month'] = $monthName;
			$mailContent['year'] = $year;
			$mailContent['openingdebit'] = $openingDebit;
			$mailContent['openingcredit'] = $openingCredit;
			$mailContent['grandtotaldebit'] = $grandTotalDebit;
			$mailContent['grandtotalcredit'] = $grandTotalCredit;
			$mailContent['balancecredit'] = $amountCredit;
			$mailContent['balancedebit'] = $amountDebit;
			$mailContent['logo'] = yii::$app->params['imagePath'].$institutionLogo;
			$mailContent['transactiondate'] = $day;
			if(!empty($bills))
			{
				$mailContent['transdata'] = $bills;
			}
			$mailContent['institutionname'] = $institutionName;
			$attach = '';
			$temp =  $mailobj->sendEmail($from,$email,$title,$subject,$mailContent,$attach);
			return true;
		}else{
			return false;
		}
		
	}
	/**
	 * Setting array for normal bill
	 */
	public function normalBill($data)
	{
		if($data['type'] != 1) {
			return $data;
		}
	}
	/**
	 * setting array for opening bill
	 */
	public function openingBill($data)
	{
		if($data['type'] == 1) {
			return $data;
		}
	}
}
