<?php

namespace backend\controllers;

use Yii;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use backend\controllers\BaseController;
use common\components\FileuploadComponent;
use common\components\RestaurantComponent;
use common\models\extendedmodels\ExtendedProperty;
use common\models\extendedmodels\ExtendedPropertyimages;
use common\models\extendedmodels\ExtendedPropertycategory;



/**
 * RestaurantController implements the CRUD actions for ExtendedPropertycategory model.
 */
class RestaurantController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
    function beforeAction($action)
    {
    	//if manage restaurant permission is enabled user can access.
    	if (!Yii::$app->user->can('69b1b6c1-fffc-11e6-b48e-000c2990e707')) {
    		throw new \yii\web\ForbiddenHttpException('You are not allowed to perform the requested action');
    	}
    	return parent::beforeAction($action);
    }
    
    /**
     * Lists all ExtendedPropertycategory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $restaurantObject = new RestaurantComponent();
        $dataProvider = new ActiveDataProvider([
            'query' => ExtendedPropertycategory::find(),
        ]);
        $institutionid = $this->currentUser()->institutionid;
        $categoryList = $restaurantObject->getAllPropertyCategoryByInstitutionId($institutionid, 1);
        $categoryAll = $restaurantObject->getAllPropertyCategoryByInstitutionId($institutionid, null);
        $propertyList = $restaurantObject->getInstitutionProperties($institutionid, null);
        return $this->render('index', [
            'categoryList' => $categoryList, 
            'propertyList' => $propertyList,
            'dataProvider' => $dataProvider,
            'institutionId' => $institutionid,
            'categoryAll' => $categoryAll
        ]);
    }

    /**
     * Creates a new ExtendedPropertycategory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ExtendedProperty();
        $restaurantObject = new RestaurantComponent();

        $imageModel = new \yii\base\DynamicModel(['image1', 'image2',
         'image3', 'image4']);

        $institutionid = $this->currentUser()->institutionid;
        $categories = $restaurantObject->getAllPropertyCategoryByInstitutionId($institutionid, 1);

        //get all categories
        $categoryList = ArrayHelper::map($categories,'propertycategoryid','category');
        $model->institutionid = $institutionid;
        $model->active = '1';
        if($model->load(Yii::$app->request->post()) && $model->validate()) {
            
            //Image upload
            if (UploadedFile::getInstance($model, 'thumbnailimage')){
                $memberImage = UploadedFile::getInstance($model,'thumbnailimage');

                $targetPath = 'institution/'.$institutionid.'/'.Yii::$app->params['image']['products']['propertyImage'];
                $memberImage = $this->fileUpload($memberImage,$targetPath);
                $model->thumbnailimage = $memberImage['orginal'];
                if($model->getErrors()){
                    //print_r($model->getErrors());
                }
                else{
                    $model->save();
                }
            }
            else{
                $model->save();
            }

            $propertyId = $model->getPrimaryKey();

            //Image1 upload
            if (UploadedFile::getInstance($imageModel, 'image1')){
                $propertyImageModel = new ExtendedPropertyImages();
                $memberImage = UploadedFile::getInstance($imageModel,'image1');
                $targetPath = 'institution/'.$institutionid.'/'.Yii::$app->params['image']['products']['propertyImage'].'/'.$propertyId;
                $memberImage = $this->fileUpload($memberImage,$targetPath);
                $propertyImageModel->imageurl = $memberImage['orginal'];
                $propertyImageModel->propertyid = $propertyId;
                $propertyImageModel->imageorder = 0;
                $propertyImageModel->save();
            }

            //Image2 upload
            if (UploadedFile::getInstance($imageModel, 'image2')){
                $propertyImageModel = new ExtendedPropertyImages();
                $memberImage = UploadedFile::getInstance($imageModel,'image2');
                $targetPath = 'institution/'.$institutionid.'/'.Yii::$app->params['image']['products']['propertyImage'].'/'.$propertyId;
                $memberImage = $this->fileUpload($memberImage,$targetPath);
                $propertyImageModel->imageurl = $memberImage['orginal'];
                $propertyImageModel->propertyid = $propertyId;
                $propertyImageModel->imageorder = 1;
                $propertyImageModel->save();
            }

            //Image3 upload
            if (UploadedFile::getInstance($imageModel, 'image3')){
                $propertyImageModel = new ExtendedPropertyImages();
                $memberImage = UploadedFile::getInstance($imageModel,'image3');
                $targetPath = 'institution/'.$institutionid.'/'.Yii::$app->params['image']['products']['propertyImage'].'/'.$propertyId;
                $memberImage = $this->fileUpload($memberImage,$targetPath);
                $propertyImageModel->imageurl = $memberImage['orginal'];
                $propertyImageModel->propertyid = $propertyId;
                $propertyImageModel->imageorder = 2;
                $propertyImageModel->save();
            }

            //Image4 upload
            if (UploadedFile::getInstance($imageModel, 'image4')){
                $propertyImageModel = new ExtendedPropertyImages();
                $memberImage = UploadedFile::getInstance($imageModel,'image4');
                $targetPath = 'institution/'.$institutionid.'/'.Yii::$app->params['image']['products']['propertyImage'].'/'.$propertyId;
                $memberImage = $this->fileUpload($memberImage,$targetPath);
                $propertyImageModel->imageurl =$memberImage['orginal'];
                $propertyImageModel->propertyid = $propertyId;
                $propertyImageModel->imageorder = 3;
                $propertyImageModel->save();
            }
            return $this->redirect(['index']);
        }
        if($model->getErrors()){
            $this->sessionAddFlashArray('error', $model->getErrors(), true);
        }

        $propertyImageModel = new ExtendedPropertyImages();

        return $this->render('create', [
            'model' => $model,
            'imageModel' => $imageModel,
            'categoryList' => $categoryList,
            'propertyImageModel' => $propertyImageModel
        ]);
    }

    /**
     * Updates an existing ExtendedPropertycategory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $imageModel = new \yii\base\DynamicModel(['image1', 'image2',
         'image3', 'image4']);

        $restaurantObject = new RestaurantComponent();
        $institutionid = $this->currentUser()->institutionid;
        $categories = $restaurantObject->getAllPropertyCategoryByInstitutionId($institutionid, 1);

        $categoryList = ArrayHelper::map($categories,'propertycategoryid','category');

        $model = ExtendedProperty::findOne($id);
        $propertyImageModel = ArrayHelper::map(
            ExtendedPropertyImages::find()
            ->where(['propertyid' => $id])
            ->all(),'imageorder','imageurl');
        $model->institutionid = $institutionid;
        $model->active = (string)$model->active;
        $image = $model->thumbnailimage;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) { 
           if (UploadedFile::getInstance($model, 'thumbnailimage')){
                $imagePath = !empty($model->thumbnailimage) ? Yii::getAlias('@service'. $model->thumbnailimage) : "";
                if(file_exists($imagePath)) {
                    unlink($imagePath);    
                }
                $memberImage = UploadedFile::getInstance($model,'thumbnailimage');
                $targetPath = 'institution/'.$institutionid.'/'.Yii::$app->params['image']['products']['propertyImage'];
                $memberImage = $this->fileUpload($memberImage,$targetPath);
                $model->thumbnailimage = $memberImage['orginal'];
            }
            else{
                $model->thumbnailimage = $image;
            }
            $model->save();
            $propertyId = $model->getPrimaryKey();

            //Image1 upload
            if (UploadedFile::getInstance($imageModel, 'image1')){
                $propertyImageModel = ExtendedPropertyImages::findOne(
                    ['propertyid' => $id, 'imageorder' => 0]);
                if($propertyImageModel === null){
                    $propertyImageModel = new ExtendedPropertyImages();
                    
                }
                $memberImage = UploadedFile::getInstance($imageModel,'image1');
                $imagePath = !empty($propertyImageModel->imageurl) ? Yii::getAlias('@service'. $propertyImageModel->imageurl) : "";
                //$imagePath = Yii::getAlias('@service'. $propertyImageModel->imageurl);
                if(file_exists($imagePath)) {
                    unlink($imagePath);    
                }
                $targetPath = 'institution/'.$institutionid.'/'.Yii::$app->params['image']['products']['propertyImage'].'/'.$propertyId;
                $memberImage = $this->fileUpload($memberImage,$targetPath);
                $propertyImageModel->imageurl = $memberImage['orginal'];
                $propertyImageModel->propertyid = $propertyId;
                $propertyImageModel->imageorder = 0;
                $propertyImageModel->save();
            }

            //Image2 upload
            if (UploadedFile::getInstance($imageModel, 'image2')){
                $propertyImageModel = ExtendedPropertyImages::findOne(
                    ['propertyid' => $id, 'imageorder' => 1]);
                if($propertyImageModel === null){
                    $propertyImageModel = new ExtendedPropertyImages();
                    
                }
                $memberImage = UploadedFile::getInstance($imageModel,'image2');
                $imagePath = !empty($propertyImageModel->imageurl) ? Yii::getAlias('@service'. $propertyImageModel->imageurl) : "";
                // $imagePath = Yii::getAlias('@service'. $propertyImageModel->imageurl);
                if(file_exists($imagePath)) {
                    unlink($imagePath);    
                }
                $targetPath = 'institution/'.$institutionid.'/'.Yii::$app->params['image']['products']['propertyImage'].'/'.$propertyId;
                $memberImage = $this->fileUpload($memberImage,$targetPath);
                $propertyImageModel->imageurl = $memberImage['orginal'];
                $propertyImageModel->propertyid = $propertyId;
                $propertyImageModel->imageorder = 1;
                $propertyImageModel->save();
            }

            //Image3 upload
            if (UploadedFile::getInstance($imageModel, 'image3')){
                $propertyImageModel = ExtendedPropertyImages::findOne(
                    ['propertyid' => $id, 'imageorder' => 2]);
                if($propertyImageModel === null){
                    $propertyImageModel = new ExtendedPropertyImages();
                    
                }
                $memberImage = UploadedFile::getInstance($imageModel,'image3');
                $imagePath = !empty($propertyImageModel->imageurl) ? Yii::getAlias('@service'. $propertyImageModel->imageurl) : "";
                //$imagePath = Yii::getAlias('@service'. $propertyImageModel->imageurl);
                if(file_exists($imagePath)) {
                    unlink($imagePath);    
                }
                $targetPath = 'institution/'.$institutionid.'/'.Yii::$app->params['image']['products']['propertyImage'].'/'.$propertyId;
                $memberImage = $this->fileUpload($memberImage,$targetPath);
                $propertyImageModel->imageurl = $memberImage['orginal'];
                $propertyImageModel->propertyid = $propertyId;
                $propertyImageModel->imageorder = 2;
                $propertyImageModel->save();
            }

            //Image4 upload
            if (UploadedFile::getInstance($imageModel, 'image4')){
                $propertyImageModel = ExtendedPropertyImages::findOne(
                    ['propertyid' => $id, 'imageorder' => 0]);
                if($propertyImageModel === null){
                    $propertyImageModel = new ExtendedPropertyImages();
                    
                }
                $memberImage = UploadedFile::getInstance($imageModel,'image4');
                $imagePath = !empty($propertyImageModel->imageurl) ? Yii::getAlias('@service'. $propertyImageModel->imageurl) : "";
                // $imagePath = Yii::getAlias('@service'. $propertyImageModel->imageurl);
                if(file_exists($imagePath)) {
                    unlink($imagePath);    
                }
                $targetPath = 'institution/'.$institutionid.'/'.Yii::$app->params['image']['products']['propertyImage'].'/'.$propertyId;
                $memberImage = $this->fileUpload($memberImage,$targetPath);
                $propertyImageModel->imageurl = $memberImage['orginal'];
                $propertyImageModel->propertyid = $propertyId;
                $propertyImageModel->imageorder = 3;
                $propertyImageModel->save();
            }

            return $this->redirect(['index']);
        }
        if ($model->getErrors()) {
             $this->sessionAddFlashArray('error', $model->getErrors(), true);
        }
        return $this->render('update', [
            'model' => $model, 'imageModel' => $imageModel,
            'propertyImageModel' => $propertyImageModel,
            'categoryList' => $categoryList
        ]);
    }


    /**
     * Finds the ExtendedPropertycategory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ExtendedPropertycategory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ExtendedPropertycategory::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Available / Unavailable FoodMenu Item.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @return ExtendedProperty the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionActivateDeactivateFoodItem(){
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (yii::$app->request->isAjax) {
            $propertyId = yii::$app->request->post('propertyId');
            $isActive = yii::$app->request->post('isActive');
            if ($propertyId) {
                $propertyId = (int)$propertyId;
                if(($model = ExtendedProperty::findOne($propertyId)) !== null)
                {
                    $model->active = $isActive;
                    $model->save();
                    return ['status' => 'success','data' => null];
                }
                else{
                    return ['status' => 'error','data' => null];
                }
            }
            else{
                return ['status' => 'error','data' => null];
            }
        }
        else{
            return ['status' => 'error','data' => null];
        }
    }

    /**
     * Get property categories by institution.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @return ExtendedPropertyCategory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionPropertyCategoriesByInstitution(){
        $restaurantObject = new RestaurantComponent();

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (yii::$app->request->isAjax) {
            $institutionId = yii::$app->request->get('institutionId');
            $isActive = yii::$app->request->get('isActive');
            if ($institutionId) {
                $institutionId = (int)$institutionId;
                $categoryList = $restaurantObject->getAllPropertyCategoryByInstitutionId($institutionId, null);
                if($categoryList != null)
                {
                    $categoryHtml =  $this->renderPartial('_category',
                        ['categoryList' => $categoryList]);
                    return ['status' => 'success','data' => $categoryHtml];
                }
                else{
                    return ['status' => 'error','data' => null];
                }
            }
            else{
                return ['status' => 'error','data' => null];
            }
        }
        else{
            return ['status' => 'error','data' => null];
        }
    }

    /**
     * save/update property categories by institution.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @return ExtendedPropertyCategory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionSavePropertyCategory(){
        $restaurantObject = new RestaurantComponent();
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        //Save property category
        if (yii::$app->request->isAjax) {
            $model = new ExtendedPropertycategory();
            $model->propertycategoryid = yii::$app->request->post('propertyCategoryId');
            $model->propertygroupid = yii::$app->params['constants']['restaurant'];
            $model->category = yii::$app->request->post('categoryName');
            $model->institutionid = $this->currentUser()->institutionid;

            $propertyCategoryId = (int)yii::$app->request->post('propertyCategoryId');

            if ($propertyCategoryId == 0) {
                $model->active = '1';
                $model->createdby = $this->currentUser()->id;
                $model->createddatetime = date_format(date_create(),Yii::$app->params['dateFormat']['sqlDandTFormat']);
                $model->save();
                return ['status' => 'success' ,'data'=> null];
            }
            else{
                $model = ExtendedPropertycategory::findOne($propertyCategoryId);
                $model->active = (string)$model->active;
                $model->category = yii::$app->request->post('categoryName');
                $model->modifiedby = $this->currentUser()->id;
                $model->modifieddatetime = date_format(date_create(),Yii::$app->params['dateFormat']['sqlDandTFormat']);
                $model->save();
                return ['status' => 'success','data' => $model->getErrors()];
            }
        }
        else{
            return ['status' => 'error','data' => null];
        }
    }

    /**
     * Available / Unavailable food category Item.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @return ExtendedPropertyCategory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionActivateDeactivateCategoryItem(){
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        //Activate/Deactivate food category
        if (yii::$app->request->isAjax) {
            $propertyCategoryId = yii::$app->request->post('propertyCategoryId');
            $isActive = yii::$app->request->post('isActive');
            if ($propertyCategoryId) {
                $propertyCategoryId = (int)$propertyCategoryId;
                if(($model = ExtendedPropertyCategory::findOne($propertyCategoryId)) !== null)
                {
                    $model->active = $isActive;
                    $model->modifiedby = $this->currentUser()->id;
                    $model->modifieddatetime = date_format(date_create(),Yii::$app->params['dateFormat']['sqlDandTFormat']);
                    $model->save();
                    return ['status' => 'success','data' => null];
                }
                else{
                    return ['status' => 'error','data' => null];
                }
            }
            else{
                return ['status' => 'error','data' => null];
            }
        }
        else{
            return ['status' => 'error','data' => null];
        }
    }

    /**
     * To upload the images
     */
    protected  function fileUpload($image,$targetPath)
    {
        
        $fileHandlerObj = new FileuploadComponent();
        
        $tempName = $image->tempName;
        $uploadFilename = $image->name;
        
        $uploadImages = $fileHandlerObj->uploader($uploadFilename,$targetPath,$tempName,false,false,false,false);
        
        return $uploadImages;
    }
}
