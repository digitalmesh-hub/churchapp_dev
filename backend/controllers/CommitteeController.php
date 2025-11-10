<?php

namespace backend\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use Exception;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use common\models\extendedmodels\ExtendedMember;
use common\models\extendedmodels\ExtendedCommittee;
use common\models\extendedmodels\ExtendedDesignation;
use common\models\extendedmodels\ExtendedCommitteegroup;
use common\models\extendedmodels\ExtendedCommitteePeriod;


/**
 * CommitteeController implements the CRUD actions for ExtendedCommitteegroup model.
 */
class CommitteeController extends BaseController
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
        //if Manage committe privilage is set user has access.
        if (!Yii::$app->user->can('b46fb1de-ec46-11e6-b48e-000c2990e707')) {
            throw new \yii\web\ForbiddenHttpException('You are not allowed to perform the requested action');
        }
        return parent::beforeAction($action);
    }
    /**
     * Lists all ExtendedCommitteegroup models.
     * @return mixed
     */
    public function actionIndex()
    {  
        try{
            $typeModel = new ExtendedCommitteegroup();
            $designationModel = new ExtendedDesignation();
            $periodModel = new ExtendedCommitteePeriod();
            $institutionid = $this->currentUser()->institutionid;
            //Committe Type
            $query = ExtendedCommitteegroup::find()
                        ->where(['institutionid' => $institutionid]);
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => false,
                'sort' => [
                    'defaultOrder' => [
                    'order' => SORT_ASC, 
                    ]
                ],
            ]);
            $typeCount = $dataProvider->getTotalCount();

            //Committee Designation
            $query = ExtendedDesignation::find()
                        ->where(['institutionid' => $institutionid]);
            $designationProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => false,
                'sort' => [
                    'defaultOrder' => [
                    'designationorder' => SORT_ASC, 
                    ]
                ],
            ]);
            $designationCount = $designationProvider->getTotalCount();

            //Committee type
            $committeTypeList = ArrayHelper::map(
                ExtendedCommitteegroup::find()
                ->where(['institutionid' => $institutionid, 'active' =>1])
                ->orderBy('order')
                ->all(),'committeegroupid','description');
            //committe period
            $periodModel = new ExtendedCommitteePeriod();
            $institutionid = $this->currentUser()->institutionid;
            $periodResult = $periodModel->getAllCommitteePeriod($institutionid);

            //Sort array by description
            ArrayHelper::multisort($periodResult,['description','period_from'],[SORT_ASC,SORT_ASC]);

            //Committee Member
            $memberModel = new \yii\base\DynamicModel(['committeeType', 'committeePeriod']);

            //Find Member
            $memberSearch = new \yii\base\DynamicModel(['memberName', 'isSpouse']);

            $memberSearch->addRule(['memberName'], 'string', ['max' => 128])
                         ->addRule(['memberName'], 'required')
                         ->validate();

            //Find Member deatails
            $memberObject = new ExtendedMember();
            $memberName = null;
            $isSpouse = 'm';
            $memberId = null;

            //get committee members
            $committeMemberList = $memberObject->getCommitteeMemberDetailsForAutoComplete(
                $memberName, $isSpouse, $institutionid, $memberId);
           
            return $this->render('index', [
                'dataProvider' => $dataProvider,
                'model' => $typeModel,
                'count' => $typeCount,
                'designationProvider' => $designationProvider,
                'designationCount' => $designationCount,
                'designationModel' => $designationModel,
                'periodModel' => $periodModel,
                'committeTypeList' => $committeTypeList,
                'periodResult' => $periodResult,
                'memberModel' => $memberModel,
                'memberSearch' => $memberSearch,
                'committeMemberList' => $committeMemberList
            ]);
        } catch(Exception $e) {
           yii::error($e->getMessage());
        }
    }


    /**
     * Save committee type
     * If creation is successful, the browser will be redirected to the 'Committee Type tab' page.
     * @return mixed
     */
    public function actionSaveCommitteeType()
    {
        $model = new ExtendedCommitteegroup();
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        try{
            //Save committee type
            if (yii::$app->request->isAjax) {
                $model->committeegroupid = yii::$app->request->post('committeeGroupId');
                $model->description = yii::$app->request->post('description');
                if($model->validate()){
                    $model->institutionid = $this->currentUser()->institutionid;
                    $committeeGroupId = (int)$model->committeegroupid;
                    if($committeeGroupId == 0 || $committeeGroupId == null){
                        $status = $model->addCommitteeType($model);
                    }
                    else{
                        $status = $model->updateCommitteeType($model);
                    }
                    if($status){
                        return ['status' => 'success','data' => null];
                    }
                    else{
                        return ['status' => 'error','data' => null];
                    }
                }
                else{
                    return ['status' => 'error','data' => $model->getErrors()];
                }
            }
        }
        catch(Exception $e){
            yii::error($e->getMessage());
            return ['status' => 'error','data' => null];
        }
    }

    /**
     * Activate / Deactivate committee type.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @return ExtendedCommitteegroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionActivateDeactivateCommitteeType(){

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try{
            if (yii::$app->request->isAjax) {
                $committeeGroupId = yii::$app->request->post('committeeGroupId');
                $active = yii::$app->request->post('active');
                if ($committeeGroupId) {
                    $committeeGroupId = (int)$committeeGroupId;
                    if(($model = ExtendedCommitteegroup::findOne($committeeGroupId)) !== null)
                    {
                        $model->active = $active;
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
        catch(Exception $e){
            yii::error($e->getMessage());
            return ['status' => 'error','data' => null];
        }
    }

    /**
     * Get all committee type.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @return ExtendedCommitteegroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionGetCommitteeType(){

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        //Get all committee types

        try{
            $model = new ExtendedCommitteegroup();
            $institutionid = $this->currentUser()->institutionid;
            $query = ExtendedCommitteegroup::find()
                        ->where(['institutionid' => $institutionid]);

            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => false,
                'sort' => [
                    'defaultOrder' => [
                    'order' => SORT_ASC, 
                    ]
                ],
            ]);
            $count = $dataProvider->getTotalCount();

            if (yii::$app->request->isAjax) {
                $html =  $this->renderPartial('_committeetype',
                            ['dataProvider' => $dataProvider,
                            'model' => $model, 'count' => $count]);
                return ['status' => 'success','data' => $html];
            }
            else{
                return ['status' => 'error','data' => ''];
            }
        }
        catch(Exception $e){
            yii::error($e->getMessage());
            return ['status' => 'error','data' => ''];
        }
    }

    /**
     * Update committee type order.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @return ExtendedCommitteegroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdateCommitteeTypeOrder(){

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = new ExtendedCommitteegroup();

        try{
            if (yii::$app->request->isAjax) {
                $currentValue = yii::$app->request->post('currentValue');
                $oldValue = yii::$app->request->post('oldValue');
                if ($currentValue && $oldValue) {
                    $groupId = (int)$currentValue['groupId'];
                    $order = (int)$currentValue['order'];
                    $currentStatus = $model->updateCommitteeTypeOrder($groupId, $order);
                    $oldGroupId = (int)$oldValue['oldGroupId'];
                    $oldOrder = (int)$oldValue['oldOrder'];
                    $oldStatus = $model->updateCommitteeTypeOrder($oldGroupId, $oldOrder);
                    if($currentStatus && $oldStatus){
                        return ['status' => 'success','data' => $currentStatus,
                        'data1' => $oldStatus];
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
        catch(Exception $e){
           yii::error($e->getMessage());
           return ['status' => 'error','data' => null]; 
        }
    }

    /**
     * Save Committee designation
     * If creation is successful, the browser will be redirected to the 'Committee Type tab' page.
     * @return mixed
     */
    public function actionSaveCommitteeDesignation()
    {
        $model = new ExtendedDesignation();
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        //Save committee type
        try{
            if (yii::$app->request->isAjax) {
                $model->designationid = yii::$app->request->post('designationId');
                $model->description = yii::$app->request->post('designation');
                $model->institutionid = $this->currentUser()->institutionid;
                if($model->validate()){
                    $designationid = (int)$model->designationid;
                    if($designationid == 0 || $designationid == null){
                        $status = $model->addCommitteeDesignation($model);
                    } else {
                        $status = $model->updateCommitteeDesignation($model);
                    }
                    if($status){
                        return ['status' => 'success','data' => null];
                    }else {
                        return ['status' => 'error','data' => null];
                    }
                } else {
                    return ['status' => 'error','data' => $model->getErrors()];
                }
            }
        }
        catch(Exception $e){
            yii::error($e->getMessage());
            return ['status' => 'error','data' => $e];
        }
    }

    /**
     * Get all committee designation.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @return ExtendedCommitteegroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionGetCommitteeDesignation()
    {

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        //Get all committee types
        try{
            $designationModel = new ExtendedDesignation();
            $institutionid = $this->currentUser()->institutionid;
            $query = ExtendedDesignation::find()
                        ->where(['institutionid' => $institutionid]);

            $designationProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => false,
                'sort' => [
                    'defaultOrder' => [
                    'designationorder' => SORT_ASC, 
                    ]
                ],
            ]);
            $designationCount = $designationProvider->getTotalCount();

            if (yii::$app->request->isAjax) {
                $html =  $this->renderAjax('_designation',
                            ['designationProvider' => $designationProvider,
                            'designationModel' => $designationModel, 
                            'designationCount' => $designationCount]);
                return ['status' => 'success','data' => $html];
            } else{
                return ['status' => 'error','data' => ''];
            }
        } catch(Exception $e){
            yii::error($e->getMessage());
            return ['status' => 'error','data' => ''];
        }
    }

    /**
     * Update committee type order.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @return ExtendedCommitteegroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdateCommitteeDesignationOrder()
    {

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = new ExtendedDesignation();
        try{
            if (yii::$app->request->isAjax) {
                $currentValue = yii::$app->request->post('currentValue');
                $oldValue = yii::$app->request->post('oldValue');
                if ($currentValue && $oldValue) {
                    $designationId = (int)$currentValue['designationId'];
                    $order = (int)$currentValue['order'];
                    $currentStatus = $model->updateCommitteeDesignationOrder($designationId, $order);
                    $oldDesignationId = (int)$oldValue['oldDesignationId'];
                    $oldOrder = (int)$oldValue['oldOrder'];
                    $oldStatus = $model->updateCommitteeDesignationOrder($oldDesignationId, $oldOrder);
                    if($currentStatus && $oldStatus){
                        return ['status' => 'success', 'data' => null];
                    } else {
                        return ['status' => 'error','data' => null];
                    }
                } else {
                    return ['status' => 'error','data' => null];
                }
            } else {
                return ['status' => 'error','data' => null];
            }
        } catch(Exception $e){
            yii::error($e->getMessage());
            return ['status' => 'error','data' => $e];
        }
    }

    /**
     * Save Committee period
     * If creation is successful, the browser will be redirected to the 'Committee Type tab' page.
     * @return mixed
     */
    public function actionSaveCommitteePeriod()
    {
       
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        //Save committee type
        try{
            if (yii::$app->request->isAjax) {
                $periodModel = new ExtendedCommitteePeriod();
                $periodModel->committee_period_id = yii::$app->request->post('committeePeriodId');
                $periodModel->committeegroupid = yii::$app->request->post('committeeType');
                $periodFrom = yii::$app->request->post('periodFrom');
                $periodTo = yii::$app->request->post('periodTo');
                $periodModel->institutionid = $this->currentUser()->institutionid;
                $periodModel->createddatetime = date('Y-m-d H:i:s');
                $periodModel->period_from = date_format(date_create($periodFrom),Yii::$app->params['dateFormat']['sqlDateFormat']);
                $periodModel->period_to = date_format(date_create($periodTo),Yii::$app->params['dateFormat']['sqlDateFormat']);
                $periodModel->active = 1;

                if($periodModel->validate()){
                    $periodId = (int)$periodModel->committee_period_id;
                    if($periodId == 0 || $periodId == null){
                        $isValid = $periodModel->checkCommitteePeriodExist($periodModel, false);
                        if($isValid && $periodModel->save()) {
                            return ['status' => 'success','data' => null];
                        } else {
                            return ['status' => 'invalid','data' => 'Committee Period already exist.'];
                        }
                    } else {
                        $model = ExtendedCommitteePeriod::findOne($periodId);
                        $periods = $periodModel->checkCommitteePeriodExist($periodModel, true);
                         if($periods){
                            $model->period_from = $periodModel->period_from;
                            $model->period_to = $periodModel->period_to;
                            $model->createddatetime = $periodModel->createddatetime;
                            $model->save();
                            return ['status' => 'success','data' => null];
                        } else {
                            return ['status' => 'invalid','data' => 'Committee Period already exist.'];
                        }  
                    }
                } else {
                    return [
                        'status' => 'error',
                        'data' => $periodModel->getErrors()
                    ];
                }
            }
        } catch(Exception $e){
            yii::error($e->getMessage());
            return ['status' => 'error','data' => $e,"msg"=>$e->getMessage()];
        }
    }

     /**
     * Get all committee periods.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @return ExtendedCommitteegroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionGetCommitteePeriod()
    {

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        //Get all committee periods
        try{
            if (yii::$app->request->isAjax) {
                $periodModel = new ExtendedCommitteePeriod();
                $institutionid = $this->currentUser()->institutionid;
                $periodResult = $periodModel->getAllCommitteePeriod($institutionid);
                //Sort array by description
                ArrayHelper::multisort($periodResult,['description','period_from'],[SORT_ASC,SORT_ASC]);
                $committeTypeList = ArrayHelper::map(
                ExtendedCommitteegroup::find()
                ->where(['institutionid' => $institutionid, 'active' =>1])
                ->orderBy('order')
                ->all(),'committeegroupid','description');
                $html =  $this->renderAjax('_partialPeriodData',
                            ['periodResult' => $periodResult]);
                return ['status' => 'success','data' => $html];
            } else {
                return ['status' => 'error','data' => ''];
            }
        } catch(Exception $e) {
            yii::error($e->getMessage());
            return ['status' => 'error','data' => ''];
        }
    }

     /**
     * Activate / Deactivate committee period.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @return ExtendedCommitteegroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionActivateDeactivateCommitteePeriod()
    {

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try{
            if (yii::$app->request->isAjax) {
                $committeePeriodId = yii::$app->request->post('committeePeriodId');
                $active = yii::$app->request->post('active');
                if ($committeePeriodId) {
                    $committeePeriodId = (int)$committeePeriodId;
                    if(($model = ExtendedCommitteePeriod::findOne($committeePeriodId)) !== null) {
                        $model->active = $active;
                        $model->save();
                        return ['status' => 'success','data' => null];
                    } else {
                        return ['status' => 'error','data' => null];
                    }
                } else {
                    return ['status' => 'error','data' => null];
                }
            } else {
                return ['status' => 'error','data' => null];
            }
        } catch(Exception $e){
            yii::error($e->getMessage());
            return ['status' => 'error','data' => null];
        }
    }

    /**
     * Get all committee period by committee type.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @return ExtendedCommitteegroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionGetCommitteePeriodByType()
    {

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        //Get all committee types
        try{
            $model = new ExtendedCommitteePeriod();
            $institutionid = $this->currentUser()->institutionid;
            if (yii::$app->request->isAjax) {
                $committeeType = yii::$app->request->get('committeeType');
                 $committePeriodList = ArrayHelper::map(
                    ExtendedCommitteePeriod::find()
                    ->where(['institutionid' => $institutionid])
                    ->andWhere(['committeegroupid' => $committeeType])
                    ->andWhere(['active' => 1])
                    ->orderBy('period_from')
                    ->all(),'committee_period_id',
                    function($model) {
                        return date('d F Y',strtotimeNew($model['period_from'])).' - '.date('d F Y',strtotimeNew($model['period_to']));
                    }
                );
                return ['status' => 'success','data' => $committePeriodList];
            } else {
                return ['status' => 'error','data' => ''];
            }
        }catch(Exception $e){
            yii::error($e->getMessage());
            return ['status' => 'error','data' => ''];
        }
    }

    /**
     * Get committee members.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @return ExtendedCommitteegroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionGetCommitteeMembers()
    {
        $date = null;
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        try{
            if (yii::$app->request->isAjax) {
                $institutionId = $this->currentUser()->institutionid;
                $periodModel = new 
                    ExtendedCommitteePeriod();
                $groupModel = new ExtendedCommitteegroup();
                $committeeType = yii::$app->request->get('committeeType');
                $committeePeriod = yii::$app->request->get('committeePeriod');
                if($committeeType == "false" || $committeePeriod == "false"){
                    $committeeType = null;
                    $committeePeriod = null;
                    $date = gmdate('Y-m-d');
                }
                $committeeMemberDetails = $periodModel->getCommitteeMembers($committeeType, $committeePeriod, $institutionId, $date);
                $committeePeriodList = $periodModel->getCommitteePeriodList($institutionId, $committeeType);
                $committeeGroupList = $groupModel->getCommitteeGroupList($institutionId);
                $html = $this->renderAjax('_committeemembers',
                            [
                                'committeeMemberDetails' => $committeeMemberDetails,
                                'periodList' => $committeePeriodList,
                                'committeeGroupList' => $committeeGroupList
                            ]);

                return ['status' => 'success', 'data' => $html];
            } else {
                return ['status' => 'error','data' => ''];
            }
        } catch(Exception $e) {
            yii::error($e->getMessage());
            return ['status' => 'error','data' => $e->getMessage()];
        }
    }

    /**
     * Get committee members name.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @return ExtendedCommitteegroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionGetCommitteeMemberDetails()
    {

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        try{
            if (yii::$app->request->isAjax) {
                $institutionId = $this->currentUser()->institutionid;
                $memberModel = new ExtendedMember();
                $memberName = yii::$app->request->get('memberName');
                $isSpouse = yii::$app->request->get('isSpouse');
                $isSpouse = strtolower($isSpouse) == 'true' ? 's' : 'm';
                $memberId = yii::$app->request->get('memberId');
               /* if($memberName){
                    $memberName = preg_replace('!\s+!', ' ', $memberName);
                }*/
                //get committee members
                $committeMemberDetails = $memberModel->getCommitteeMemberDetails(
                    $memberName, $isSpouse, $institutionId, $memberId);
                //committe add
                $committeeModel = new \yii\base\DynamicModel(['committeeType', 'designationType','periodType']);
                $query = ExtendedDesignation::find()
                        ->where(['institutionid' => $institutionId]);         
                $committeDesignationList = ArrayHelper::map(
                    ExtendedDesignation::find()
                            ->where(['institutionid' => $institutionId])
                    ->orderBy('designationorder')
                    ->all(),'designationid','description');

                $committeTypeList = ArrayHelper::map(
                    ExtendedCommitteegroup::find()
                    ->where(['institutionid' => $institutionId, 'active' =>1])
                    ->orderBy('order')
                    ->all(),'committeegroupid','description');

                $html = $this->renderPartial('_addmember',
                            ['committeMemberDetails' => $committeMemberDetails, 
                            'committeeModel' => $committeeModel,
                            'committeDesignationList' => $committeDesignationList,
                            'committeTypeList' => $committeTypeList]);
                return ['status' => 'success','data' => $html];
            } else {
                return ['status' => 'error','data' => ''];
            }
        } catch(Exception $e){
            yii::error($e->getMessage());
            return ['status' => 'error','data' => $e->getMessage().$$e->getLine()];
        }
    }

    /**
     * Save committee member.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @return ExtendedCommittee the loaded model
     * @throws NotFoundHttpException if the model cannot be found
    */
    public function actionSaveCommitteeMember()
    {

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        try
        {            
            if (yii::$app->request->isAjax) {
                $model = new ExtendedCommittee();
                $createdBy = $this->currentUserId();
                $committeeType = 
                    yii::$app->request->post('committeeType');
                $designationId = 
                    yii::$app->request->post('designationId');
                $institutionId = 
                    yii::$app->request->post('institutionId');
                $memberId = yii::$app->request->post('memberId');
                $userId = yii::$app->request->post('userId');
                $isSpouse = yii::$app->request->post('isSpouse');
                $committeeGroupId = 
                    yii::$app->request->post('committeeGroupId');
                $committeePeriodId = 
                    yii::$app->request->post('committeePeriodId');
                $createdDatetime = gmdate(Yii::$app->params['dateFormat']['sqlDandTFormat']);
                $committeeCount = $model->checkMemberAvailbaleInCommittee($committeePeriodId, $designationId, $memberId, $committeeGroupId);
                if ((int)$committeeCount == 0) {
                    if ($userId != null || $userId != 0) {
                        $status = $model->saveCommitteeMember($userId, 
                            $designationId, $memberId, $institutionId,
                            $isSpouse, $createdDatetime, 
                            $createdBy, $committeeGroupId, 
                            $committeePeriodId);
                        if($status == true){
                            return [
                                'status' => 'success', 'data' => ''
                            ];
                        } else {
                            return [
                                'status' => 'message', 'data' => 'An error occured'
                            ];
                        } 
                    } else {
                        return [
                            'status' => 'message', 'data' => 'Invalid member details'
                        ];
                    }
                } else {
                     return [
                        'status' => 'message',
                        'data' => 'Member already exists as committee member'
                    ];
                }
            } else {
                return ['status' => 'error','data' => ''];
            }
        } catch(Exception $e){
            yii::error($e->getMessage());
            return ['status' => 'error','data' => $e->getMessage(),'line' =>$e->getLine()];
        }
    }

    /**
     * Delete committee member .
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @return ExtendedCommitteegroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDeleteCommitteeMember()
    {

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        try{
            if (yii::$app->request->isAjax) {
                $committeeId = yii::$app->request->post('committeeId');
                if ($committeeId) {
                    $committeeId = (int)$committeeId;

                    Yii::$app->db->createCommand('DELETE FROM committee
                        WHERE committeeid= :committeeid')
                    ->bindValue(':committeeid' , $committeeId)
                    ->execute();
                    
                    return ['status' => 'success','data' => null];
                }
                else{
                    return ['status' => 'error','data' => null];
                }
            }
            else{
                return ['status' => 'error','data' => null];
            }
        } catch(Exception $e){
            yii::error($e->getMessage());
            return ['status' => 'error','data' => null];
        }
    }

    /**
     * Delete committee member .
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @return ExtendedCommitteegroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionGetMemberForSearch()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (yii::$app->request->isAjax) {
            
            $memberName = yii::$app->request->post('memberName');
            $isSpouse = yii::$app->request->post('isSpouse');
            $memberObject = new ExtendedMember();
            $membername = (trim($memberName) == '') ? null : $memberName;  
            $isSpouse = ($isSpouse == 'true') ? 's' : 'm';
            $memberId = null;
            $institutionid = $this->currentUser()->institutionid;

            //get committee members
             $committeMemberList = $memberObject->getCommitteeMemberDetailsForAutoComplete(
                $memberName, $isSpouse, $institutionid, $memberId);

            //Find Member
            //$memberSearch = new \yii\base\DynamicModel(['memberName', 'isSpouse']);
            /*$html = $this->renderAjax('_members', [
                    'memberSearch' => $memberSearch,
                    'committeMemberList' => $committeMemberList
            ]);*/
                return ['status' => 'success', 'wife' => $isSpouse, 'list'=> $committeMemberList];
        } else {
                return ['status' => 'error','data' => null];
        }
    }

    /**
     * Get all committee types.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @return ExtendedCommitteegroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionGetCommitteeTypeList()
    {

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        //Get all committee periods
        try{
            if (yii::$app->request->isAjax) {
                //Committee type
                $institutionid = $this->currentUser()->institutionid;
                $committeTypeList = ArrayHelper::map(
                    ExtendedCommitteegroup::find()
                    ->where(['institutionid' => $institutionid, 'active' =>1])
                    ->orderBy('order')
                    ->all(),'committeegroupid','description');
                $return = '<option value ="">Please Select</option>';
                if(!empty($committeTypeList)){
                    foreach ($committeTypeList as $key => $value) {
                     $return .= '<option value='.$key.'>'.$value.'</option>';
                    }  
                } 
                return ['status' => 'success','data' => $return];
            }
            else{
                return ['status' => 'error','data' => ''];
            }
        }
        catch(Exception $e){
            yii::error($e->getMessage());
            return ['status' => 'error','data' => ''];
        }
    }
}