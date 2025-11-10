<?php

namespace common\models\searchmodels;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\extendedmodels\ExtendedEvent;

/**
 * EventSearch represents the model behind the search form of `common\models\extendedmodels\ExtendedEvent`.
 */
class NewsSearch extends ExtendedEvent
{
    public $searchKeyword;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['searchKeyword'], 'string'],
            [['searchKeyword'],'trim']
            
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
    public function search($params,$institutionId)
    {
        $query = ExtendedEvent::find();
        $this->institutionid = $institutionId;


        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                     
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $query->andFilterWhere([
            'institutionid' => $this->institutionid,
            'eventtype' =>'A']

        );
        // $query->andFilterWhere(
        //     ['>=','expirydate',date('Y-m-d')]
        // );

     if($this->searchKeyword){
        $query->andFilterWhere(
           [
             'or',
            ['like', 'notehead', $this->searchKeyword],
               [
               'between', 'activitydate', date('Y-m-d H:i:s',strtotimeNew($this->searchKeyword.'00:00:00')),
               date('Y-m-d H:i:s',strtotimeNew($this->searchKeyword.'23:59:59'))
               ]
           ]   

        );
        }

        

//         $query->andFilterWhere(['like', 'notehead', $this->notehead])
//             ->andFilterWhere(['like', 'notebody', $this->notebody])
//             ->andFilterWhere(['like', 'noteurl', $this->noteurl])
//             ->andFilterWhere(['like', 'eventtype', $this->eventtype])
//             ->andFilterWhere(['like', 'venue', $this->venue])
//             ->andFilterWhere(['like', 'time', $this->time])
//             ->andFilterWhere(['like', 'rsvpavailable', $this->rsvpavailable])
//             ->andFilterWhere(['like', 'iseventpublishable', $this->iseventpublishable]);

        return $dataProvider;
    }
}
