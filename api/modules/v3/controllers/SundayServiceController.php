<?php

namespace api\modules\v3\controllers;

use Yii;
use api\modules\v3\controllers\BaseController;
use api\modules\v3\models\responses\ApiResponse;
use yii\helpers\ArrayHelper;
use common\models\extendedmodels\ExtendedUserCredentials;
use common\models\extendedmodels\ExtendedSundayService;
use yii\data\ActiveDataProvider;

class SundayServiceController extends BaseController
{
    public $statusCode;
    public $message = "";
    public $data;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => \yii\filters\VerbFilter::className(),
                    'actions' => [
                        'list-sunday-services' => ['GET'],
                    ]
                ],
            ]
        );
    }

    /**
     * Index action
     * @return $statusCode int
     */
    public function actionIndex()
    {
        $this->statusCode = 404;
        throw new \yii\web\HttpException($this->statusCode);
    }

    /**
     * List all active Sunday services with pagination
     * 
     * Query Parameters:
     * - page: Page number (default: 1)
     * - per-page: Items per page (default: 10)
     * - all: If set to 'true' or '1', returns all records without pagination
     * 
     * @return ApiResponse
     */
    public function actionListSundayServices()
    {
        $request = Yii::$app->request;
        $userId = Yii::$app?->user?->identity?->id;
        
        if ($userId) {
            $institutionId = ExtendedUserCredentials::getUserInstitution($userId);
            $institutionId = $institutionId['institutionid'];
            
            // Check if Sunday Service is enabled for this institution
            $enabledInstitutions = env('SUNDAY_SERVICE_ENABLED_INSTITUTIONS', '');
            $enabledInstitutionsList = array_filter(array_map('trim', explode(',', $enabledInstitutions)));
            
            if (!in_array($institutionId, $enabledInstitutionsList)) {
                $this->statusCode = 403;
                $this->message = 'Sunday Service feature is not enabled for this institution';
                $this->data = new \stdClass();
                return new ApiResponse($this->statusCode, $this->data, $this->message);
            }
            
            $all = $request->get('all', false);

            // Check if 'all' parameter is set to return all records
            if ($all === 'true' || $all === '1' || $all === true) {
                // Return all active records without pagination
                $sundayServices = ExtendedSundayService::find()
                    ->where(['institution_id' => $institutionId, 'active' => 1])
                    ->andWhere(['>=', 'service_date', date('Y-m-d')])
                    ->orderBy(['service_date' => SORT_ASC])
                    ->all();

                if ($sundayServices) {
                    $serviceList = [];
                    foreach ($sundayServices as $service) {
                        $result = [
                            'id' => (int)$service->id,
                            'serviceDate' => date(Yii::$app->params['dateFormat']['viewDateFormat'], strtotime($service->service_date)),
                            'serviceDateRaw' => $service->service_date,
                            'content' => $service->content,
                            'active' => (int)$service->active,
                            'createdAt' => date(Yii::$app->params['dateFormat']['viewDateFormat'], strtotime($service->created_at)),
                        ];
                        array_push($serviceList, $result);
                    }

                    $data = [
                        'sundayServices' => $serviceList,
                        'totalCount' => count($serviceList),
                        'pagination' => false,
                    ];
                    $this->statusCode = 200;
                    $this->message = 'Sunday services retrieved successfully';
                    $this->data = $data;
                    return new ApiResponse($this->statusCode, $this->data, $this->message);
                } else {
                    $data = [
                        'sundayServices' => [],
                        'totalCount' => 0,
                        'pagination' => false,
                    ];
                    $this->statusCode = 200;
                    $this->message = 'No active Sunday services found';
                    $this->data = $data;
                    return new ApiResponse($this->statusCode, $this->data, $this->message);
                }
            } else {
                // Return paginated results
                $page = $request->get('page', 1);
                $perPage = $request->get('per-page', 10);

                $query = ExtendedSundayService::find()
                    ->where(['institution_id' => $institutionId, 'active' => 1])
                    ->andWhere(['>=', 'service_date', date('Y-m-d')])
                    ->orderBy(['service_date' => SORT_ASC]);

                $dataProvider = new ActiveDataProvider([
                    'query' => $query,
                    'pagination' => [
                        'page' => $page - 1, // ActiveDataProvider uses 0-based indexing
                        'pageSize' => $perPage,
                    ],
                ]);

                $sundayServices = $dataProvider->getModels();
                $pagination = $dataProvider->pagination;

                if ($sundayServices) {
                    $serviceList = [];
                    foreach ($sundayServices as $service) {
                        $result = [
                            'id' => (int)$service->id,
                            'serviceDate' => date(Yii::$app->params['dateFormat']['viewDateFormat'], strtotime($service->service_date)),
                            'serviceDateRaw' => $service->service_date,
                            'content' => $service->content,
                            'active' => (int)$service->active,
                            'createdAt' => date(Yii::$app->params['dateFormat']['viewDateFormat'], strtotime($service->created_at)),
                        ];
                        array_push($serviceList, $result);
                    }

                    $data = [
                        'sundayServices' => $serviceList,
                        'pagination' => [
                            'totalCount' => (int)$pagination->totalCount,
                            'pageCount' => (int)$pagination->pageCount,
                            'currentPage' => (int)$page,
                            'perPage' => (int)$perPage,
                        ],
                    ];
                    $this->statusCode = 200;
                    $this->message = 'Sunday services retrieved successfully';
                    $this->data = $data;
                    return new ApiResponse($this->statusCode, $this->data, $this->message);
                } else {
                    $data = [
                        'sundayServices' => [],
                        'pagination' => [
                            'totalCount' => 0,
                            'pageCount' => 0,
                            'currentPage' => (int)$page,
                            'perPage' => (int)$perPage,
                        ],
                    ];
                    $this->statusCode = 200;
                    $this->message = 'No Sunday services found';
                    $this->data = $data;
                    return new ApiResponse($this->statusCode, $this->data, $this->message);
                }
            }
        } else {
            $this->statusCode = 498;
            $this->message = 'Session invalid';
            $this->data = new \stdClass();
            return new ApiResponse($this->statusCode, $this->data, $this->message);
        }
    }
}
