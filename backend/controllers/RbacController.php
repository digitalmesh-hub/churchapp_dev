<?php 

namespace backend\controllers;

use yii;
use backend\controllers\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\extendedmodels\ExtendedInstitution;
use common\models\basemodels\CustomRoleModel;
use common\models\extendedmodels\ExtendedPrivilege;
use common\models\extendedmodels\ExtendedRoleGroup;
use yii\helpers\ArrayHelper;

class RbacController extends BaseController
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
                        'roles', 
                        'privileges', 
                        'manage-category',
                        'get-selected-role-categories',
                        'add-edit-role', 
                        'get-selected-roles', 
                        'add-role-privileges'
                 ],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'privileges'
                        ],
                        'roles' => ['superadmin']
                    ],
                    [
                        'allow' => true,
                        'actions' => [
                            'roles',
                            'manage-category',
                            'get-selected-role-categories',
                            'get-selected-roles',
                            'add-role-privileges',
                            'add-edit-role'
                    ],
                        'roles' => ['a83cbb99-fff4-11e6-b48e-000c2990e707'],           
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST']
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
    public function actionRoles($id)
    { 
        $institutionModel = $this->findInstitutionModel($id);
        $institutionRoles = CustomRoleModel::getInstitutionRoleList($id);
        $defaultRoles = $this->getDefaultRoleGroup();
        $allotedRoles= CustomRoleModel::getAllotedRoles($id);
        $dataArray = [];
        $currentUserId = yii::$app->user->id;
        $currentUserRole = \Yii::$app->authManager->getRolesByUser($currentUserId);
        if ((!empty($institutionRoles) && !empty($defaultRoles)) && !empty($currentUserRole) ) {
            foreach ($defaultRoles as $dkey => $dvalue) {
                foreach ($institutionRoles as $ikey => $ivalue) {
                    if ($ivalue['roleGroupName'] === $dvalue) {
                        $dataArray[$dvalue][] = $ivalue;
                    }
                }
            }
        }
        return $this->render(
            'role-listing',
            [
            'institutionModel' => $institutionModel,
            'dataArray' => $dataArray,
            'allotedRoles' => $allotedRoles,
            'currentUserRole' => array_keys($currentUserRole)
            ]
        );
    }
    protected function findInstitutionModel($id)
    {
        if (($model = ExtendedInstitution::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    public function actionManageCategory()
    {
        $id = yii::$app->request->get('id');
        $institutionModel = $this->findInstitutionModel($id);
        $defaultRoles = $this->getDefaultRoleGroup();
        if (Yii::$app->request->isPost) {
            $roleCategory = Yii::$app->request->post();
            $response = CustomRoleModel::createRoleCategory($roleCategory);
            if (!$response['success']) {
                $this->sessionAddFlashArray('error', $response['errors'], true);
            } else {
                $this->sessionAddFlashArray('success', 'Successfully saved role category', true);
            }
            return $this->redirect(['manage-category', 'id' => $id]);
        }

        return $this->render(
            '_form',
            [
             'defaultRoles' => $defaultRoles,
             'institutionModel' => $institutionModel,
            ]
        );
    }

    public function actionPrivileges()
    {
        $id = yii::$app->request->get('id');
        $institutionModel = $this->findInstitutionModel($id);
        $institutionPrivileges = CustomRoleModel::getInstitutionPrivileges($id);
        $provider = ArrayHelper::map(
            ExtendedPrivilege::find()
            ->select(['PrivilegeID', 'Description'])
            ->orderBy('Description')->all(),
            'PrivilegeID',
            'Description'
        );
        if (Yii::$app->request->isPost) {
            $institutionPrivileges = Yii::$app->request->post();
            $response = CustomRoleModel::createInstitutionPrivileges($institutionPrivileges);
            if (!$response['success']) {
                $this->sessionAddFlashArray('error', $response['errors'], true);
            } else {
                $this->sessionAddFlashArray('success', 'Successfully saved institution privileges', true);
                return $this->redirect(['institution/list-institution']);
            }
            return $this->redirect(['privileges', 'id' => $id]);
        }
        return $this->render(
            'privileges',
            [
            'institutionModel' => $institutionModel,
            'provider' => $provider,
            'institutionPrivileges' => $institutionPrivileges
            ]
        );

    }

    public function actionGetSelectedRoleCategories()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $response = "";
        if ($request->isAjax) {
            $roleId = $request->post('roleId');
            $institutionId = $request->post('institutionId');
            if ($roleId && $institutionId) {
                $selectedRoleCategroy =  CustomRoleModel::getRoleCategories($roleId, $institutionId);
                $response = $this->renderPartial(
                        '_selected-role-category',
                        [
                        'selectedRoleCategroy'=> $selectedRoleCategroy
                        ]
                );
                if (!empty($response)) {
                    return [
                        'status' => 'success' ,'data' => $response
                    ];
                }
                
            } else {
                return [
                    'status' => 'error' ,'data' => []
                ];

            }
        }
         
    }
    protected function getDefaultRoleGroup()
    {
        return ArrayHelper::map(
            ExtendedRoleGroup::find()
                ->select(['RoleGroupID', 'Description'])
                ->orderBy('Description')->all(),
            'RoleGroupID',
            'Description'
        );
    }
    public function actionAddEditRole()
    {
        $request = Yii::$app->request;
        $institutionId = $request->get('institutionId');
        $institutionModel = $this->findInstitutionModel($institutionId);
        $roleCategoryId = $request->get('roleCategoryId');
        $roleGroupId = $request->get('roleGroupId');
        $institutionAllRoleCategories = CustomRoleModel::getInstitutionAllRoleCategories($institutionId, $roleGroupId);
        if ($institutionId && $roleCategoryId) {
            if (Yii::$app->request->isPost) {
                $roles = Yii::$app->request->post();
                $response = CustomRoleModel::createRoles($roles);
                if (!$response['success']) {
                    $this->sessionAddFlashArray('error', $response['errors'], true);
                } else {
                    $this->sessionAddFlashArray('success', 'Successfully saved role details', true);
                }
                    return $this->redirect(
                        [
                        'add-edit-role',
                        'institutionId' => $institutionId,
                        'roleCategoryId' => $roleCategoryId,
                        'roleGroupId' => $roleGroupId
                        ]
                    );
            }
            return $this->render(
                '_add-edit-role',
                [
                'institutionModel' => $institutionModel,
                'dropDownData' => $institutionAllRoleCategories,
                'roleCategoryId' => $roleCategoryId,
                'roleGroupId' => $roleGroupId
                ]
            );
        }
    }
    public function actionGetSelectedRoles()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $response = "";
        if ($request->isAjax) {
            $roleCategoryId = $request->post('roleCategoryId');
            $institutionId = $request->post('institutionId');
            if ($roleCategoryId && $institutionId) {
                $selectedRoles =  CustomRoleModel::getselectedRoles($roleCategoryId, $institutionId);
                    $response = $this->renderPartial(
                        '_selected-roles',
                        [
                        'selectedRoles'=> $selectedRoles
                        ]
                    );
                if (!empty($response)) {
                    return [
                        'status' => 'success' ,'data' => $response
                    ];
                } 
            } else {
                return [
                    'status' => 'error' ,'data' => []
                ];

            }
        }     
    }
    public function actionAddRolePrivileges()
    {

        $request = Yii::$app->request;
        $institutionId = $request->get('institutionId');
        $roleId = $request->get('roleId');
        $roleGroupId = $request->get('roleGroupId');
        $institutionModel = $this->findInstitutionModel($institutionId);
        if ($roleId && $roleGroupId) {
            $auth = Yii::$app->authManager;
            $role = $auth->getRole($roleId);
            $roleName = $role->description;
            $assignedRoles = array_keys(CustomRoleModel::getChildrenRole($roleId));
            $privileges = CustomRoleModel::getGroupRolePrivileges($roleId, $roleGroupId, $institutionId);
            if (Yii::$app->request->isPost) {
                $postData = Yii::$app->request->post();
                $response = CustomRoleModel::createGroupRolePrivileges($postData);
                if (!$response['success']) {
                    $this->sessionAddFlashArray('error', $response['errors'], true);
                } else {
                    $this->sessionAddFlashArray('success', 'Successfully saved role details', true);
                    return $this->redirect(['rbac/roles', 'id' => $institutionId]);
                }
                    return $this->redirect(
                        [
                            'add-role-privileges',
                            'institutionId' => $institutionId,
                            'roleId' => $roleId,
                            'roleGroupId' => $roleGroupId
                        ]
                    );
            }
            return $this->render(
                '_add-edit-privileges',
                [
                'institutionModel' => $institutionModel,
                'provider' => $privileges,
                'roleId' => $roleId,
                'roleGroupId' => $roleGroupId,
                'assignedRoles' => $assignedRoles,
                'roleName' => $roleName
                ]
            );
        }

    }
}