<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Lookup;

/**
 * This is the model class for table "lookup".
 *
 * @property int $id
 * @property string $category
 * @property string $description
 * @property int $value
 */
class ExtendedLookup extends Lookup
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lookup';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['value'], 'required'],
            [['value'], 'integer'],
            [['category', 'description'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category' => 'Category',
            'description' => 'Description',
            'value' => 'Value',
        ];
    }
}
