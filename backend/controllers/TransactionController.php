<?php
/**
 * @author Digital Mesh
 * @link http://www.digitalmesh.com/
 */
namespace backend\controllers;

use yii\data\ActiveDataProvider;
use common\models\basemodels\PaymentTransactions;
use yii\filters\AccessControl;
use Yii;
use yii\data\Pagination;
use common\models\basemodels\Member;
use backend\controllers\BaseController;
use common\models\basemodels\Institution;
use common\components\ManageTransaction;

class TransactionController extends BaseController
{
	public function behaviors() {
		return [
		];
	}
	public function actions() {
		return [ 
				'error' => [ 
						'class' => 'yii\web\ErrorAction' 
				] 
		];
	}
	/**
	 * rendering to the index page
	 * @return string
	 */
	public function actionIndex() {
		
		
		$dataProvider = new ActiveDataProvider ( [ 
				'query' => PaymentTransactions::find (),
				 
		] );
		
		return $this->render ( 'index', [ 
				'data' => $dataProvider,
		] );
	}
	/**
	 * To perform search using different keywords
	 * @return txnid,memberno,status,firstname,startdate,enddate
	 */
	public function actionSearch() {
		$qryParams = array();
		$qryParams['txnid'] = Yii::$app->request->post ( "txnid" );
		$qryParams['memberno'] = Yii::$app->request->post ( "memberno" );
		$qryParams['status'] = Yii::$app->request->post ( "status" );
		$qryParams['startdate'] = Yii::$app->request->post ( "startdate" );
		$qryParams['name']=Yii::$app->request->post ("name");
		$qryParams['enddate'] = Yii::$app->request->post ( "enddate" );
		$qryParams['page']=Yii::$app->request->post("page")?Yii::$app->request->post("page"):0;
		if($qryParams['page']>0)
		{
		 	$qryParams['page']=$qryParams['page']-1; 
		}
		
		$result = $this->getQueryResult($qryParams);
		$showOtherInfo = false;
		if(yii::$app->user->identity->institutionid == 66 || yii::$app->user->identity->institutionid == 59)
		{
			$showOtherInfo = true;
			foreach ($result['listData'] as &$listData)
			{
				$listData['brn'] = '--';
				$listData['pmd'] = '--';
				if(!empty($listData['response'])){
					$response = json_decode($listData['response']);
					if(!empty($response->data->BRN)){
						$listData['brn'] = $response->data->BRN;
						$listData['pmd'] = $response->data->PMD;
						if(!empty(Yii::$app->params['axisPMD'][$response->data->PMD]))
						{
							$listData['pmd'] = Yii::$app->params['axisPMD'][$response->data->PMD];
						}
					}
				}
			}

		}
		$value = '';
		$value = $this->renderPartial ( 'search', [ 
				'showOtherInfo' => $showOtherInfo,
				'listData' => $result['listData'],
				'totalCount'=>$result['totalCount'],
				'page'=>$qryParams['page'],
				'count'=>$result['count']
		],false, true );	
		return $value;
	}
	/* *
	* payment status enquiry currently only enabled in Cochin Yacht Club
	*/
	public function actionSyncPendingPayment() {
		$data = [
			'memberId' =>yii::$app->request->get("memberId"),
			'transactionId' =>yii::$app->request->get("txnId"),
			'institutionId' =>yii::$app->user->identity->institution->id,
			'type' =>'live',
		];
		$manageTransaction = new ManageTransaction;
		$response = $manageTransaction->transactionStatusCheck($data);
		echo json_encode([
			'error' =>$response['error'] ?? false,
			'status' =>$response['status'] ?? ''
		]);

	}

