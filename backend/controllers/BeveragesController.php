<?php

namespace backend\controllers;

use Yii;
use \yii\base\Model;
use yii\helpers\Html;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use backend\controllers\BaseController;
use common\models\basemodels\BaseModel;
use common\models\custom\BevcoItemsForm;
use common\models\custom\BeverageBookingForm;
use common\models\custom\BevcoCategoryCustomData;
use common\models\extendedmodels\ExtendedBevcoSlots;
use common\models\extendedmodels\ExtendedBevcoCategory;
use common\models\extendedmodels\ExtendedBevcoSettings;
use common\models\extendedmodels\ExtendedBevcoProducts;
use common\models\searchmodels\ExtendedBevcoCategorySearch;
use common\models\searchmodels\BevcoOrder as BevcoOrderSearch;
use common\models\searchmodels\BevcoProduct as BevcoProductSearch;
use common\models\extendedmodels\ExtendedBevcoOrder;

/**
 * BeveragesController implements the CRUD actions.
 */
class BeveragesController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [],
            ],
        ];
    }

    public function beforeAction($action)
    {   
        $bookingActions = [
            'manage-booking',
            'new-booking',
            'complete-booking',
            'get-slots',
            'booking',
            'complete-order',
            'slot-lock',
            'slot-unlock',
            'get-products'
        ];

        if(in_array($action->id ,$bookingActions)) {
            //if manage BeveragesController permission is enabled user can access.
            if (!Yii::$app->user->can('d560f85e-46c4-4b14-b2b4-9e6d613f4d2b')) {
                throw new \yii\web\ForbiddenHttpException('You are not allowed to perform the requested action');
            }
        } else {
            //if manage BeveragesController permission is enabled user can access.
            if (!Yii::$app->user->can('9c22eb89-6f01-49b1-b7c5-10254330fcce')) {
                throw new \yii\web\ForbiddenHttpException('You are not allowed to perform the requested action');
            }
        }
    	return parent::beforeAction($action);
    }

    public function actionIndex()
    {   
        $viewData = [];
        
        $user = yii::$app->user->identity;
        $categorySearchModel = new ExtendedBevcoCategorySearch();
        $productSearchModel = new BevcoProductSearch();
        $settings =  ExtendedBevcoSettings::findOne(['institution_id' => $user->institutionid ]);
        $settings = ($settings) ? $settings : new ExtendedBevcoSettings();

        $categoryDataProvider = $categorySearchModel->search(Yii::$app->request->queryParams);
        $productsDataProvider = $productSearchModel->search(Yii::$app->request->queryParams);
        $viewData['category_search_model'] = $categorySearchModel;
        $viewData['category_data_provider'] = $categoryDataProvider;
        $viewData['product_search_model'] = $productSearchModel;
        $viewData['product_data_provider'] = $productsDataProvider;
        
        $settings->convertCustomDataToModels();
        $viewData['settings'] = $settings;

        return $this->render('index', $viewData);
    }

    public function actionCreateCategory()
    {

        $category_model = new ExtendedBevcoCategory();
        if (Yii::$app->request->isPost) {
             if ($category_model->load(Yii::$app->request->post())) {
                $user = yii::$app->user->identity;
                $category_model->created_by = $user->id;
                $category_model->institution_id = $user->institutionid;
                if ($category_model->validate() && $category_model->save(false)) {
                    $this->sessionAddFlashArray('success', 'Successfully added new category', true);
                    return $this->redirect(['index']);
                } else {
                    $this->sessionAddFlashArray('error', 'Unable to process you request', true);
                }
            }
        }

        $viewData['category_model'] = $category_model;
        $viewData['category_custom_data_model']  = new BevcoCategoryCustomData();

        return $this->render('create-category', $viewData);
    
    }

    protected function findCategoryModel($id)
    {
        if (($model = ExtendedBevcoCategory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    protected function findProductModel($id)
    {
        if (($model = ExtendedBevcoProducts::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findOrder($id)
    {
        if (($model = ExtendedBevcoOrder::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionCategoryActivate($id)
    {
        if (yii::$app->request->isAjax) {
            $model = $this->findCategoryModel($id);
            $model->is_available = ExtendedBevcoCategory::IS_AVAILABLE;
            if ($model->update()) {                     
                return $this->asJson(['success' => true]); 
            }  
        }
        return $this->asJson(['success' => false]); 
    }

    public function actionCategoryDeactivate($id)
    {
        if (yii::$app->request->isAjax) {
            $model = $this->findCategoryModel($id);
            $model->is_available = ExtendedBevcoCategory::IS_NOT_AVAILABLE;
            if ($model->update()) {
                return $this->asJson(['success' => true]); 
            }  
        }
        return $this->asJson(['success' => false]); 
    }

    public function actionEditCategory($id)
    {
       
        $category_model = $this->findCategoryModel($id);
        if (Yii::$app->request->isPost) {
             if ($category_model->load(Yii::$app->request->post())) {
                if ($category_model->validate() && $category_model->save(false)) {
                    $this->sessionAddFlashArray('success', 'Successfully updated category', true);
                    return $this->redirect(['index']);
                } else {
                    $this->sessionAddFlashArray('error', 'Unable to process you request', true);
                }
            }
        }

        $customCategoryDataModels = $category_model->convertCustomDataToModels();
        $viewData['category_model'] = $category_model;
        $viewData['category_custom_data_model']  = $customCategoryDataModels;

        return $this->render('edit-category', $viewData);

    }

    public function actionAddSettings()
    { 
        if (Yii::$app->request->isAjax) {
            $user = yii::$app->user->identity;
            $model =  ExtendedBevcoSettings::findOne(['institution_id' => $user->institutionid ]);
            $model = ($model) ? $model : new ExtendedBevcoSettings();
            if ($model->load(Yii::$app->getRequest()->getBodyParams())) {
                $model->institution_id = $user->institutionid;
                if($model->validate() && $model->save()) {
                    $this->sessionAddFlashArray('success', 'Setting successfully saved', true);
                    return $this->asJson(['success' => true]);
                }
            }
            $result = [];
            foreach ($model->getErrors() as $attribute => $errors) {
                $result[\yii\helpers\Html::getInputId($model, $attribute)] = $errors;
            }
            return $this->asJson(['validation' => $result]);
        }
        return $this->asJson(['success' => false]);
    }

    public function actionAddProduct()
    {
        $model = new ExtendedBevcoProducts();

        if (Yii::$app->request->isPost) {
             if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                if ($model->save(false)) {
                    $this->sessionAddFlashArray('success', 'Successfully added new product', true);
                    return $this->redirect(['index']);
                } else {
                    $this->sessionAddFlashArray('error', 'Unable to process you request', true);
                }
            }
        }

        $viewData['model'] = $model;
        return $this->render('create-product', $viewData);
    }

    public function actionEditProduct($id)
    {
        $model = $this->findProductModel($id);
        if (Yii::$app->request->isPost) {
             if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                if ($model->save(false)) {
                    $this->sessionAddFlashArray('success', 'Successfully updated product', true);
                    return $this->redirect(['index']);
                } else {
                    $this->sessionAddFlashArray('error', 'Unable to process you request', true);
                }
            }
        }

        $viewData['model'] = $model;
        return $this->render('edit-product', $viewData);
    }

    public function actionManageBooking()
    {
        $viewData = [];
        $filterModel = new BevcoOrderSearch();
        $filterModel->order_date = date('d M Y');
        $orderProvider = $filterModel->search(Yii::$app->request->queryParams);
        $viewData['filterModel'] = $filterModel;
        $viewData['orderProvider'] = $orderProvider;
        
        return $this->render('booking-dashboard', $viewData);
    }

    public function actionNewBooking()
    {
        $viewData = [];
        $model = new BeverageBookingForm();
        $model->order_date = date('d M Y');
        $item = [new BevcoItemsForm];
        $viewData['model'] =  $model;
        $viewData['item'] = (empty($item)) ? [new BevcoItemsForm] : $item;
        return $this->render('new-booking', $viewData);
    }
    
    public function actionGetSlots()
    {   
        if(Yii::$app->request->isAjax) {
            $order_date = yii::$app->request->get('order_date');
            if($order_date) {
                $user = yii::$app->user->identity;
                $settings =  ExtendedBevcoSettings::findOne(['institution_id' => $user->institutionid ]);
                if(!$settings) {
                    throw new NotFoundHttpException("Sorry, we couldn't find any beverage settings for your institution. Please configure them.");
                }
                $slots = ExtendedBevcoSlots::find()
                        ->where(['institution_id' => $user->institutionid])
                        ->andWhere(['between', 'slot_date', date('Y-m-d H:i:s', strtotimeNew($order_date.'00:00:00')),
                            date('Y-m-d H:i:s',strtotimeNew($order_date.'23:59:59'))])
                        ->indexBy('slot_number')->orderBy('slot_number asc')->all();
                return $this->renderpartial('slots', [
                    'slots' => $slots,
                    'settings' => $settings->_customDataObject,
                    'order_date' => $order_date
                ]);     
            }
        }
        return $this->asJson(['success' => false]);
    }

    public function actionCompleteBooking()
    {
        $model = new BeverageBookingForm();
        $settings =  ExtendedBevcoSettings::findOne(['institution_id' => $model->institution_id ]);
        if(!$settings) {
            throw new NotFoundHttpException("Sorry, we couldn't find any beverage settings for your institution. Please configure them.");
        }
        $result = [];
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            if($model->load($data) && $model->validate()) {

                $itemModels = BaseModel::createMultiple(BevcoItemsForm::classname());
                Model::loadMultiple($itemModels, Yii::$app->request->post());
                
                $t = ActiveForm::validateMultiple($itemModels);
                if(!empty($t)) {
                    return $this->asJson(['success' => false]);
                }
               
                $model->settings = $settings;
                $model->items = $itemModels;
                $response = $model->completeBooking();
                if($response['success']) {
                    return $this->asJson(['success' => true]);
                } else {
                    return $this->asJson(['success' => false, 'errors' => $response['errors']]);
                }
            }
            foreach ($model->getErrors() as $attribute => $errors) {
                $result[\yii\helpers\Html::getInputId($model, $attribute)] = $errors;
            }
            return $this->asJson(['validation' => $result]);
        }

        return $this->asJson(['success' => false]);
    }

    public function actionBooking($id)
    {
        $order = $this->findOrder($id);
        return $this->render('order',['order' => $order]);
    }

    public function actionCompleteOrder($id)
    {

        $model = $this->findOrder($id);
        $user = yii::$app->user->identity;
        $result = [];
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            if($model->load($data) && $model->validate()) {
                if($model->save()) {
                    return $this->asJson(['success' => true]);  
                }
            }
            foreach ($model->getErrors() as $attribute => $errors) {
                $result[\yii\helpers\Html::getInputId($model, $attribute)] = $errors;
            }
            return $this->asJson(['validation' => $result]);
        }

        return $this->asJson(['success' => false]);
    }

    public function actionSlotLock()
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $slot_id = Yii::$app->request->post('slot_id');
            if (($slot = ExtendedBevcoSlots::findOne($slot_id)) !== null) {
                return $this->asJson(['success' => $slot && !!$slot->lock()]);
            } else {
                throw new NotFoundHttpException('The requested resource does not exist.');
            }
        }
        return $this->asJson(['success' => false]);
    }

    public function actionSlotUnlock()
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $slot_id = Yii::$app->request->post('slot_id');
            if (($slot = ExtendedBevcoSlots::findOne($slot_id)) !== null) {
                return $this->asJson(['success' => $slot && !!$slot->unlock(true)]);
            } else {
                throw new NotFoundHttpException('The requested resource does not exist.');
            }
        }
        return $this->asJson(['success' => false]);
    }

    public function actionGetProducts($cat_id)
    {
        $out = "";
        $products = ExtendedBevcoProducts::find()->joinWith('category')
            ->Where(['bevco_products.category_id' => $cat_id])
            ->andWhere(['bevco_products.is_available' => ExtendedBevcoProducts::IS_AVAILABLE])
            ->andWhere(['bevco_category.is_available' => ExtendedBevcoCategory::IS_AVAILABLE])
            ->orderBy('name asc')
            ->all();

        $out .= "<option value='' selected>--Select Product--</option>";
        
        if (!empty($products)) {
            foreach($products as $product) {
                $out .= "<option value='".$product->id."'>".$product->name."</option>";
            }
        }
        
        return $out;
    }
}
