<?php

namespace common\models\searchmodels;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\extendedmodels\ExtendedFeedback;

/**
 * ExtendedFeedbackSearch represents the model behind the search form of `common\models\extendedmodels\ExtendedFeedback`.
 */
class ExtendedFeedbackSearch extends ExtendedFeedback
{
    /**
     * @inheritdoc
     */
    public $start_date;
    public $end_date;

    public function rules()
    {
        return [
            [['feedbacktypeid','feedbackrating', 'institutionid'], 'integer'],
            [['start_date', 'end_date'], 'date', 'format' => 'php:d M Y'],
            ['end_date', 'validateEndDate']
        ];

    }
           
    public function validateEndDate($attribute, $params, $validator)
    {   
        if (strtotimeNew($this->end_date.'23:59:00') < strtotimeNew($this->start_date)) {
            $this->addError('end_date', 'End Date should be greater or equal to Start Date');
        } 
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
        $this->institutionid = yii::$app->user->identity->institutionid;
        $query = ExtendedFeedback::find()
        ->where(['institutionid' => $this->institutionid]);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
            'defaultOrder' => [
              'createddatetime' => SORT_DESC,
            ]
          ],
        ]);
        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        if($this->start_date && $this->end_date) {
            $query->andFilterWhere([
                  'between', 'createddatetime', date(Yii::$app->params['dateFormat']['sqlDandTFormat'],strtotimeNew($this->start_date."00:00:00")),
                    date(Yii::$app->params['dateFormat']['sqlDandTFormat'], strtotimeNew($this->end_date."23:59:00"))
              ]
            ); 
        }
        if($this->feedbackrating) {
          // grid filtering conditions
            $query->andFilterWhere(['feedbackrating' => $this->feedbackrating]);
        } 
        if($this->feedbacktypeid){
          // grid filtering conditions
            $query->andFilterWhere(['feedbacktypeid' => $this->feedbacktypeid]);

        }
        return $dataProvider;
    }
}