	/**
	 * To perform search using different keywords
	 * @return txnid,memberno,status,firstname,startdate,enddate
	 */
	public function actionDownload() {
		$qryParams = array();
		$qryParams['txnid'] = Yii::$app->request->get ( "txnid" );
		$qryParams['memberno'] = Yii::$app->request->get ( "memberno" );
		$qryParams['status'] = Yii::$app->request->get ( "status" );
		$qryParams['startdate'] = Yii::$app->request->get ( "startdate" );
		$qryParams['name']=Yii::$app->request->get ("name");
		$qryParams['enddate'] = Yii::$app->request->get ( "enddate" );
		$multiKey = array();
		$selectQuery = $this->buildQuery($multiKey, $qryParams, false);
		
		$selectQuery .= " ORDER BY pt.created DESC";
		
		$result = Yii::$app->db->createCommand ( $selectQuery )->bindValues ( $multiKey )->queryAll ();
		$showOtherInfo = false;
		$filename = "transactions_".$qryParams['startdate']."-".$qryParams['enddate'].".csv";
        $fp = fopen('php://output', 'w');
        ob_start();
		$header = array("Sl.No","Membership No.","Name","Transaction ID","Status","Amount","Date","Source");
		if(yii::$app->user->identity->institutionid == 66 || yii::$app->user->identity->institutionid == 59)
		{
			$showOtherInfo = true;
			array_push($header,'Other Info');
		}
		fputcsv($fp, $header);
		foreach($result as $key=>$data) {
            $row = array();
			$row['slno'] = $key+1;
			$row['membershipNo'] = $data['memberno'];
			$row['name'] = $data['firstName'].' '. $data['middleName'].' '. $data['lastName'];
			$row['transactionID'] = $data['txnid'];
			$row['status'] = $data['status'];
			$row['amount'] = (string)yii::$app->MoneyFormat->decimalWithComma($data['amount']);
			$date = new \DateTime( $data['created'], new \DateTimeZone('Asia/Kolkata'));
			$row['date'] = $date->format('Y-F-d h:i a');
			$row['source'] = $data['source'];
			if ($showOtherInfo)
			{
				$brn = '--';
				$pmd = '--';
				if(!empty($data['response'])){
					$response = json_decode($data['response']);
					if(!empty($response->data->BRN)){
						$brn = $response->data->BRN;
					}
					if (isset($response->data->PMD) && !empty(Yii::$app->params['axisPMD'][$response->data->PMD])) {
						$pmd = Yii::$app->params['axisPMD'][$response->data->PMD];
					}
				}
				$row['otherInfo'] = "BRN: ".$brn.", MOP: ".$pmd;
			}
            fputcsv($fp, $row);
        }

        header('Content-Description: File Transfer');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename='.$filename);
        exit();
	}
	/**
	 * @return query results
	 * @return count,totalCount,txnid,memberno,status,firstname,startdate,enddate
	 * @param array $qryParams
	 * @return number[]|string[]|NULL[]|\yii\db\false[]
	 */
	private function getQueryResult($qryParams = [])
	{
		$multiKey = array();
		
		$countQuery = $this->buildQuery($multiKey, $qryParams, true);
		$selectQuery = $this->buildQuery($multiKey, $qryParams, false);
 		
		$pages=20;
		
		$count = Yii::$app->db->createCommand ( $countQuery )->bindValues ( $multiKey )->queryScalar ();
		
		$totalCount=ceil($count/$pages);
		$startIndex=($qryParams['page']*$pages);
		
		$selectQuery .= " ORDER BY pt.created DESC LIMIT $startIndex,$pages";
		
		$listData = Yii::$app->db->createCommand ( $selectQuery )->bindValues ( $multiKey )->queryAll ();
		$result=array();
		$result['count']=$count;
		$result['totalCount']=$totalCount;
		$result['listData']=$listData;
		
		return $result;
	}
	/**
	 * building query using $selectFragment,$fromFragmnet and $whereFragment
	 * @param array $qryParams
	 * @param string $isCount
	 * @return string
	 */
	private function buildQuery(&$multiKey, $qryParams = [], $isCount = false)
	{
		$selectFragment = '';
		
		if ($isCount){
			$selectFragment = $this->actionCount();
		} else {
			$selectFragment = $this->actionSelect();
		}
		$fromFragment = $this->actionFrom();
		$whereFragment = $this->actionWhere($multiKey, $qryParams);
		return $selectFragment.$fromFragment.$whereFragment;
	}
	/**
	 * Find the total number of records
	 * @return integer
	 */
	private function actionCount()
	{
		$count = "SELECT COUNT(*) AS count ";
		return $count;
	}
	/**
	 * select query
	 * @return string
	 */
	private function actionSelect()
	{
		$dataSelect = "select
				pt.txnid,
				pt.status,
				mb.firstName,
				mb.middleName,
				mb.lastName,
				mb.memberno,
				pt.memberId,
				pt.amount,
				pt.response,
				pt.created,
				ip.`source`";
		return $dataSelect;
	}
	/**
	 * from condition of query
	 * @return string
	 */
	private function actionFrom()
	{
		$from = "from payment_transactions pt LEFT JOIN member mb
				ON pt.memberId=mb.memberid LEFT JOIN institution_payment_gateways ip ON pt.guid = ip.guid "; 
		return $from;
	}
	/**
	 * where condition of query
	 * @param array $qryParams
	 * @param unknown $multiKey
	 * @return string
	 */
	private function actionWhere(&$multiKey,$qryParams = [])
	{
		 $id = yii::$app->user->identity->institutionid;
		$conditionFlag = 1;
		$query = ' WHERE mb.institutionid = :institutionid';
		$multiKey [':institutionid'] = $id;
		if ($qryParams['txnid'] != "") {
			if ($conditionFlag) {
				$query .= " AND ";
			} else {
				$query .= " WHERE ";
				$conditionFlag ++;
			}
			$query .= "(pt.txnid LIKE :txnid)";
			$multiKey [':txnid'] = '%' . trim ( $qryParams['txnid'] ) . '%';
		}
		if ($qryParams['memberno'] != "") {
			if ($conditionFlag) {
				$query .= " AND ";
			} else {
				$query .= " WHERE ";
				$conditionFlag ++;
			}
			$query .= "(mb.memberno LIKE :memberno)";
			$multiKey [':memberno'] = '%' . trim ( $qryParams['memberno'] ) . '%';
		}
		
		if ($qryParams['name'] != "") {
			if ($conditionFlag) {
				$query .= " AND ";
			} else {
				$query .= " WHERE ";
				$conditionFlag ++;
			}
			$query .= "((mb.firstName LIKE :name OR mb.middleName LIKE :name OR mb.lastName LIKE :name 
					OR (concat(mb.firstName,' ',mb.middleName,' ',mb.lastName)) LIKE :name)
					OR (concat(mb.firstName,' ',mb.lastName) LIKE :name ))"; 
			$multiKey ['name'] = '%' . trim ( $qryParams['name'] ) . '%';
		} 
		if ($qryParams['status'] != "") {
			if ($conditionFlag) {
				$query .= " AND ";
			} else {
				$query .= " WHERE ";
				$conditionFlag ++;
			}
			$query .= "(pt.status LIKE :status)";
			$multiKey [':status'] = '%' . trim ( strtolower($qryParams['status']) ) . '%';
		}
		
		
		if ($qryParams['startdate'] !== "") {
			if ($conditionFlag) {
				$query .= " AND ";
			} else {
				$query .= " WHERE ";
				$conditionFlag ++;
			}
			$startdate = date ( 'Y-m-d', strtotime ( $qryParams['startdate'] ) );
			$query .= "(DATE(pt.created) >= :startdate)";
			$multiKey [':startdate'] = trim ( $qryParams['startdate'] );
		}
		if ($qryParams['enddate'] !== "") {
			if ($conditionFlag) {
				$query .= " AND ";
			} else {
				$query .= " WHERE ";
				$conditionFlag ++;
			}
			$enddate = date ( 'Y-m-d', strtotime ( $qryParams['enddate'] ) );
			$query .= "(DATE(pt.created) <= :enddate )";
			$multiKey [':enddate'] = trim ( $qryParams['enddate'] );
		}
		return $query;
		
	}
}

