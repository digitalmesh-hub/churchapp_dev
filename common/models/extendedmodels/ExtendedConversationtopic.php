<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Conversationtopic;
use common\models\extendedmodels\ExtendedCommittee;
use Exception;
use yii\base\ErrorException;


/**
 * This is the model class for table "conversationtopic".
 *
 * @property int $conversationtopicid
 * @property string $subjecttitle
 * @property string $subject
 * @property int $institutionid
 * @property int $createdby
 * @property string $createddatetime
 * @property int $modifiedby
 * @property string $modifieddatetime
 * @property int $isactive
 *
 * @property Conversation[] $conversations
 * @property Usercredentials $createdby0
 * @property Institution $institution
 * @property Usercredentials $modifiedby0
 * @property Userconversationtopic[] $userconversationtopics
 */
class ExtendedConversationtopic extends Conversationtopic
{
	/**
	 * To get the count of
	 * unread conversations
	 * @param $userId int
	 */
	public static function getUnreadConversationCount($userId)
	{   
		$unreadConversationCount = null;
		try {
			$institutionId = ExtendedCommittee::getAllCommitteeInstitutionOfUser($userId);
			//$institutionId = yii\helpers\ArrayHelper::getColumn($institutionId, 'institutionid');
			
			if ($institutionId) {
			    $institutionId = implode(', ', $institutionId);
				$unreadConversationCount = self::getTotalConversationCount($userId, $institutionId);
			}
			return $unreadConversationCount;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * To get the total count
	 * of conversations
	 * @param $userId int
	 * @param $institutions int
	 */
	public static function getTotalConversationCount($userId, $institutions)
	{
		try {
			$conversationCount = Yii::$app->db->createCommand(
								"CALL get_total_conversation_count(:userid, :institutions)")
								->bindValue(':userid', $userId)
								->bindValue(':institutions', $institutions)
								->queryOne();
			return $conversationCount;
			
		} catch (Exception $e) {
			return false;
		}
	}

	/**
     * Close coversation
     * @param int topicId 
     * @param int userId 
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
    */
    public function closeConversation($topicId, $userId)
    {
        try{
        	$topicList = Yii::$app->db->createCommand("SELECT conversationtopicid FROM conversationtopic WHERE conversationtopicid = :conversationtopicid AND createdby = :createdby AND isactive = 1")
            ->bindValue(':conversationtopicid', $topicId)
            ->bindValue(':createdby', $userId)
            ->execute();

            if($topicList){
            	$command = Yii::$app->db->createCommand("UPDATE conversationtopic SET isactive = 0 WHERE conversationtopicid = :conversationtopicid")
	            ->bindValue(':conversationtopicid' , $topicId);
	            $command->execute();
	            return true;
            }
            else{
            	return false;
            } 
        }
        catch(ErrorException $e){
        	yii::error($e->getMessage());
            return false;
        }
    }

    /**
	 * To get conversation topics
	 * @param $userId int
	 * @param $institutions string

	 */
	public static function getConversationTopics($userId, $institutions)
	{
		try {
			$conversations = Yii::$app->db->createCommand(
								"CALL get_conversation_topics(:userid,:institutions)")
								->bindValue(':userid', $userId)
								->bindValue(':institutions', $institutions)
								->queryAll();
			return $conversations;
			
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * To get participants in vonversation under topic
	 * @param $conversationTopicId int
	 * @param $institutions int
	 */
	public static function getAllCommitteeMemberUnderTopic($institutionId, $conversationTopicId)
	{
		try {
			$participants = Yii::$app->db->createCommand(
								"CALL get_committeemember_bytopic(:conversationtopicid, :institutionid)")
								->bindValue(':conversationtopicid', $conversationTopicId)
								->bindValue(':institutionid', $institutionId)
								->queryAll();
			return $participants;
			
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Add conversation topic
	 * @param $conversationTopic array
	 */
	public static function addConversationTopic($conversationTopic)
	{
		try {
			$topicId = Yii::$app->db->createCommand(
				"CALL create_conersation_topic(:subjecttitle, :subject, :institutionid, :createdby, :createddatetime)")
				->bindValue(':subjecttitle', $conversationTopic['subjectTitle'])
				->bindValue(':subject', $conversationTopic['subject'])
				->bindValue(':institutionid', $conversationTopic['institutionId'])
				->bindValue(':createdby', $conversationTopic['createdBy'])
				->bindValue(':createddatetime', $conversationTopic['createdDateTime'])
				->queryAll();
			return $topicId[0]['LAST_INSERT_ID()'];
			
		} catch (Exception $e) {
			return false;
		}
	}

}

