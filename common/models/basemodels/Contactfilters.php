<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "contactfilters".
 *
 * @property int $contactfilterid
 * @property string $description
 * @property int $filteroptiontypeid
 *
 * @property Institutioncontactfilters[] $institutioncontactfilters
 */
class Contactfilters extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'contactfilters';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['contactfilterid', 'description', 'filteroptiontypeid'], 'required'],
            [['contactfilterid', 'filteroptiontypeid'], 'integer'],
            [['description'], 'string', 'max' => 100],
            [['contactfilterid'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'contactfilterid' => 'Contactfilterid',
            'description' => 'Description',
            'filteroptiontypeid' => 'Filteroptiontypeid',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitutioncontactfilters()
    {
        return $this->hasMany(Institutioncontactfilters::className(), ['contactfilterid' => 'contactfilterid']);
    }
}
