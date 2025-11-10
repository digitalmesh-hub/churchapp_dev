<?php

namespace common\models\searchmodels;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\extendedmodels\ExtendedAffiliatedinstitution;

/**
 * ExtendedAffiliatedinstitutionSearch represents the model behind the search form of `common\models\extendedmodels\ExtendedAffiliatedinstitution`.
 */
class ExtendedAffiliatedinstitutionSearch extends ExtendedAffiliatedinstitution
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['affiliatedinstitutionid', 'institutionid', 'CountryID', 'createduser', 'modifieduser'], 'integer'],
            [['name', 'address1', 'address2', 'district', 'state', 'pin', 'phone1_countrycode', 'phone1_areacode', 'phone1', 'location', 'mobilenocountrycode', 'phone2', 'email', 'active', 'phone3_countrycode', 'phone3_areacode', 'phone3', 'url', 'institutionlogo', 'presidentname', 'presidentmobile', 'presidentmobile_countrycode', 'secretaryname', 'secretarymobile', 'secretarymobile_countrycode', 'meetingvenue', 'meetingday', 'meetingtime', 'remarks'], 'safe'],
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
    public function search($params, $institutionid, $isRotary)
    {
        $query = ExtendedAffiliatedinstitution::find();

        // add conditions that should always apply here
        $this->institutionid = $institutionid;
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [ 'pageSize' => 10 ],
            'sort' => [
                'defaultOrder' => [
                'name' => SORT_ASC, 
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'affiliatedinstitutionid' => $this->affiliatedinstitutionid,
            'institutionid' => $this->institutionid,
            'CountryID' => $this->CountryID,
            'createduser' => $this->createduser,
            'modifieduser' => $this->modifieduser,
        ]);

        if($isRotary){
            $query->andFilterWhere(['like', 'name', $this->name])
                ->andFilterWhere(['like', 'meetingvenue', $this->meetingvenue])
                ->andFilterWhere(['like', 'meetingday', $this->meetingday])
                ->andFilterWhere(['like', 'meetingtime', $this->meetingtime]);
        }
        else{
            $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'district', $this->district])
            ->andFilterWhere(['like', 'state', $this->state])
            ->orFilterWhere(['like', 'phone1_countrycode', $this->phone1])
            ->orFilterWhere(['like', 'phone1_areacode', $this->phone1])
            ->orFilterWhere(['like', 'phone1', $this->phone1]);
        }
        return $dataProvider;
    }
}