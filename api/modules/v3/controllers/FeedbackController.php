<?php 

namespace api\modules\v3\controllers;

use Yii;
use api\modules\v3\models\responses\ApiResponse;
use yii\helpers\ArrayHelper;
use common\models\extendedmodels\ExtendedUserMember;
use common\models\extendedmodels\ExtendedFeedback;
use common\models\extendedmodels\ExtendedInstitution;
use api\modules\v3\controllers\BaseController;
use common\components\FileuploadComponent;
use common\models\extendedmodels\ExtendedFeedbackimagedetails;
use common\models\extendedmodels\ExtendedFeedbacknotification;
use common\models\extendedmodels\ExtendedMember;
use common\components\EmailHandlerComponent;
use common\models\extendedmodels\ExtendedTitle;
use common\models\extendedmodels\ExtendedFeedbacktype;


class FeedbackController extends BaseController 
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
								'save-feedback' => ['POST'],
								'get-feedback-data' => ['GET'],
								'add-feedback-image' => ['POST'],			
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
	 * To save feedback details
	 * @param $memberId string
	 * @param $institutionId string
	 * @param $feedbackType int
	 * @param $feedback string
	 * @param $rating int
	 * @return $statusCode
	 */
	public function actionSaveFeedback()
	{
		$request = Yii::$app->request;
		$memberId = $request->getBodyParam('memberId');
		$institutionId = $request->getBodyParam('institutionId');
		$feedbackType = $request->getBodyParam('feedbackType');
		$feedback = $request->getBodyParam('feedback');
		$rating = $request->getBodyParam('rating');
		if ($memberId && $institutionId) {
			$memberId = filter_var($memberId, FILTER_SANITIZE_NUMBER_INT);
			$institutionId = filter_var($institutionId, FILTER_SANITIZE_NUMBER_INT);
			$userType = Yii::$app->user->identity->usertype;
			$userId = ExtendedUserMember::getUserId($memberId,$userType);	
			$userId = $userId['userid'];
			$timeZone = Yii::$app->user->identity->institution->timezone;
			date_default_timezone_set($timeZone);
			$createdTime = date('Y-m-d H:i:s');
			
			$isResponded = 0;
			
			if ($userId) {			
				$saveFeedback = ExtendedFeedback::saveFeedbackData($feedbackType,$userId,
																	$feedback,$createdTime,$isResponded,$rating,$institutionId);
				if ($saveFeedback == true) {
					                                                                                                                                                                                                                                                                                                               
					$institutionName = ExtendedInstitution::getInstitutionName($institutionId);
					$institutionsName = $institutionName['name'];
					$lastFeedbackId = $saveFeedback['last_insert_id()'];
					$model = $this->findModel($lastFeedbackId);
					//sending mail
					
					$sendMail = $this->sendFeedbackMail($feedback,$memberId,$institutionId,$feedbackType,$rating,$model);
					//sending notification
					ExtendedFeedbacknotification::feedbackNotification($model,$institutionId,$userId,$userType);
					$data = new \stdClass();
					$data->feedbackId = (string)$lastFeedbackId;
					$this->statusCode = 200;
					$this->message = 'Feedback submitted to ' . $institutionsName;
					$this->data = $data;
					return new ApiResponse($this->statusCode,$this->data,$this->message);
				}
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
	}
	/**
	 * To get the feedback data
	 * @param $institutionId string
	 * @param $userId string
	 */
	public function actionGetFeedbackData()
	{
		$request = Yii::$app->request;
		$institutionId = $request->get('institutionId');
		$userId = $request->get('userId');	
		if($userId && $institutionId)
		{
			$institutionId = filter_var($institutionId, FILTER_SANITIZE_NUMBER_INT);
			$userId = filter_var($userId, FILTER_SANITIZE_NUMBER_INT);
			$responseInstitution = ExtendedInstitution::getUserInstitutions($userId);
			if($responseInstitution)
			{
				$result = [];
				$institutionlist = [];
				$institutions = [];
				$institution = [];
				foreach ($responseInstitution as $key => $value)
				{
					if($value['feedbackenabled'] == 1)
					{
						$result = [
								'institutionId' => (!empty($value['id'])) ? $value['id'] : '',
								'institutionName' => (!empty($value['name'])) ? $value['name'] : '',
								'feedbackTypes' => [
								],
						];
						$instId = $value['id'];
						$responseInstitutionDetails = ExtendedInstitution::getInstitutionFeatureDetails($instId);
						if($responseInstitutionDetails || $responseInstitutionDetails['feedbackenabled']==1) {
							$feedbackResponse = ExtendedInstitution::institutionFeedbackTypes($instId, true);
							$feedbacks = [];
							if($feedbackResponse) {
								foreach ($feedbackResponse as $key => $value) {
									$datas = [
											'type' => (!empty($value['description'])) ? $value['description'] : '',
											'typeId' => (!empty($value['feedbacktypeid'])) ? $value['feedbacktypeid'] : '',
									];
									array_push($feedbacks,$datas);
								}
							} else {
								$datas = [
										'type' => 'General',
										'typeId' => 1,
								];
								array_push($feedbacks,$datas);
							}
							$result['feedbackTypes'] = $feedbacks;
						}
						array_push($institutions, $result);
					}
				}
				$institution['feedbackEnabledInstitutions'] = $institutions;
				array_push($institutionlist, $institution);
				$data = [
					'feedbackEnabledInstitutions' => $institutions,
				];
				$this->statusCode = 200;
				$this->message = '';
				$this->data = $data;
				return new ApiResponse($this->statusCode, $this->data,$this->message);
				
			}else{
				$data = [
						'feedbackEnabledInstitutions' => []
				];
				$this->statusCode = 200;
				$this->message = '';
				$this->data = $data;
				return new ApiResponse($this->statusCode,$this->data,$this->message);
			}
		}else{
			$this->statusCode = 500;
			$this->message = 'An error occurred while processing the request';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		}
	}
	/**
	 * To add a feedback image
	 * @param $userId string
	 * @param $feedbackId string
	 */
	public function actionAddFeedbackImage()
	{
		$request = Yii::$app->request;
		$userId = $request->getBodyParam('userId');
		$feedbackId = $request->getBodyParam('feedbackId');
		if ($userId && $feedbackId) {
			$userId = filter_var($userId, FILTER_SANITIZE_NUMBER_INT);
			$feedbackId = filter_var($feedbackId, FILTER_SANITIZE_NUMBER_INT);
			$institutionId = Yii::$app->user->identity->institutionid;
		
			if(isset($_FILES['file'])){
				$image = $_FILES['file'];
				$size = $_FILES['file']['size'];
				if($size < 5242880){
					$filename = explode('.', $_FILES['file']['name']);
					$extension = end($filename);
					if(strtolower($extension) == 'jpg' || strtolower($extension) == 'jpeg' || strtolower($extension) == 'png'){
						
						$targetPath = 'institution/'.$institutionId.'/'.Yii::$app->params ['image'] ['feedback'] ['main'] . '/' .Yii::$app->params ['image'] ['feedback'] ['feedbackImage'];
						$thumbnail = 'institution/'.$institutionId.'/'.Yii::$app->params ['image'] ['feedback'] ['main'] . '/' .Yii::$app->params ['image'] ['feedback'] ['thumbnailFeedback'];
						$feedbackImages = $this->fileUpload($image, $targetPath,$thumbnail);
						$createdDate = date('Y-m-d H:i:s');
						$status = ExtendedFeedbackimagedetails::saveFeedbackImage($feedbackId,$feedbackImages['thumbnail'],$createdDate);
						if ($status) {
							$this->statusCode = 200;
							$this->message = '';
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
			$this->statusCode = 500;
			$this->message = 'An error occurred while processing the request';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		}
	}
	/**
	 * To upload the images
	 */
	protected  function fileUpload($image,$targetPath,$thumbnail)
	{
	
		$fileHandlerObj = new FileuploadComponent();
	
		$tempName = $image['tmp_name'];
		$uploadFilename = $image['name'];
	
		$uploadImages = $fileHandlerObj->uploader($uploadFilename,$targetPath,$tempName,$thumbnail,false,false,false);
	
		return $uploadImages;
	}
	protected function findModel($id)
	{
		if (($model = ExtendedFeedback::findOne($id)) !== null) {
			return $model;
		}
	
		throw new NotFoundHttpException('The requested page does not exist.');
	}
	/**
	 * To send feedback mail to the admin
	 */
	protected function sendFeedbackMail($content,$memberId,$institutionId,$feedbackType,$rating,$model)
	{

		$mailobj = new EmailHandlerComponent();
		//$memberDetails = ExtendedMember::getMemberById($memberId);
		$userId = Yii::$app->user->identity->id;
		$memberDetails = ExtendedMember::getMemberData($userId,$institutionId);
		$memberNo = $memberDetails['memberno'];
		/* $getTitle = ExtendedTitle::getMemberTitle($memberId);
		$memberTitle = $getTitle['description']; */
		$memberDetails['memberimage'] = (!empty($memberDetails['memberimage'])) ? $memberDetails['memberimage'] : '/Member/default-user.png';
		$memberImage = yii::$app->params['imagePath'].$memberDetails['memberimage'];
		$toName = $memberDetails['username'];
		$from = yii::$app->user->identity->emailid;
		$adminMail = ExtendedInstitution::find()
					->where('id = :institutionid', [':institutionid' => $institutionId])
					->one();
		$typeDescription = ExtendedFeedbacktype::find()
					->where('feedbacktypeid = :feedbacktypeid' , [':feedbacktypeid' => $feedbackType])
					->one();
		$description = $typeDescription->description;
		
		$toadmin = 'ADMIN';
		$institutionLogo = '';
		if($rating == 1)
		{
			$ratingImage = yii::$app->params['imagePath'].'/rating/rating1.png';
		}elseif ($rating == 2){
			$ratingImage = yii::$app->params['imagePath'].'/rating/rating2.png';
		}elseif ($rating == 3){
			$ratingImage = yii::$app->params['imagePath'].'/rating/rating3.png';
		}elseif ($rating == 4){
			$ratingImage = yii::$app->params['imagePath'].'/rating/rating4.png';
		}elseif ($rating == 5){
			$ratingImage = yii::$app->params['imagePath'].'/rating/rating5.png';
		}else{
			$ratingImage = yii::$app->params['imagePath'].'/rating/rating0.png';
		}
		
		if(!empty($adminMail->feedbackemail)){
			$email = explode(",",$adminMail->feedbackemail);
			$toEmailId = $email;
			$title ='';
			$createdDate = date_format(date_create($model->createddatetime),Yii::$app->params['dateFormat']['viewDateFormat']);
			$subject ='Feedback Received From - ' .$toName .' - '.$description .' - '. $createdDate;
			$mailContent = [];
			$mailContent['template'] = 'feedback';
			$mailContent['content'] = $content;
			$mailContent['logo'] =$institutionLogo;
			$mailContent['name'] = $toName;
			$mailContent['toname'] = $toadmin;
			$mailContent['memberno'] = $memberNo;
			$mailContent['ratingImage'] = $ratingImage;
			$mailContent['memberImage'] = $memberImage;
			$attach = '';
			try{
				$temp = $mailobj->sendEmail($from,$toEmailId,$title,$subject,$mailContent,$attach);
			}
			catch(Exception $e){
				yii::error($e->getMessage());
			}	
		}
		
		
	}
}