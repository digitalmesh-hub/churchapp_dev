<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "committee".
 *
 * @property int $committeeid
 * @property int $userid
 * @property int $designationid
 * @property int $datefrom
 * @property int $dateto
 * @property int $memberid
 * @property int $institutionid
 * @property int $isspouse
 * @property int $active
 * @property string $createddatetime
 * @property int $createdby
 * @property int $committeegroupid
 * @property int $committeeperiodid
 *
 * @property Institution $institution
 * @property Committeegroup $committeegroup
 * @property CommitteePeriod $committeeperiod
 * @property Usercredentials $createdby0
 * @property Designation $designation
 * @property Member $member
 * @property Usercredentials $user
 */
class Committee extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'committee';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userid', 'designationid', 'memberid', 'institutionid', 'isspouse', 'createddatetime', 'createdby'], 'required'],
            [['userid', 'designationid', 'datefrom', 'dateto', 'memberid', 'institutionid', 'createdby', 'committeegroupid', 'committeeperiodid'], 'integer'],
            [['createddatetime'], 'safe'],
            [['isspouse', 'active'], 'string', 'max' => 4],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
            [['committeegroupid'], 'exist', 'skipOnError' => true, 'targetClass' => Committeegroup::className(), 'targetAttribute' => ['committeegroupid' => 'committeegroupid']],
            [['committeeperiodid'], 'exist', 'skipOnError' => true, 'targetClass' => CommitteePeriod::className(), 'targetAttribute' => ['committeeperiodid' => 'committee_period_id']],
            [['createdby'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['createdby' => 'id']],
            [['designationid'], 'exist', 'skipOnError' => true, 'targetClass' => Designation::className(), 'targetAttribute' => ['designationid' => 'designationid']],
            [['memberid'], 'exist', 'skipOnError' => true, 'targetClass' => Member::className(), 'targetAttribute' => ['memberid' => 'memberid']],
            [['userid'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['userid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'committeeid' => 'Committeeid',
            'userid' => 'Userid',
            'designationid' => 'Designationid',
            'datefrom' => 'Datefrom',
            'dateto' => 'Dateto',
            'memberid' => 'Memberid',
            'institutionid' => 'Institutionid',
            'isspouse' => 'Isspouse',
            'active' => 'Active',
            'createddatetime' => 'Createddatetime',
            'createdby' => 'Createdby',
            'committeegroupid' => 'Committeegroupid',
            'committeeperiodid' => 'Committeeperiodid',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitution()
    {
        return $this->hasOne(Institution::className(), ['id' => 'institutionid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommitteegroup()
    {
        return $this->hasOne(Committeegroup::className(), ['committeegroupid' => 'committeegroupid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommitteeperiod()
    {
        return $this->hasOne(CommitteePeriod::className(), ['committee_period_id' => 'committeeperiodid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedby0()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'createdby']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDesignation()
    {
        return $this->hasOne(Designation::className(), ['designationid' => 'designationid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Member::className(), ['memberid' => 'memberid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'userid']);
    }
}
