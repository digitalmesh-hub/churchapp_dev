<?php
namespace common\models\formmodels;

use yii\base\Model;

/**
 * Signup form
 */
class SurveyLoginFormModel extends Model
{
    public $username;
    public $password;

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'string', 'min' => 2, 'max' => 255],
            ['password', 'required'],
            ['password', 'string', 'min' => 6],
             [['password'],'match', 'pattern' =>'/^.*(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).*$/'],
        ];
    }
    public function createSurveySignup()
    {
        if (true) {
            return ['success' => true];
        } else {
            return ['success' => false, 'errors' => 'test'];
        }
    }
}
