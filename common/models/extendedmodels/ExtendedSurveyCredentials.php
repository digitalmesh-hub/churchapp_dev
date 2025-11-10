<?php

namespace common\models\extendedmodels;

use common\models\basemodels\SurveyCredentials;
use Yii;

class ExtendedSurveyCredentials extends SurveyCredentials
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password', 'institutionid', 'createddatetime', 'createdby'], 'required'],
            [['institutionid', 'createdby', 'modifiedby'], 'integer'],
            [['createddatetime', 'modifieddatetime'], 'safe'],
            [['username', 'password'], 'string', 'max' => 100],
            [['token'], 'string', 'max' => 38],
            [['institutionid'], 'unique'],
            [['username'], 'unique'],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedInstitution::className(), 'targetAttribute' => ['institutionid' => 'id']],
            [['modifiedby'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedUsercredentials::className(), 'targetAttribute' => ['modifiedby' => 'id']],
            [['createdby'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedUsercredentials::className(), 'targetAttribute' => ['createdby' => 'id']],
            [['username'],'trim']
        ];
    }
    public function createSurveyCredentials()
    {
        if ($this->save(true)) {
            return ['success' => true];
        } else {
            return ['success' => false, 'errors' => $this->getErrors()];
        }
    }

    /**
     * Get survey credentials
     * @param $institutionId
     */
    public static function getSurveyCredentials($institutionId){
        
        try
        {
            $credentials = Yii::$app->db->createCommand("SELECT `surveycredentialsid`,`username`,`password`,`token`,`institutionid` FROM `surveycredentials`WHERE `institutionid` = :institutionid")
            ->bindValue(':institutionid' , $institutionId);
            $credentials = $credentials->queryOne();
            return $credentials;
        }
        catch(ErrorException $e){
            return false;
        }
    }
}
