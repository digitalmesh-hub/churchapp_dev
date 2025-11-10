<?php
namespace common\components\admin\widgets;

use yii\base\Widget;
use common\models\searchmodels\ExtendedAdminSearch;
use yii;
use yii\helpers\ArrayHelper;
use common\models\extendedmodels\ExtendedInstitution;

/**
 * Created by amal
 * Date: 15/03/18
 * Time: 10:52 PM
 */

class ListAdminWidget extends Widget
{

    public $searchClass;
    public $dataProvider;
    public $searchModel;
    public $institutions;
    
    public function init()
    {
        $this->searchClass = ExtendedAdminSearch::className();
        $this->searchModel = new $this->searchClass();
        $this->dataProvider = $this->searchModel->search(Yii::$app->request->queryParams);
        $this->institutions = $this->getInstitutions();
        parent::init();
    }
    public function run()
    {
        return $this->render(
            '_viewAdminPager.php',
            [
            'dataProvider' => $this->dataProvider,
            'searchModel' => $this->searchModel,
            'institutions' => $this->institutions
            ]
        );
    }
    protected function getInstitutions()
    {
        return ArrayHelper::map(
            ExtendedInstitution::find()
            ->select(['id', 'name'])
            ->orderBy('name')->all(),
            'id',
            'name'
        );
    }
}