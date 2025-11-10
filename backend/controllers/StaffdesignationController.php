<?php

namespace backend\controllers;

use Yii;
use common\models\extendedmodels\ExtendedStaffdesignation;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\controllers\BaseController;
use yii\filters\AccessControl;
use common\models\searchmodels\ExtendedStaffdesignationSearch;

/**
 * StaffdesignationController implements the CRUD actions for ExtendedStaffdesignation model.
 */
class StaffdesignationController extends BaseController
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
                			'deactivate' => ['POST'],
                			'activate' => ['POST'],
                	],
                ],
        ];
    }
    function beforeAction($action)
    {   
        //if Manage Family Units privilage is set user has access.
        if (!Yii::$app->user->can('1041a93a-153b-11e7-b48e-000c2990e707')) {
            throw new \yii\web\ForbiddenHttpException('You are not allowed to perform the requested action');
        }
        return parent::beforeAction($action);
    }

    /**
     * Lists all ExtendedStaffdesignation models.
     * @return mixed
     */
    public function actionIndex()
    {
       
    	$searchModel = new ExtendedStaffdesignationSearch();
    	$model = new ExtendedStaffdesignation();
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
     * Creates a new ExtendedStaffdesignation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
    	$model = new ExtendedStaffdesignation();
    	if ($model->load(Yii::$app->request->post())) {
    		
    		$model->institutionid = $this->currentUser()->institutionid;
    		$model->createddatetime = date('Y-m-d H:i:s');
    		$model->createdby = Yii::$app->user->identity->id;
    		$model->save();
    		return $this->redirect(['staffdesignation/index']);
    	}
    
    	return $this->redirect(['staffdesignation/index']);
    }

    /**
     * Updates an existing ExtendedStaffDesignation model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
    	$model = $this->findModel($id);
    	$searchModel = new ExtendedStaffdesignationSearch();
    	$dataProvider = $searchModel->search(Yii::$app->request->queryParams,$this->currentUser()->institutionid);
    	$update ='update';
    
    	if ($model->load(Yii::$app->request->post())) {
    
    		$data = Yii::$app->request->post();
    		$model->designation = $data['ExtendedStaffdesignation']['designation'];
    		$model->modifieddatetime = date('Y-m-d H:i:s');
    		$model->modifiedby = Yii::$app->user->identity->id;
    		$model->save();
    
    		return $this->redirect(['staffdesignation/index']);
    	}
    
    	return $this->render('index', [
    			'model'  =>$model,
    			'update'  =>$update,
    			'searchModel' => $searchModel,
    			'dataProvider' => $dataProvider,
    	]);
    }
    /**
     * To activate a deactivated
     * staff designation
     * @return string[]|NULL[]
     */
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
    /**
     * To deactivate an
     * activated staff designation
     * @return string[]|NULL[]
     */
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
     * Finds the ExtendedStaffdesignation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ExtendedStaffdesignation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ExtendedStaffdesignation::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
