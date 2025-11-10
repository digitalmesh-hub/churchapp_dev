<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "editmember".
 *
 * @property int $editmemberid
 * @property int $memberid
 * @property string $tempmemberid
 *
 * @property Member $member
 */
class Editmember extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'editmember';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['memberid'], 'required'],
            [['memberid'], 'integer'],
            [['tempmemberid'], 'string', 'max' => 38],
            [['memberid'], 'exist', 'skipOnError' => true, 'targetClass' => Member::className(), 'targetAttribute' => ['memberid' => 'memberid']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'editmemberid' => 'Editmemberid',
            'memberid' => 'Memberid',
            'tempmemberid' => 'Tempmemberid',
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
