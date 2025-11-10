<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Migration;

/**
 * This is the model class for table "migration".
 *
 * @property string $version
 * @property int $apply_time
 */
class ExtendedMigration extends Migration
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'migration';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['version'], 'required'],
            [['apply_time'], 'integer'],
            [['version'], 'string', 'max' => 180],
            [['version'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'version' => 'Version',
            'apply_time' => 'Apply Time',
        ];
    }
}
