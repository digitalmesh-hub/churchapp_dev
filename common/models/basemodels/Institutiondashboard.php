<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "institutiondashboard".
 *
 * @property int $institutiondashboardid
 * @property int $dashboardid
 * @property int $institutionid
 * @property int $sortorder
 * @property int $isactive
 *
 * @property Dashboard $dashboard
 * @property Institution $institution
 */
class Institutiondashboard extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'institutiondashboard';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dashboardid', 'institutionid', 'sortorder'], 'required'],
            [['dashboardid', 'institutionid', 'sortorder'], 'integer'],
            [['isactive'], 'string', 'max' => 4],
            [['dashboardid'], 'exist', 'skipOnError' => true, 'targetClass' => Dashboard::className(), 'targetAttribute' => ['dashboardid' => 'dashboardid']],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'institutiondashboardid' => 'Institutiondashboardid',
            'dashboardid' => 'Dashboardid',
            'institutionid' => 'Institutionid',
            'sortorder' => 'Sortorder',
            'isactive' => 'Isactive',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDashboard()
    {
        return $this->hasOne(Dashboard::className(), ['dashboardid' => 'dashboardid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitution()
    {
        return $this->hasOne(Institution::className(), ['id' => 'institutionid']);
    }
}
