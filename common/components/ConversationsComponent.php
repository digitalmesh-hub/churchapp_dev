<?php
namespace common\components;

use Yii;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use common\models\extendedmodels\ExtendedConversationtopic;

class ConversationsComponent extends component
{

	/**
     * getConversationSubjectTitle.
     * @param integer $institutionId
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
    */
	public function getConversationSubjectTitle($institutionId)
	{
		$subjectTitleModel = ArrayHelper::map(
        		ExtendedConversationtopic::find()
        		->select('conversationtopicid,subjecttitle')
        		->where(['institutionid' => $institutionId])
        		->orderBy('subjecttitle')
        		->all(),
        		'conversationtopicid','subjecttitle'
        		);
		return $subjectTitleModel;
	}

	/**
     * getMemberName
     * @param integer $institutionId
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
    */
	public function getMemberName($institutionId)
	{
		$membeModel = Yii::$app->db->createCommand("CALL get_membername_conversation(:institutionid)")
		->bindValue(':institutionid' , $institutionId )
		->queryAll();
		return $membeModel;
	}

	/**
     * getMemberName
     * @param integer $institutionId,integer $search_title, 
		string $search_word, date $start_date, date $end_date, integer $member_name
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
    */
	public function getConversationsTopicList($institutionId, $search_title, 
		$search_word, $start_date, $end_date, $member_name)
	{
		$conversationList = Yii::$app->db->createCommand("CALL get_conversation_topiclist(:institutionid, :memberid, :conversationtopicid, :subject, :startdatetime, :enddatetime)")
		->bindValue(':institutionid' , $institutionId )
		->bindValue(':memberid' , $member_name)
		->bindValue(':conversationtopicid' ,  $search_title)
		->bindValue(':subject', $search_word)
		->bindValue(':startdatetime', $start_date)
		->bindValue(':enddatetime', $end_date)
		->queryAll();
		return $conversationList;
	}

	/**
     * getConversationByTopic
     * @param integer $topicId
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
    */
	public function getConversationByTopic($topicId)
	{
		$topicList = Yii::$app->db->createCommand("CALL get_conversationdetails_under_conversation_topic(:conversationtopicid)")
		->bindValue(':conversationtopicid' , $topicId)
		->queryAll();
		return $topicList;
	}
	
	/**
     * getConversationSubjectTitleByTopic
     * @param integer $topicId
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
    */
	public function getConversationSubjectTitleByTopic($topicId)
	{
		$subjectTitle = Yii::$app->db->createCommand("CALL get_conv_details(:conversationtopicid)")
		->bindValue(':conversationtopicid' , $topicId)
		->queryAll();
		return $subjectTitle;
	}
}