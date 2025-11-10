<?php
namespace common\models\formmodels;

use Yii;
use yii\base\Model;
use common\models\extendedmodels\ExtendedAddresstype;
use common\models\basemodels\CustomRoleModel;
use yii\helpers\ArrayHelper;

class MemberSettingForm extends Model
{
    public $communcation_address;
    public $institution_notification_member;
    public $institution_notification_spouse;
    public $birthday_notification_member;
    public $birthday_notification_spouse;
    public $anniversary_notification_member;
    public $anniversary_notification_spouse;
    public $email_notification_member;
    public $email_notification_spouse;
    public $member_role_category;
    public $member_role;
    public $spouse_role_category;
    public $spouse_role;
   
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [   
                    'member_role_category', 
                    'member_role',
                    'communcation_address',
                ],
            'required'
            ],
            [
                [   
                    'spouse_role_category', 
                    'spouse_role',
                ],
            'required',
            'whenClient' => "function (attribute, value) {
                    return $('#memberformmodel-spouse_mobile_number').val() != '';
            }"
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'communcation_address' => 'Communcation Address',
            'institution_notification' => 'Institution Notification',
            'birthday_notification' => 'Birthda Notification',
            'anniversary_notification' => 'Anniversary Notification',
            'email_notification' => 'Email Notification',
            'member_role_category' => 'Member Role Category',
            'member_role' => 'Member Role',
            'spouse_role_category' => 'Spouse Role Category',
            'spouse_role' => 'Spouse Role',
        ];
    }
    public function getCommunicationAddress() 
    {
        return ArrayHelper::map(
            ExtendedAddresstype::find()
            ->select(['id', 'type'])
            ->orderBy('type')->all(),
            'id',
            'type'
        );
    }
    public function getRoleCategories()
    {
        $roleGroupId = $this->getMemberRoleGroupID("Member");
        $institutionId = yii::$app->user->identity->institutionid;
        $roleCategories = ArrayHelper::map(
            CustomRoleModel::getRoleCategories($roleGroupId, $institutionId),
            "RoleCategoryID", 
            "Description"
        );
        return $roleCategories;

    }
    protected function getMemberRoleGroupID($type)
    {
        try {
           return  Yii::$app->db->createCommand('
            SELECT RoleGroupID FROM rolegroup where description = :type'
            )->bindValue(':type', $type)->queryScalar(); 
        } catch (Exception $e) {
            yii::error($e->getMessage()); 
        } 
        return false;
    }
}
