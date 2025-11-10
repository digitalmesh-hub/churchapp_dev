<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "rolegroup".
 *
 * @property int $RoleGroupID
 * @property string $Description
 *
 * @property Rolecategory[] $rolecategories
 */
class RoleGroup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rolegroup';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Description'], 'required'],
            [['Description'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'RoleGroupID' => 'Role Group ID',
            'Description' => 'Description',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRolecategories()
    {
        return $this->hasMany(Rolecategory::className(), ['RoleGroupID' => 'RoleGroupID']);
    }
}
