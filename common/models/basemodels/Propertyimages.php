<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "propertyimages".
 *
 * @property int $propertyimageid
 * @property string $imageurl
 * @property int $propertyid
 * @property int $imageorder
 *
 * @property Property $property
 */
class Propertyimages extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'propertyimages';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['imageurl', 'propertyid', 'imageorder'], 'required'],
            [['propertyid', 'imageorder'], 'integer'],
            [['imageurl'], 'string', 'max' => 100],
            [['propertyid'], 'exist', 'skipOnError' => true, 'targetClass' => Property::className(), 'targetAttribute' => ['propertyid' => 'propertyid']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'propertyimageid' => 'Propertyimageid',
            'imageurl' => 'Imageurl',
            'propertyid' => 'Propertyid',
            'imageorder' => 'Imageorder',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProperty()
    {
        return $this->hasOne(Property::className(), ['propertyid' => 'propertyid']);
    }
}
