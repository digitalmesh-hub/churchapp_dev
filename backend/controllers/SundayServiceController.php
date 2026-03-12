<?php

namespace backend\controllers;

use Yii;
use common\models\extendedmodels\ExtendedSundayService;
use common\models\searchmodels\SundayServiceSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * SundayServiceController implements the CRUD actions for ExtendedSundayService model.
 */
class SundayServiceController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => [
                    'index',
                    'view',
                    'create',
                    'update',
                    'delete'
                ],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'view',
                            'create',
                            'update',
                            'delete'
                        ],
                        'roles' => ['@'] // Allow authenticated users - adjust role as needed
                    ],
                    [
                        'allow' => true,
                        'actions' => [
                            'index'
                        ],
                        'roles' => ['@'] // Allow authenticated users - adjust role as needed
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Check if Sunday Service is enabled for the current institution
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        // Check if Sunday Service is enabled for this institution
        $institutionId = $this->currentUser()->institutionid ?? null;
        
        if ($institutionId) {
            $enabledInstitutions = env('SUNDAY_SERVICE_ENABLED_INSTITUTIONS', '');
            $enabledInstitutionsList = array_filter(array_map('trim', explode(',', $enabledInstitutions)));
            
            if (!in_array($institutionId, $enabledInstitutionsList)) {
                throw new \yii\web\ForbiddenHttpException('Sunday Service feature is not enabled for your institution.');
            }
        }

        return true;
    }

    /**
     * Lists all ExtendedSundayService models with server-side pagination.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SundayServiceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->currentUser()->institutionid);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ExtendedSundayService model.
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
     * Creates a new ExtendedSundayService model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ExtendedSundayService();
        $post = Yii::$app->request->post();
        $userInstitution = Yii::$app->user->identity->institution;
        $institutionId = $userInstitution->id;
        $timeZone = $userInstitution->timezone;
        date_default_timezone_set($timeZone);

        if ($model->load($post)) {
            $model->institution_id = $institutionId;
            $model->created_by = Yii::$app->user->identity->id;
            
            if ($model->validate()) {
                $model->service_date = date(Yii::$app->params['dateFormat']['sqlDateFormat'], strtotimeNew($model->service_date));
                
                if ($model->save(false)) {
                    Yii::$app->session->setFlash('success', 'Sunday Service created successfully.');
                    return $this->redirect(['index']);
                }
            }
        }

        $model->service_date = ($model->service_date) ? 
            date(Yii::$app->params['dateFormat']['viewDateFormat'], strtotimeNew($model->service_date)) : 
            date(Yii::$app->params['dateFormat']['viewDateFormat']);

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ExtendedSundayService model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $post = Yii::$app->request->post();
        $userInstitution = Yii::$app->user->identity->institution;
        $timeZone = $userInstitution->timezone;
        date_default_timezone_set($timeZone);

        // Store original service_date for display
        $originalServiceDate = $model->service_date;

        if ($model->load($post)) {
            $model->updated_by = Yii::$app->user->identity->id;
            
            if ($model->validate()) {
                $model->service_date = date(Yii::$app->params['dateFormat']['sqlDateFormat'], strtotimeNew($model->service_date));
                
                if ($model->save(false)) {
                    Yii::$app->session->setFlash('success', 'Sunday Service updated successfully.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        }

        $model->service_date = ($model->service_date) ? 
            date(Yii::$app->params['dateFormat']['viewDateFormat'], strtotimeNew($model->service_date)) : 
            date(Yii::$app->params['dateFormat']['viewDateFormat']);

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ExtendedSundayService model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Sunday Service deleted successfully.');

        return $this->redirect(['index']);
    }

    /**
     * Finds the ExtendedSundayService model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ExtendedSundayService the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ExtendedSundayService::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
