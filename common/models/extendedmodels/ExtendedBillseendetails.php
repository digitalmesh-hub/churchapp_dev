<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Billseendetails;

/**
 * This is the model class for table "billseendetails".
 *
 * @property int $id
 * @property int $memberid
 * @property string $usertype
 * @property int $month
 * @property int $year
 * @property int $institutionid
 * @property string $createddatetime
 *
 * @property Institution $institution
 * @property Member $member
 */
class ExtendedBillseendetails extends Billseendetails
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'billseendetails';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['memberid', 'usertype', 'month', 'year', 'institutionid', 'createddatetime'], 'required'],
            [['memberid', 'month', 'year', 'institutionid'], 'integer'],
            [['createddatetime'], 'safe'],
            [['usertype'], 'string', 'max' => 1],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedInstitution::className(), 'targetAttribute' => ['institutionid' => 'id']],
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
            'usertype' => 'Usertype',
            'month' => 'Month',
            'year' => 'Year',
            'institutionid' => 'Institutionid',
            'createddatetime' => 'Createddatetime',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitution()
    {
        return $this->hasOne(Institution::className(), ['id' => 'institutionid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Member::className(), ['memberid' => 'memberid']);
    }
    /**
     * To get the bill seen count
     * @param unknown $billSeenDetails
     * @return \yii\db\false|boolean
     */
    public static function getBillSeenCount($billSeenDetails)
    {
    	$memberId = $billSeenDetails->memberid;
    	$userType = $billSeenDetails->usertype;
    	$month = $billSeenDetails->month;
    	$year = $billSeenDetails->year;
    	$institutionId = $billSeenDetails->institutionid;
    	try {
    		$billSeenCount = Yii::$app->db->createCommand('SELECT count(id) AS count FROM billseendetails WHERE
                                        memberid = :memberid and 
                                        usertype =:usertype and
                                        month = :month and 
                                        year = :year and
	                                    institutionid = :institutionid  ')
    					->bindValue(':memberid',$memberId)
    					->bindValue(':usertype',$userType)
    					->bindValue(':month',$month)
    					->bindValue(':year',$year)
    					->bindValue(':institutionid',$institutionId)
    					->queryOne();
    		return $billSeenCount;
    		
    	} catch (Exception $e) {
    		return false;
    	}
    }
}
