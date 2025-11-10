<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use backend\controllers\BaseController;
use common\models\extendedmodels\ExtendedSurvey;
use common\models\extendedmodels\ExtendedSurveystatus;
use common\models\extendedmodels\ExtendedSurveyCredentials;
use yii\helpers\Url;
use yii\base\ActionEvent;

/**
 * SurveyController implements the CRUD actions for ExtendedSurvey *model.
*/

class SurveyController extends BaseController
{   
    public $statusCode;
    public $message = "";
    public $data;
    /**
    * @inheritdoc
    */
    function beforeAction($action)
    {   
        //if survey privilage is set to user,the will have access over the control.
        $this->on(self::EVENT_BEFORE_ACTION,function(ActionEvent $event)
        {
           if (in_array($event->action->id,['index', 'add-survey'])) {
                if (!Yii::$app->user->can('3db740af-1515-11e7-b48e-000c2990e707')) {
                    throw new \yii\web\ForbiddenHttpException('You are not allowed to perform the requested action');
                }       
           }
        });
        return parent::beforeAction($action);
    }

    /**
     * Get survey page
     * @return mixed
    */
    public function actionIndex()
    {
        try
        {
            $surveyCredentialModel = new ExtendedSurveyCredentials();
            $institutionId = $this->currentUser()->institutionid;
            $credentials = $surveyCredentialModel->getSurveyCredentials($institutionId);
            if($credentials){
                $token = $credentials['username'].'{SEPARATE}'. $credentials['password'];
                $token = base64_encode($token);
                $url = yii::$app->params['surveyURL'].$token;
                $headers = get_headers($url);
                if(trim($headers[0]) == "HTTP/1.1 200 OK" || trim($headers[0]) == "HTTP/1.1 302 Found")
                {
                	header("Cache-Control: no-cache");
                	header("Pragma: no-cache");
                	$temp = header("Location:$url");
                } else {
                	$this->sessionAddFlashArray('error', 'Incorrect Username / Password!', true);
                	return $this->redirect(['account/home']);
                }
                exit;
            } else {
                return $this->redirect(['account/home']);
            }
        }catch(\Exception $e){
            yii::error($e->getMessage());
        }
    }
    /**
     * Add survey details
     * @return mixed
    */
    public function actionAddSurvey()
    {
        try
        {
            if(yii::$app->request->isGet) {
                $surveyId = yii::$app->request->get('surveyId');
                $title = yii::$app->request->get('title');
                $status = (int)yii::$app->request->get('status');
                $institutionId = $this->currentUser()->institutionid;
                $userId = $this->currentUserId();
                if (($model = ExtendedSurvey::findOne($surveyId)) 
                    !== null) { 
                    $model->description = $title;
                    $model->institutionid = $institutionId;
                    $model->active = $status == 1 ? '1' : '0';
                    $model->modifiedby = $userId;
                    $model->modifieddatetime = gmdate("Y-m-d H:i:s");
                    $model->save();
                    $this->sessionAddFlashArray('success', 'Survey details saved successfully!', true);
                } else {
                    $model = new ExtendedSurvey();
                    $model->surveyid = $surveyId;
                    $model->description = $title;
                    $model->institutionid = $institutionId;
                    $model->active = $status == 1 ? '1' : '0';
                    $model->createdby = $userId;
                    $model->createddatetime = gmdate("Y-m-d H:i:s");
                    $model->save();
                    $this->sessionAddFlashArray('success', 'Survey details saved successfully!', true);
                    if($model->getErrors()){
                        yii::error($e->getMessage());
                    }
                }
            }
            return $this->redirect(['account/home']);
        } catch(\Exception $e) {
            yii::error($e->getMessage());
        }
    }
     /**
     * Update survey status
     * @return mixed
    */
    public function actionUpdateSurveyStatus()
    {  
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if(yii::$app->request->isGet) {
            $token = yii::$app->request->get('token');
            if($token) {
                yii::error($token);
                $model = ExtendedSurveystatus::findOne(['token' => $token]);
                if($model){
                    $model->isattended = 1;
                } else {
                    $this->statusCode = 500;
                    $this->message = "An error occurred while processing the request";
                    $this->data = new \stdClass();
                }
                if ($model->save()) {
                    $this->statusCode = 200;
                    $this->message = "Survey Status Updated Successfully";
                    $this->data = new \stdClass();
                } else {
                    yii::error(print_r($model->getErrors(),true));
                    $this->statusCode = 500;
                    $this->message = "An error occurred while processing the request";
                    $this->data = new \stdClass();
                }
            } else {
                $this->statusCode = 500;
                $this->message = "An error occurred while processing the request";
                $this->data = new \stdClass();
            }
            return ['statusCode' => $this->statusCode, 'message' => $this->message, 'data' => $this->data];
        }
    }
}