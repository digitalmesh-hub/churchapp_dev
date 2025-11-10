<?php 
namespace backend\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use backend\controllers\BaseController;
use common\models\formmodels\BillFormModel;
use common\models\searchmodels\ExtendedBillSearch;
use yii\web\UploadedFile;

/**
 * BillController implements the CRUD actions for Dynamic model
 */
class BillController extends BaseController
{
	public function behaviors()
	{
		return [
			'verbs' => [
					'class' => VerbFilter::className(),
					'actions' => [
					],
			],
		];
	}
	function beforeAction($action)
    {   
        //if manage bills permission is enabled user can access.
        if (!Yii::$app->user->can('a65b8d57-ec46-11e6-b48e-000c2990e707')) {
            throw new \yii\web\ForbiddenHttpException('You are not allowed to perform the requested action');
        }
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {   
        $formModel = new BillFormModel();
        $searchModel = new ExtendedBillSearch();
    	$institutionId = yii::$app->user->identity->institution->id;
    	$formModel->institutionid = $institutionId;
        $queryBack = yii::$app->request->get('queryBack', null);
        $loadPost = false;
        if (Yii::$app->request->isPost) {
            $loadPost = $formModel->load(Yii::$app->request->post());
            $postData = Yii::$app->request->post();
            $queryBack = [ 'year' =>$postData['BillFormModel']['year'], 'month' => $postData['BillFormModel']['month'] ];
            $formModel->invoice = UploadedFile::getInstance($formModel, 'invoice');
            if ($loadPost) {
                $response = $formModel->saveBill();
                if (!$response['success']) {
                    $this->sessionAddFlashArray('error', isset($response['errors']['message']) ? $response['errors']['message'] : "Bill upload failed.Please try again.", true);
                } else {
                    $message = [
                        "Bill successfully uploaded",
                        $response['errors']["insertedRows"]. " rows inserted out of " . $response['errors']["totalRows"] ." rows"

                    ];
                    $this->settingBillsNotifications([
                        'institutionid' => $institutionId,
                        'month' => $postData['BillFormModel']['month'],
                        'year' => $postData['BillFormModel']['year'],
                    ]);
                    $this->sessionAddFlashArray('success', $message, true);
                }
            } else {
                $this->sessionAddFlashArray('error', $formModel->getErrors(), true);
            }
            $errorData = isset($response['errors']['errorData']) ? $response['errors']['errorData'] : [];
            if (!empty($errorData)) {

                return $this->render('_error-data', [
                    'data' => $errorData,
                    'heading' => $response['errors']['heading']
                ]);
            }
            return $this->redirect(['index', 'queryBack' => $queryBack]);
        }
        $searchData = Yii::$app->request->queryParams;
        if(!empty($searchData)) {
           $dataProvider = $searchModel->search($searchData); 
           $data = self::_group_by($dataProvider, 'memberNo');
           $htmlData = $this->renderPartial('_bill-view',['provider' => $data]); 
        } else {
           $htmlData = "";
        }
    	return $this->render(
    			'index',
    			[
    			    'formModel' => $formModel,
                    'searchModel' => $searchModel,
                    'htmlData' => $htmlData,
                    'queryBack' => $queryBack 
    			]
    	);
    }

    public static function _group_by($array, $key) 
    {
        $return = [];
        foreach($array as $val) {
            $return[$val[$key]][] = $val;
        }
        return $return;
    }

    protected function settingBillsNotifications($response) {
        $bills = BillFormModel::checkBillAlreadyExist($response);
        if(empty($bills)) {
            BillFormModel::insertUploadedBills($response);
        }
        else {
            $response['created'] = $bills['created']; 
            BillFormModel::updateUploadedBills($response);
        }
        $id = date('ymdhi');
        $institutionDetails = Yii::$app->user->identity->institution;
	    $institutionName = $institutionDetails['name'];
        $createdBy = Yii::$app->user->identity->id;
        Yii::$app->consoleRunner->run('bill-notification/bill-notification  --eventType="P" --eventId='.$id.' --year="'.$response['year'].'" --institutionName="'.$institutionName.'" --institutionId='.$response['institutionid'].' --month='.$response['month'].' --createdBy='.$createdBy);
        return true;
    }
}

