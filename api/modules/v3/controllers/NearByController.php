<?php 

namespace api\modules\v3\controllers;

use Yii;
use yii\base\ErrorException;
use api\modules\v3\controllers\BaseController;
use common\models\extendedmodels\ExtendedUserLocation;
use api\modules\v3\models\responses\ApiResponse;
use yii\helpers\ArrayHelper;
use Exception;
class NearByController extends BaseController
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
							'remember-my-location' => ['POST'],
							'get-members-near-by' => ['GET']
										
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
		$this->statusCode = 404;
		throw new \yii\web\HttpException($this->statusCode);
	}
	
	/**
	 * To save the user location details
	 * @param $userId integer
	 * @param $latitude float
	 * @param $longitude float
	 * @return $statusCode int
	 */
	public function actionRememberMyLocation()
	{
		
		$request = Yii::$app->request;
		$userId = $request->getBodyParam('userId');
		$latitude = $request->getBodyParam('latitude');
		$longitude = $request->getBodyParam('longitude');
		$lastUpdatedDateTime = gmdate('Y-m-d H:i:s');
		
		//Loggin user
		$logginUserId = Yii::$app->user->identity->id;
		try{
			if ($logginUserId) {
				if($userId && $latitude && $longitude) {
				
					/*$isUserIdExist = ExtendedUserLocation::find()
								    ->where( [ 'userid' => $userId ] )
								    ->exists(); 
					
					if ($isUserIdExist) {
						$deleteUserLocation = ExtendedUserLocation::deleteUserLocation($userId);
					}
					$addUserLocation = ExtendedUserLocation::addUserLocation($userId,$latitude,$longitude,$lastUpdatedDateTime);*/

					$locationModel = ExtendedUserLocation::find()->where([ 'userid' => $userId ])->one();
					if(!$locationModel) {
						$locationModel = new ExtendedUserLocation();
						$locationModel->userid = $userId;
					}
					$locationModel->latitude = $latitude;
					$locationModel->longitude = $longitude;
					$locationModel->lastupdateddatetime = $lastUpdatedDateTime;

					if ($locationModel->save()) {
						$this->statusCode = 200;
						$this->message = 'Location saved';
						$this->data = new \stdClass();
					}
					return new ApiResponse($this->statusCode,$this->data,$this->message);
				} else {
					$this->statusCode = 500;
					$this->message = 'An error occurred while processing the request';
					$this->data = new \stdClass();
					return new ApiResponse($this->statusCode,$this->data,$this->message);
				}
			} else {
				$this->statusCode = 498;
				$this->message = 'Session invalid';
				$this->data = new \stdClass();
				return new ApiResponse($this->statusCode,$this->data,$this->message);
			}
		} catch(Exception $e){
			$this->statusCode = 500;
			$this->message = 'An error occurred while processing the request';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		}	
	}
	
	/**
	 * To get find nearest members
	 * @param $userId int
	 * @param $institutionId int
	 * @param $latitude double
	 * @param $longitude double
	 * @param $distance float
	 * @return $statusCode int
	 */
	public function actionGetMembersNearBy()
	{
		
		$request = Yii::$app->request;
		$institutionId = $request->get('institutionId');
		$userId = $request->get('userId');
		$latitude = $request->get('latitude');
		$longitude = $request->get('longitude');
		$distance = $request->get('distance');
		$lastUpdatedDateTime = gmdate('Y-m-d H:i:s');
		//loggin user
		$logginUserId = Yii::$app->user->identity->id;
		try{
			if ($logginUserId) {
				if ($institutionId && $userId && $latitude && $longitude && $distance) {
					$memberData = ExtendedUserLocation::getNearByMembers($institutionId,$userId,$latitude,$longitude,$distance);
					
					/*$deleteUserLocation = ExtendedUserLocation::deleteUserLocation($userId);
					$addUserLocation = ExtendedUserLocation::addUserLocation($userId,$latitude,$longitude,$lastUpdatedDateTime);*/

					$locationModel = ExtendedUserLocation::find()->where([ 'userid' => $userId ])->one();
					if(!$locationModel) {
						$locationModel = new ExtendedUserLocation();
						$locationModel->userid = $userId;
					}
					$locationModel->latitude = $latitude;
					$locationModel->longitude = $longitude;
					$locationModel->lastupdateddatetime = $lastUpdatedDateTime;
					$locationModel->save();
					
					$resultSet = [];
					$result = new \stdClass();
					foreach ($memberData as $key => $value){
						$data = new \stdClass();
						$data->memberId = (!empty($value['memberid'])) ? (int)$value['memberid'] : '';
						$data->memberTitle = (!empty($value['title'])) ? $value['title'] : '';
						$data->memberName = (!empty($value['membername'])) ? $value['membername'] : '';
						$data->memberImage = (!empty($value['memberimage'])) ? (string) preg_replace('/\s/', "%20", yii::$app->params['imagePath'].$value['memberimage']) : '';
						$data->institutionName = (!empty($value['institutionname'])) ? $value['institutionname'] : '';
						$data->updatedOn = (!empty($value['updatedon'])) ? $this->timeAgo($value['updatedon']) : '';
						$data->distance = ($value['distance'] < 1) ? (string)round((($value['distance']-$value['distance'])*1000),2)." Meters away" : round($value['distance'],2)."km away";
						$data->userGroup = (!empty($value['usergroup'])) ? (int)$value['usergroup'] : 0;
						$resultSet[] = $data;
					}
					$result->nearByMembers = $resultSet;
					$this->statusCode = 200;
					$this->message = '';
					$this->data = $result;
					return new ApiResponse($this->statusCode, $this->data,$this->message);
					
				} else {

					$this->statusCode = 500;
					$this->message = 'An error occurred while processing the request';
					$this->data = new \stdClass();
					return new ApiResponse($this->statusCode,$this->data,$this->message);
				}
			} else {
				$this->statusCode = 498;
				$this->message = 'Session invalid';
				$this->data = new \stdClass();
				return new ApiResponse($this->statusCode,$this->data,$this->message);
			}
		} catch(Exception $e) {
			yii::error($e->getMessage());
			$this->statusCode = 500;
			$this->message = 'An error occurred while processing the request';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		}
	}
	/**
	 * 
	 */
	protected function timeAgo($time_ago)
	{
	    $time_ago = strtotimeNew($time_ago);
	    $cur_time   = time();
	    $time_elapsed   = $cur_time - $time_ago;
	    $seconds    = $time_elapsed ;
	    $minutes    = round($time_elapsed / 60 );
	    $hours      = round($time_elapsed / 3600);
	    $days       = round($time_elapsed / 86400 );
	    $weeks      = round($time_elapsed / 604800);
	    $months     = round($time_elapsed / 2600640 );
	    $years      = round($time_elapsed / 31207680 );
	    // Seconds
	    if($seconds <= 60){
	        return "just now";
	    }
	    //Minutes
	    else if($minutes <=60){
	        if($minutes==1){
	            return "one minute ago";
	        } else{
	            return "$minutes minutes ago";
	        }
	    }
	    //Hours
	    else if($hours <=24){
	        if($hours==1){
	            return "an hour ago";
	        } else {
	            return "$hours hrs ago";
	        }
	    }
	    //Days
	    else if($days <= 7){
	        if($days==1){
	            return "yesterday";
	        } else {
	            return "$days days ago";
	        }
	    }
	    //Weeks
	    else if($weeks <= 4.3){
	        if($weeks==1){
	            return "a week ago";
	        } else {
	            return "$weeks weeks ago";
	        }
	    }
	    //Months
	    else if($months <=12){
	        if($months==1){
	            return "a month ago";
	        } else {
	            return "$months months ago";
	        }
	    }
	    //Years
	    else{
	        if($years==1){
	            return "one year ago";
	        } else {
	            return "$years years ago";
	        }
	    }
	}
}

?>
