<?php

namespace common\models\custom;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use common\models\extendedmodels\ExtendedBevcoProducts;
use common\models\extendedmodels\ExtendedBevcoCategory;

/**
 */
class BevcoItemsForm extends Model
{
    public $product_id;
    public $quantity;
    public $institution_id;
    public $category;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'quantity', 'category'], 'required'],
            [['quantity'], 'integer', 'min' => 1]
        ];
    }

    public function attributeLabels()
    {
        return [
            'product_id' => 'Product'
        ];
    }

    public function init()
    {
        parent::init();
        $this->institution_id = yii::$app->user->identity->institutionid;
    }


    public function getProducts()
    {
        return ArrayHelper::map(
            ExtendedBevcoProducts::find()
            ->joinWith('category')
            ->where(['institution_id' => $this->institution_id])
            ->andWhere(['bevco_products.is_available' => ExtendedBevcoProducts::IS_AVAILABLE])
            ->andWhere(['bevco_category.is_available' => ExtendedBevcoCategory::IS_AVAILABLE])
            ->all(), 'id', 'name');
    }

    public function getCategories()
    {
        return ArrayHelper::map( ExtendedBevcoCategory::find()->select(['id', 'name'])
            ->where([
                'is_available' => ExtendedBevcoCategory::IS_AVAILABLE,
                'institution_id' => $this->institution_id
            ])->orderBy('name')->all(),'id','name'
        );
    }

}