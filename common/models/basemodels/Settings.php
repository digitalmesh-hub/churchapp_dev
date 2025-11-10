<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "settings".
 *
 * @property int $id
 * @property int $memberid
 * @property int $addresstypeid
 * @property int $membernotification
 * @property int $birthday
 * @property int $anniversary
 * @property int $memberemail
 * @property int $membersms
 * @property int $spouseemail
 * @property int $spousesms
 * @property int $spousenotification
 * @property int $spousebirthday
 * @property int $spouseanniversary
 * @property int $synccontactinterval
 * @property int $membermobilePrivacyEnabled
 * @property int $spousemobilePrivacyEnabled
 *
 * @property Addresstype $addresstype
 * @property Member $member
 */
class Settings extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'settings';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['memberid', 'membernotification', 'birthday', 'anniversary', 'memberemail', 'membersms', 'spouseemail', 'spousesms', 'spousenotification', 'spousebirthday', 'spouseanniversary'], 'required'],
            [['memberid', 'addresstypeid', 'synccontactinterval'], 'integer'],
            [['membernotification', 'birthday', 'anniversary', 'memberemail', 'membersms', 'spouseemail', 'spousesms', 'spousenotification', 'spousebirthday', 'spouseanniversary', 'membermobilePrivacyEnabled', 'spousemobilePrivacyEnabled'], 'string', 'max' => 4],
            [['addresstypeid'], 'exist', 'skipOnError' => true, 'targetClass' => Addresstype::className(), 'targetAttribute' => ['addresstypeid' => 'id']],
            [['memberid'], 'exist', 'skipOnError' => true, 'targetClass' => Member::className(), 'targetAttribute' => ['memberid' => 'memberid']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'memberid' => 'Memberid',
            'addresstypeid' => 'Addresstypeid',
            'membernotification' => 'Membernotification',
            'birthday' => 'Birthday',
            'anniversary' => 'Anniversary',
            'memberemail' => 'Memberemail',
            'membersms' => 'Membersms',
            'spouseemail' => 'Spouseemail',
            'spousesms' => 'Spousesms',
            'spousenotification' => 'Spousenotification',
            'spousebirthday' => 'Spousebirthday',
            'spouseanniversary' => 'Spouseanniversary',
            'synccontactinterval' => 'Synccontactinterval',
            'membermobilePrivacyEnabled' => 'Membermobile Privacy Enabled',
            'spousemobilePrivacyEnabled' => 'Spousemobile Privacy Enabled',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddresstype()
    {
        return $this->hasOne(Addresstype::className(), ['id' => 'addresstypeid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Member::className(), ['memberid' => 'memberid']);
    }
}
