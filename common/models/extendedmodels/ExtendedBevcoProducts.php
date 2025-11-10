<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\BevcoProducts;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

class ExtendedBevcoProducts extends BevcoProducts
{   
    const IS_AVAILABLE = 1;
    const IS_NOT_AVAILABLE = 0;

    public function rules()
    {
        return [
            [['category_id', 'name', 'price'], 'required'],
            [['category_id', 'is_available'], 'integer'],
            [['price'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'image'], 'string', 'max' => 150],
            [['description'], 'string', 'max' => 255],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedBevcoCategory::className(), 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

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

    public function getCategories()
    {
        return ArrayHelper::map( ExtendedBevcoCategory::find()->select(['id', 'name'])
            ->where([
                'is_available' => ExtendedBevcoCategory::IS_AVAILABLE,
                'institution_id' => yii::$app->user->identity->institutionid
            ])->orderBy('name')->all(),'id','name'
        );
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(ExtendedBevcoCategory::className(), ['id' => 'category_id']);
    }
 
}
