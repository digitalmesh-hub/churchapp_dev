<?php

namespace common\models\extendedmodels;

use Yii;
use yii\base\ErrorException;
use common\models\basemodels\Conversation;
use Exception;

/**
 * This is the model class for table "conversation".
 *
 * @property int $conversationid
 * @property int $conversationtopicid
 * @property string $conversation
 * @property int $createdby
 * @property string $createddatetime
 *
 * @property Usercredentials $createdby0
 * @property Conversationtopic $conversationtopic
 * @property Userconversation[] $userconversations
 */
class ExtendedConversation extends Conversation
{

	/**
	 * To get conversations under topic
	 * @param $topicId int
	 * @return $conversations array
	 */
	public static function getConversationsUnderTopic($topicId)
	{
		try {
			$conversations = Yii::$app->db->createCommand(
    				"CALL get_conversationdetails_under_conversation_topic(:conversationtopicid)")
    				->bindValue(':conversationtopicid', $topicId)
    				->queryAll();
			return $conversations;
		} 
		catch (ErrorException $e) {
			yii::error($e->getMessage());
			return false;
		}
	}

	/**
	 * To get conversations details
	 * @param topicId int
	 * @return $conversations array
	 */
	public static function getConversationsDetails($topicId)
	{
		try {
			$conversations = Yii::$app->db->createCommand(
    				"CALL get_conversationdetails(:conversationtopicid)")
    				->bindValue(':conversationtopicid', $topicId)
    				->queryOne();
			return $conversations;
		} 
		catch (ErrorException $e) {
			return false;
		}
	}

	/**
	 * Create conversation
	 * @param $conversationId int
	 * @param $chatContent string
	 * @param $userId int
	 * @param $dateTime dateTime
	 * @return boolean value
	 */
	public static function createConversation($conversationId, $chatContent, $userId, $dateTime)
	{
		try {
			$conversations = Yii::$app->db->createCommand("INSERT INTO 
				conversation(conversationtopicid,conversation,createdby, 	
				createddatetime) VALUES (:conversationtopicid, :conversation, :createdby, :createddatetime)")
				->bindValue(':conversationtopicid', $conversationId)
				->bindValue(':conversation', $chatContent)
				->bindValue(':createdby', $userId)
				->bindValue(':createddatetime', $dateTime)
				->execute();
			$id = Yii::$app->db->getLastInsertID();
			return $id;
		} 
		catch (ErrorException $e) {
			return false;
		}
	}

	/**
	 * To get conversations recepients
	 * @param topicId int
	 * @return $conversations array
	 */
	public static function getConversationsRecepients($topicId)
	{
		try {
			$userList = Yii::$app->db->createCommand(
    				"CALL get_conversation_recipients(:conversationtopicid)")
    				->bindValue(':conversationtopicid', $topicId)
    				->queryAll();
    		return $userList;
		} 
		catch (ErrorException $e) {
			return false;
		}
	}

	/**
	 * To get the conversation count
	 * @param unknown $userId
	 * @param unknown $memberUserId
	 * @param unknown $institutionID
	 * @return boolean
	 */
	
	public static function getConversationCreatedCount($memberUserId){
	
		$sql = "select count(conversationid) as conversationcount from conversation where createdby = :memberuserid";
		try{
			$conversationsCount = Yii::$app->db->createCommand($sql)
			->bindValue(':memberuserid', $memberUserId)
			->queryAll();
			return $conversationsCount;
		}catch (Exception $e) {
			return false;
		}
	}
	/**
	 * To update the conversation created user
	 * @param unknown $userId
	 * @param unknown $memberUserId
	 * @return boolean
	 */
	public static function updateConversationCreated($userId, $memberUserId){
		
		$sql = "update conversation set createdby = :userid where createdby = :memberuserid";
		
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
