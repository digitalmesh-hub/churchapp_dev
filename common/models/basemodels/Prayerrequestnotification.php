<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "prayerrequestnotification".
 *
 * @property int $id
 * @property int $prayerrequestid
 * @property int $userid
 * @property string $createddatetime
 *
 * @property Prayerrequest $prayerrequest
 * @property Usercredentials $user
 */
class Prayerrequestnotification extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'prayerrequestnotification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['prayerrequestid', 'userid', 'createddatetime'], 'required'],
            [['prayerrequestid', 'userid'], 'integer'],
            [['createddatetime'], 'safe'],
            [['prayerrequestid'], 'exist', 'skipOnError' => true, 'targetClass' => Prayerrequest::className(), 'targetAttribute' => ['prayerrequestid' => 'prayerrequestid']],
            [['userid'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['userid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'prayerrequestid' => 'Prayerrequestid',
            'userid' => 'Userid',
            'createddatetime' => 'Createddatetime',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrayerrequest()
    {
        return $this->hasOne(Prayerrequest::className(), ['prayerrequestid' => 'prayerrequestid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'userid']);
    }
}
