<?php

namespace common\models\extendedmodels;

use Yii;
use yii\base\ErrorException;
use common\models\basemodels\UserConversationTopic;

/**
 * This is the extended model class for table "userconversationtopic".
 *
 * @property int $userconversationtopicid
 * @property int $conversationtopicid
 * @property int $userid
 * @property int $isread
 * @property string $readtime
 *
 * @property Conversationtopic $conversationtopic
 * @property Usercredentials $user
 */
class ExtendedUserConversationTopic extends UserConversationTopic
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['conversationtopicid', 'userid'], 'required'],
            [['conversationtopicid', 'userid'], 'integer'],
            [['readtime'], 'safe'],
            [['isread'], 'string', 'max' => 4],
            [['conversationtopicid'], 'exist', 'skipOnError' => true, 'targetClass' => Conversationtopic::className(), 'targetAttribute' => ['conversationtopicid' => 'conversationtopicid']],
            [['userid'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['userid' => 'id']],
        ];
    }

    /**
     * Update coveration topic status
     * @param int itemId 
     * @param int itemId 
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
    */
    public function updateConversationTopicStatus($itemId, $userId)
    {
        try{
            $command = Yii::$app->db->createCommand("UPDATE userconversationtopic SET isread = 1, readtime = :readtime WHERE conversationtopicid = :conversationtopicid and userid = :userid")
            ->bindValue(':readtime' , gmdate("Y-m-d H:i:s"))
            ->bindValue(':conversationtopicid' , $itemId)
            ->bindValue(':userid' , $userId);
            $command->execute();
            return true;
        }
        catch(ErrorException $e){
            yii::error($e->getMessage());
            return false;
        }
    }

    /**
     * Add user conversation topic details
     * @param $conversationArray array
    */
    public static function addUserConversionTopic($recipientArray)
    {
        try 
        {
            Yii::$app->db->createCommand()->batchInsert('userconversationtopic', ['conversationtopicid', 'userid', 'isread'], $recipientArray)
            ->execute();
            return true;
        } catch (ErrorException $e) {
            return false;
        }
    } 
    
    public static function getUserConversationCreatedCount($memberUserId, $institutionID){

    	try {
    		 
    		$conversationCount = Yii::$app->db->createCommand("select count(conversationtopicid) as conversationtopiccount from conversationtopic where createdby =:memberuserid and institutionid = :institutionid")
					    		->bindValue(':memberuserid', $memberUserId)
					    		->bindValue(':institutionid', $institutionID)
					    		->queryOne();
    		return $conversationCount;
    	} catch (Exception $e) {
    		return false;
    	}
    }
    public static function updateConversationTopicUser($userId, $memberUserId,$institutionID){
    	
    	try{
    		$command = Yii::$app->db->createCommand("update conversationtopic set createdby = :userid where createdby = :memberuserid and institutionid = :institutionid")
    		->bindValue(':memberuserid', $memberUserId)->bindValue(':userid', $userId)
					    		->bindValue(':institutionid', $institutionID);
    		$command->execute();
    		
    	}
    	catch(ErrorException $e){
    		yii::error($e->getMessage());
    		return false;
    	}
    	
    }
    
    public static function getUserConversationTopicCreatedCount($memberUserId){
    	try {
    		 
    		$conversationCount = Yii::$app->db->createCommand("select count(userconversationtopicid) as userconversationtopiccount from userconversationtopic where userid =:memberuserid")
    		->bindValue(':memberuserid', $memberUserId)
    		->queryOne();
    		return $conversationCount;
    	} catch (Exception $e) {
    		return false;
    	}
    }
    
    public static function updateUserConversationTopicUser($userId, $memberUserId){
    	
    	try{
    		$command = Yii::$app->db->createCommand("update userconversationtopic set userid = :userid where userid = :memberuserid")
    		->bindValue(':memberuserid', $memberUserId)->bindValue(':userid', $userId);
    			$command->execute();
    	
    	}
    	catch(ErrorException $e){
    		yii::error($e->getMessage());
    		return false;
    	}
    }
}
