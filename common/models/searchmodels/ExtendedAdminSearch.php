<?php 

namespace common\models\searchmodels;

use yii\data\ActiveDataProvider;
use common\models\extendedmodels\ExtendedUserProfile;
use yii\base\Model;
use yii;

class ExtendedAdminSearch extends ExtendedUserProfile
{
    
    public $fullName;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fullName',], 'string', 'max' => 150],
            [['fullName'],'trim'],
            [['institutionid'],'integer']
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
       
        $query = (new \yii\db\Query())
                ->select(
                    [
                    'us.id as profileId',
                    'in.name as institution_name',
                    'CONCAT_WS(" ", us.firstname, us.middlename, us.lastname) as fullName',
                    'us.isactive',
                    'in.id',
                    'us.userid'
                    ]
                )
                ->from('userprofile us')
                ->join('INNER JOIN', 'auth_assignment asg', 'asg.user_id = us.userid')
                ->join('INNER JOIN', 'auth_item ai', 'ai.name = asg.item_name')
                ->join('INNER JOIN', 'role r', 'r.roleid = ai.name')
                ->join('INNER JOIN', 'rolecategory rc', 'rc.RoleCategoryID = r.rolecategoryid')
                ->join('INNER JOIN', 'rolegroup rg', 'rg.RoleGroupID = rc.RoleGroupId')
                ->join('INNER JOIN', 'institution in', 'in.id = us.institutionid')
                ->where(['rg.Description' => 'Admin']);
              
        $dataProvider = new ActiveDataProvider(
            [
            'query' => $query,
        ]
        );
        $query->addOrderBy(['CONCAT_WS(" ", us.firstname, us.middlename, us.lastname)' => SORT_ASC]);
        
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([ 'in.id' => $this->institutionid]);
        $query->andFilterWhere(
            [
            'or',
            ['like', 'us.firstname', $this->fullName],
            ['like', 'us.lastname', $this->fullName],
            ['like', 'us.middlename', $this->fullName],
            ['like', 'CONCAT_WS("",us.firstname, us.middlename, us.lastname)', $this->fullName],
            ['like', 'CONCAT_WS(" ",us.firstname, us.middlename, us.lastname)', $this->fullName],
            ['like', 'CONCAT_WS(" ",us.firstname, us.lastname)', $this->fullName],
        ]
        ); 
        return $dataProvider;
    }
}
