<?php 

namespace common\models\searchmodels;

use common\models\extendedmodels\ExtendedConversation;
use yii\base\Model;
use yii;
use yii\data\ArrayDataProvider;

class ConversationSearchModel extends ExtendedConversation
{
    
    public $search_title;
    public $search_word;
    public $member_name;
    public $start_date;
    public $end_date;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['search_title','search_word','member_name'],'string'],
            [['start_date','end_date'],'safe'],
            [['start_date','end_date'],'required'],
            [['search_title','search_word','member_name'],'trim']
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {   
        $this->load($params);
        $dataProvider = [];
        try {
            $this->start_date = date_format(date_create($this->start_date), Yii::$app->params['dateFormat']['sqlDateFormat']);
            $this->end_date = date_format(date_create($this->end_date), Yii::$app->params['dateFormat']['sqlDateFormat']);
            $this->member_name = intval($this->member_name);
            $this->search_title = intval($this->search_title);

            $data = Yii::$app->db->createCommand("CALL get_conversation_topiclist(:institutionid, :memberid, :conversationtopicid, :subject, :startdatetime, :enddatetime)")
                ->bindValue(':institutionid' , yii::$app->user->identity->institutionid)
                ->bindValue(':memberid' , $this->member_name)
                ->bindValue(':conversationtopicid' ,  $this->search_title)
                ->bindValue(':subject', $this->search_word)
                ->bindValue(':startdatetime', $this->start_date)
                ->bindValue(':enddatetime', $this->end_date)
                ->queryAll();

             $dataProvider = new ArrayDataProvider([
                'allModels' => $data,
                'pagination' => [
                    'pageSize' => 10,
                ],
            ]);
              
        } catch (Exception $e) {
            yii::error('Error while fetching data'.$e->getMessage());
        }
        return $dataProvider;
    }
}
