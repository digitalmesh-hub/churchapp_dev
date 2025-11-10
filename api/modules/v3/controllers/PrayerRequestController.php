<?php

namespace api\modules\v3\controllers;

use Yii;
use yii\base\ErrorException;
use api\modules\v3\controllers\BaseController;
use api\modules\v3\models\responses\ApiResponse;
use common\models\extendedmodels\ExtendedPrayerrequest;
use yii\helpers\ArrayHelper;
use common\models\extendedmodels\ExtendedPrayerrequestnotification;
use common\models\extendedmodels\ExtendedUserMember;
use yii\web\NotFoundHttpException;
use common\components\EmailHandlerComponent;
use common\models\extendedmodels\ExtendedMember;
use common\models\extendedmodels\ExtendedInstitution;
use common\models\extendedmodels\ExtendedTitle;


class PrayerRequestController extends BaseController
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
							'request-prayer' => ['POST'],
						]
					],
				]
				);
	}
	/**
	 * To request a prayer
	 * @param $userId int
	 * @param $institutionId int
	 * @param 
	 */
	public function actionRequestPrayer()
	{
		
		$request = Yii::$app->request;
		$prayerRequestTitle = $request->getBodyParam('prayerRequestTitle');
		$prayerRequestContent = $request->getBodyParam('prayerRequestContent');
		$userId = Yii::$app->user->identity->id;
        $institutionId = Yii::$app->user->identity->institutionid;
        $timeZone = Yii::$app->user->identity->institution->timezone;
        date_default_timezone_set($timeZone);
		$createdTime = date('Y-m-d H:i:s');
		$userType = Yii::$app->user->identity->usertype;
		try{
			if ($userId) {
				if ($institutionId && $prayerRequestContent && $prayerRequestTitle && $createdTime) {
					$institutionStatus = (new \yii\db\Query())
						->select(['active'])
						->from('institution')
						->where(['id' => $institutionId])
						->one();
					if ($institutionStatus['active'] == 1) {
						$addPrayerRequest = ExtendedPrayerrequest::savePrayerRequest($userId,$institutionId,$prayerRequestTitle,$prayerRequestContent,$createdTime);
						if ($addPrayerRequest) {
							$lastPrayerId = $addPrayerRequest['last_insert_id()'];
							$model = $this->findModel($lastPrayerId);
							$memberDetails = ExtendedUserMember::getMemberId($userId, $institutionId, $userType);
							$memberId = $memberDetails['memberid'];
							//sending mail
							$sendMail = $this->sendPrayerRequestMail($prayerRequestContent,$memberId,$institutionId,$model);
							//sending notification
							ExtendedPrayerrequestnotification::prayerRequestNotification($model,$institutionId,$userId,$userType);
							$this->statusCode = 200;
							$this->data = new \stdClass();
							$this->message = 'Prayer request has been sent successfully';
						} else {
							$this->statusCode = 500;
							$this->message = 'An error occurred while processing the request';
							$this->data = new \stdClass();
						}			
					} else {
						$this->statusCode = 601;
						$this->message = 'Inactive institution';
						$this->data = new \stdClass();
					}	
				} else {
					$this->statusCode = 500;
					$this->message = 'An error occurred while processing the request';
					$this->data = new \stdClass();	
				}
			} else {
				$this->statusCode = 498;
				$this->message = 'Session invalid';
				$this->data = new \stdClass();
			}
		} catch(\Exception $e){
			yii::error($e->getMessage());
			$this->statusCode = 500;
			$this->message = 'An error occurred while processing the request';
			$this->data = new \stdClass();
		}
		return new ApiResponse($this->statusCode,$this->data,$this->message);
	}
	protected function findModel($id)
	{
		if (($model = ExtendedPrayerrequest::findOne($id)) !== null) {
			return $model;
		}
		throw new NotFoundHttpException('The requested page does not exist.');
	}
	/**
	 * Sending mail
	 */
	protected function sendPrayerRequestMail($content,$memberId,$institutionId,$model)
	{
		$mailobj = new EmailHandlerComponent();
		$userId = Yii::$app->user->identity->id;
		$memberDetails = ExtendedMember::getMemberData($userId,$institutionId);
		$memberNo = $memberDetails['memberno'];
		$memberImage = yii::$app->params['imagePath'].$memberDetails['memberimage'];
		$toName = $memberDetails['username'];
		
		$from = yii::$app->user->identity->emailid;
		$adminMail = ExtendedInstitution::find()
				->where('id = :institutionid', [':institutionid' => $institutionId])
				->one();
		$requestTitle = $model->subject;
		$createdDate = date_format(date_create($model->createdtime),Yii::$app->params['dateFormat']['viewDateFormat']);
		if (!empty($adminMail->prayeremail)) { 
			$email = explode(",",$adminMail->prayeremail);
				$toEmailId = $email;
				$bcc =  Yii::$app->params['bccEmail'];
				$toadmin = 'ADMIN';
				$institutionLogo = '';
				$title ='';
				$subject ='Prayer Request Received From - ' .$toName. '- ' .$requestTitle. ' - ' . $createdDate;
				$mailContent = [];
				$mailContent['template'] = 'prayerrequest';
				$mailContent['content'] = $content;
				$mailContent['logo'] =$institutionLogo;
				$mailContent['name'] = $toName;
				$mailContent['toname'] = $toadmin;
				$mailContent['memberno'] = $memberNo;
				$mailContent['memberImage'] = $memberImage;
				$attach = '';
				$temp = $mailobj->sendEmail($from,$toEmailId,$title,$subject,$mailContent,$attach,null,$bcc);
		}
	}
}