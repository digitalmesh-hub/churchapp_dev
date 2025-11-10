<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Privilege;

/**
 * This is the model class for table "privilege".
 *
 * @property string $PrivilegeID
 * @property string $Description
 * @property string $Code
 *
 * @property Appprivilege $appprivilege
 * @property Institutionprivilege[] $institutionprivileges
 * @property Roleprivilege[] $roleprivileges
 */
class ExtendedPrivilege extends Privilege
{   

        // The manage user profile
    const MANAGE_USER_PROFILE = "297e0e10-ec46-11e6-b48e-000c2990e707";
        // The view albums
    const VIEW_ALBUMS = "437c49cf-ec46-11e6-b48e-000c2990e707";
        // The manage album
    const MANAGE_ALBUM = "74af2974-ec46-11e6-b48e-000c2990e707";
        // The view announcement list
    const MANAGE_ANNOUNCEMENT = "893232ae-ec46-11e6-b48e-000c2990e707";
        // The manage announcement
    const LIST_ANNOUNCEMENTS = "7d0b6ab2-ec46-11e6-b48e-000c2990e707";
        // The manage bills
    const MANAGE_BILLS = "a65b8d57-ec46-11e6-b48e-000c2990e707";
        // The manage committe
    const MANAGE_COMMITTE = "b46fb1de-ec46-11e6-b48e-000c2990e707";
        // The list conversations
    const LIST_CONVERSATIONS = "22643223-ec48-11e6-b48e-000c2990e707";
        // The list events
    const LIST_EVENTS = "b0d171e3-ec48-11e6-b48e-000c2990e707";
        // The manage event
    const MANAGE_EVENT = "bdb35068-ec48-11e6-b48e-000c2990e707";
        // The manage event RSVP
    const MANAGE_EVENT_RSVP = "d4b64d8a-ec48-11e6-b48e-000c2990e707";
        //The manage family units
    const MANAGE_FAMILY_UNITS = "04cd913a-ec49-11e6-b48e-000c2990e707";
        // The manage feedbacks
    const MANAGE_FEEDBACKS = "0f74458a-ec49-11e6-b48e-000c2990e707";
        // The view members list
    const VIEW_MEMBERS_LIST = "fe083df2-ec49-11e6-b48e-000c2990e707";
        // The add edit member
    const MANAGE_MEMBER = "1092473e-ec4a-11e6-b48e-000c2990e707";
        // The view member
    const VIEW_MEMBER = "1d5bd81c-ec4a-11e6-b48e-000c2990e707";
        // The view staff list
    const VIEW_STAFF_LIST = "27122647-ec4a-11e6-b48e-000c2990e707";
        // The add edit staff
    const MANAGE_STAFF = "316c0865-ec4a-11e6-b48e-000c2990e707";
        // The view staff
    const VIEW_STAFF = "3beb7121-ec4a-11e6-b48e-000c2990e707";
        //The view pending members list
    const VIEW_PENDING_MEMBERS_LIST = "48b7c116-ec4a-11e6-b48e-000c2990e707";
        // The approve pending member
    const APPROVE_PENDING_MEMBER = "81423355-ec4a-11e6-b48e-000c2990e707";
        // The manage prayer requests
    const MANAGE_PRAYER_REQUESTS = "ca4ac940-ec4a-11e6-b48e-000c2990e707";
        // The manage titles
    const MANAGE_TITLES = "4904f428-ec4b-11e6-b48e-000c2990e707";
        // The manage institution
    const MANAGE_INSTITUTION = "05677a55-ed1a-11e6-b48e-000c2990e707";
        // The manage affiliated institution
    const MANAGE_AFFILIATED_INSTITUTION = "5a1562b9-ed1e-11e6-b48e-000c2990e707";
        // The more URL
    const MORE_URL = "7304da2e-f2ac-11e6-b48e-000c2990e707";
        // The manage roles privileges
    const MANAGE_ROLES_PRIVILEGES = "a83cbb99-fff4-11e6-b48e-000c2990e707";
        // The manage restaurant
    const MANAGE_RESTAURANT = "69b1b6c1-fffc-11e6-b48e-000c2990e707";
        // The manage food orders
    const MANAGE_FOOD_ORDERS = "fcb852d5-0005-11e7-b48e-000c2990e707";
        // The food order
    const ORDER_FOOD = "65badb2b-054c-11e7-b48e-000c2990e707";
        // The survey
    const SURVEY = "3db740af-1515-11e7-b48e-000c2990e707";
        // The manage staff designations
    const MANAGE_STAFF_DESIGNATIONS = "1041a93a-153b-11e7-b48e-000c2990e707";

    const VIEW_QURBANA = "0c26fee6-3df8-4cd6-83d9-45a556a75b64";

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'privilege';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['PrivilegeID'], 'required'],
            [['PrivilegeID'], 'string', 'max' => 38],
            [['Description', 'Code'], 'string', 'max' => 200],
            [['PrivilegeID'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'PrivilegeID' => 'Privilege ID',
            'Description' => 'Description',
            'Code' => 'Code',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppprivilege()
    {
        return $this->hasOne(Appprivilege::className(), ['PrivilegeID' => 'PrivilegeID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitutionprivileges()
    {
        return $this->hasMany(Institutionprivilege::className(), ['PrivilegeID' => 'PrivilegeID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoleprivileges()
    {
        return $this->hasMany(Roleprivilege::className(), ['PrivilegeID' => 'PrivilegeID']);
    }
}
