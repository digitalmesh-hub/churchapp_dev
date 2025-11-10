<?php

namespace common\models\searchmodels;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\extendedmodels\ExtendedAlbum;
use yii\data\SqlDataProvider;

/**
 * ExtendedAlbumSearch represents the model behind the search form of `common\models\extendedmodels\ExtendedAlbum`.
 */
class ExtendedAlbumSearch extends ExtendedAlbum
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
           
            [['albumname','activatedon','expirydate'], 'safe'],
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
    	
        $query = (new \yii\db\Query())
			        ->select(['alimg.*','al.albumname','al.ispublished','e.activatedon','e.activitydate','e.venue','e.expirydate'])
			        ->from('albumimage alimg')
			        ->join('INNER JOIN', 'album al', 'al.albumid=alimg.albumid')
			        ->join('INNER JOIN', 'events e', 'al.eventid=e.id')
         			->where(['iscover' => 1])
         			->andWhere(['e.institutionid' => $institutionId])
                    ->orderBy('alimg.createddatetime desc');
   
		$dataProvider = new ActiveDataProvider([
			        		'query' => $query,
				'pagination' => [
						'pageSize' => 300,
				],
		]);
		
        $this->load($params);
       

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
      
        $query->andFilterWhere(['like', 'albumname', $this->albumname]);
        if (!empty($params['eventStartDate']) && !empty($params['eventEndDate'])) {
        	$query->andFilterWhere(
        			[
        					'between', 'e.activitydate', date('Y-m-d H:i:s',strtotimeNew($params['eventStartDate'].'00:00:00')),
        					date('Y-m-d H:i:s',strtotimeNew($params['eventEndDate'].'23:59:59'))
        			]
        			);
        }
        if (!empty($params['eventStartDate']) && empty($params['eventEndDate'])) {
            $query->andFilterWhere(
                    [
                            'between', 'e.activitydate', date('Y-m-d H:i:s',strtotimeNew($params['eventStartDate'].'00:00:00')),
                            date('Y-m-d H:i:s',strtotimeNew($params['eventStartDate'].'23:59:59'))
                    ]
                    );
        }
         if (empty($params['eventStartDate']) && !empty($params['eventEndDate'])) {
            $query->andFilterWhere(
                    [
                            'between', 'e.activitydate', date('Y-m-d H:i:s',strtotimeNew($params['eventEndDate'].'00:00:00')),
                            date('Y-m-d H:i:s',strtotimeNew($params['eventEndDate'].'23:59:59'))
                    ]
                    );
        }
        return $dataProvider->getModels();
    }
}
