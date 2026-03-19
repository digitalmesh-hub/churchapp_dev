<?php

namespace api\modules\v3\controllers;

use Yii;
use api\modules\v3\controllers\BaseController;
use api\modules\v3\models\responses\ApiResponse;
use yii\helpers\ArrayHelper;
use common\models\extendedmodels\ExtendedUserCredentials;
use common\models\extendedmodels\ExtendedVicarDirectory;
use common\models\extendedmodels\ExtendedVicarPositions;

class VicardirectoryController extends BaseController
{
    public $statusCode;
    public $message = "";
    public $data;
    public $code;
    
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
                        'list-vicars' => ['GET'],
                        'get-main-vicar' => ['GET'],
                        'get-assistant-vicars' => ['GET'],
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
     * Returns complete vicar directory for the institution
     * Includes main vicar and all assistant vicars with member details
     * @return ApiResponse
     */
    public function actionListVicars()
    {
        $userId = Yii::$app?->user?->identity?->id;
        
        if ($userId) {
            $institutionInfo = ExtendedUserCredentials::getUserInstitution($userId);
            $institutionId = $institutionInfo['institutionid'];
            
            if ($institutionId) {
                $enabledInstitutions = env('VICAR_DIRECTORY_ENABLED_INSTITUTIONS', '');
                $enabledInstitutionsList = array_filter(array_map('trim', explode(',', $enabledInstitutions)));
                if (!in_array($institutionId, $enabledInstitutionsList)) {
                    $this->statusCode = 403;
                    $this->message = 'Vicar directory feature is not enabled for this institution';
                    $this->data = new \stdClass();
                    return new ApiResponse($this->statusCode, $this->data, $this->message);
                }
                $vicars = ExtendedVicarDirectory::getVicarDirectoryWithDetails($institutionId, true);
                
                if ($vicars) {
                    // Group vicars by position
                    $positionsMap = [];
                    
                    foreach ($vicars as $vicar) {
                        $positionId = $vicar['vicar_position_id'];
                        
                        // Initialize position if not exists
                        if (!isset($positionsMap[$positionId])) {
                            $positionsMap[$positionId] = [
                                'positionId' => (string)$positionId,
                                'positionName' => $vicar['position_name'] ?? '',
                                'positionDescription' => $vicar['position_description'] ?? '',
                                'isMainVicar' => (int)$vicar['is_main_vicar'] === 1,
                                'displayOrder' => (int)$vicar['position_display_order'],
                                'vicars' => []
                            ];
                        }
                        
                        // Add vicar data under the position
                        $positionsMap[$positionId]['vicars'][] = $this->buildVicarData($vicar, false);
                    }
                    
                    // Convert to indexed array and sort by position display order
                    $positions = array_values($positionsMap);
                    usort($positions, function($a, $b) {
                        return $a['displayOrder'] - $b['displayOrder'];
                    });
                    
                    // Sort vicars within each position by their display order
                    foreach ($positions as &$position) {
                        usort($position['vicars'], function($a, $b) {
                            return $a['displayOrder'] - $b['displayOrder'];
                        });
                    }
                    unset($position); // Break reference
                    
                    $data = [
                        'positions' => $positions,
                    ];
                    
                    $this->statusCode = 200;
                    $this->message = 'Vicar directory retrieved successfully';
                    $this->data = $data;
                    return new ApiResponse($this->statusCode, $this->data, $this->message);
                } else {
                    $data = [
                        'positions' => [],
                    ];
                    
                    $this->statusCode = 200;
                    $this->message = 'No vicars found';
                    $this->data = $data;
                    return new ApiResponse($this->statusCode, $this->data, $this->message);
                }
            } else {
                $this->statusCode = 400;
                $this->message = 'Institution not found';
                $this->data = new \stdClass();
                return new ApiResponse($this->statusCode, $this->data, $this->message);
            }
        } else {
            $this->statusCode = 498;
            $this->message = 'Session invalid';
            $this->data = new \stdClass();
            return new ApiResponse($this->statusCode, $this->data, $this->message);
        }
    }
    
