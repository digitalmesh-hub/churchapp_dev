<?php

namespace backend\controllers;

use Yii;
use common\models\extendedmodels\ExtendedAlbum;
use common\models\searchmodels\ExtendedAlbumSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use common\components\FileuploadComponent;
use Imagine\Filter\Basic\Thumbnail;
use common\models\extendedmodels\ExtendedEvent;
use yii\helpers\ArrayHelper;
use common\models\extendedmodels\ExtendedAlbumimage;
use common\models\extendedmodels\ExtendedTempAlbumImage;
use common\models\extendedmodels\ExtendedPendingAlbumImage;
use yii\filters\AccessControl;


/**
 * AlbumController implements the CRUD actions for ExtendedAlbum model.
 */
class AlbumController extends BaseController {
	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return [ 
				'verbs' => [ 
						'class' => VerbFilter::className (),
						'actions' => [ 
								'delete' => [ 
										'POST' 
								] 
						] 
				],
				'access' => [
						'class' => AccessControl::className(),
						'only' => [
								'index',
								'view',
								'create',
								'update',
								'make-album-cover',
								'publish',
								'unpublish',
								'delete-image',
								'change-caption',
								'pending-album',
								'approve-album',
								'change-caption-of-pending'
								
						],
						'rules' => [
								[
									'allow' => true,
									'actions' => ['index','view','pending-album'],
									'roles' => ['437c49cf-ec46-11e6-b48e-000c2990e707'] //view albums
								],
								[
									'allow' => true,
									'actions' => [
											'index',
											'create',
											'update',
											'make-album-cover',
											'publish',
											'unpublish',
											'delete-image',
											'change-caption',
											'approve-album',
											'change-caption-of-pending'
									],
									'roles' => ['74af2974-ec46-11e6-b48e-000c2990e707'] // manage albums
								],
						],
				],
		];
	}

	/**
	 * Lists all ExtendedAlbum models.
	 *
	 * @return mixed
	 */
	public function actionIndex()
	{
		$searchModel = new ExtendedAlbumSearch ();
		$user = Yii::$app->user->identity;
		$institutionId = $user->institutionid;
		$dataProvider = $searchModel->search ( Yii::$app->request->queryParams,$institutionId );
		$startDate = isset(Yii::$app->request->queryParams['eventStartDate'])?Yii::$app->request->queryParams['eventStartDate']:date('d-M-Y');
		$endDate = isset(Yii::$app->request->queryParams['eventEndDate'])?Yii::$app->request->queryParams['eventEndDate']:date('d-M-Y');
		$tempAlbumModel = ExtendedTempAlbumImage::getPendingAlbum($institutionId);
		$pendingImages = [];
		foreach ($tempAlbumModel as $key => $value)
		{
			$albumId = $value['albumid'];
			$pendingImage = "select count(p.pending_imageid) as imagecount from pending_album_image p
							inner join album a on a.albumid=p.albumid inner join events e on e.id=a.eventid
							where e.institutionid= :institutionid and p.albumid= :albumid ";
			$values = [
					':institutionid' => $institutionId,
					':albumid' => $albumId
			];
			$pendingImageCount = Yii::$app->db->createCommand ( $pendingImage )->bindValues($values)->queryScalar ();
			$pendingImages[$albumId]=$pendingImageCount;
				
		}
		$pendingAlbum = "select count(*) as albumcount from(SELECT count(a.albumid) FROM pending_album_image pai 
						inner join album a on a.albumid=pai.albumid inner join events e on e.id=a.eventid 
						where e.institutionid= :institutionid group by a.albumid) a";
		$values = [
				':institutionid' => $institutionId
		];
		$pendingAlbumCount = Yii::$app->db->createCommand ( $pendingAlbum )->bindValues ( $values )->queryScalar ();
		
		$date = date ( 'Y-m-d H:i:s' );
		$model = ExtendedAlbumimage::getIsPublished($date,$institutionId);
		return $this->render ( 'index', [ 
				'searchModel' => $searchModel,
				'imageModel' => $dataProvider,
				'model' => $model,
				'tempAlbumModel' => $tempAlbumModel,
				'pendingAlbumCount' => $pendingAlbumCount,
				'pendingImageCount' => $pendingImages,
				'startDate' => $startDate,
				'endDate' => $endDate,
		] );
	}
	
	/**
	 * Displays a single ExtendedAlbum model.
	 *
	 * @param integer $id        	
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionView($id) 
	{
		$imageModel = ExtendedAlbumimage::find ()->where ( [ 
				'albumid' => $id 
		] )->all ();
		$imageModelNew = new ExtendedAlbumimage ();
		$model = $this->findModel( $id );
		
		$institutionid = $this->currentUser()->institutionid;
		if ($imageModelNew->load ( Yii::$app->request->post () )) {
		
			$caption = $imageModelNew->caption;
			$eventId = $model->eventid;
			$eventName = ExtendedEvent::getEvent ( $eventId );
			$eventName = $eventName ['notehead'];
			$imageType = 'album';
			$name = 'p_' . time ();
			if (UploadedFile::getInstance ( $imageModelNew, 'imageurl' )) {
				$albumImage = UploadedFile::getInstance ( $imageModelNew, 'imageurl' );
				//$targetPath = Yii::$app->params ['image'] ['album'] ['main'] . '/' . Yii::$app->params ['image'] ['album'] ['albumimage'] . '/' . Yii::$app->params ['image'] ['album'] ['albumevent'] . $eventId;
				$targetPath = 'institution/'.$institutionid.'/'.Yii::$app->params ['image'] ['album'] ['main'] . '/' .Yii::$app->params ['image'] ['album'] ['albumimage'] . '/' . Yii::$app->params ['image'] ['album'] ['albumevent'] . $eventId;
				$thumbnail = 'institution/'.$institutionid.'/'.Yii::$app->params ['image'] ['album'] ['main'] . '/' . Yii::$app->params ['image'] ['album'] ['albumthumbnailImage'] . '/' . Yii::$app->params ['image'] ['album'] ['albumevent'] . $eventId;
				$albumImages = $this->fileUpload ( $albumImage, $targetPath, $thumbnail, $imageType, $name, false );
				
				$imageModelNew->thumbnail = $albumImages ['thumbnail'];
				$imageModelNew->imageurl = $albumImages ['orginal'];
				$imageModelNew->imageid = $name;
				$imageModelNew->albumid = $model->albumid;
				$imageModelNew->createdby = Yii::$app->user->identity->id;
				$imageModelNew->createddatetime = date ( 'Y-m-d H:i:s' );
				$imageModelNew->iscover = "0";
				$imageModelNew->caption = $caption;
				if ($imageModelNew->save ()) {
					return $this->redirect ( [
							'view',
							'id' => $model->albumid
					] );
				}
			}
		}
		return $this->render ( 'view', [ 
				'model' => $model,
				'imageModel' => $imageModel,
				'imageModelNew' => $imageModelNew 
		] );
	}
	
	/**
	 * Creates a new ExtendedAlbum model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate() 
	{
		$model = new ExtendedAlbum ();
		$imageModel = new ExtendedAlbumimage ();
		$userId = Yii::$app->user->identity->id;
		$eventDate = date ( 'Y-m-d H:i:s' );
		$institutionId = $this->currentUser()->institutionid;
		$eventModel = ExtendedEvent::getAlbumEvents ( $eventDate,$institutionId );
		$eventsArray = ArrayHelper::map ( $eventModel, 'id', 'notehead' );
		if ($model->load ( Yii::$app->request->post () ) && $imageModel->load ( Yii::$app->request->post () )) {
			$caption = $imageModel->caption;
			$eventId = $model->eventid;
			$eventName = ExtendedEvent::getEvent ( $eventId );
			$eventName = $eventName ['notehead'];
			$imageType = 'album';
			$name = 'p_' . time ();
				if (UploadedFile::getInstance ( $imageModel, 'imageurl' )) {
					$albumImage = UploadedFile::getInstance ( $imageModel, 'imageurl' );
					$targetPath = 'institution/'.$institutionId.'/'.Yii::$app->params ['image'] ['album'] ['main'] . '/' .Yii::$app->params ['image'] ['album'] ['albumimage'] . '/' . Yii::$app->params ['image'] ['album'] ['albumevent'] . $eventId;
					$thumbnail = 'institution/'.$institutionId.'/'.Yii::$app->params ['image'] ['album'] ['main'] . '/' . Yii::$app->params ['image'] ['album'] ['albumthumbnailImage'] . '/' . Yii::$app->params ['image'] ['album'] ['albumevent'] . $eventId;
					$albumImages = $this->fileUpload ( $albumImage, $targetPath, $thumbnail, $imageType, $name , false);
					
					$imageModel->thumbnail = $albumImages ['thumbnail'];
					$imageModel->imageurl = $albumImages ['orginal'];
				}
					
					$model->eventid = $eventId;
					$model->albumname = $eventName;
					$model->createddatetime = date ( 'Y-m-d H:i:s' );
					$model->createdby = Yii::$app->user->identity->id;
					$model->ispublished = "0";
					$model->isalbumchanged = "0";
					if ($model->save()) {
						$imageModel->imageid = $name;
						$imageModel->albumid = $model->albumid;
						$imageModel->createdby = Yii::$app->user->identity->id;
						$imageModel->createddatetime = date ( 'Y-m-d H:i:s' );
						$imageModel->iscover = "1";
						$imageModel->caption = $caption;
						if ($imageModel->save ()) {
							return $this->redirect ( [
									'view',
									'id' => $model->albumid
							] );
						} else {
							$model->delete();
							$this->sessionAddFlashArray('error', $imageModel->getErrors(), true);
						}
					} else {
						$this->sessionAddFlashArray('error', $model->getErrors(), true);
					}
					return $this->redirect('create');
			} 
		return $this->render ( 'create', [ 
				'model' => $model,
				'eventsArray' => $eventsArray,
				'imageModel' => $imageModel 
		] );
	}
	
	/**
	 * Updates an existing ExtendedAlbum model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id        	
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate($id) {
		$model = $this->findModel ( $id );
		
		if ($model->load ( Yii::$app->request->post () ) && $model->save ()) {
			return $this->redirect ( [ 
					'view',
					'id' => $model->albumid 
			] );
		}
		
		return $this->render ( 'update', [ 
				'model' => $model 
		] );
	}
	
	/**
	 * Deletes an existing ExtendedAlbum model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id        	
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionDelete() {
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		if (yii::$app->request->isAjax) {
			
			$albumId = yii::$app->request->post ( 'albumId' );
			
			if ($this->findModel ( $albumId )) {
				
				$response = ExtendedAlbum::deleteAlbum ( $albumId );
				if ($response) {
					return [ 
							'status' => "success",
							'msg' => "Album deleted successfully" 
					];
				}
			}
			return [ 
					'status' => "error",
					'msg' => "Unable to proccess the request" 
			];
		}
		return $this->redirect ( [ 
				'index' 
		] );
	}
	
	/**
	 * Finds the ExtendedAlbum model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id        	
	 * @return ExtendedAlbum the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id) {
		if (($model = ExtendedAlbum::findOne ( $id )) !== null) {
			return $model;
		}
		
		throw new NotFoundHttpException ( 'The requested page does not exist.' );
	}
	/**
	 * Finds the pending album
	 */
	protected function findTempAlbum($id) {
		if (($tempModel = ExtendedTempAlbumImage::findOne ( $id )) !== null) {
			return $tempModel;
		}
		throw new NotFoundHttpException ( 'The requested page does not exist.' );
	}
	/**
	 * To upload the images
	 */
	protected function fileUpload($image, $targetPath, $thumbnail, $imageType, $name, $isArray = false) {
		$fileHandlerObj = new FileuploadComponent ();
		if ($isArray) {
			
			$tempName = $image ['tmp_name'];
			$uploadFilename = $image ['name'];
		} else {
			$tempName = $image->tempName;
			$uploadFilename = $image->name;
		}
		
		$uploadImages = $fileHandlerObj->uploader ( $uploadFilename, $targetPath, $tempName, $thumbnail, $imageType, false, false, $name );
		
		return $uploadImages;
	}
	/**
	 *
	 * @param unknown $id        	
	 * @return \yii\web\Response
	 */
	public function actionMakeAlbumCover($id) {
		try {
			$model = ExtendedAlbumimage::findOne ( $id );
			
			if ($model) {
				$flag = yii::$app->db->createCommand ( "update albumimage set iscover = 0 where albumid = :albumid" )->bindValue ( ':albumid', $model->albumid )->execute ();
				
				if ($flag) {
					$model->iscover = "1";
					$model->coverchangeddatetime = date ( 'Y-m-d H:i:s' );
					if ($model->update ( false )) {
						return $this->redirect ( [ 
								'view',
								'id' => $model->albumid 
						] );
					}
				}
			}
		} catch ( \Exception $e ) {
			print_r ( $e->getMessage () );
		}
	}
	
	/**
	 * To publish an album
	 */
	public function actionPublish()
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		if (yii::$app->request->isAjax) {
			$albumId = yii::$app->request->post ( 'albumId' );
			if ($albumId) {
				$model = $this->findModel ( $albumId );
				if (! empty ( $model )) {
					$model->ispublished = 1;
					if ($model->update(false)) {
						return [ 
								'status' => 'success',
								'data' => null 
						];
					} else {
						return [ 
								'status' => 'error',
								'data' => null 
						];
					}
				}
				return [ 
						'status' => 'error',
						'data' => null 
				];
			}
			return [ 
					'status' => 'error',
					'data' => null 
			];
		}
	}
	/**
	 * To unpublish an album
	 */
	public function actionUnpublish() {
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		if (yii::$app->request->isAjax) {
			$albumId = yii::$app->request->post ( 'albumId' );
			if ($albumId) {
				$model = $this->findModel ( $albumId );
				if (! empty ( $model )) {
					$model->ispublished = 0;
					
					if ($model->update ( false )) {
						return [ 
								'status' => 'success',
								'data' => null 
						];
					} else {
						return [ 
								'status' => 'error',
								'data' => null 
						];
					}
				}
				return [ 
						'status' => 'error',
						'data' => null 
				];
			}
			return [ 
					'status' => 'error',
					'data' => null 
			];
		}
	}
	/**
	 * To delete an image
	 * from an album
	 */
	public function actionDeleteImage() {
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		if (yii::$app->request->isAjax) {
			$imageId = yii::$app->request->post ( 'imageId' );
			if ($imageId) {
				$deleteImage = ExtendedAlbumimage::deleteImage ( $imageId );
				if ($deleteImage == true) {
					return [ 
							'status' => 'success',
							'data' => null 
					];
				} else {
					return [ 
							'status' => 'error',
							'data' => null 
					];
				}
			}
			return [ 
					'status' => 'error',
					'data' => null 
			];
		}
	}
	/**
	 * To change the caption
	 */
	public function actionChangeCaption() {
		if (yii::$app->request->isPost) {
			$albumImage = yii::$app->request->post ( 'ExtendedAlbumimage' );
			//print_r($albumImage);die;
			if ($albumImage) {
				$imageId = $albumImage ['id'];
				$caption = $albumImage ['caption'];
				$albumId = $albumImage ['albumid'];
				
				$updateCaption = ExtendedAlbumimage::changeCaption ( $imageId, $caption );
				
				if ($updateCaption == true) {
					return $this->redirect ( [ 
							'view',
							'id' => $albumId 
					] );
				}
			}
		}
	}
	/**
	 * To view the images
	 * in pending album
	 */
	public function actionPendingAlbum($id) {
		
		$institutionId = Yii::$app->user->identity->institutionid;
		$imageModelNew = new ExtendedTempAlbumImage ();
		$model = ExtendedPendingAlbumImage::getPendingAlbumImages($institutionId, $id);
		$tempAlbumData = ExtendedAlbum::getAlbumDetails($id);
		return $this->render ( 'pendingalbum', [ 
				'model' => $model,
				'tempAlbumData' => $tempAlbumData,
				'imageModelNew' => $imageModelNew 
		] );
	}
	/**
	 * To change the caption of
	 * pending image
	 */
	public function actionChangeCaptionOfPending() {
		if (yii::$app->request->isPost) {
			$tempAlbumImage = yii::$app->request->post ( 'ExtendedTempAlbumImage' );
			if ($tempAlbumImage) {
				$imageId = $tempAlbumImage ['id'];
				$caption = $tempAlbumImage ['caption'];
				$albumId = $tempAlbumImage ['albumid'];
				$updateCaption = ExtendedPendingAlbumImage::updatePendingImageCaption($imageId, $albumId, $caption);
				if ($updateCaption == true) {
					return $this->redirect ( [ 
							'pending-album',
							'id' => $albumId 
					] );
				}
			}
		}
	}
	/**
	 * To approve pending album
	 */
	public function actionApproveAlbum() 
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		if (yii::$app->request->isAjax) {
			try { 
				$imageId = yii::$app->request->post ( 'imageId' );
				$albumId = yii::$app->request->post ( 'albumId' );
				$approveImageCount = count($imageId);
				$tempAlbum = "select id from pending_album_image where albumid= :albumid";
				$values = [ 
						':albumid' => $albumId 
				];
				$tempAlbumImage = Yii::$app->db->createCommand ( $tempAlbum )->bindValues ( $values )->queryAll ();
				$tempImageIds = [ ];
				foreach ( $tempAlbumImage as $key => $data ) {
					$images = $data ['id'];
					array_push ( $tempImageIds, $images );
				}
				$approveImageId = implode(',',$imageId);
					if (! empty ( $imageId ) && $albumId) {
						
						$approveImage = ExtendedTempAlbumImage::addDataToAlbumImage($albumId, $approveImageId);
						$deleteMovedImage = "delete from pending_album_image where albumid=:albumId and id=:imageId";
						$values = [
								':albumId' => $albumId,
								':imageId' => implode(',', $imageId)
						];
						$approveImage = Yii::$app->db->createCommand ( $deleteMovedImage )->bindValues( $values )->execute();
						$updateImage = "delete from pending_album_image where albumid=:albumId and id NOT IN (:imageId)";
						$params = [
								':albumId' => $albumId,
								':imageId' => implode(',', $imageId)
						];
						$approveImage = Yii::$app->db->createCommand ( $updateImage )->bindValues( $params )->execute(); 
						//$sendApprovalMail = $this->sendMailOnApproval();
							
						return [
								'status' => 'success',
								'data' => null
						];
					} else {
						return [
								'status' => 'error',
								'data' => null
						];
					}
				
			 } catch ( \Exception $e ) {
				return [ 
						'status' => 'error',
						'data' => null 
				];
			}
		}
	 }
	 /**
	  * Send mail to the user on
	  * album approval
	  */
	/*  protected function sendMailOnApproval()
	 {
	 	
	 } */
}



