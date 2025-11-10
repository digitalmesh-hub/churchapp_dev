<?php

namespace api\modules\v3\controllers;

use Yii;
use api\modules\v3\models\responses\ApiResponse;
use yii\helpers\ArrayHelper;
use api\modules\v3\controllers\BaseController;
use common\models\basemodels\Qurbana;
use common\models\basemodels\QurbanaType;
use common\models\basemodels\UserMember;
use common\models\searchmodels\ExtendedQurbanaSearch;
use common\models\extendedmodels\ExtendedMember;
use common\models\extendedmodels\ExtendedInstitution;
use yii\web\UnauthorizedHttpException;
use yii\web\BadRequestHttpException;
use common\components\EmailHandlerComponent;
use common\models\basemodels\Institution;
use yii\base\ActionEvent;


class QurbanaController extends BaseController
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
						'request-qurbana' => ['POST'],
						'list-qurbana-requests' => ['GET'],
						'get-types' => ['GET'],
					]
				],
			]
		);
	}

	function beforeAction($action)
	{
		$this->on(self::EVENT_BEFORE_ACTION, function (ActionEvent $event) {
			$auth = Yii::$app->authManager;
			$user = Yii::$app->user->identity;
			if (in_array($event->action->id,['list-qurbana-requests'])) {
				$userMemberId = $user->getUserMember();
				if (!$auth->checkAccess($userMemberId, '0c26fee6-3df8-4cd6-83d9-45a556a75b64')) {
					throw new UnauthorizedHttpException;
				}      
			}
		});
		return parent::beforeAction($action);
	}

	/**
	 * Error action
	 *
	 * @return void
	 */
	public function actionIndex()
	{
		$this->statusCode = 404;
		throw new \yii\web\HttpException($this->statusCode);
	}

	/**
	 * Post Qurbana Data
	 * 
	 * This method handles the request to post Qurbana data. It validates the request parameters,
	 * checks the user's session, and ensures the requested Qurbana date is valid. If all checks pass,
	 * it processes the Qurbana request.
	 * 
	 * @return ApiResponse
	 * @throws \yii\web\HttpException if there is an error in the request
	 */

	public function actionRequestQurbana()
	{
		date_default_timezone_set('Asia/Kolkata');
		$request = Yii::$app->request;
		$qbType = $request->getBodyParam('type');
		$qbDate = $request->getBodyParam('date');
		$qbName = $request->getBodyParam('qurbanaFor');
		$userId = Yii::$app->user->identity->id ?? null;
		$institutionId = Yii::$app->user->identity->institution->id ?? null;
		$userMember = new UserMember();
		$userMemberData = $userMember->find()->where(['userid' => $userId, 'institutionid' => $institutionId])->one();
		$memberId = $userMemberData->memberid ?? null;

		try {
			if (!$userId || !$memberId) {
				return $this->generateApiResponse(498, 'Session invalid');
			}

			if (!$institutionId || !$qbType || !$qbDate) {
				return $this->generateApiResponse(500, 'Required parameters are missing');
			}

			if (!$this->checkQurbanaDateIsValid($qbDate)) {
				return $this->generateApiResponse(500, 'You cannot request for the selected date');
			}
			$qurbanaModel = new Qurbana();
			$qbTypeName = QurbanaType::find()
				->select('type')
				->where(['id' => $qbType])
				->scalar();
			$qurbanaAlreadyPresent = $qurbanaModel->find()->where(['member_id' => $memberId, 'institution_id' => $institutionId, 'qurbana_type_id' => $qbType,  'qurbana_date' => $qbDate])->one();
			if (!empty($qurbanaAlreadyPresent)) {
				return $this->generateApiResponse(500, 'Qurbana already requested for the same date and type');
			}

			if ($qurbanaModel->saveQurbanaRequest($memberId, $institutionId, $qbType, $qbDate, $qbName)) {
				$qbDate = date(Yii::$app->params['dateFormat']['viewDateFormat'], strtotime($qbDate));
				$memberName = ExtendedMember::getMemberName($memberId);
				$fullname = trim($memberName['firstName'] . ' ' . ($memberName['middleName'] ?? '') . ' ' . $memberName['lastName']);
				$adminMail = ExtendedInstitution::find()
					->where('id = :institutionid', [':institutionid' => $institutionId])
					->one();
				$toEmailId = $adminMail->email;
				$institutionLogo = Yii::$app->user->identity->institution->institutionlogo;
				$subject  = "Qurbana Request Received";
				$contentToMember = "A new Qurbana request received from $fullname for $qbTypeName on $qbDate";
				if($qbName){
					$contentToMember .= " for $qbName";
				}
				$this->toSendMsg('ADMIN', $toEmailId, $contentToMember, $institutionLogo, $subject);
				return $this->generateApiResponse(200, 'Qurbana request added successfully');
			} else {
				return $this->generateApiResponse(500, 'An error occurred while processing the request');
			}
		} catch (\Exception $e) {
			return $this->generateApiResponse(500, $e->getMessage());
		}
	}
	/**
	 * Send Email
	 * 
	 * This method sends an email to the admin when a Qurbana request is made.
	 * 
	 * @param string $toadmin The name of the admin
	 * @param string $toEmailId The email address of the admin
	 * @param string $content The content of the email
	 * @param string $institutionLogo The logo of the institution
	 * @param string $subject The subject of the email
	 * 
	 * @return void
	 */
	protected function toSendMsg($toadmin,$toEmailId,$content,$institutionLogo, $subject)
	{ 
		$mailContent = [];
		$from = yii::$app->user->identity->emailid;
		$mailContent['template'] = 'qurbana-request';

		$title = '';
		$emailModal = 	new EmailHandlerComponent();
		$mailContent['content'] = $content;
		$mailContent['name'] = 'ADMIN';
		$mailContent['toname'] = $toadmin;
		$mailContent['logo'] = !empty($institutionLogo) ? yii::$app->params['imagePath'].$institutionLogo : '';
		$attach = '';
		$emailModal->sendEmail($from,$toEmailId,$title,$subject,$mailContent,$attach);
	}
		
	

	/**
	 * Check Qurbana Date is Valid
	 * @param string $date
	 * @return bool
	 */
	private function checkQurbanaDateIsValid($date)
	{

		date_default_timezone_set('Asia/Kolkata');
		$currentDate = date('Y-m-d');
		$requestedTime = date('H:i:s');

		$cutoffTime = '8:00:00';
		$requestedTime = new \DateTime($requestedTime);
		$cutoffTime = new \DateTime($cutoffTime);

		if ($date < $currentDate) {
			return false;
		} else if ($date === $currentDate && $requestedTime > $cutoffTime) {
			return false;
		}
		return true;
	}

	/**
	 * Get Qurbana Types
	 *
	 * @return ApiResponse The response containing the list of Qurbana types.
	 */
	public function actionGetTypes()
	{
		$userId = Yii::$app->user->identity->id;
		try {
			if (!$userId) {
				return $this->generateApiResponse(498, 'Session invalid');
			}
			$qurbanaTypes = QurbanaType::find()->asArray()->all();
			$data['types'] = array_map(function ($item) {
				return [
					'id' => (string)$item['id'],
					'description' => $item['type'],
				];
			}, $qurbanaTypes);

			if (!empty($qurbanaTypes)) {
				return $this->generateApiResponse(200, 'Qurbana types fetched successfully', $data);
			} else {
				return $this->generateApiResponse(404, 'No Data Found');
			}
		} catch (\Exception $e) {
			Yii::error($e->getMessage(), __METHOD__);
			return $this->generateApiResponse(500, 'An error occurred while processing the request');
		}

		return new ApiResponse($this->statusCode, $this->data, $this->message);
	}

	/**
	 * List all Qurbana requests.
	 *
	 * @return ApiResponse The response containing the list of Qurbana requests.
	 */
	public function actionListQurbanaRequests()
	{
		date_default_timezone_set('Asia/Kolkata'); //Convert it to user timezone,from devicee details table
		$currentDate = date('Y-m-d H:i:s');
		$userId = Yii::$app->user->identity->id ?? null;
		$institutionId = Yii::$app->user->identity->institution->id ?? null;
		$institution = Institution::findOne($institutionId);
		$institutionName = $institution->name;

		$this->data = new \stdClass();

		try {
			if (!$userId) {
				return $this->generateApiResponse(498, 'Session invalid');
			}
			if (!$institutionId) {
				return $this->generateApiResponse(500, 'Institution is not associated with the user');
			}
			$searchModel = new ExtendedQurbanaSearch();
			$reqParams = Yii::$app->request->queryParams;
			$reqParams['qurbana_date'] = $reqParams['qurbana_date'] ?? date('Y-m-d');
			$params['ExtendedQurbanaSearch'] = $reqParams;
			$dataProvider = $searchModel->search($params);

			$qurbanaRequests = $dataProvider->getModels();
			$data = [];

			foreach ($qurbanaRequests as $qurbana) {
				$date = date(Yii::$app->params['dateFormat']['viewDateFormat'], strtotime($qurbana['created_at']));
				$time = date(Yii::$app->params['dateFormat']['time12Hr'], strtotime($qurbana['created_at']));
				$qdate = date(Yii::$app->params['dateFormat']['viewDateFormat'], strtotime($qurbana['qurbana_date']));
				$memberData = $qurbana->member;

				$data[] = [
					'qurbanaType' => (string)$qurbana->qurbanatype->type ?? '',
					'qurbanaDate' => $qdate,
					'requestedOn' => $date,
					'qurbanaFor' => (!empty($qurbana['name'])) ? $qurbana['name'] : (string)$qurbana->member->FullNameWithTitle,
					'requestedTime' => $time,
					'institution' => $institutionName,
					'memberId' => (string)$qurbana->member->memberid,
					'name' => $qurbana->member->FullNameWithTitle,
					'image' => (!empty($memberData->member_pic)) ?
						(string) preg_replace('/\s/', "%20", yii::$app->params['imagePath'] . $memberData->member_pic) : '',
				];
			}

			$this->data = [
				'qurbanas' => $data,
			];

			return $this->generateApiResponse(200, 'Qurbana requests retrieved successfully', $this->data);
		} catch (BadRequestHttpException $e) {
			return $this->generateApiResponse(400, $e->getMessage());
		} catch (\Exception $e) {
			return $this->generateApiResponse(500, $e->getMessage());
		}
	}
	/**
	 * Generate API response
	 *
	 * @param int $statusCode The status code of the response
	 * @param string $message The message of the response
	 * @param mixed $data The data of the response
	 * @return ApiResponse The API response
	 */
	private function generateApiResponse($statusCode, $message, $data = null)
	{
		$data = $data ?? new \stdClass();
		return new ApiResponse($statusCode, $data, $message);
	}
}
