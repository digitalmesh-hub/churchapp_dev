<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Memberadditionalinfo;
use common\models\extendedmodels\ExtendedMember;


/**
 * This is the model class for table "memberadditionalinfo".
 *
 * @property int $id
 * @property int $memberid
 * @property string $tagcloud
 *
 * @property Member $member
 */
class ExtendedMemberadditionalinfo extends Memberadditionalinfo
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
            [['memberid'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedMember::className(), 'targetAttribute' => ['memberid' => 'memberid']],
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
    /**
     * To get additional info
     * of a member
     * @param unknown $memberId
     * @return \yii\db\false|boolean
     */
    public static function getAdditionalInfo($memberId)
    {
    	try {
    		$memberInfo = Yii::$app->db->createCommand('
    				select * from memberadditionalinfo where memberid=:memberid ')
    				->bindValue(':memberid', $memberId)
    				->queryOne();
    		return $memberInfo;
    	} catch (Exception $e) {
    		return false;
    	}
    }
}

