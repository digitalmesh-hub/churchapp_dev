<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "appprivilege".
 *
 * @property string $PrivilegeID
 * @property string $Description
 * @property string $Code
 * @property string $sortorder
 *
 * @property Privilege $privilege
 */
class Appprivilege extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'appprivilege';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['PrivilegeID', 'Description', 'sortorder'], 'required'],
            [['PrivilegeID'], 'string', 'max' => 38],
            [['Description', 'Code'], 'string', 'max' => 200],
            [['sortorder'], 'string', 'max' => 45],
            [['PrivilegeID'], 'unique'],
            [['PrivilegeID'], 'exist', 'skipOnError' => true, 'targetClass' => Privilege::className(), 'targetAttribute' => ['PrivilegeID' => 'PrivilegeID']],
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
            'sortorder' => 'Sortorder',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrivilege()
    {
        return $this->hasOne(Privilege::className(), ['PrivilegeID' => 'PrivilegeID']);
    }
}
