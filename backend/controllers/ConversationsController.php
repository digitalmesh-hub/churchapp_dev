<?php
namespace backend\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\data\ArrayDataProvider;
use yii\web\NotFoundHttpException;
use backend\controllers\BaseController;
use common\components\ConversationsComponent;
use common\models\searchmodels\ConversationSearchModel;

/**
 * ConversationController implements the CRUD actions for Dynamic model
 */
class ConversationsController extends BaseController
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => []
            ]
        ];
    }

    function beforeAction($action)
    {
        // if list conversation permission is enabled user can access.
        if (! Yii::$app->user->can('22643223-ec48-11e6-b48e-000c2990e707')) {
            throw new \yii\web\ForbiddenHttpException('You are not allowed to perform the requested action');
        }
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $conversationObject = new ConversationsComponent();
        $model = new ConversationSearchModel();
        $model->start_date = date('d F Y');
        $model->end_date = date('d F Y');
        $institutionId = yii::$app->user->identity->institution->id;
        $subjectTitleModel = $conversationObject->getConversationSubjectTitle($institutionId);
        $memberNameModel = $conversationObject->getMemberName($institutionId);
        $memberNameModel = ArrayHelper::map($memberNameModel, 'userid', 'name');
        $dynamic = Yii::$app->request->queryParams;
        $dataProvider = $model->search($dynamic);
        $model->start_date = ($model->start_date) ? date('d F Y',strtotimeNew($model->start_date)) : date('d F Y');
        $model->end_date = ($model->end_date) ? date('d F Y',strtotimeNew($model->end_date)) : date('d F Y');
        return $this->render('index', [
                'dynamic' => $dynamic,
                'model' => $model,
                'subjectTitleModel' => $subjectTitleModel,
                'memberNameModel' => $memberNameModel,
                'conversations' => $dataProvider
            ]);
    }

    /**
     * getConversationByTopic.
     *
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionViewConversations()
    {
        $conversationObject = new ConversationsComponent();
        
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (yii::$app->request->isAjax) {
            $topicId = yii::$app->request->get('topicId');
            if ($topicId) {
                $topicId = (int) $topicId;
                $conversationsResult = $conversationObject->getConversationByTopic($topicId);
                
                $conversationStart = $conversationObject->getConversationSubjectTitleByTopic($topicId);
                
                $conversationList = $this->renderPartial('_conversation-view', [
                    'dataProvider' => $conversationsResult,
                    'subjectTitle' => $conversationStart
                ]);
                
                return [
                    'status' => 'success',
                    'result' => $conversationList
                ];
            } else {
                return [
                    'status' => 'error',
                    'result' => null
                ];
            }
        } else {
            return [
                'status' => 'error',
                'result' => null
            ];
        }
    }
}