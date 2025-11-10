<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "dashboard".
 *
 * @property int $dashboardid
 * @property string $description
 * @property string $imageurl
 * @property string $iconurl
 *
 * @property Institutiondashboard[] $institutiondashboards
 */
class Dashboard extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dashboard';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description', 'imageurl', 'iconurl'], 'required'],
            [['description'], 'string', 'max' => 100],
            [['imageurl', 'iconurl'], 'string', 'max' => 300],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'dashboardid' => 'Dashboardid',
            'description' => 'Description',
            'imageurl' => 'Imageurl',
            'iconurl' => 'Iconurl',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitutiondashboards()
    {
        return $this->hasMany(Institutiondashboard::className(), ['dashboardid' => 'dashboardid']);
    }
}
