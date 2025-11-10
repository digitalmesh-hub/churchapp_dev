<?php
/**
* @author Digital Mesh
* @link http://www.digitalmesh.com/
*/
namespace backend\controllers;

use yii;
use yii\filters\AccessControl;
use common\models\formmodels\SubscriptionFeeForm;
use common\models\extendedmodels\ExtendedMember;
use common\models\extendedmodels\ExtendedBills;
use common\models\extendedmodels\ExtendedInstitution;
use common\models\extendedmodels\ExtendedMonthlySubscriptionFee;
use common\models\searchmodels\ExtendedSubscriptionFeeSearch;
use backend\controllers\BaseController;

/**
 *This controller is used for managing bulk insertion of monthly subscription fee.
 *Iam dirty don't use me for large number of record..i will die
 */

class SubscriptionController extends BaseController
{
    const MEMBERTYPE = 0;
    public function behaviors()
    {
        return[
                'access' => [
                    'class' => AccessControl::className(),
                    'only' => [
                        'index,delete'
                    ],
                    'rules' => [
                        [
                            'actions' => [
                            'index,delete'
                        ],
                        'allow' => true,
                        'roles' => [
                            '@'
                        ]
                        ]
                    ]
                ]
        ];
    }
    public function actions()
    {
        return [
        'error' => [
            'class' => 'yii\web\ErrorAction'
        ]
        ];
    }

    /**
     * this action will load index page for collecting information from user.
     * @return [type] [description]
     */
    public function actionIndex()
    {
        
        $model = new ExtendedMonthlySubscriptionFee();
        $institutionId = yii::$app->user->identity->institutionid;
        if (yii::$app->request->isPost) {

            $model->userId = yii::$app->user->id;
            $model->institutionId = $institutionId;

            if ($model->load(yii::$app->request->post()) && $model->validate()) {
                $model->transactionDate = date(
                    yii::$app->params['dateFormat']['sqlDandTFormat'],
                    strtotimeNew($model->transactionDate)
                );
                if ($model->save()) {
                    Yii::$app->getSession()->setFlash('success', 'Successfully saved');
                } else {
                    Yii::error(
                        'Error while saving data in actionIndex subscriptionController, '.
                        var_export(
                            [
                            'error' => $model->getErrors(),
                            'class' => get_class()
                            ],
                            true
                        )
                    );
                    Yii::$app->getSession()->setFlash('danger', 'Operation failed');
                }
               
            } else {
                Yii::error(
                    'Error while saving data in actionIndex subscriptionController, '.
                    var_export(
                        [
                            'error' => $model->getErrors(),
                            'class' => get_class()
                        ],
                        true
                    )
                );
                Yii::$app->getSession()->setFlash('danger', 'Something went wrong!Please try again');
            }
            return $this->redirect(['subscription/index']);
        }

        // return the number of members
        
            $memberCount = ExtendedMember::find()
                ->where(['institutionId' => $institutionId, 'membertype' =>self::MEMBERTYPE])
                ->count();


        $model->amount = yii::$app->params['subscriptionFee']['defaultAmount'];
        $model->description = yii::$app->params['subscriptionFee']['defaultDescription'].' '.date('M').'-'.date('Y');
        $model->transactionDate = date("d-m-Y");
         
        $searchModel = new ExtendedSubscriptionFeeSearch();
        $dataProvider = $searchModel->search();
        //print_r($subscriptionFeeData);die;
        return $this->render(
            'index',
            [
              'model' => $model,
              'dataProvider' => $dataProvider,
              'searchModel' => $searchModel,
              'memberCount' =>$memberCount
            ]
        );
        
    }
    public function actionDelete()
    {
        if (yii::$app->request->isget) {
            $id = yii::$app->request->get();
            $model = ExtendedMonthlySubscriptionFee::findOne($id);
            if ($model->status == 0) {
                if ($model->delete()) {
                    Yii::$app->getSession()->setFlash('success', 'Deletion Successful');
                } else {

                    Yii::error(
                        'Error while deleting data in actionDelete subscriptionController, '.
                        var_export(
                            [
                                'error' => $model->getErrors(),
                                'class' => get_class()
                            ],
                            true
                        )
                    );
                    Yii::$app->getSession()->setFlash('danger', 'Something went wrong!Please try again');
                }
            }
            return $this->redirect(['subscription/index']);
        }
    }
}
