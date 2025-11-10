<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "memberadditionalinfo".
 *
 * @property int $id
 * @property int $memberid
 * @property string $tagcloud
 *
 * @property Member $member
 */
class Memberadditionalinfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'memberadditionalinfo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['memberid'], 'required'],
            [['memberid'], 'integer'],
            [['tagcloud'], 'string', 'max' => 200],
            [['memberid'], 'exist', 'skipOnError' => true, 'targetClass' => Member::className(), 'targetAttribute' => ['memberid' => 'memberid']],
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
            'tagcloud' => 'Tagcloud',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Member::className(), ['memberid' => 'memberid']);
    }
}
