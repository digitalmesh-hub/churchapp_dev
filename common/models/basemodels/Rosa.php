<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "rosa".
 *
 * @property int $rosaid
 * @property int $year
 * @property string $name
 * @property string $mobile
 * @property string $dob
 * @property string $email
 * @property string $createdby
 * @property string $createddatetime
 * @property string $modifiedby
 * @property string $modifieddatetime
 * @property string $middlename
 * @property string $lastname
 * @property string $countrycode
 */
class Rosa extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rosa';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['year'], 'integer'],
            [['name', 'mobile', 'dob'], 'required'],
            [['dob', 'createddatetime'], 'safe'],
            [['name', 'email', 'countrycode'], 'string', 'max' => 45],
            [['mobile', 'createdby', 'modifiedby', 'modifieddatetime', 'middlename', 'lastname'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'rosaid' => 'Rosaid',
            'year' => 'Year',
            'name' => 'Name',
            'mobile' => 'Mobile',
            'dob' => 'Dob',
            'email' => 'Email',
            'createdby' => 'Createdby',
            'createddatetime' => 'Createddatetime',
            'modifiedby' => 'Modifiedby',
            'modifieddatetime' => 'Modifieddatetime',
            'middlename' => 'Middlename',
            'lastname' => 'Lastname',
            'countrycode' => 'Countrycode',
        ];
    }
}
