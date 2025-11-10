<?php

namespace backend\controllers;

use Yii;
use common\models\extendedmodels\ExtendedPrayerrequest;
use common\models\extendedmodels\ExtendedPrayerrequestnotificationsent;
use common\models\searchmodels\ExtendedPrayerrequestSearch;
use common\components\EmailHandlerComponent;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
/**
 * PrayerrequestController implements the CRUD actions for ExtendedPrayerrequest model.
 */
class PrayerrequestController extends BaseController
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
        //if manag prayer request privilage is set user has access.
        if (!Yii::$app->user->can('ca4ac940-ec4a-11e6-b48e-000c2990e707')) {
            throw new \yii\web\ForbiddenHttpException('You are not allowed to perform the requested action');
        }
        if(Yii::$app->controller->action->id === 'prayerrequestmail' || 'save-prayer-request-email') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }
   
    /**
     * Lists all ExtendedPrayerrequest models.
     * @return mixed
     */
    public function actionIndex()
    {    
   
        $searchModel = new ExtendedPrayerrequestSearch();
        $searchModel->created_time_start = date('d M Y');
        $searchModel->created_time_end = date('d M Y');
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $getmail = ExtendedPrayerrequest::getPrayerEmail($this->currentUser()->institutionid);
        return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'getmail' => $getmail,
            ]);
    }
    
   

    /**
     * Finds the ExtendedPrayerrequest model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ExtendedPrayerrequest the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ExtendedPrayerrequest::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
    public function actionPrayerrequestmail()
    {   

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (yii::$app->request->isAjax) {
            $user = yii::$app->user->identity;
            $institution = $user->institution;
            $mailobj = new EmailHandlerComponent();
            $from = $user->emailid;
            $email =yii::$app->request->post('emailid');

            $id = yii::$app->request->post('prid');
            $userid = yii::$app->request->post('userid');

            if($institution->institutionlogo){
                $institutionLogo = Yii::$app->params['imagePath'].$institution->institutionlogo;
            } else {
                $institutionLogo = Yii::$app->params['imagePath'].'/institution/institution-icon-grey.png';
            }

            $toemail =$email;
            $title ='';
            $subject ='Prayer Request Acknowledgement';
            $mailContent['name'] = 'Admin';
            $mailContent['content'] = yii::$app->request->post('data');
            $mailContent['template'] = 'prayer-request';
            $mailContent['logo'] = $institutionLogo;
            $mailContent['institutionname'] = $institution->name;
            $attach = '';
            $mailSent =  $mailobj->sendEmail($from,$toemail,$title,$subject,$mailContent,$attach);
            if ($mailSent) {
                $insertnotification =  new ExtendedPrayerrequestnotificationsent();
                $insertnotification->prayerrequestid = $id ;
                $insertnotification->userid = $userid;
                $insertnotification->createddatetime = date(yii::$app->params['dateFormat']['sqlDandTFormat']); 
                if($insertnotification->save(false)){
                    $prayerrequestdetails = ExtendedPrayerrequest::findOne($id);
                    if($prayerrequestdetails){
                        $prayerrequestdetails->isresponded = 1;
                        if($prayerrequestdetails->update(false)) {
                            return ['status' => 'success','data' => null];
                        } elseif(empty($prayerrequestdetails->getErrors())) {
                            return ['status' => 'success','data' => null];
                        } else {
                            yii::error(var_export($prayerrequestdetails->getErrors(),true));
                            return ['status' => 'error','data' => null]; 
                        }
                    } else {
                       return ['status' => 'error','data' => null]; 
                    }
                } else {
                    yii::error(var_export($insertnotification->getErrors(),true));
                    return ['status' => 'error','data' => null];
                }        
            } else {
                yii::error('Error while sending mail');
                return ['status' => 'error','data' => null];
            } 
        } 
               
    }   
    public function actionSavePrayerRequestEmail()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (yii::$app->request->isAjax) {
           $mail = yii::$app->request->post('data');
           $savemail =  ExtendedPrayerrequest::savePrayerEmail($this->currentUser()->institutionid,$mail);
           if($savemail){
             return true;
           }
        }
    }
}
