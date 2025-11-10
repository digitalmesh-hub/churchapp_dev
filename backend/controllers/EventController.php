<?php

namespace backend\controllers;

use Yii;
use common\models\extendedmodels\ExtendedEvent;
use common\models\extendedmodels\ExtendedRsvpdetails;
use common\models\searchmodels\EventSearch;
use common\models\searchmodels\ExtendedRsvpDetailSearch;
use common\components\EmailHandlerComponent;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\FileHelper;
use yii\filters\AccessControl;
use common\models\basemodels\BaseModel;
use common\components\PushNotificationHandler;
use common\components\NotificationHandler;
use common\models\extendedmodels\ExtendedMember;
use yii\helpers\ArrayHelper;
use common\models\extendedmodels\ExtendedFamilyunit;
use common\models\extendedmodels\ExtendedOrders;
use common\models\extendedmodels\ExtendedPrivilege;
use common\models\extendedmodels\ExtendedRosa;


/**
 * EventController implements the CRUD actions for ExtendedEvent model.
 */
class EventController extends BaseController
{
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
                        'rsvp-listing',
                        'rsvpfilter',
                        'delete',
                        'publish',
                        'upload',
                        'acknowledgemail'      
                    ],
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => ['rsvp-listing', 'rsvpfilter'],
                            'roles' => ['d4b64d8a-ec48-11e6-b48e-000c2990e707'] //manage rsvp
                        ],
                        [
                            'allow' => true,
                            'actions' => [
                                'create',
                                'view',
                                'update',
                                'delete',
                                'publish',
                                'upload',
                                'acknowledgemail'
                            ],
                            'roles' => ['bdb35068-ec48-11e6-b48e-000c2990e707'] // manage events
                        ],
                        [
                            'allow' => true,
                            'actions' => [
                                'index'
                            ],
                            'roles' => ['b0d171e3-ec48-11e6-b48e-000c2990e707'] //list events
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

 
    function beforeAction($action)
    {   
        if(Yii::$app->controller->action->id == 'upload') {
            $this->enableCsrfValidation = false; 
        }
        return parent::beforeAction($action);
    }
 
    /**
     * Lists all ExtendedEvent models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EventSearch();
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
        $model = new ExtendedEvent(['scenario' => ExtendedEvent::SCENARIO_EVENTS]);  
        $memberModel = new ExtendedMember();
        $institutionId = $this->currentUser()->institutionid;
        $timeZone = Yii::$app->user->identity->institution->timezone;
        date_default_timezone_set($timeZone); 
        $members = $memberModel::getMemberForEventAutoSuggest($institutionId);
        $familyUnits =  ArrayHelper::map(
            ExtendedFamilyunit::find()
            ->where(['institutionid' => $institutionId])->
            andWhere(['active' => 1])
            ->all(), 
            'familyunitid', 'description');

    
        $model->batch = 'All';
        $allBatches = BaseModel :: getBatches();

        if ($model->load(Yii::$app->request->post())) {
            $eventarray = yii::$app->request->post();
            //print_r($eventarray);exit;
            if(array_key_exists("rsvpavailable",$eventarray['ExtendedEvent']) != 1) {
                $eventarray['ExtendedEvent']['rsvpavailable'] = 0;
            }
            $selectedBatches = (!empty($eventarray['ExtendedEvent']['batch'] )) 
                                ? implode($eventarray['ExtendedEvent']['batch'],',') 
                                : 'All';
            
            //print_r($selectedBatches);exit;
            $model->institutionid = $this->currentUser()->institutionid;
            $model->rsvpavailable= $eventarray['ExtendedEvent']['rsvpavailable'];
            $model->createduser = Yii::$app->user->identity->id;
            $model->createddate = date(yii::$app->params['dateFormat']['sqlDandTFormat']); 
            $model->eventtype = 'E';
            $model->batch = $selectedBatches;

            if($model->validate()) {
                $model->activitydate =  date(yii::$app->params['dateFormat']['sqlDandTFormat'],strtotimeNew($model->activitydate));
                if ($model->expirydate) {
                    $model->expirydate = date(yii::$app->params['dateFormat']['sqlDateFormat'],strtotimeNew($model->expirydate));
                }
                $model->activatedon = date(yii::$app->params['dateFormat']['sqlDateFormat'],strtotimeNew($model->activatedon));
                $model->batch = ($selectedBatches == 'All') ? null : $selectedBatches;
                if($model->save(false)) {
                    return $this->redirect(['index']); 
                }
            }  
        } 

        $model->activitydate    = ($model->activitydate) ? date(yii::$app->params['dateFormat']['viewDateFormatandT24hr'],strtotimeNew($model->activitydate)) : date(yii::$app->params['dateFormat']['viewDateFormatandT24hr']);
        $model->expirydate      = ($model->expirydate) ? date(yii::$app->params['dateFormat']['viewDateFormat'],strtotimeNew($model->expirydate)) : date(yii::$app->params['dateFormat']['viewDateFormat']);
        $model->activatedon     = ($model->activatedon) ? date(yii::$app->params['dateFormat']['viewDateFormat'],strtotimeNew($model->activatedon)) : date(yii::$app->params['dateFormat']['viewDateFormat']) ;
        return $this->render('create', [
            'model' => $model,
            'members' => $members,
            'familyUnits' => $familyUnits,
            'batches' => $allBatches,
        	'memberModel' => $memberModel
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
        $model = $this->findModel($id);
        $model->scenario = ExtendedEvent::SCENARIO_EVENTS;
        $institutionId = $this->currentUser()->institutionid;
        $timeZone = Yii::$app->user->identity->institution->timezone;
        date_default_timezone_set($timeZone);
        $members = ExtendedMember::getMemberForEventAutoSuggest($institutionId);
        $familyUnits =  ArrayHelper::map(
            ExtendedFamilyunit::find()
            ->where(['institutionid' => $institutionId])->
            andWhere(['active' => 1])
            ->all(), 
            'familyunitid', 'description');
            $allBatches = BaseModel :: getBatches();
        if ($model->load(Yii::$app->request->post())) {
            $eventarray = Yii::$app->request->post();
            $model->iseventpublishable = ($model->expirydate) ? ((strtotimeNew($model->expirydate) < strtotimeNew(date('Y-m-d'))) ? 1 : 0 ): 0;;
            $stripedDesc = strip_tags($model->notebody);
            if(empty($stripedDesc)){
                $model->notebody = null;
            }
            $selectedBatches = !empty($eventarray['ExtendedEvent']['batch'] )? implode($eventarray['ExtendedEvent']['batch'],',') : 'All';
            $model->institutionid = (int)$this->currentUser()->institutionid;
            $model->eventtype = 'E';
            $model->modifiedby = Yii::$app->user->identity->id;
            $model->modifieddatetime = date(yii::$app->params['dateFormat']['sqlDandTFormat']);
            $model->batch = $selectedBatches;
             if($model->validate()) {
                $model->activitydate =  date(yii::$app->params['dateFormat']['sqlDandTFormat'],strtotimeNew($model->activitydate));
                if ($model->expirydate) {
                    $model->expirydate = date(yii::$app->params['dateFormat']['sqlDateFormat'],strtotimeNew($model->expirydate));
                }
                $model->activatedon = date(yii::$app->params['dateFormat']['sqlDateFormat'],strtotimeNew($model->activatedon));
                $model->batch = ($selectedBatches == 'All') ? null : $selectedBatches;
                if($model->save(false)) {
                    return $this->redirect(['index']);
                } elseif(empty($model->getErrors())) {
                    return $this->redirect(['index']);
                }
            }
        }
        $existingBatches = $model->batch;

        $model->activitydate = ($model->activitydate) ? date(yii::$app->params['dateFormat']['viewDateFormatandT24hr'],strtotimeNew($model->activitydate)) : date(yii::$app->params['dateFormat']['viewDateFormatandT24hr']);
        $model->expirydate = ($model->expirydate) ? date(yii::$app->params['dateFormat']['viewDateFormat'],strtotimeNew($model->expirydate)) : null;
        $model->activatedon = ($model->activatedon) ? date(yii::$app->params['dateFormat']['viewDateFormat'],strtotimeNew($model->activatedon)) : date(yii::$app->params['dateFormat']['viewDateFormat']) ;
        return $this->render('update', [
            'model' => $model,
        	'members' => $members,
            'familyUnits' => $familyUnits,
            'batches' => $allBatches,
            'existingBatch' => $existingBatches
        ]);
    }
    public function actionRsvpListing($id)
    {   
        $model = $this->findModel($id);
        $membercount = 0;
        $childrencount = 0;
        $guestcount = 0;
        $searchModel = new ExtendedRsvpDetailSearch();
        $searchModel->id = $id;
        $searchModel->searchParam = 0;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('rsvplisting', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
    public function actionRsvpfilter()
    {   
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (yii::$app->request->isAjax) {
            $data = yii::$app->request->post('data');
            $id = yii::$app->request->post('id');
            $eventModel = new ExtendedRsvpdetails();
            $eventdetails = $eventModel->getRsvpEventFilter($id,$data);
        
             return $this->renderPartial('rsvplisting', [
                'model' => $eventModel,
                'eventdetails' => $eventdetails,
            ]);
        } 
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
        $institutionId = $this->currentUser()->institutionid;
        if (yii::$app->request->isAjax) {
            $id = yii::$app->request->post('id');
            if ($id) {
                $sql1 = 'DELETE FROM `albumimage` WHERE albumid IN ( Select albumid from album where eventid = :eventid )';
                $sql2 = 'DELETE FROM `album` WHERE eventid = :eventid';
                $sql3 = 'DELETE FROM `eventseendetails` WHERE eventid = :eventid';
                $sql7 = 'DELETE FROM `rsvpnotification` WHERE rsvpid  IN (Select id from rsvpdetails where rsvpid = :eventid)';
                $sql8 = 'DELETE FROM `rsvpnotificationsent` WHERE rsvpid  IN (Select id from rsvpdetails where rsvpid = :eventid)';
                $sql9 = 'DELETE FROM `rsvpdetails` WHERE rsvpid = :eventid';
                $sql4 = 'DELETE FROM `events` WHERE id = :eventid';
                $sql5 = 'DELETE from successfulleventsent WHERE notificationid =:eventid AND institutionid= :institutionid';
                $sql6 = 'DELETE FROM `notificationlog` WHERE eventid = :eventid';              
                
                $db = Yii::$app->db;
                    $transaction = $db->beginTransaction();
                try {
                        $db->createCommand($sql5)->bindValue(':eventid', $id)
                            ->bindValue(':institutionid', $institutionId)
                            ->execute();
                        $db->createCommand($sql6)->bindValue(':eventid', $id)->execute();
                        $db->createCommand($sql1)->bindValue(':eventid', $id)->execute();
                        $db->createCommand($sql2)->bindValue(':eventid', $id)->execute();
                        $db->createCommand($sql3)->bindValue(':eventid', $id)->execute();
                        $db->createCommand($sql7)->bindValue(':eventid', $id)->execute();
                        $db->createCommand($sql8)->bindValue(':eventid', $id)->execute();
                        $db->createCommand($sql9)->bindValue(':eventid', $id)->execute();
                        $db->createCommand($sql4)->bindValue(':eventid', $id)->execute();
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
        
        try{
            if (yii::$app->request->isAjax) {
                $id = yii::$app->request->post('id');
                $noteHead = yii::$app->request->post('noteHead');
                $activityDate = yii::$app->request->post('activityDate');
                $venue = yii::$app->request->post('venue');
                $familyUnitId = yii::$app->request->post('familyUnitId')?yii::$app->request->post('familyUnitId'):null;
                $institutionid = $this->currentUser()->institutionid;
                $institutionDetails = Yii::$app->user->identity->institution;
                $institutionName = $institutionDetails['name'];
                $pushNotificationHandler = Yii::$app->PushNotificationHandler;
                $notificationHandler = Yii::$app->NotificationHandler;

        
                
                $isSent = false;
                $message = '';
                if ($id) {
                    $id = (int)$id;
                    $model = $this->findModel($id);
                    
                    if (!empty($model)) {
                        $eventBatch = $model->batch;
                        $model->iseventpublishable = 1; 
                        $model->publishedon = date(yii::$app->params['dateFormat']['sqlDandTFormat']);      
                                
                        $expiresOn = date('d-m-Y', strtotimeNew($model->expirydate));
                        
                        if ($model->update(false)) {
                            if($institutionid){
                                Yii::$app->consoleRunner->run('publish-notification/publish --eventType="E" --eventId='.$id.' --noteHead="'.$noteHead.'" --institutionName="'.$institutionName.'" --institutionId='.$institutionid.' --familyUnitId='.$familyUnitId.' --createdBy='.$createdBy.' --activityDate="'.$activityDate.'" --venue="'.$venue.'" --expiryDate="'.$expiresOn.'" --batch="'.$eventBatch.'"');
                               
                                return [
                                    'status' => 'success',
                                    'data' => 'Event published to all available devices',
                                ];
                            }
                            else{
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
   
    public function actionUpload()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (yii::$app->request->isAjax) {
            if($_FILES['img']['name'] != ''){
                $filename = explode('.', $_FILES['img']['name']);
                $extension = end($filename);    
                $name = rand(100,999).'.'.$extension;
                $path = \Yii::getAlias('@service').'/NewsUploads/'. $this->currentUser()->institutionid.'/';
                $path =  FileHelper::createDirectory($path);
                $location =  \Yii::getAlias('@service').'/NewsUploads/'. $this->currentUser()->institutionid.'/'.$name;
                if( move_uploaded_file($_FILES['img']['tmp_name'], $location)) {
                   $location =  Yii::$app->params['imagepath'].'/NewsUploads/'. $this->currentUser()->institutionid.'/'.$name;
                    return ['hasError' => 0,'url' =>  $location ];
                } {
                    return ['hasError' => 1,'url' =>  '' ];
                }
            } {
                return ['hasError' => 1,'url' =>  '' ];
            }
        }
    }
    public function actionAcknowledgemail()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $timeZone = Yii::$app->user->identity->institution->timezone;
        date_default_timezone_set($timeZone);
        if (yii::$app->request->isAjax) {
            $rsvpId= yii::$app->request->post('rsvpId');
            $eventId = yii::$app->request->post('eventId');
            $memberId = yii::$app->request->post('memberId');
            $userId = yii::$app->request->post('userId');
            $mailobj = new EmailHandlerComponent();

            $institutionId =  Yii::$app->user->identity->institutionid;
            $memberDetails = ExtendedMember::getMemberEmail($userId, $institutionId);
            $userDetails = ExtendedMember::getUserName($userId, $institutionId);
            $eventDetails = ExtendedEvent::getInstitutionEventDetails($eventId, $institutionId);
            $from = Yii::$app->params['clientEmail'];
            $email = $memberDetails['emailid'];
            $title = '';
            $subject = 'RSVP: '.$eventDetails['notehead'];
            $mailContent = [];
            $mailContent['toname'] = $userDetails['firstName'];
            $mailContent['name'] = yii::$app->user->identity->institution->name;
            $mailContent['content'] = yii::$app->request->post('noteBody');
            $mailContent['logo'] = '';
            $mailContent['template'] = 'email-message';
            $attach = '';
            $createdTime = date('Y-m-d H:i:s');
            $temp =  $mailobj->sendEmail($from, $email, $title, $subject, $mailContent,$attach);
            //$this->addRSVPNotificationSent($institutionId, $rsvpId, $createdTime);
            $eventModel = new ExtendedRsvpdetails();
            $eventdetails = $eventModel::find()->where(['id' => $rsvpId])->one();
            $eventdetails->acksentdatetime = $createdTime;  
            if($eventdetails->update(false)) {
                 return $temp;        
            }      
        }   
    }
    /*
    *Add RSVP notification 
    */
    protected function addRSVPNotificationSent($institutionId, $rsvpId, $createdTime){
        $privilegeId = ExtendedPrivilege::MANAGE_EVENT_RSVP;
        $deviceList = ExtendedOrders::getOrderNotificationDeviceList($institutionId, $privilegeId);
        $fields = ['userid','rsvpid','createddatetime'];
        if(!empty($deviceList)){
            $bulkInsert = [];
            foreach ($deviceList as $device)
            {
                $bulkInsert[] = [$rsvpId, $device['userid'], $createdTime];
            }
            Yii::$app->db->createCommand()->batchInsert('rsvpnotificationsent', $fields,$bulkInsert )->execute();
        }
    }
}
