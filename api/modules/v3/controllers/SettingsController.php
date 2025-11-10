<?php 

namespace api\modules\v3\controllers;

use Yii;
use api\modules\v3\controllers\BaseController;
use api\modules\v3\models\responses\ApiResponse;
use yii\helpers\ArrayHelper;
use common\models\extendedmodels\ExtendedUserCredentials;
use common\models\extendedmodels\ExtendedTitle;
use common\models\extendedmodels\ExtendedSettings;
use common\models\extendedmodels\ExtendedUserMember;
use common\models\extendedmodels\ExtendedMember;
use common\models\extendedmodels\ExtendedLocalAuthenticationRegisteredUser;

class SettingsController extends BaseController
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
								'get-application-data' => ['GET'],
								'get-application-settings' => ['GET'],
								'save-application-settings' => ['POST'],
								'register-device-for-biometric-authentication' => ['POST'],
								'deregister-device-from-biometric-authentication' => ['POST']
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
	 * returns all data for 
	 * the default drop-down values 
	 * or any other data.
	 */
	public function actionGetApplicationData()
	{
		$request = Yii::$app->request;
		$userId = Yii::$app->user->identity->id;
		if ($userId) {
			$institutionId = ExtendedUserCredentials::getUserInstitution($userId);
			$institutionId = $institutionId['institutionid'];
			$titleObj = new ExtendedTitle();
			$titleResponse = $titleObj->getActiveTitles($institutionId);
			if ($titleResponse) {
				$title = [];
				foreach ($titleResponse as $key => $value) {
					$result = [
							'optionId' => (!empty($value['TitleId'])) ? (string)$value['TitleId'] :'',
							'description' => (!empty($value['Description'])) ? $value['Description'] :''
					];
					array_push($title, $result);
				}
				$data = [
						'title' => $title
				];
				$this->statusCode = 200;
				$this->message = '';
				$this->data = $data;
				return new ApiResponse($this->statusCode, $this->data,$this->message);
			} else {
				$data = [
						'title' => []
				];
				$this->statusCode = 200;
				$this->message = '';
				$this->data = $data;
				return new ApiResponse($this->statusCode, $this->data,$this->message);
			}	
		} else {
			$this->statusCode = 498;
			$this->message = 'Session invalid';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		}
	}
	/**
	 * to retrieve user's 
	 * current settings on the app.
	 * @param $userId string
	 */
	public function actionGetApplicationSettings()
	{
		$request = Yii::$app->request;
		$userId = $request->get('userId');
		if ($userId) {
			$institutionId = ExtendedUserCredentials::getUserInstitution($userId);
			$institutionId = $institutionId['institutionid'];
			$userType = ExtendedUserCredentials::getUserType($userId);
			$userType = $userType['usertype'];
			$responseSettings = ExtendedSettings::getUserSettings($userId, $institutionId);
			$data = new \stdClass();
			
			if($userType == ExtendedMember::USER_TYPE_MEMBER) {
				$data->mobilePrivacyEnabled = (!empty($responseSettings['membermobilePrivacyEnabled']))?(bool)$responseSettings['membermobilePrivacyEnabled']: false;
			} elseif ($userType == ExtendedMember::USER_TYPE_SPOUSE) {
				$data->mobilePrivacyEnabled = (!empty($responseSettings['spousemobilePrivacyEnabled']))? (bool)$responseSettings['spousemobilePrivacyEnabled']: false;
			} else{
				$data->mobilePrivacyEnabled = false;
			}
			$this->statusCode = 200;
			$this->message = '';
			$this->data = $data;
			return new ApiResponse($this->statusCode, $this->data,$this->message);
		} else {
			$this->statusCode = 500;
			$this->message = 'An error occurred while processing the request';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		}
	}
	/**
	 * to save user's 
	 * current settings.
	 * @param $mobilePrivacyEnabled boolean
	 */
	public function actionSaveApplicationSettings()
	{
		$request = Yii::$app->request;
		$mobilePrivacyEnabled = $request->getBodyParam('mobilePrivacyEnabled');
		$userId = Yii::$app->user->identity->id;
	  	$userType = Yii::$app->user->identity->usertype;
	  	$institutionId = Yii::$app->user->identity->institutionid;
		if($userId) {
			$userMemberResponse = self::getUserMember($userId, $institutionId);
			if ($userMemberResponse) {
				$memberId = $userMemberResponse['memberid'];
				$response = ExtendedSettings::updatePrivacy($memberId, $userType, $mobilePrivacyEnabled);
				if ($response == true) {
						$this->statusCode = 200;
						$this->message = 'Saved successfully';
						$this->data = new \stdClass();
						return new ApiResponse($this->statusCode, $this->data,$this->message);
				} else {
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
		} else {
			$this->statusCode = 498;
			$this->message = 'Session invalid';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		}
	}
	 
    public static function getUserMember($userId, $institutionId)
    {
    	return  Yii::$app->db->createCommand("select usermember.* from usermember where usermember.userid=:userid 
    										  and institutionid =:institutionid")
    				->bindValue(':userid', $userId)
    				->bindValue(':institutionid', $institutionId)
    				->queryOne();
	}
	

	public function actionRegisterDeviceForBiometricAuthentication() {

		$request = Yii::$app->request;
		$userId = Yii::$app->user->identity->id;
		$deviceIdentifier = $request->getBodyParam('deviceIdentifier');

		if($userId) {

			if (!$deviceIdentifier || trim($deviceIdentifier) === "") {
				$this->statusCode = 500;
				$this->message = 'Sorry, something went wrong. Please try again.';
				$this->data = new \stdClass();
			}
			else {

				$extendedModelLocalAuthRegisteredUser = new ExtendedLocalAuthenticationRegisteredUser();
				$status = $extendedModelLocalAuthRegisteredUser->registerUserForLocalAuthentication($userId, $deviceIdentifier);
				
				if ($status) {
					$this->statusCode = 200;
					$this->message = 'Your device has been successfully registered.';
					$this->data = new \stdClass();
				}
				else {
					$this->statusCode = 500;
					$this->message = 'Sorry, we cannot complete your request right now. Please try after sometime.';
					$this->data = new \stdClass();
				}

			}

			return new ApiResponse($this->statusCode,$this->data,$this->message);

		} else {
			$this->statusCode = 498;
			$this->message = 'Session invalid';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		}

	}


	public function actionDeregisterDeviceFromBiometricAuthentication() {

		$request = Yii::$app->request;
		$userId = Yii::$app->user->identity->id;
		$deviceIdentifier = $request->getBodyParam('deviceIdentifier');
		
		if($userId) {

			if (!$deviceIdentifier && trim($deviceIdentifier) === "") {
				$this->statusCode = 500;
				$this->message = 'Sorry, something went wrong. Please try again.';
				$this->data = new \stdClass();
			}
			else {

				$extendedModelLocalAuthRegisteredUser = new ExtendedLocalAuthenticationRegisteredUser();
				$status = $extendedModelLocalAuthRegisteredUser->deregisterUserFromLocalAuthentication($userId, $deviceIdentifier);
				
				$this->statusCode = 200;
				$this->message = 'Your device has been successfully removed.';
				$this->data = new \stdClass();
			}

			return new ApiResponse($this->statusCode,$this->data,$this->message);
		} else {
			$this->statusCode = 498;
			$this->message = 'Session invalid';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		}

	}
}
