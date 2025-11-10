<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "tempmemberadditionalinfo".
 *
 * @property int $id
 * @property int $memberid
 * @property string $temptagcloud
 * @property int $isapproved
 *
 * @property Member $member
 */
class Tempmemberadditionalinfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tempmemberadditionalinfo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['memberid'], 'integer'],
            [['temptagcloud'], 'string', 'max' => 200],
            [['isapproved'], 'string', 'max' => 4],
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
            'temptagcloud' => 'Temptagcloud',
            'isapproved' => 'Isapproved',
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
