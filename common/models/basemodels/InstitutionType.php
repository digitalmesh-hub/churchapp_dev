<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "institutiontype".
 *
 * @property int $institutiontypeid
 * @property string $institutiontype
 *
 * @property Institution[] $institutions
 */
class InstitutionType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'institutiontype';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['institutiontype'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'institutiontypeid' => 'Institutiontypeid',
            'institutiontype' => 'Institutiontype',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitutions()
    {
        return $this->hasMany(Institution::className(), ['institutiontype' => 'institutiontypeid']);
    }
}
