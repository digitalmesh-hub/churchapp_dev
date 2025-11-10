<?php

namespace common\models\extendedmodels;

use Yii;
use yii\helpers\ArrayHelper;
use common\models\basemodels\BevcoCategory;
use common\models\extendedmodels\ExtendedInstitution;
use common\models\extendedmodels\ExtendedUserCredentials;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use common\models\custom\BevcoCategoryCustomData;
use ArrayObject;

class ExtendedBevcoCategory extends BevcoCategory
{
    const IS_AVAILABLE = 1;
    const IS_NOT_AVAILABLE = 0;

    public $_customDataObject;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'institution_id', 'created_by'], 'required'],
            [['is_available', 'institution_id', 'created_by'], 'integer'],
            [['created_at', 'updated_at','custom_data'], 'safe'],
            [['name'], 'string', 'max' => 100],
            [['description'], 'string', 'max' => 255],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedUserCredentials::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['institution_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedInstitution::className(), 'targetAttribute' => ['institution_id' => 'id']],
            ['is_available', 'default', 'value' => self::IS_NOT_AVAILABLE],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public function load($data, $formName = null)
    {
        if (!$ok = parent::load($data,$formName))
            return false;

        $this->custom_data = self::extractFormCustomData($data);
        return true;
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        parent::afterFind();
        if ($this->hasAttribute('custom_data')) {
            $startingData = $this->custom_data ? json_decode($this->custom_data,JSON_OBJECT_AS_ARRAY) : [];
            $this->_customDataObject = new ArrayObject($startingData);
        }
    }

    /**
     *
    */
    public static function extractFormCustomData($data)
    {
        $model = new BevcoCategoryCustomData();
        $custom_data = [];
        
        if($model->load($data) && $model->validate()) {
            $custom_data = ArrayHelper::toArray($model);
        }
        return json_encode($custom_data);
    }

    public function convertCustomDataToModels()
    {
        $customDataModel = new BevcoCategoryCustomData();
        foreach ($this->_customDataObject as $key => $value) {
            if(property_exists($customDataModel, $key)){
                $customDataModel->$key = $value;
            }
        }
       return $customDataModel;
    }
}