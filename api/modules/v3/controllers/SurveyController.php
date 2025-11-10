<?php 

namespace api\modules\v3\controllers;

use Yii;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use api\modules\v3\models\responses\ApiResponse;
use common\models\extendedmodels\ExtendedSurvey;
use common\models\extendedmodels\ExtendedUserMember;
use common\models\extendedmodels\ExtendedSurveystatus;

class SurveyController extends BaseController
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
						'get-surveys' => ['GET'],
						'intimate-survey-participation' => ['POST']		
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
	 * Get surveys 
	 * @return $statusCode int
	 * @return $data array
	 * @return $message string
	*/
	public function actionGetSurveys()
	{
	
		$surveyModel = new ExtendedSurvey();
		$userId = Yii::$app->user->identity->id;
		$institutionId = Yii::$app->user->identity->institutionid;
		$memberDetails = ExtendedUserMember::getMemberIdByUserIdAndInstitutionId($userId, $institutionId);
        $memberId = $memberDetails['memberid'];
        $userType = $memberDetails['usertype'];
        $surveys = [];
		try {
			if ($userId) {
				$surveyList = $surveyModel->getSurveys($memberId, $userType, $institutionId);
				if ($surveyList) {
					if(is_array($surveyList)){
						foreach($surveyList as $model) {
							$result = [
								'surveyId' => (!empty($model['surveyid']))? $model['surveyid'] : '',
								'surveyTitle' => (!empty($model['Description'])) ? $model['Description'] : '',
								'userReferenceId' => (!empty($model['token'])) ? $model['token'] : '',
							];
							array_push($surveys, $result);
						}

						$data = [
							'memberId' => (!empty($surveyList[0]['memberid'])) ? $surveyList[0]['memberid'] : '',
							'firstName' => (!empty($surveyList[0]['firstname'])) ? $surveyList[0]['firstname'] : '',
							'middleName' => (!empty($surveyList[0]['middlename'])) ? $surveyList[0]['middlename']: '',
							'lastName' => (!empty($surveyList[0]['lastName'])) ? $surveyList[0]['lastName']: '',
							'emailAddress' => (!empty($surveyList[0]['email'])) ? $surveyList[0]['email'] : '',
							'mobileNumber' => (!empty($surveyList[0]['mobilenumber'])) ? $surveyList[0]['mobilenumber'] : '',
							'surveys' => $surveys
						];
					} else {
						$data = [
							'memberId' => '',
							'firstName' =>  '',
							'middleName' => '',
							'lastName' => '',
							'emailAddress' =>  '',
							'mobileNumber' =>  '',
							'surveys' => $surveys
						];
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
			} else {
				$this->statusCode = 498;
				$this->message = 'Session invalid';
				$this->data = new \stdClass();
				return new ApiResponse($this->statusCode,$this->data,$this->message);
			}
		} catch(\Exception $e) {
			yii::error($e->getMessage());
			$this->statusCode = 500;
			$this->message = 'An error occurred while processing the request';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		}
	}

	/**
	 * Intimate survey participation
	 * @return $statusCode int
	 * @return $data array
	 * @return $message string
	*/
	public function actionIntimateSurveyParticipation()
	{
		$request = Yii::$app->request;
		$surveyId = $request->post('surveyId');
		$memberId = $request->post('memberId');
		$token = $request->post('token');
		$userId = Yii::$app->user->identity->id;
		$institutionId = Yii::$app->user->identity->institutionid;

		$memberDetails = ExtendedUserMember::getMemberIdByUserIdAndInstitutionId($userId, $institutionId);
        $memberType = $memberDetails['usertype'];
		if($token && $surveyId && $memberId) {
			$model = ExtendedSurveystatus::findOne([
   							'surveyid' => $surveyId,
   							'memberid' => $memberId,
   							'membertype' => $memberType
			]);
			if (!$model) {
				$model = new ExtendedSurveystatus();
				$model->loadDefaultValues();
				$model->surveyid = $surveyId;
				$model->memberid = $memberId;
				$model->membertype = $memberType;
				$model->token = $token;
			} else {
				$model->token = $token;
			}
			if($model->save()){
				$this->statusCode = 200;
				$this->message = "";
				$this->data = new \stdClass();
			} else {
				yii::error('Update failed '.print_r($model->getErrors(),true));
				$this->statusCode = 500;
				$this->message = 'An error occurred while processing the request';
				$this->data = new \stdClass();
			}
		} else {
				$this->statusCode = 500;
				$this->message = 'Missing required parameter(s)';
				$this->data = new \stdClass();
		}	
		return new ApiResponse($this->statusCode, $this->data, $this->message);
	}
}