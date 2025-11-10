<?php

namespace common\models\searchmodels;

use Yii;
use common\models\extendedmodels\ExtendedMonthlySubscriptionFee;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for searching table "monthly_subscription_fee".
 *
 * @property integer $id
 * @property string $create_at
 * @property double $amount
 * @property string $description
 * @property string $transactionDate
 * @property integer $userId
 * @property integer $institutionId
 * @property integer $status
 */
class ExtendedSubscriptionFeeSearch extends ExtendedMonthlySubscriptionFee
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'monthly_subscription_fee';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_at', 'transactionDate'], 'safe'],
            [['amount', 'description', 'userId', 'institutionId','transactionDate'], 'required'],
            [['amount'], 'number',
            'min' => yii::$app->params['subscriptionFee']['minAmount'],
            'message' => 'Invalid amount',],
            [['userId', 'institutionId', 'status'], 'integer'],
            [['description'], 'string', 'max' => 256],
        ];
    }
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }
    public function search()
    {
        //print_r($this->transactionDate);die;
        $query = ExtendedMonthlySubscriptionFee::find()
        ->where(
            ['institutionId' => yii::$app->user->identity->institutionid
        ]
        );

        $dataProvider = new ActiveDataProvider(
            [
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                'create_at' => SORT_DESC
            ]
            ],
            'pagination' => [
        'pageSize' => 20,
        ],
        ]
        );

        return $dataProvider;
    }
}
