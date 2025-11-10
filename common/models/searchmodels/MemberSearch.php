<?php

namespace common\models\searchmodels;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\extendedmodels\ExtendedMember;

/**
 * MemberSearch represents the model behind the search form about `common\models\basemodels\Member`.
 */
class MemberSearch extends ExtendedMember
{
    public $FullName;
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['memberid', 'institutionid', 'membertitle', 'spousetitle', 'countrycode', 'areacode', 'membertype', 'staffdesignation', 'familyunitid'], 'integer'],
            [['memberno', 'membershiptype', 'membersince', 'FullName', 'firstName', 'middleName', 'lastName', 'business_address1',
            		'business_address2', 'business_address3', 'business_district', 'business_state', 'business_pincode', 'member_dob',
            		'member_mobile1', 'member_mobile2', 'member_musiness_Phone1', 'member_business_Phone2', 'member_residence_Phone1',
            		'member_residence_Phone2', 'member_email', 'spouse_firstName', 'spouse_middleName', 'spouse_lastName', 'spouse_dob',
            		'dom', 'spouse_mobile1', 'spouse_mobile2', 'spouse_email', 'residence_address1', 'residence_address2',
            		'residence_address3', 'residence_district', 'residence_state', 'residence_pincode', 'member_pic', 'spouse_pic',
            		'app_reg_member', 'app_reg_spouse', 'businessemail', 'membernickname', 'spousenickname', 'lastupdated',
            		'createddate', 'homechurch', 'occupation', 'spouseoccupation', 'member_mobile1_countrycode',
            		'spouse_mobile1_countrycode', 'member_business_phone1_countrycode', 'member_business_phone1_areacode',
            		'member_business_phone2_countrycode', 'memberImageThumbnail', 'spouseImageThumbnail', 'member_business_Phone3',
            		'member_business_phone3_countrycode', 'member_business_phone3_areacode', 'newmembernum', 'memberbloodgroup',
            		'spousebloodgroup', 'member_residence_phone1_areacode', 'member_residence_Phone1_countrycode',
            		'member_residence_phone2_areacode', 'member_residence_Phone2_countrycode', 'companyname',
            		
            'member_business_phone2_areacode'], 'safe'],
            [['active'], 'boolean'],
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
    	$query = ExtendedMember::find()->where('institutionid = :InstitutionId', [':InstitutionId' => yii::$app->user->identity->institutionid]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            	'query' => $query,
        ]);
		
        $dataProvider->setSort([
        		'defaultOrder' => ['FullName'=>SORT_ASC],
        		'attributes' => [
        				'FullName' => [
        						'asc' => ['firstName' => SORT_ASC, 'middleName' => SORT_ASC,'lastName' => SORT_ASC],
        						'desc' => ['firstName' => SORT_DESC,'middleName' => SORT_DESC, 'lastName' => SORT_DESC],
        						'label' => 'Full Name',
        						'default' => SORT_ASC
        				],
        				'memberno',
        				'member_email',
        				'member_mobile1'
        		]
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

        $query->andFilterWhere(['like', "CONCAT_WS(' ', firstName, middleName, lastName)", $this->FullName])
        ->andFilterWhere(['like', 'memberno', $this->memberno])
        ->andFilterWhere(['like', 'member_email', $this->member_email])
        ->andFilterWhere(['like', 'member_mobile1', $this->member_mobile1]);
        
        return $dataProvider;
    }
}