    /**
     * Returns only the main vicar for the institution
     * @return ApiResponse
     */
    public function actionGetMainVicar()
    {
        $userId = Yii::$app?->user?->identity?->id;
        
        if ($userId) {
            $institutionInfo = ExtendedUserCredentials::getUserInstitution($userId);
            $institutionId = $institutionInfo['institutionid'];
            
            if ($institutionId) {
                $vicar = ExtendedVicarDirectory::getMainVicar($institutionId);
                
                if ($vicar) {
                    $vicarData = $this->buildVicarData($vicar, true);
                    
                    $data = ['mainVicar' => $vicarData];
                    
                    $this->statusCode = 200;
                    $this->message = 'Main vicar retrieved successfully';
                    $this->data = $data;
                    return new ApiResponse($this->statusCode, $this->data, $this->message);
                } else {
                    $data = ['mainVicar' => null];
                    
                    $this->statusCode = 200;
                    $this->message = 'No main vicar found';
                    $this->data = $data;
                    return new ApiResponse($this->statusCode, $this->data, $this->message);
                }
            } else {
                $this->statusCode = 400;
                $this->message = 'Institution not found';
                $this->data = new \stdClass();
                return new ApiResponse($this->statusCode, $this->data, $this->message);
            }
        } else {
            $this->statusCode = 498;
            $this->message = 'Session invalid';
            $this->data = new \stdClass();
            return new ApiResponse($this->statusCode, $this->data, $this->message);
        }
    }
    
    /**
     * Returns only assistant vicars for the institution
     * @return ApiResponse
     */
    public function actionGetAssistantVicars()
    {
        $userId = Yii::$app?->user?->identity?->id;
        
        if ($userId) {
            $institutionInfo = ExtendedUserCredentials::getUserInstitution($userId);
            $institutionId = $institutionInfo['institutionid'];
            
            if ($institutionId) {
                $vicars = ExtendedVicarDirectory::getAssistantVicars($institutionId);
                
                if ($vicars) {
                    $assistantList = [];
                    
                    foreach ($vicars as $vicar) {
                        $assistantList[] = $this->buildVicarData($vicar, true);
                    }
                    
                    $data = [
                        'assistantVicars' => $assistantList,
                        'totalCount' => count($assistantList),
                    ];
                    
                    $this->statusCode = 200;
                    $this->message = 'Assistant vicars retrieved successfully';
                    $this->data = $data;
                    return new ApiResponse($this->statusCode, $this->data, $this->message);
                } else {
                    $data = [
                        'assistantVicars' => [],
                        'totalCount' => 0,
                    ];
                    
                    $this->statusCode = 200;
                    $this->message = 'No assistant vicars found';
                    $this->data = $data;
                    return new ApiResponse($this->statusCode, $this->data, $this->message);
                }
            } else {
                $this->statusCode = 400;
                $this->message = 'Institution not found';
                $this->data = new \stdClass();
                return new ApiResponse($this->statusCode, $this->data, $this->message);
            }
        } else {
            $this->statusCode = 498;
            $this->message = 'Session invalid';
            $this->data = new \stdClass();
            return new ApiResponse($this->statusCode, $this->data, $this->message);
        }
    }

    /**
     * Build vicar data array from database record
     * @param array $vicar Raw vicar data from database
     * @param bool $includePosition Whether to include position details
     * @return array Formatted vicar data
     */
    private function buildVicarData($vicar, $includePosition = false)
    {
        // Build photo URLs
        $photoThumbnailUrl = '';
        $photoFullUrl = '';
        
        if (!empty($vicar['memberImageThumbnail'])) {
            $photoThumbnailUrl =  yii::$app->params['imagePath'] . $vicar['memberImageThumbnail'];
        }
        
        if (!empty($vicar['memberImage'])) {
            $photoFullUrl = Yii::$app->params['imagePath'] . $vicar['memberImage'];
        }
        
        // Build full name without extra spaces
        $nameParts = array_filter([
            $vicar['firstName'] ?? '',
            $vicar['middleName'] ?? '',
            $vicar['lastName'] ?? ''
        ]);
        $fullName = implode(' ', $nameParts);
        
        $data = [
            'id' => (string)$vicar['id'],
            'memberId' => (string)$vicar['member_id'],
            'membershipNo' => $vicar['memberno'] ?? '',
            'title' => $vicar['memberTitle'] ?? '',
            'memberName' => $fullName,
            'firstName' => $vicar['firstName'] ?? '',
            'middleName' => $vicar['middleName'] ?? '',
            'lastName' => $vicar['lastName'] ?? '',
            'photoThumbnailUrl' => $photoThumbnailUrl,
            'photoUrl' => $photoFullUrl,
            'mobileNo' => $vicar['member_mobile1'] ?? '',
            'email' => $vicar['member_email'] ?? '',
            'startDate' => $vicar['start_date'] ?? '',
            'endDate' => $vicar['end_date'] ?? null,
            'displayOrder' => (int)$vicar['display_order'],
            'remarks' => $vicar['remarks'] ?? '',
        ];
        
        // Add position details if requested
        if ($includePosition) {
            $data['positionId'] = (string)$vicar['vicar_position_id'];
            $data['positionName'] = $vicar['position_name'] ?? '';
            $data['positionDescription'] = $vicar['position_description'] ?? '';
        }
        
        return $data;
    }
}
