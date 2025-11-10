<?php

namespace common\models\searchmodels;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\extendedmodels\ExtendedUserprofile;

/**
 * UserSearch represents the model behind the search form about `common\models\extendedmodels\ExtendedUserprofile`.
 */
class UserSearch extends ExtendedUserprofile
{
	public $FullName;
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'userid', 'usertype', 'institutionid'], 'integer'],
            [['firstname', 'middlename', 'lastname', 'FullName', 'emailid', 'mobilenumber', 'photo'], 'safe'],
            [['isactive'], 'boolean'],
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
        $query = ExtendedUserprofile::find()->where('userid != :UserId', [':UserId' => yii::$app->user->id]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            	
        ]);

        $query->andFilterWhere(['like', "CONCAT_WS(firstname, ' ', middlename, ' ', lastname)", $this->FullName])
            ->andFilterWhere(['like', 'emailid', $this->emailid])
            ->andFilterWhere(['like', 'mobilenumber', $this->mobilenumber]);
        
      	if(yii::$app->user->identity->usertype == "A"){
            $query->andFilterWhere(['=', 'institutionid', yii::$app->user->identity->institutionid]);
      	}
        return $dataProvider;
    }
}
