<?php

namespace common\models\extendedmodels;

use Yii;
use yii\base\ErrorException;
use common\models\basemodels\UserConversation;

/**
 * This is the model class for table "userconversation".
 *
 * @property int $userconversationid
 * @property int $conversationid
 * @property int $userid
 * @property int $isread
 * @property string $readtime
 *
 * @property Conversation $conversation
 * @property Usercredentials $user
 */
class ExtendedUserConversation extends UserConversation
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['conversationid', 'userid'], 'required'],
            [['conversationid', 'userid'], 'integer'],
            [['readtime'], 'safe'],
            [['isread'], 'string', 'max' => 4],
            [['conversationid'], 'exist', 'skipOnError' => true, 'targetClass' => Conversation::className(), 'targetAttribute' => ['conversationid' => 'conversationid']],
            [['userid'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['userid' => 'id']],
        ];
    }

    /**
     * Save user conversation details
     * @param $conversationArray array
    */
    public static function saveUserConversation($conversationArray)
    {
        try 
        {
            Yii::$app->db->createCommand()->batchInsert('userconversation', ['conversationid', 'userid', 'isread', 'readtime'], 
                $conversationArray)
            ->execute();
            return true;;
        } catch (ErrorException $e) {
            return false;
        }
    }

     /**
     * Save user conversation details
     * @param $conversationArray array
    */
    public static function saveUserConversationChat($conversationId, $userId, $isRead, $readTime)
    {
        try 
        {
            Yii::$app->db->createCommand('INSERT INTO userconversation(conversationid, userid, isread,readtime) VALUES(:conversationid, :userid, :isread, :readTime)')
                ->bindValue(':conversationid', $conversationId)
                ->bindValue(':userid', $userId)
                ->bindValue(':isread', $isRead)
                ->bindValue(':readTime', $readTime)
            ->execute();
            return true;;
        } catch (ErrorException $e) {
            return false;
        }
    }      
    public static function getUserConversationCreatedCount($memberUserId){
    	
    	try {
    	
    		$conversationCount = Yii::$app->db->createCommand("select count(userconversationid) as userconversationcount from userconversation where userid =:memberuserid")
    		->bindValue(':memberuserid', $memberUserId)
    		->queryOne();
    		return $conversationCount;
    	} catch (Exception $e) {
    		return false;
    	}
    }
    public static function updateUserConversationUser($userId, $memberUserId){
    	
    	$sql = "update userconversation set userid = :userid where userid = :memberuserid";
    	try{
    		$committeeUpdate = Yii::$app->db->createCommand($sql)
    		->bindValue(':userid', $userid)
    		->bindValue(':memberuserid', $memberUserId)
    		->execute();
    	}catch (Exception $e) {
    		return false;
    	}
    }
}
