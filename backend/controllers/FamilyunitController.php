<?php

namespace backend\controllers;

use Yii;
use common\models\extendedmodels\ExtendedFamilyunit;
use common\models\searchmodels\FamilyunitSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;


/**
 * FamilyunitController implements the CRUD actions for ExtendedFamilyunit model.
 */
class FamilyunitController extends BaseController
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
                    'activate' => ['POST'],
                    'deactivate' => ['POST']
                ],
            ],
        ];
    }
    function beforeAction($action)
    {   
        //if Manage Family Units privilage is set user has access.
        if (!Yii::$app->user->can('04cd913a-ec49-11e6-b48e-000c2990e707')) {
            throw new \yii\web\ForbiddenHttpException('You are not allowed to perform the requested action');
        }
        return parent::beforeAction($action);
    }

    /**
     * Lists all ExtendedFamilyunit models.
     * @return mixed
     */
    public function actionIndex()
    {
       
        $searchModel = new FamilyunitSearch();
        $model = new ExtendedFamilyunit();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$this->currentUser()->institutionid);
        
        $update ='add';
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'update'  =>$update,
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ExtendedFamilyunit model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    /*public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
*/
    /**
     * Creates a new ExtendedFamilyunit model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
     public function actionCreate()
    {
        $model = new ExtendedFamilyunit(); 
        if ($model->load(Yii::$app->request->post())) {
            
            $model->institutionid = $this->currentUser()->institutionid;
            $model->save();           
            return $this->redirect(['familyunit/index']);
        }

       return $this->redirect(['familyunit/index']);
    }
 
    /**
     * Updates an existing ExtendedFamilyunit model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    { 
        $model = $this->findModel($id);
        $searchModel = new FamilyunitSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$this->currentUser()->institutionid);
        $update ='update';
    
        if ($model->load(Yii::$app->request->post())) {

            $data = Yii::$app->request->post();
            $model->description = $data['ExtendedFamilyunit']['description'];
            $model->save();

            return $this->redirect(['familyunit/index']);
        }
 
            return $this->render('index', [
            'model'  =>$model,
            'update'  =>$update,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    
    public function actionActivate()
    {
        
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (yii::$app->request->isAjax) {
            $id = yii::$app->request->post('id');
            if ($id) {
                $id = (int)$id;
                $model = $this->findModel($id);
                if (!empty($model)) {
                     $model->active = 1;
                    if ($model->update()) {
                            return [
                                'status' => 'success',
                                'data' => null,
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
                        'data' => null,
                ];
            }
             return [
                        'status' => 'error',
                        'data' => null,
            ];
        }
    }
    public function actionDeactivate()
    {   
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (yii::$app->request->isAjax) {
            $id = yii::$app->request->post('id');
            if ($id) {
                $id = (int)$id;
                $model = $this->findModel($id);
                if (!empty($model)) {
                     $model->active = 0;
                     
                    if ($model->update()) {
                            return [
                                'status' => 'success',
                                'data' => null,
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
                        'data' => null,
                ];
            }
             return [
                        'status' => 'error',
                        'data' => null,
            ];
        }
    }
            

    /**
     * Deletes an existing ExtendedFamilyunit model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
   /* public function actionDelete($id)
    {

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }*/
    /**
     * Finds the ExtendedFamilyunit model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ExtendedFamilyunit the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ExtendedFamilyunit::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
