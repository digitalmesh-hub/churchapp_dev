<?php

namespace common\models\searchmodels;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\extendedmodels\ExtendedPrayerrequest;

/**
 * ExtendedPrayerrequestSearch represents the model behind the search form of `common\models\extendedmodels\ExtendedPrayerrequest`.
 */
class ExtendedPrayerrequestSearch extends ExtendedPrayerrequest
{
    /**
     * @inheritdoc
     */
    
    public $created_time_start;
    public $created_time_end;

    public function rules()
    {
        return [
            [['institutionid'], 'integer'],
            [['created_time_start', 'created_time_end'], 'required'],
            [['created_time_start','created_time_end'], 'date', 'format' => 'php:d M Y'],
            ['created_time_end', 'validateExpiryDate'],   
        ];
    }

    public function validateExpiryDate($attribute, $params, $validator)
    {   
        if (strtotimeNew($this->created_time_end.'23:59:00') < strtotimeNew($this->created_time_start)) {
            $this->addError('created_time_end', 'End Date should be greater or equal to Start Date');
        } 
    }

    public function attributeLabels()
    { 
          return [
            'created_time_start' => 'Start Date',
            'created_time_end' => 'End Date', 
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
        $query = ExtendedPrayerrequest::find();
        $this->institutionid = yii::$app->user->identity->institutionid;
        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
             'sort' => [
            'defaultOrder' => [
              'createdtime' => SORT_DESC,
            ]
          ],
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'institutionid' => $this->institutionid
           ]

        );
        $query->andFilterWhere([
                  'between', 'createdtime', date(Yii::$app->params['dateFormat']['sqlDandTFormat'],strtotimeNew($this->created_time_start."00:00:00")),
                    date(Yii::$app->params['dateFormat']['sqlDandTFormat'], strtotimeNew($this->created_time_end."23:59:00"))
              ]
            ); 
        return $dataProvider;
    }
}
