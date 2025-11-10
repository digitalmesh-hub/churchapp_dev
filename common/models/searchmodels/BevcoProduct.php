<?php

namespace common\models\searchmodels;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\extendedmodels\ExtendedBevcoProducts;


class BevcoProduct extends ExtendedBevcoProducts
{
    public $institution_id;


    public function init()
    {
        parent::init();
        $this->institution_id = yii::$app->user->identity->institutionid;
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'category_id'], 'integer'],
            [['name', 'price'], 'string'],
            [['name'], 'trim']
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
        $query = ExtendedBevcoProducts::find();
        $query->joinWith('category');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider(
            [
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ]
            ],
        ]
        );

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'institution_id' => $this->institution_id,
            'category_id' => $this->category_id,
            'price' => $this->price,
            'bevco_products.id' => $this->id
        ]);

        $query->andFilterWhere(['like', 'bevco_products.name', $this->name]);
        return $dataProvider;
    }
}
