<?php 

namespace api\modules\v3\controllers;

use Yii;
use api\modules\v3\controllers\BaseController;
use api\modules\v3\models\responses\ApiResponse;
use yii\helpers\ArrayHelper;
use common\models\extendedmodels\ExtendedUserCredentials;
use common\models\extendedmodels\ExtendedZone;

class ZoneController extends BaseController
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
								'list-zones' => ['GET'],
								'list-active-zones' => ['GET']
							]
						],
					]
				);
	}
	
	/**
	 * Index action
	 * @return $statusCode int
	 */
	public function actionIndex()
	{
		$this->statusCode = 404;
		throw new \yii\web\HttpException($this->statusCode);
	}
	
	/**
	 * Returns all zones for the institution
	 * @return ApiResponse
	 */
	public function actionListZones()
	{
		$request = Yii::$app->request;
		$userId = Yii::$app?->user?->identity?->id;
		
		if ($userId) {
			$institutionId = ExtendedUserCredentials::getUserInstitution($userId);
			$institutionId = $institutionId['institutionid'];
			
			$zoneModel = new ExtendedZone();
			$zones = $zoneModel->find()
				->where(['institutionid' => $institutionId])
				->orderBy(['description' => SORT_ASC])
				->all();
			
			if ($zones) {
				$zoneList = [];
				foreach ($zones as $zone) {
					$result = [
						'zoneId' => (!empty($zone->zoneid)) ? (string)$zone->zoneid : '',
						'description' => (!empty($zone->description)) ? $zone->description : '',
						'institutionId' => (!empty($zone->institutionid)) ? (string)$zone->institutionid : '',
						'active' => (isset($zone->active)) ? (int)$zone->active : 0
					];
					array_push($zoneList, $result);
				}
				
				$data = [
					'zones' => $zoneList
				];
				$this->statusCode = 200;
				$this->message = 'Zones retrieved successfully';
				$this->data = $data;
				return new ApiResponse($this->statusCode, $this->data, $this->message);
			} else {
				$data = [
					'zones' => []
				];
				$this->statusCode = 200;
				$this->message = 'No zones found';
				$this->data = $data;
				return new ApiResponse($this->statusCode, $this->data, $this->message);
			}	
		} else {
			$this->statusCode = 498;
			$this->message = 'Session invalid';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode, $this->data, $this->message);
		}
	}
	
	/**
	 * Returns only active zones for the institution
	 * @return ApiResponse
	 */
	public function actionListActiveZones()
	{
		$request = Yii::$app->request;
		$userId = Yii::$app?->user?->identity?->id;
		
		if ($userId) {
			$institutionId = ExtendedUserCredentials::getUserInstitution($userId);
			$institutionId = $institutionId['institutionid'];
			
			$zoneModel = new ExtendedZone();
			$zones = $zoneModel->getActiveZones($institutionId);
			
			if ($zones) {
				$zoneList = [];
				foreach ($zones as $zone) {
					$result = [
						'zoneId' => (!empty($zone['zoneid'])) ? (string)$zone['zoneid'] : '',
						'description' => (!empty($zone['description'])) ? $zone['description'] : '',
						'institutionId' => (!empty($zone['institutionid'])) ? (string)$zone['institutionid'] : '',
					];
					array_push($zoneList, $result);
				}
				
				$data = [
					'zones' => $zoneList
				];
				$this->statusCode = 200;
				$this->message = 'Active zones retrieved successfully';
				$this->data = $data;
				return new ApiResponse($this->statusCode, $this->data, $this->message);
			} else {
				$data = [
					'zones' => []
				];
				$this->statusCode = 200;
				$this->message = 'No active zones found';
				$this->data = $data;
				return new ApiResponse($this->statusCode, $this->data, $this->message);
			}	
		} else {
			$this->statusCode = 498;
			$this->message = 'Session invalid';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode, $this->data, $this->message);
		}
	}
}
