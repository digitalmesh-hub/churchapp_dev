<?php 

namespace backend\controllers;

use yii;
use backend\controllers\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\extendedmodels\ExtendedInstitution;
use common\models\extendedmodels\ExtendedSurveyCredentials;
use common\models\searchmodels\ExtendedInstitutionSearch;
use common\models\extendedmodels\ExtendedDashboard;
use common\models\extendedmodels\ExtendedInstitutiondashboard;

/**
* 
*/
class InstitutionController extends BaseController
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
                    'create-institution',
                    'create-survey-credentials',
                    'list-institution',
                    'edit',
                    'deactivate',
                    'activate',
                    'create-dashboard-item',
                    'edit-institution'
                ],
                'rules' => [
                    [
                        'actions' => [
                            'create-institution',
                            'create-survey-credentials',
                            'list-institution',
                            'edit',
                            'deactivate',
                            'activate',
                            'create-dashboard-item'   
                        ],
                        'allow' => true,
                        'roles' => ['superadmin']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['edit-institution'],
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                                return Yii::$app->checkAdminGroup->checkAdminGroupAccess($this->currentUserId());
                        },       
                    ],
                    [
                        'allow' => true,
                        'actions' => ['get-auto-country-code'],
                        'roles' => ['?'],
                        
                    ]
                ],
            ],
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
    
    public function actions()
    {
        return
            [
            'error' => [
            'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionCreateInstitution()
    {  
      
        $formModel = new ExtendedInstitution();
        $surveyFormModel = new ExtendedSurveyCredentials();
        if (Yii::$app->request->isPost) {
            $institutionData = Yii::$app->request->post();
            $social_media = [
            'facebook' => $institutionData['ExtendedInstitution']['facebook'] ?? null,
            'instagram' => $institutionData['ExtendedInstitution']['instagram'] ?? null,
            'twitter' => $institutionData['ExtendedInstitution']['twitter'] ?? null,
            'youtube' => $institutionData['ExtendedInstitution']['youtube'] ?? null,
            ];
            $institutionData['ExtendedInstitution']['social_media'] = $social_media;  
            if ($formModel->load($institutionData)) {
                if ($formModel->validate()) {
                    $response = $formModel->createInstitution();
                    if (!$response['success']) {
                        $this->sessionAddFlashArray('error', $response['errors'], true);
                    } else {
                        $this->sessionAddFlashArray('success', 'Successfully saved institution details!', true);
                        return $this->redirect(['list-institution']);
                    }
                } else {
                     $this->sessionAddFlashArray('error', $formModel->getErrors(), true);
                }
                return $this->redirect('create-institution');
            }

        }
        return $this->render(
            'create_form',
            [
                'formModel' => $formModel,
                'surveyFormModel' => $surveyFormModel
            ]
        );
    }
    public function actionCreateSurveyCredentials()
    {   
        if (Yii::$app->request->isPost) {
             $data = Yii::$app->request->post();
             $institutionId = $data['ExtendedSurveyCredentials']['institutionid']; 
             $surveyFormModel = $this->findSurveyCredentialModel($institutionId);
            if ($surveyFormModel->load($data)) {
                $surveyFormModel->createdby = yii::$app->user->Identity->id;
                $surveyFormModel->createddatetime = date('Y-m-d H:i:s');
                $response = $surveyFormModel->createSurveyCredentials();
                if (!$response['success']) {
                    $this->sessionAddFlashArray('error', $response['errors'], true);
                } else {
                    $this->sessionAddFlashArray('success', 'Successfully updated survey credential', true);
                }
               
            }
        }
        return $this->redirect(['edit', 'id'=> $surveyFormModel->institutionid]);
    }
    public function actionListInstitution()
    {
        $searchModel = new ExtendedInstitutionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render(
            'index',
            [
            'dataProvider' => $dataProvider,
            'model' => $searchModel
            ]
        );
    }
    public function actionEdit($id)
    {  

        $model = $this->findModel($id);
        $dashboardItem = $this->findDashboardModel($model->id);
        $surveyFormModel = $this->findSurveyCredentialModel($model->id);
        $surveyFormModel->institutionid = $model->id;
        $oldImage = $model->institutionlogo;
        $institutionData = Yii::$app->request->post();
        $model->social_media = [
            'facebook' => $institutionData['ExtendedInstitution']['facebook'] ?? null,
            'instagram' => $institutionData['ExtendedInstitution']['instagram'] ?? null,
            'twitter' => $institutionData['ExtendedInstitution']['twitter'] ?? null,
            'youtube' => $institutionData['ExtendedInstitution']['youtube'] ?? null,
        ];
        if ($model->load($institutionData)) {
            if ($model->validate()) {
                     $response = $model->updateInstitution($oldImage);
                if (!$response['success']) {
                    $this->sessionAddFlashArray('error', $response['errors'], true);
                } else {
                    $this->sessionAddFlashArray('success', 'Successfully updated institution details', true);
                    return $this->redirect(['list-institution']);
                }
            } else {
                $this->sessionAddFlashArray('error', $model->getErrors(), true);
            }
            return $this->redirect(['edit', 'id' => $model->id]);
        } else {
            return $this->render(
                'update_form',
                [
                'formModel' => $model,
                'surveyFormModel' => $surveyFormModel,
                'dashboardItem' => $dashboardItem
                ]
            );
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
                     $model->modifieduser = yii::$app->user->identity->id;
                     $model->modifieddate = date('Y-m-d H:i:s');
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
                     $model->modifieduser = yii::$app->user->identity->id;
                     $model->modifieddate = date('Y-m-d H:i:s');
                    if ($model->update()) {
                            return [
                                'status' => 'success',
                                'data' => null,
                            ];
                    } else {
                        yii::error($model->getErrors());
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
    protected function findModel($id)
    { 
        if (($model = ExtendedInstitution::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    protected function findSurveyCredentialModel($id)
    {
        if (($model = ExtendedSurveyCredentials::findOne(['institutionid' => $id])) !== null) {
            return $model;
        } else {
            return new ExtendedSurveyCredentials();
        }
    }
    protected function findDashboardModel($institutionId)
    {  
        try {
            return Yii::$app->db->createCommand("CALL getinstitution_dashboard(:institutionid)")
                ->bindValue(':institutionid' , $institutionId)
                ->queryAll();  
        } catch (\Exception $e) {
            yii::error($e->getMessage());
        } 
        return false;   
    }
    public function actionCreateDashboardItem() 
    {
        $request = Yii::$app->request;
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if ($request->isAjax && $request->isPost) {
            $postData = $request->post();
            $institutionId = $request->post('institutionId');
            $data = $postData['data'];
            if(!empty($data) && $institutionId ) {
                $response = ExtendedInstitution::addDashBoardItems($data, $institutionId);
                if ($response) {
                    //successfully saved
                    return ['hasError' => false,'Success'=> true];
                }  else {
                    return ['hasError' => true, 'Success' => false]; 
                } 
            } else {
                return ['hasError' => true, 'Success' => false]; 
            }
            
        }else {
            return ['hasError' => true, 'Success' => false];
        }
    }
    public function actionGetAutoCountryCode() 
    { 
        $request = Yii::$app->request;
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if ($request->isAjax && $request->isPost) {
            $inuptData = $request->post('inuptData');
            if ($inuptData['countryId']) {
               $sql = 'SELECT countrycode,telephonecode from country where countryid = :countryId';
               $data = yii::$app->db->createCommand($sql)->bindValue(':countryId', $inuptData['countryId'])->queryOne();
               if(!empty($data)) {
                return [
                    'Success'=> true,
                    'countryCode' => $data['countrycode'],
                    'telephoneCode' => $data['telephonecode']
                ];
               } else {
                    return ['Success' => false];
               }
            }   else {
               return ['Success' => false]; 
            }  
        } else {
            return ['Success' => false];
        }
    }
    public function actionEditInstitution()
    {
        $institutionId = yii::$app->user->identity->institution->id;
        if ($institutionId) {
            $model = $this->findModel($institutionId);
            $oldImage = $model->institutionlogo;
            $institutionData = Yii::$app->request->post();
            $model->social_media = [
                'facebook' => $institutionData['ExtendedInstitution']['facebook'] ?? null,
                'instagram' => $institutionData['ExtendedInstitution']['instagram'] ?? null,
                'twitter' => $institutionData['ExtendedInstitution']['twitter'] ?? null,
                'youtube' => $institutionData['ExtendedInstitution']['youtube'] ?? null,
            ];
            if ($model->load($institutionData)) {
                if ($model->validate()) {
                     $response = $model->updateInstitution($oldImage);
                if (!$response['success']) {
                    $this->sessionAddFlashArray('error', $response['errors'], true);
                } else {
                    $this->sessionAddFlashArray('success', 'Successfully updated institution details', true);
                   return $this->goBack();
                }
            } else {
                $this->sessionAddFlashArray('error', $model->getErrors(), true);
            }
            return $this->redirect(['edit-institution', 'id' => $model->id]);
        } else {
            return $this->render(
                'admin_institution_update_form',
                [
                'formModel' => $model
                ]
            );
        }

        }
    }

}


