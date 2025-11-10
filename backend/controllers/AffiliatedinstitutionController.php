<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;
use yii\base\ErrorException;
use yii\helpers\FileHelper;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use backend\controllers\BaseController;
use common\models\extendedmodels\ExtendedCountry;
use common\models\extendedmodels\ExtendedAffiliatedinstitution;
use common\models\searchmodels\ExtendedAffiliatedinstitutionSearch;

/**
 * AffiliatedinstitutionController implements the CRUD actions for ExtendedAffiliatedinstitution model.
 */
class AffiliatedinstitutionController extends BaseController
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
                    'getCountryCode' => ['GET'],
                ],
            ],
        ];
    }

    function beforeAction($action)
    {   
        //if manage affilated institution privilage is set user has access.
        if (!Yii::$app->user->can('5a1562b9-ed1e-11e6-b48e-000c2990e707')) {
            throw new \yii\web\ForbiddenHttpException('You are not allowed to perform the requested action');
        }
        return parent::beforeAction($action);
    }

    /**
     * Lists all ExtendedAffiliatedinstitution models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new ExtendedAffiliatedinstitution();
        $searchModel = new ExtendedAffiliatedinstitutionSearch();

        //IsRotary
        $institutionDetails = Yii::$app->user->identity->institution;
        $isRotary = $institutionDetails['isrotary'];
        // $meetingDays = Yii::$app->params['meetingDays'];
        if ($isRotary) {
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->currentUser()->institutionid, $isRotary);

            return $this->render('rotaryindex', [
                'searchModel' => $searchModel,
                'model' => $model,
                'dataProvider' => $dataProvider,
                'isRotary' => $isRotary,
            ]);
        }
        else {
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->currentUser()->institutionid, $isRotary);
            return $this->render('index', [
                'searchModel' => $searchModel,
                'model' => $model,
                'dataProvider' => $dataProvider,
                'isRotary' => $isRotary
            ]);
        }
    }

    /**
     * Displays a single ExtendedAffiliatedinstitution model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ExtendedAffiliatedinstitution model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
       
        $model = new ExtendedAffiliatedinstitution();
        $model->institutionid = $this->currentUser()->institutionid;
        $model->createduser = $this->currentUser()->id;
        //IsRotary
        $institutionDetails = Yii::$app->user->identity->institution;
        $isRotary = $institutionDetails->isrotary;
        $meetingDays = Yii::$app->params['meetingDays'];

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $filename = explode('.', $_FILES['ExtendedAffiliatedinstitution']
                ['name']['institutionlogo']);
            $extension = end($filename);   
            $name = rand(100000,999999).'.'.$extension;
            $path = \Yii::getAlias('@service').'/institutionlogo/'. $this->currentUser()->institutionid.'/';
            $path =  FileHelper::createDirectory($path);
            $location =  \Yii::getAlias('@service').'/institutionlogo/'. $this->currentUser()->institutionid.'/'.$name;
                
            if(move_uploaded_file($_FILES['ExtendedAffiliatedinstitution']
                ['tmp_name']['institutionlogo'], $location)) {
               $location =  Yii::$app->params['imagePath'].'/institutionlogo/'. $this->currentUser()->institutionid.'/'.$name;

                $model->institutionlogo = $name;
                $model->save();
                return $this->redirect(['index']);
            }
            else{
                $model->save();
                return $this->redirect(['index']);
            }
        }
        if($model->getErrors()){
            $this->sessionAddFlashArray('error', $model->getErrors(), true);
        } 
        $countryList = ArrayHelper::map(
                ExtendedCountry::find()
                ->select('countryid, CountryName')
                ->orderBy('CountryName')->all(),
                'countryid','CountryName'
        );
        return $this->render('create', [
            'model' => $model, 'countryList' => $countryList,
            'isRotary' => $isRotary, 'meetingDays' => $meetingDays
        ]);
    }

    /**
     * Updates an existing ExtendedAffiliatedinstitution model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $tempModel= $model->institutionlogo;
        //IsRotary
        $institutionDetails = Yii::$app->user->identity->institution;
        $isRotary = $institutionDetails->isrotary;
        $meetingDays = Yii::$app->params['meetingDays'];

        $countryList = ArrayHelper::map(
                ExtendedCountry::find()
                ->select('countryid, CountryName')
                ->orderBy('CountryName')->all(),
                'countryid','CountryName'
        );
        $model->active = '1';
        $model->modifieduser = $this->currentUser()->id;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if($_FILES['ExtendedAffiliatedinstitution']
                ['name']['institutionlogo']){
                $filename = explode('.', $_FILES['ExtendedAffiliatedinstitution']
                ['name']['institutionlogo']);
                $extension = end($filename);   
                $name = rand(100000,999999).'.'.$extension;
                $path = \Yii::getAlias('@service').'/institutionlogo/'. $this->currentUser()->institutionid.'/';
                $path =  FileHelper::createDirectory($path);
                $location =  \Yii::getAlias('@service').'/institutionlogo/'. $this->currentUser()->institutionid.'/'.$name;
                    
                if(move_uploaded_file($_FILES['ExtendedAffiliatedinstitution']
                    ['tmp_name']['institutionlogo'], $location)) {
                   $location =  Yii::$app->params['imagePath'].'/institutionlogo/'. $this->currentUser()->institutionid.'/'.$name;
                   $model->institutionlogo = $name;
                   $model->save();
                   return $this->redirect(['index']);
                }
            }
            else{
                $model->institutionlogo = $tempModel;
                $model->save();
                return $this->redirect(['index']);
            }
        }
        else{
            if($model->getErrors()){
                $this->sessionAddFlashArray('error', $model->getErrors(), true);
            } 
        }
        return $this->render('update', [
            'model' => $model, 'countryList' => $countryList,
            'isRotary' => $isRotary, 'meetingDays' => $meetingDays
        ]);
    }

    /**
     * Deletes an existing ExtendedAffiliatedinstitution model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (yii::$app->request->isAjax) {
            $affiliatedinstitutionid = yii::$app->request->post('affiliatedinstitutionid');
            if ($affiliatedinstitutionid) {
                $affiliatedinstitutionid = (int)$affiliatedinstitutionid;
                $this->findModel($affiliatedinstitutionid )->delete();
                return ['status' => 'success'];
            }
            else{
                return ['status' => 'error'];
            }
        }
        else{
            return ['status' => 'error'];
        }
    }

    /**
     * Finds the ExtendedAffiliatedinstitution model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ExtendedAffiliatedinstitution the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ExtendedAffiliatedinstitution::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * getCountryCode from  ExtendedCountry model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionGetCountryCode()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (yii::$app->request->isAjax) {
            $countryId = yii::$app->request->get('countryId');
            if ($countryId) {
                $countryId = (int)$countryId;
                if (($model = ExtendedCountry::findOne($countryId)) != null) {
                    return ['status' => 'success' , 'countryCode' => $model->telephonecode];
                }
                else{
                    return ['status' => 'error' , 'countryCode' => null];
                }
            }
            else{
               return ['status' => 'error' , 'countryCode' => null]; 
            }
        }
    }
}
