<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "userlocation".
 *
 * @property int $userlocationid
 * @property int $userid
 * @property double $latitude
 * @property double $longitude
 * @property string $lastupdateddatetime
 *
 * @property Usercredentials $user
 */
class UserLocation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'userlocation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userid', 'latitude', 'longitude', 'lastupdateddatetime'], 'required'],
            [['userid'], 'integer'],
            [['latitude', 'longitude'], 'number'],
            [['lastupdateddatetime'], 'safe'],
            [['userid'], 'unique'],
            [['userid'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['userid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'userlocationid' => 'Userlocationid',
            'userid' => 'Userid',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'lastupdateddatetime' => 'Lastupdateddatetime',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'userid']);
    }
}
