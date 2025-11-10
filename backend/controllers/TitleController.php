<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\data\Pagination;
use yii\filters\VerbFilter;
use yii\base\ErrorException;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use backend\controllers\BaseController;
use common\models\extendedmodels\ExtendedTitle;
use common\helpers\CacheHelper;

/**
 * TitleController implements the CRUD actions for ExtendedTitle model.
 */
class TitleController extends BaseController
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
                    'deactivate' => ['POST'],
                    'activate' => ['POST'],
                ],
            ],
        ];
    }

    function beforeAction($action)
    {   
        //if Manage Titles privilage is set user has access.
        if (!Yii::$app->user->can('4904f428-ec4b-11e6-b48e-000c2990e707')) {
            throw new \yii\web\ForbiddenHttpException('You are not allowed to perform the requested action');
        }
        return parent::beforeAction($action);
    }

   
    /**
     * Lists all ExtendedTitle models.
     * @return mixed
     */
    public function actionIndex()
    {
        try{
            $model = new ExtendedTitle();
            if ($model->load(Yii::$app->request->post())) {
                if($model->validate()) {
                    $model->institutionid = $this->currentUser()->institutionid;
                    $model->Description = trim($model->Description);
                    if (!$model->save()) {
                        $this->sessionAddFlashArray('error', 'Unable to process the request' , true);  
                    }
                    else{
                        // Clear the titles cache for this institution after successfully creating a new title
                        CacheHelper::clearTitlesCache($this->currentUser()->institutionid);
                        
                        $model->Description = '';
                    }
                }
                else{
                    
                    if($model->getErrors()){
                        //$this->sessionAddFlashArray('error', 'Title already exist' , true);
                    } 
                } 
                $model->refresh();
                return $this->redirect(['index']);                 
            } 

            $dataProvider = new ActiveDataProvider([
                'query' => ExtendedTitle::find()->where(['institutionid' => 
                    $this->currentUser()->institutionid ]),
                'pagination' => [ 'pageSize' => 10 ],
                'sort' => [
                    'defaultOrder' => [
                    'Description' => SORT_ASC, 
                    ]
                ],
            ]);  
            return $this->render('index', [
                'dataProvider' => $dataProvider,
                'model' => $model
            ]);
        }
        catch(ErrorException $e){
            yii::error($e->getMessage());
        }   
    }

    /**
     * Edit a single ExtendedTitle model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionViewTitle($id)
    {
        return $this->redirect(['edit','id' => $id]);
    }

    /**
     * Updates an existing ExtendedTitle model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionEdit($id)
    {
        try{
            $model = $this->findModel($id);
       
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                if (!$model->save()) {
                    $this->sessionAddFlashArray('error', $model->getErrors(), true);  
                }
                else{
                    // Clear the titles cache for this institution after successfully updating a title
                    CacheHelper::clearTitlesCache($this->currentUser()->institutionid);
                }
                return $this->redirect(['index']);
            }

            $dataProvider = new ActiveDataProvider([
                'query' => ExtendedTitle::find()->where(['institutionid' => 
                    $this->currentUser()->institutionid ]),
                'pagination' => [ 'pageSize' => 10 ],
                'sort' => [
                    'defaultOrder' => [
                    'Description' => SORT_ASC, 
                    ]
                ],
            ]);

            return $this->render('edit', [
                'model' => $model, 'dataProvider'=>$dataProvider
            ]);
        }
        catch(ErrorException $e){
            yii::error($e->getMessage());
        }
        
    }

    /**
     * Deactivate an existing ExtendedTitle model.
     * If deactivation is successful, the browser will be redirected to the 'title' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDeactivate()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        try{
            if (yii::$app->request->isAjax) {
                $id = yii::$app->request->post('id');
                if ($id) {
                    $id = (int)$id;
                    $model = $this->findModel($id);
                    $model->active = 0;
                    if($model->save(false)){
                        // Clear the titles cache for this institution
                        CacheHelper::clearTitlesCache($this->currentUser()->institutionid);
                        
                        return ['status' => 'success','data' => null];  
                    } else {
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
        catch(ErrorException $e){
            yii::error($e->getMessage());
        }
    }

    /**
     * Activates an existing ExtendedTitle model.
     * If activation is successful, the browser will be redirected to the 'title' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionActivate()
    {
        
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        try{
            if (yii::$app->request->isAjax) {
                $id = yii::$app->request->post('id');
                if ($id) {
                    $id = (int)$id;
                    $model = $this->findModel($id);
                    $model->active = 1;
                    if($model->save()){
                        // Clear the titles cache for this institution
                        CacheHelper::clearTitlesCache($this->currentUser()->institutionid);
                        
                        return ['status' => 'success','data' => null];
                    }else {
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
        catch(ErrorException $e){
            yii::error($e->getMessage());
        }
        
    }

    /**
     * Finds the ExtendedTitle model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ExtendedTitle the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        try{
            if (($model = ExtendedTitle::findOne($id)) !== null) {
                return $model;
            }

            throw new NotFoundHttpException('The requested page does not exist.');
        }
        catch(ErrorException $e){
            yii::error($e->getMessage());
        }
    }
}
