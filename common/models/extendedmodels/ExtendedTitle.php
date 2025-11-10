<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Title;
use common\models\basemodels\Institution;

/**
 * This is the model class for table "title".
 *
 * @property int $TitleId
 * @property string $Description
 * @property int $institutionid
 * @property int $active
 *
 * @property DeleteMember[] $deleteMembers
 * @property DeleteMember[] $deleteMembers0
 * @property Dependant[] $dependants
 * @property Member[] $members
 * @property Member[] $members0
 * @property Tempdependant[] $tempdependants
 * @property Tempdependantmail[] $tempdependantmails
 * @property Tempmembermail[] $tempmembermails
 * @property Tempmembermail[] $tempmembermails0
 * @property Institution $institution
 */
class ExtendedTitle extends Title
{
	/**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['institutionid'], 'integer'],
            [['Description'], 'string', 'max' => 75],
            [['active'], 'integer'],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
            [['Description'], 'required'],
            // [['Description'], 'unique',
            //     'message'=>'Title already exist.']
        ];
    }
    
    
    public function getActiveTitles($institutionId)
    {
    	$sql = "select TitleId, Description,active from title where institutionid=:institutionId and active=1 order by Description asc;";
		$result = Yii::$app->db->createCommand($sql)
			    ->bindValue(':institutionId' , $institutionId )    	 
		        ->queryAll();
    	return $result;
    }
    /**
     * get member title
     */
    public static function getMemberTitle($memberId)
    {
    	$sql = "select title.description from title 
				inner join member on member.membertitle = title.TitleId
				where member.memberid=:memberid";
    	try {
    		$titleData = Yii::$app->db->createCommand($sql)
    					->bindValue(':memberid', $memberId)
    					->queryOne();
    		return $titleData;
    	} catch (Exception $e) {
    		return false;
    	}
    }
}
