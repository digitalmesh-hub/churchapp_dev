<?php 
namespace common\models\basemodels;

use yii;
use yii\base\Model;
use common\models\extendedmodels\ExtendedInstitutionPrivileges;
use common\models\extendedmodels\ExtendedRoleCategory;
use common\models\extendedmodels\ExtendedRole;
use yii\helpers\ArrayHelper;
use Exception;

class CustomRoleModel extends Model
{
    const ROLE_ADMIN = 1;
    const ROLE_MEMBER = 2;
    const ROLE_STAFF = 3;

    public static function createInstitutionPrivileges($institutionPrivileges = [])
    {   

        $dataArray = [];
        $data = [];
        $auth = Yii::$app->authManager;
        $institutionId = $institutionPrivileges['institutionId'];
        $allPriviLages = self::getInstitutionPrivileges($institutionId);
        unset($institutionPrivileges['_csrf-backend']);
        unset($institutionPrivileges['institutionId']);
        $allRoles = yii::$app->db->createCommand("SELECT roleid FROM role where institutionid = :id")->bindValue(':id', $institutionId)->queryColumn();
        $temp = array_keys($institutionPrivileges);
        
        try{
            foreach ($allPriviLages as $key => $value) {
                if(!in_array($value, $temp)) {
                    foreach ($allRoles as $parentName) {
                        $parentRole = $auth->getRole($parentName);
                        $child = $auth->getPermission($value);
                        if($parentRole && $child){
                            $auth->removeChild($parentRole, $child);  
                        }   
                    }      
                } 
                else {
                    foreach ($allRoles as $parentName) {
                        $parentRole = $auth->getRole($parentName);
                        $child = $auth->getPermission($value);
                        if($parentRole && $child){
                            $auth->removeChild($parentRole, $child);
                            $auth->addChild($parentRole, $child);  
                        }   
                    }
                }
            }
        } catch(Exception $e) {
            yii::error($e->getMessage());
        } 
        if ($institutionId && !empty($institutionPrivileges)) {
            foreach ($institutionPrivileges as $key => $value) {
                $data['InstitutionID'] = $institutionId;
                $data['PrivilegeID'] = $key;
                $dataArray [] = $data;
            }
        }
        $sql = 'DELETE FROM institutionprivilege
                    WHERE InstitutionID = :institutionId';
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
                $db->createCommand($sql)->bindvalue(':institutionId', $institutionId)->execute();
                if (!empty($dataArray)) {
                    Yii::$app->db->createCommand()->batchInsert(
                        ExtendedInstitutionPrivileges::tableName(),
                        ['InstitutionID', 'PrivilegeID'],
                        $dataArray
                    )
                    ->execute();
                } 
                $transaction->commit();
                return ['success' => true];
            } catch(\Exception $e) {
                $transaction->rollBack();
                yii::error($e->getMessage());
                return ['success' => false, 'errors' => 'Something went wrong! Please try again'];
            }   
    }

    public static function getInstitutionPrivileges($id)
    {
        $query = 'SELECT PrivilegeID from institutionprivilege WHERE InstitutionID = :institutionId';
        $privileges = Yii::$app->db->createCommand($query)->bindvalue(':institutionId', $id)->queryColumn();
        if (!empty($privileges)) {
            return $privileges;
        } else {
            return [];
        }
    }
    public static function createRoleCategory($roleCategory = [])
    {
        $dataArray = [];
        $data = [];
        $institutionId = $roleCategory['institutionId'];
        unset($roleCategory['_csrf-backend']);
        if ($institutionId && !empty($roleCategory['field_name'])) {
            foreach ($roleCategory['field_name'] as $key => $value) {
                $data['Description'] = $value;
                $data['InstitutionID'] = $institutionId;
                $data['RoleGroupID'] = $roleCategory['role_group'];
                $dataArray [] = $data;
            }
        }
        if (!empty($dataArray) || isset($roleCategory['update_field_name'])) {
            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();
            try {
                if (!empty($roleCategory['update_field_name'])) {
                    
                    foreach ($roleCategory['update_field_name'] as $upKey => $upValue) {
                        $db->createCommand('UPDATE rolecategory SET Description = :value WHERE RoleCategoryID = :id')
                        ->bindvalue(':value', $upValue)->bindvalue(':id', $upKey)->execute();
                    }
                }
                if (!empty($dataArray)) {
                    $db->createCommand()->batchInsert(
                        ExtendedRoleCategory::tableName(),
                        ['Description', 'InstitutionID', 'RoleGroupID'],
                        $dataArray
                    )
                    ->execute();

                }
                $transaction->commit();
                return ['success' => true];
            } catch(\Exception $e) {
                $transaction->rollBack();
                yii::error($e->getMessage());
                return ['success' => false, 'errors' =>'Something went wrong! Please try again' ];
            }
        } else {
            return ['success' => false, 'errors' =>'Something went wrong! Please try again' ];
        }
        

    }
    public static function getRoleCategories($roleGroupId, $institutionId)
    {
        $query = 'SELECT Description,RoleCategoryID 
                    FROM 
                    rolecategory 
                    WHERE 
                    InstitutionID = :institutionId 
                    AND 
                    RoleGroupID = :roleGroupId';
        $categories = Yii::$app->db->createCommand($query)->bindvalue(':institutionId', $institutionId)->bindvalue(':roleGroupId', $roleGroupId)->queryAll();
        if (!empty($categories)) {
            return $categories;
        } else {
            return [];
        }
    }
    public static function getInstitutionRoleList($id)
    {
        $data = (new \yii\db\Query())
            ->select(
                "
                rc.Description as roleCategoryName,
                rg.Description as roleGroupName,
                rc.RoleCategoryId,
                rg.RoleGroupId,
                rc.InstitutionID,
                "
            )
            ->from('rolecategory rc')
            ->leftJoin('rolegroup rg', 'rg.RoleGroupID = rc.RoleGroupID')
            ->where('rc.InstitutionId = :institutionId', [':institutionId' => $id])
            ->all();

        return $data;
    }

    public static function getInstitutionAllRoleCategories($id, $roleGroupId)
    {
        return ArrayHelper::map(
            ExtendedRoleCategory::find()
                ->select(['rolecategory.RoleCategoryID', 'rolecategory.Description'])
                ->innerJoin('rolegroup rg', '`rg`.`RoleGroupID` = `rolecategory`.`RoleGroupID`')
                ->where(['InstitutionID' => $id])
                ->andWhere('rg.RoleGroupID = :roleGroupId', [':roleGroupId' => $roleGroupId])
                ->orderBy('rolecategory.Description')->all(),
            'RoleCategoryID',
            'Description'
        );
    }
    public static function createRoles($roles = [])
    {  
        $auth = Yii::$app->authManager;
        $rbacUser = [];
        $institutionId = $roles['institutionId'];
        unset($roles['_csrf-backend']);
        $dataArray = [];
        $oldRoleId = [];
        if ($institutionId && !empty($roles['field_name']['roledescription'])) {
            foreach ($roles['field_name']['roledescription'] as $key => $value) {
                $dataArray[$key]['roleid'] = uniqid();
                $dataArray[$key]['roledescription'] = $value;
                $dataArray[$key]['rolecategoryid'] = $roles['role_category'];
                $dataArray[$key]['institutionid'] = $institutionId;
            }
        }

        if (!empty($dataArray) || isset($roles['update_field_name'])) {
            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();
        
            try {
                if (!empty($roles['update_field_name'])) {
                    foreach ($roles['update_field_name'] as $upKey => $upValue) {
                        $db->createCommand('UPDATE role SET roledescription = :value WHERE roleid = :roleId')
                        ->bindvalue(':value', $upValue)->bindvalue(':roleId', $upKey)->execute();
                        $rmRole = $auth->getRole($upKey);
                        if ($rmRole) {
                            $rl = $auth->createRole($upKey);
                            $rl->description = $upValue;
                            $auth->update($upKey,$rl);
                        }
                    }
                }

                if (!empty($dataArray)) {
                    $db->createCommand()->batchInsert(
                        ExtendedRole::tableName(),
                        ['roleid', 'roledescription', 'rolecategoryid', 'institutionid'],
                        $dataArray
                    )
                    ->execute();
                }
                foreach ($dataArray as $key => $dValue) {
                    $role = $auth->createRole($dValue['roleid']);
                    $role->description = $dValue['roledescription'];
                    $rbacUser[$dValue['roledescription']] = $role;
                }
                $transaction->commit();
               
                $parentRole = null;
                
                $roleGroup = yii::$app->db->createCommand('select RoleGroupID from rolecategory where RoleCategoryID = :roleCategoryId')->bindvalue(':roleCategoryId', $roles['role_category'])->queryScalar();

                switch ($roleGroup) {
                    case 1:
                        $parentRole = $auth->getRole('admin');
                    break;
                    case 2:
                        $parentRole = $auth->getRole('member');
                    break;
                    case 3:
                        $parentRole = $auth->getRole('staff');
                    break;
                    default:
                        break;
                }
                
                //Adding Roles
                if (!empty($parentRole)) {
                    foreach ($rbacUser as $role) {
                        $childRole = $auth->add($role);
                        $auth->addChild($parentRole, $role);
                    }
                } else {
                    return ['success' => false, 'errors' =>'Something went wrong! Please try again' ];
                }
                
                return['success' => true];
            } catch (\Exception $e) {
                yii::error($e->getMessage());
                $transaction->rollBack();
                return ['success' => false, 'errors' =>'Something went wrong! Please try again' ];
            }
        } else {
            return ['success' => false, 'errors' =>'Something went wrong! Please try again' ];
        }
    }
    public static function getselectedRoles($rolecategoryId, $institutionId)
    {
        $query = 'SELECT 
                    roledescription,roleid 
                    FROM 
                    role 
                    WHERE 
                    institutionid = :institutionId 
                    AND 
                    rolecategoryid = :rolecategoryId';
                $roles = Yii::$app->db->createCommand($query)->bindvalue(':institutionId', $institutionId)->bindvalue(':rolecategoryId', $rolecategoryId)->queryAll();
        if (!empty($roles)) {
            return $roles;
        } else {
            return [];
        }
    }
    public static function getAllotedRoles($id)
    {
         $data = (new \yii\db\Query())
                    ->select(
                        "
                        rc.RoleCategoryId,
                        rg.RoleGroupId,
                        rc.InstitutionID,
                        r.roledescription as role,
                        r.roleid 
                        "
                    )
                    ->from('rolecategory rc')
                    ->innerJoin('rolegroup rg', 'rg.RoleGroupID = rc.RoleGroupID')
                    ->innerJoin('role r', 'r.rolecategoryid = rc.RoleCategoryID')
                    ->where('rc.InstitutionId = :institutionId', [':institutionId' => $id])
                    ->all();

                return $data;
    }

    public static function getGroupRolePrivileges($roleId, $roleGroupId, $institutionId)
    {
        try {
            switch ($roleGroupId) {
                case self::ROLE_ADMIN:
                    $data = Yii::$app->db->createCommand('CALL Privileges_ByRole(:roleId,:institutionId)')->bindvalue(':institutionId', $institutionId)->bindvalue(':roleId', $roleId)
                        ->queryAll();
                    break;
                case self::ROLE_MEMBER:
                case self::ROLE_STAFF:
                    $data = Yii::$app->db->createCommand('CALL App_Privileges_ByRole(:roleId,:institutionId)')
                    ->bindvalue(':institutionId', $institutionId)->bindvalue(':roleId', $roleId)
                    ->queryAll();
                    break;
                default:
                    $data = [];
                    break;
            }
            return $data;
        } catch (\yii\db\Exception $e) {
            // Log the error for debugging
            Yii::error('Failed to get group role privileges: ' . $e->getMessage(), __METHOD__);
            
            // Return empty array as fallback
            return [];
        }
    }
    public static function createGroupRolePrivileges($data = [])
    {
        $institutionId = $data['institutionId'];
        $role = $data['role'];
        unset($data['_csrf-backend']);
        unset($data['institutionId']);
        unset($data['role']);
        $createPermission = [];
        $auth = Yii::$app->authManager;
        if (!empty($data) && $role) {
            try {
                // add permission
                foreach ($data as $permissionKey => $permissionValue) {
                        $createPermission[$permissionKey] = $auth->getPermission($permissionKey);
                }
                if (!empty($createPermission)) {
                    $parent = $auth->getRole($role);
                    if ($parent) {
                        $auth->removeChildren($parent);
                        foreach ($createPermission as $childKey => $childPermission) {
                            $auth->addChild($parent, $childPermission);
                        }
                    } else {
                        return ['success' => false, 'errors' =>'Something went wrong! Please try again'];
                    }
 
                } else {
                    return ['success' => false, 'errors' =>'Something went wrong! Please try again'];
                }
                return ['success' => true];
            } catch (Exception $e) {
                yii::error($e->getMessage());
                return ['success' => false, 'errors' =>'Something went wrong! Please try again'];
            }
        } else {
                $parent = $auth->getRole($role);
                if ($parent) {
                    $auth->removeChildren($parent);
                }
            return ['success' => true];
        }
    }
    public static function getChildrenRole($roleId)
    {  
        $children = [];
        if (!empty($roleId)) {
            $auth = Yii::$app->authManager;
            $children = $auth->getChildren($roleId);
        }
        return $children;
    }
    public static function loadRoleForUpdate($userId)
    {
        $query = (new \yii\db\Query())
                ->select(
                    [
                    'rc.RoleCategoryID',
                    'r.roleid'
                    ]
                )
                ->from('usercredentials uc')
                ->join('INNER JOIN', 'auth_assignment asg', 'asg.user_id = uc.id')
                ->join('INNER JOIN', 'auth_item ai', 'ai.name = asg.item_name')
                ->join('INNER JOIN', 'role r', 'r.roleid = ai.name')
                ->join('INNER JOIN', 'rolecategory rc', 'rc.RoleCategoryID = r.rolecategoryid')
                ->join('INNER JOIN', 'rolegroup rg', 'rg.RoleGroupID = rc.RoleGroupId')
                ->where(['uc.id' => $userId])
                ->one();
        return $query;
    }
    public static function loadMemberRole($userMemberId)
    {   
        $query = (new \yii\db\Query())
                ->select(
                    [
                    'rc.RoleCategoryID',
                    'r.roleid'
                    ]
                )
                ->from('usermember um')
                ->join('INNER JOIN', 'auth_assignment asg', 'asg.user_id = um.id')
                ->join('INNER JOIN', 'auth_item ai', 'ai.name = asg.item_name')
                ->join('INNER JOIN', 'role r', 'r.roleid = ai.name')
                ->join('INNER JOIN', 'rolecategory rc', 'rc.RoleCategoryID = r.rolecategoryid')
                ->join('INNER JOIN', 'rolegroup rg', 'rg.RoleGroupID = rc.RoleGroupId')
                ->where(['um.id' => $userMemberId])
                ->one();
        return $query;
    }
}
