<?php 

namespace api\modules\v3\controllers;

use Yii;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;
use api\modules\v3\models\GalleryManager;
use common\components\FileuploadComponent;
use api\modules\v3\controllers\BaseController;
use common\models\extendedmodels\ExtendedAlbum;
use api\modules\v3\models\responses\ApiResponse;
use common\models\extendedmodels\ExtendedInstitution;
use common\models\extendedmodels\ExtendedUserCredentials;
use common\models\extendedmodels\ExtendedPendingAlbumImage;
use yii\web\UnauthorizedHttpException;
use yii\base\ActionEvent;
use Exception;

class GalleryController extends BaseController
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
									'get-albums' => ['GET'],
									'get-photos-in-album' => ['GET'],
									'get-albums-for-approval' => ['GET'],
									'approve-album-photos' => ['POST'],
									'add-photo-to-album' => ['POST']
								]
						],
				]
				);
	}


	function beforeAction($action)
    {
        $this->on(self::EVENT_BEFORE_ACTION,function(ActionEvent $event)
        {
           $auth = Yii::$app->authManager;
           $user = Yii::$app->user->identity;
           $permissionName = "74af2974-ec46-11e6-b48e-000c2990e707";
           if (in_array($event->action->id,['get-albums-for-approval','approve-album-photos'])) {
           	$userMemberId = $user->getUserMember();
               if(!$auth->checkAccess ($userMemberId, $permissionName)){
					throw new UnauthorizedHttpException;
               }       
           }
        });
        return parent::beforeAction($action);
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
	 * To get all
	 * album details of the user
	 * @param $userId int
	 * @param $lastUpdatedOn dateTime
	 */
	public function actionGetAlbums()
	{
		$request = Yii::$app->request;
		$userId = $request->get('userId');
		$lastUpdatedOn = $request->get('lastUpdatedOn');
		if ($userId) {   
			if ($lastUpdatedOn) {
				$time = strtotimeNew($lastUpdatedOn);
				$time = $time - (1 * 60);
				$lastUpdatedOn = date("Y-m-d H:i:s", $time);
			}
			$dateUpdated = ($lastUpdatedOn) ? $lastUpdatedOn : gmdate("Y-m-d H:i:s");
			$response = ExtendedAlbum::getAllUserAlbums($userId, $dateUpdated);
			if (!empty($response)) {
				$albumList = [];
				foreach ($response as $key => $value) {
					$result = [
							'albumId' => (!empty($value['albumid'])) ? (int)$value['albumid'] : 0,
							'album' => (!empty($value['albumname'])) ? $value['albumname'] : '',
							'dateTime' => (!empty($value['activitydate'])) ? date(Yii::$app->params['dateFormat']['serviceDateFormat'],strtotimeNew($value['activitydate'])): '',
							'venue' => (!empty($value['venue'])) ? $value['venue'] : '',
							'institution' => (!empty($value['institution'])) ? $value['institution']:'',
							'institutionId' => (!empty($value['institutionid'])) ? (int)$value['institutionid']:0,
							'coverPhoto' => (!empty($value['coverimageurl'])) ? (string)preg_replace('/\s/', "%20",yii::$app->params['imagePath'].$value['coverimageurl']):'',
							'hasUpdate' => (!empty($value['hasupdate'])) ? (bool)$value['hasupdate']:false
					];
					array_push($albumList,$result);
				}
				$data = [
					'albums' => $albumList
				];

				$this->statusCode = 200;
				$this->message = "";
				$this->data = $data;
				return new ApiResponse($this->statusCode, $this->data,$this->message);
			} else {
				$this->statusCode = 200;
				$this->message = '';
				$this->data = [
					'albums' => []
				];
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
	 * To get all photos in
	 * albums.
	 * @param $userId int
	 * @param $albumId int
	 * @param $lastUpdatedOn dateTime
	 * @return \api\modules\v3\models\responses\ApiResponse
	 */
	public function actionGetPhotosInAlbum()
	{
		$request = Yii::$app->request;
		$userId = $request->get('userId');
		$albumId = $request->get('albumId');
		$lastUpdatedOn = $request->get('lastUpdatedOn') ?: null;
		if ($userId != 0) {
			$time = strtotimeNew($lastUpdatedOn);
			$time = $time - (1 * 60);
			$date = date("Y-m-d H:i:s", $time);
			$dateUpdated = ($lastUpdatedOn != null) ? $date : $lastUpdatedOn;
			$photoResponse = ExtendedAlbum::getPhotosInAlbum($userId, $albumId, $dateUpdated);
			$albumResponse = ExtendedAlbum::getAlbumInfo($albumId);
			
			if ($photoResponse && $albumResponse) {
				$data = new \stdClass();
				$data->albumId = (!empty($albumResponse['albumid'])) ? (int)$albumResponse['albumid'] : 0;
				$data->album = (!empty($albumResponse['albumname'])) ? $albumResponse['albumname'] : '';
				$data->dateTime = (!empty($albumResponse['activitydate'])) ? date('d-m-Y H:i:s',strtotimeNew($albumResponse['activitydate'])): '';
				$data->venue = (!empty($albumResponse['venue'])) ? (!empty($albumResponse['venue'])) : '';
				$data->institution = (!empty($albumResponse['institution'])) ? $albumResponse['institution']:'';
				$data->institutionId = (!empty($albumResponse['institutionid'])) ? $albumResponse['institutionid']:'';
				$data->coverPhoto = (!empty($albumResponse['coverimageurl'])) ? (string)preg_replace('/\s/', "%20",yii::$app->params['imagePath'].$albumResponse['coverimageurl']):'';
				$photoList = [];
				foreach ($photoResponse as $key => $row) {
					$result = [
							'photoId' => (!empty($row['imageid'])) ? (int)$row['imageid']:0,
							'photoThumbnail' => (!empty($row['thumbnailurl'])) ? (string)preg_replace('/\s/', "%20",yii::$app->params['imagePath'].$row['thumbnailurl']):'',
							'photo' => (!empty($row['imageurl'])) ? (string)preg_replace('/\s/', "%20",yii::$app->params['imagePath'].$row['imageurl']):'',
							'caption' => (!empty($row['caption'])) ? $row['caption']:'',
							'hasUpdate' => (($row['hasupdate']) == 1) ? (bool)$row['hasupdate'] : false,
					];
					array_push($photoList,$result);
				}
				$data->photos = $photoList;
				$this->statusCode = 200;
				$this->message = "";
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
	}
	/**
	 * To get the details
	 * of the albums for approval
	 * @return \api\modules\v3\models\responses\ApiResponse
	 */
	public function actionGetAlbumsForApproval()
	{
		$userId = Yii::$app->user->identity->id;
		$institutionId = yii::$app->user->identity->institutionid;
		$institutionName =  yii::$app->user->identity->institution->name;
		$data = [];
		if($userId) {
			$album = [];
			$albumResponse = ExtendedAlbum::getPendingAlbumList($userId);
			if ($albumResponse) {
				foreach ($albumResponse as $key => $value) {
					$album[$value['albumid']]['albumId'] = ($value['albumid']) ? (int)$value['albumid'] : 0;
					$album[$value['albumid']]['album'] = $value['albumname'];
					$album[$value['albumid']]['institutionName'] = $value['institutionname'];
					$album[$value['albumid']]['institutionId'] = ($value['institutionid']) ? (int)$value['institutionid'] : 0;
					$album[$value['albumid']]['coverPhoto'] = (string)preg_replace('/\s/', "%20",yii::$app->params['imagePath'].$value['coverpic']);
					$album[$value['albumid']]['dateTime'] = date(yii::$app->params['dateFormat']['viewDandTFormat'],strtotimeNew($value['activitydate']));
					$album[$value['albumid']]['venue'] = $value['venue'];
					$album[$value['albumid']]['photos'][] = [ 
							'photoId' => $value['pending_imageid'],
							'photoThumbnail' => (string)preg_replace('/\s/', "%20",yii::$app->params['imagePath'].$value['pending_imageurl']),
							'uploadedBy' => $value['username'],
							'caption' => $value['caption'],
							'memberId' => (string)$value['memberid'],
						];
				}
			} 
			$data['albums'] = (!empty($album)) ? array_values($album) : $album ;
			$this->statusCode = 200;
			$this->message = '';
			$this->data = $data;
			return new ApiResponse($this->statusCode, $this->data,$this->message);
		} else {
			$this->statusCode = 498;
			$this->message = 'Session invalid';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		}
	}
	/**
	 * To approve album photos
	 * @param albumId integer
	 * @param coverPhotoId integer
	 * @param photos array
	 */
	public function actionApproveAlbumPhotos()
	{
		$request = Yii::$app->request;
		$albumId = $request->getBodyParam('albumId');
		$coverPhotoId = $request->getBodyParam('coverPhotoId');
		$photos = $request->getBodyParam('photos');
		$userId = Yii::$app->user->identity->id;
		try{
			if ($userId) {
				$albumId = filter_var($albumId, FILTER_SANITIZE_NUMBER_INT);
				$albumResponse = $this->addFromPendingListService($photos, $albumId, $coverPhotoId, $userId);
				if($albumResponse) {
					
					$this->statusCode = 200;
					$this->message = 'Your request has been processed successfully.';
					$this->data = new \stdClass();
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
		}
		catch(Exception $e){
			$this->statusCode = 500;
			$this->message = 'An error occurred while processing the request';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		}
	}
	/**
	 * Approve pending images
	 * @param albumId integer
	 * @param coverPhotoId integer
	 * @param photos array
	 */
	protected function addFromPendingListService($photos, $albumId, $coverPhotoId, $userId)
	{
		$eventId = 0;
		$memberList = [];
		$albumImageList = [];
		$createdDateTime = gmdate("Y-m-d H:i:s");
		$galleryManager = new GalleryManager();

		try{
			$eventId = ExtendedAlbum::getEventId($albumId);
			if ($photos && count($photos) > 0) {
				foreach ($photos as $item) {
					$isApproved = filter_var($item['isApproved'], FILTER_VALIDATE_BOOLEAN);
					if ($isApproved != true) {
						continue;
					} else {
						if(!in_array($item['memberId'], $memberList)){
							array_push($memberList, $item['memberId']);
						}

						$photoId = $item['photoId'];
						$pendingResponse = $galleryManager->getPendingImagesByAlbumId($photoId, $albumId);
						if($pendingResponse && is_array($pendingResponse)){
							$albumImageList = [
								'pendingImageId' => $pendingResponse['pending_imageid'],
								'albumId' => $pendingResponse['albumid'],
								'ImageUrl' => $pendingResponse['pending_imageurl'],
								'caption' =>  $item['caption'],
								'isAlbumCover' => false,
								'createdBy' => $pendingResponse['createdby'],
								'createdDateTime' => $pendingResponse['createddatetime'],
								'thumbnail' => $pendingResponse['thumbnail'],
							];
							$galleryManager->addAlbumImage($albumImageList);
						}
					}
				}

				//Set cover photo
				if($coverPhotoId){
					$galleryManager->addAlbumCoverPic($albumId, $coverPhotoId, $userId, $createdDateTime);
					$galleryManager->updateAlbumByAlbumId($albumId, $createdDateTime);
				}

				// Sent mail
				if($memberList){
					foreach($memberList as $memberId){
						$userId = $galleryManager->getUserIdByMemberId($memberId);

						if($userId){
							$galleryManager->sentApprovedAlbum($userId, $eventId['eventid']);
						}
					}
				}

				//Delete images
				if($photos) {
					foreach ($photos as $item) {
						$isApproved = filter_var($item['isApproved'], FILTER_VALIDATE_BOOLEAN);
						$pendingResponse = $galleryManager->getPendingImagesByAlbumId($item['photoId'], $albumId);
						if(!empty($pendingResponse)) {
							if(!$isApproved){
								$originalPath = ($pendingResponse['pending_imageurl']) ? Yii::getAlias('@service').$pendingResponse['pending_imageurl'] : "abcd";
								$thumbnailPath = ($pendingResponse['thumbnail']) ? Yii::getAlias('@service').$pendingResponse['thumbnail'] : "abcd";
								try{
									if (file_exists($originalPath)) {
										unlink($originalPath);
									}
									if (file_exists($thumbnailPath)) {
										unlink($thumbnailPath);
									}
								} catch(Exception $e){
									yii::error($e->getMessage());
								}
							}
							$deleteStatus = $galleryManager->deletePendingImages($item['photoId'], $albumId);
							$deleteImageStatus = $galleryManager->deletePendingImageNotification($albumId, $pendingResponse['createdby']);
							$deleteImageSentStatus = $galleryManager->deletePendingImageSentNotification($albumId, $pendingResponse['createdby']);
						}
					}
				}
				return true;
			}
		} catch(Exception $e){
			yii::error($e->getMessage());
			return false;
		}
	}

	/**
	 * Add photo to album
	 * @param $userId int
	 * @param $albumId int
	 * @param $lastUpdatedOn dateTime
	 */
	public function actionAddPhotoToAlbum()
	{
		$request = Yii::$app->request;
		$userId = $request->getBodyParam('userId');
		$albumId = $request->getBodyParam('albumId');
		$caption= $request->getBodyParam('caption');
		$createdDateTime = gmdate("Y-m-d H:i:s");
 		$user = Yii::$app->user->identity;
		$institutionId = $user->institutionid;
		$createdBy = $user->id; 

		$albumModel = new ExtendedPendingAlbumImage();
		try{
			if ($createdBy) {
				if (isset($_FILES['file'])) {
					$image = $_FILES['file'];
					$size = $_FILES['file']['size'];
					$error = $_FILES['file']['error'];
					if($size < 5242880 && !$error) {
						$filename = explode('.', $_FILES['file']['name']);
	                	$extension = end($filename);
	                	if(strtolower($extension) == 'jpg' || strtolower($extension) == 'jpeg' || strtolower($extension) == 'png') {
			                $pendingImageId = 'p_' . time();
			                $targetPath = 'institution/'.$institutionId.'/'.Yii::$app->params['image']['album']['main'].'/'.Yii::$app->params['image']['album']['albumimage'].'/'.Yii::$app->params['image']['album']['albumevent'].$albumId;
			                $thumbnailPath = 'institution/'.$institutionId.'/'.Yii::$app->params['image']['album']['main'].'/'.Yii::$app->params['image']['album']['albumthumbnailImage'].'/'.Yii::$app->params['image']['album']['albumevent'].$albumId;

			                $albumImage = $this->fileUpload($image, $targetPath, $thumbnailPath);

			                $status = $albumModel->savePendingImage($albumId, $pendingImageId, $albumImage['orginal'], $caption, $createdBy, $createdDateTime, $albumImage['thumbnail']);
			                
			                if($status){
								$this->statusCode = 200;
								$this->message = 'Photo is successfully uploaded and submitted to administrators approval.';
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
						$this->message = 'Please upload image(s) with size less than 2MB.';
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
		} catch(Exception $e) {
			$this->statusCode = 500;
			$this->message = 'An error occurred while processing the request';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		}	
	}

	/**
     * To upload the images
     */
    protected  function fileUpload($image, $targetPath, $thumbnailPath)
    {
        $fileHandlerObj = new FileuploadComponent();
        $tempName = $image['tmp_name'];
        $uploadFilename = $image['name'];
        $uploadImages = $fileHandlerObj->uploader($uploadFilename, $targetPath, $tempName, $thumbnailPath, 'album', false, false);
        return $uploadImages;
    }
}
