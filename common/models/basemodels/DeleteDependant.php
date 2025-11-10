<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "delete_dependant".
 *
 * @property int $id
 * @property int $memberid
 * @property string $dependantname
 * @property string $dob
 * @property string $relation
 *
 * @property Member $member
 */
class DeleteDependant extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'delete_dependant';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['memberid'], 'integer'],
            [['dob'], 'safe'],
            [['dependantname'], 'string', 'max' => 50],
            [['relation'], 'string', 'max' => 45],
            
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
            'dependantname' => 'Dependantname',
            'dob' => 'Dob',
            'relation' => 'Relation',
        ];
    }

   
}
