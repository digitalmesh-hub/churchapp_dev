<?php

namespace common\models\basemodels;

use Yii;

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
class Privilege extends \yii\db\ActiveRecord
{
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
