<?php
namespace backend\controllers;

use Yii;
use common\models\extendedmodels\ExtendedEvent;
use common\models\searchmodels\NewsSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\FileHelper;
use yii\filters\AccessControl;
use common\components\PushNotificationHandler;
use common\components\NotificationHandler;
use common\models\basemodels\BaseModel;
use common\models\extendedmodels\ExtendedMember;
use yii\helpers\ArrayHelper;
use common\models\extendedmodels\ExtendedFamilyunit;

/**
 * EventController implements the CRUD actions for ExtendedEvent model.
 */
class NewsController extends BaseController
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
                            'delete',
                            'publish',
                            'upload'
                    ],
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => [
                                'view',
                                'create',
                                'update',
                                'delete',
                                'publish',
                                'upload'
                            ],
                            'roles' => ['893232ae-ec46-11e6-b48e-000c2990e707']
                        ], 
                        [
                            'allow' => true,
                            'actions' => [
                                'index'
                            ],
                            'roles' => ['7d0b6ab2-ec46-11e6-b48e-000c2990e707']
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
     * Lists all ExtendedEvent models.
     * @return mixed
     */
    public function actionIndex()
    {
      
        $searchModel = new NewsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$this->currentUser()->institutionid);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ExtendedEvent model.
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
     * Creates a new ExtendedEvent model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ExtendedEvent(['scenario' => ExtendedEvent::SCENARIO_NEWS]);
        $post = Yii::$app->request->post(); 
        $userInstitution = Yii::$app->user->identity->institution;
        $institutionId = $userInstitution->id;
        $timeZone = $userInstitution->timezone;
        date_default_timezone_set($timeZone);
        $members = ExtendedMember::getMemberForEventAutoSuggest($institutionId);
        $familyUnits =  ArrayHelper::map(
            ExtendedFamilyunit::find()
            ->where(['institutionid' => $institutionId])->
            andWhere(['active' => 1])
            ->all(), 
            'familyunitid', 'description');

        $allBatches = BaseModel :: getBatches();
        $model->batch = 'All';
        
        if ($model->load($post)) {
            $model->institutionid = $institutionId;
            $model->eventtype = 'A';
            $model->createddate = date(yii::$app->params['dateFormat']['sqlDandTFormat']); 
            $model->createduser = Yii::$app->user->identity->id;
            $selectedBatches = !empty($post['ExtendedEvent']['batch'] )? implode($post['ExtendedEvent']['batch'],',') : 'All';
            $model->batch = $selectedBatches;
            if ($model->validate()) {
                $model->activitydate =  date(yii::$app->params['dateFormat']['sqlDandTFormat'],strtotimeNew($model->activitydate." 12:00:00"));
                $model->batch = ($selectedBatches == 'All') ? null : $selectedBatches;
                if ($model->expirydate ) {
                    $model->expirydate = date(yii::$app->params['dateFormat']['sqlDateFormat'],strtotimeNew($model->expirydate ));
                }
                if ($model->save(false)) {
                    return $this->redirect(['index']);
                } 
            }
        }

        $model->activitydate = ($model->activitydate) ? date(yii::$app->params['dateFormat']['viewDateFormat'],strtotimeNew($model->activitydate)) : date(yii::$app->params['dateFormat']['viewDateFormat']);
        $model->expirydate = ($model->expirydate) ? date(yii::$app->params['dateFormat']['viewDateFormat'],strtotimeNew($model->expirydate)) : date(yii::$app->params['dateFormat']['viewDateFormat']);
        return $this->render('create', [
            'model' => $model,
        	'members' => $members,
            'familyUnits' => $familyUnits,
            'batches' => $allBatches,
        ]);
    }
    /**
     * Updates an existing ExtendedEvent model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {   
        $userInstitution = Yii::$app->user->identity->institution;
        $timeZone = $userInstitution->timezone;
        date_default_timezone_set($timeZone);
        $model = $this->findModel($id);
        $model->scenario = ExtendedEvent::SCENARIO_NEWS;
        $post = Yii::$app->request->post();
        $institutionId = $userInstitution->id;
        $members = ExtendedMember::getMemberForEventAutoSuggest($institutionId);
        $familyUnits =  ArrayHelper::map(
            ExtendedFamilyunit::find()
            ->where(['institutionid' => $institutionId])->
            andWhere(['active' => 1])
            ->all(), 
            'familyunitid', 'description');
        $allBatches = BaseModel :: getBatches();

        if ($model->load($post)) {
            $model->eventtype = 'A';
            $model->institutionid = $institutionId;
            $model->modifiedby = Yii::$app->user->id;
            $model->modifieddatetime =  date(yii::$app->params['dateFormat']['sqlDandTFormat']); 
            $selectedBatches = !empty($post['ExtendedEvent']['batch'] )? implode($post['ExtendedEvent']['batch'],',') : 'All';
            $model->batch = $selectedBatches;
            if ($model->validate()) {
                $model->iseventpublishable = ($model->expirydate) ? ((strtotimeNew($model->expirydate) < strtotimeNew(date('Y-m-d')))  ? 1 : 0 ): 0;
                $model->activitydate =  date(yii::$app->params['dateFormat']['sqlDandTFormat'], strtotimeNew($model->activitydate." 12:00:00"));
                if ($model->expirydate ){
                    $model->expirydate = date(yii::$app->params['dateFormat']['sqlDateFormat'], strtotimeNew($model->expirydate));
                }
                $model->batch = ($selectedBatches == 'All') ? null : $selectedBatches;
                if($model->update(false)) {
                    return $this->redirect(['index']);
                } else if(empty($model->getErrors())) {
                    return $this->redirect(['index']);
                } 
            } 
        }
        $model->activitydate = ($model->activitydate) ? date(yii::$app->params['dateFormat']['viewDateFormat'],strtotimeNew($model->activitydate)) : null;
        $model->expirydate = ($model->expirydate) ? date(yii::$app->params['dateFormat']['viewDateFormat'],strtotimeNew($model->expirydate)) : null;
        return $this->render('update', [
            'model' => $model,
            'members' => $members,
            'familyUnits' => $familyUnits,
            'batches' => $allBatches,
        ]);
    }
    /**
     * Deletes an existing ExtendedEvent model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $status = 'error';
        if (yii::$app->request->isAjax) {
            $id = yii::$app->request->post('id');
            if ($id) {
                $sql1 = 'DELETE FROM `albumimage`
                        WHERE albumid IN
                        (
                            Select albumid from
                            album where
                            eventid = :eventid
                        )';
                $sql2 = 'DELETE FROM `album` WHERE eventid = :eventid';
                $sql3 = 'DELETE FROM `eventseendetails` WHERE eventid = :eventid';
                $sql4 = "DELETE FROM `notificationlog` WHERE eventid = :eventid";  
                $sql5 = 'DELETE FROM `events` WHERE id = :eventid';   

                
                $db = Yii::$app->db;
                    $transaction = $db->beginTransaction();
                try {
                        $db->createCommand($sql1)->bindValue(':eventid', $id)->execute();
                        $db->createCommand($sql2)->bindValue(':eventid', $id)->execute();
                        $db->createCommand($sql3)->bindValue(':eventid', $id)->execute();
                        $db->createCommand($sql4)->bindValue(':eventid', $id)->execute();
                        $db->createCommand($sql5)->bindValue(':eventid', $id)->execute();
                        $transaction->commit();
                        $status = 'success';
                } catch(\Exception $e) {
                    $transaction->rollBack();
                    yii::error($e->getMessage());
                    $status = 'error'; 
                }
            } else {
                $status = 'error';
            }
        }
        return [
            'status' => $status,
            'data' => null
        ];
    }

    /**
     * Finds the ExtendedEvent model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ExtendedEvent the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ExtendedEvent::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
    public function actionPublish()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $createdBy = Yii::$app->user->identity->id;
        $timeZone = Yii::$app->user->identity->institution->timezone;
        date_default_timezone_set($timeZone);
        try 
	    {
	        if (yii::$app->request->isAjax) {
	            $id = yii::$app->request->post('id');
	            $noteHead = yii::$app->request->post('noteHead');
	            $activityDate = yii::$app->request->post('activityDate');
	            // $venue = yii::$app->request->post('venue');
	            $familyUnitId = yii::$app->request->post('familyUnitId', null);
	            $institutionid = $this->currentUser()->institutionid;
	            $institutionDetails = Yii::$app->user->identity->institution;
	            $institutionName = $institutionDetails['name'];
	            
	            if ($id) {
	                $id = (int)$id;
	                $model = $this->findModel($id);
	                if (!empty($model)) {
                        $announcementBatch = $model->batch;
	                     $model->iseventpublishable = 1;
                         $model->publishedon = date(yii::$app->params['dateFormat']['sqlDandTFormat']); 

                         $expiresOn = date('d-m-Y', strtotimeNew($model->expirydate));
                         $activityDate = date('d-m-Y', strtotimeNew($activityDate));
                         $currentDate = date('d-m-Y');

	                 	if ($model->update(false)) {
	                 	 	if($institutionid) {
                                if (strtotime($currentDate) >= strtotime($activityDate) && strtotime($currentDate) <= strtotime($expiresOn)) {
	                 	 	    Yii::$app->consoleRunner->run('publish-notification/publish --eventType="A" --eventId='.$id.' --noteHead="'.$noteHead.'" --institutionName="'.$institutionName.'" --institutionId='.$institutionid.' --familyUnitId='.$familyUnitId.' --createdBy='.$createdBy.' --activityDate="'.$activityDate.'" --venue="" --expiryDate="'.$expiresOn.'" --batch="'.$announcementBatch.'"');
	                 	 	    return [
	                 	 	        'status' => 'success',
	                 	 	        'data' => 'Announcements published to all available devices',
	                 	 	    ];
                                }else{
                                    return [
                                        'status' => 'success',
                                        'data' => 'Announcements published successfully',
                                    ];
                                }
	                 	 	} else {
	                 	 		return [
	                 	 				'status' => 'error',
	                 	 				'data' => null
	                 	 		];
	                 	 	}
	                 	 	
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
	    } catch(\Exception $e){
	    	yii::error($e->getMessage());
	    	return [
	    			'status' => 'error',
	    			'data' => null,
	    	];
	    }
	}
    public function beforeAction($action) 
    {   
        if(Yii::$app->controller->action->id === 'upload') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action); 
    }
 
    public function actionUpload()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (yii::$app->request->isAjax) {
            if(isset($_FILES['img']['name']) && $_FILES['img']['name'] != '') {
                $filename = explode('.', $_FILES['img']['name']);
                $extension = end($filename); 
                $name = rand(100,999).'.'.$extension;
                $path = \Yii::getAlias('@service').'/NewsUploads/'. $this->currentUser()->institutionid.'/';
                $path =  FileHelper::createDirectory($path);
                $location =  \Yii::getAlias('@service').'/NewsUploads/'. $this->currentUser()->institutionid.'/'.$name;
                
                if( move_uploaded_file($_FILES['img']['tmp_name'], $location)) {
                    $location =  Yii::$app->params['imagePath'].'/NewsUploads/'. $this->currentUser()->institutionid.'/'.$name;
 
                     return ['hasError' => false,'url' =>  $location ];
                 } {
                     return ['hasError' => true,'url' =>  ''];
                 }
             } {
                 return ['hasError' => true,'url' => '' ];
             }
        }
    }
}
