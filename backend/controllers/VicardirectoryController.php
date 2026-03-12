<?php

namespace backend\controllers;

use Yii;
use common\models\extendedmodels\ExtendedVicarPositions;
use common\models\extendedmodels\ExtendedVicarDirectory;
use common\models\extendedmodels\ExtendedMember;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\controllers\BaseController;
use yii\web\Response;

/**
 * VicarDirectoryController implements the CRUD actions for Vicar Directory Management.
 */
class VicardirectoryController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete-position' => ['POST'],
                    'delete-vicar' => ['POST'],
                    'activate-position' => ['POST'],
                    'deactivate-position' => ['POST'],
                    'activate-vicar' => ['POST'],
                    'deactivate-vicar' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Vicar Positions
     * @return mixed
     */
    public function actionPositions()
    {
        $institutionId = $this->currentUser()->institutionid;
        $model = new ExtendedVicarPositions();
        
        $positions = ExtendedVicarPositions::find()
            ->where(['institutionid' => $institutionId])
            ->orderBy(['display_order' => SORT_ASC, 'position_name' => SORT_ASC])
            ->all();

        if (Yii::$app->request->isPost) {
            return $this->actionCreatePosition();
        }

        return $this->render('positions', [
            'model' => $model,
            'positions' => $positions,
        ]);
    }

    /**
     * Create new Vicar Position
     * @return mixed
     */
    public function actionCreatePosition()
    {
        $model = new ExtendedVicarPositions();
        
        if ($model->load(Yii::$app->request->post())) {
            $model->institutionid = $this->currentUser()->institutionid;
            $model->createdby = Yii::$app->user->identity->id;
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Vicar position created successfully.');
            } else {
                Yii::$app->session->setFlash('error', 'Failed to create vicar position.');
            }
            
            return $this->redirect(['positions']);
        }

        return $this->redirect(['positions']);
    }

    /**
     * Update existing Vicar Position
     * @param integer $id
     * @return mixed
     */
    public function actionUpdatePosition($id)
    {
        $model = $this->findPositionModel($id);
        $institutionId = $this->currentUser()->institutionid;
        
        $positions = ExtendedVicarPositions::find()
            ->where(['institutionid' => $institutionId])
            ->orderBy(['display_order' => SORT_ASC, 'position_name' => SORT_ASC])
            ->all();

        if ($model->load(Yii::$app->request->post())) {
            $model->modifiedby = Yii::$app->user->identity->id;
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Vicar position updated successfully.');
            } else {
                Yii::$app->session->setFlash('error', 'Failed to update vicar position.');
            }
            
            return $this->redirect(['positions']);
        }

        return $this->render('positions', [
            'model' => $model,
            'positions' => $positions,
            'update' => true,
        ]);
    }

    /**
     * Activate a vicar position
     * @return array
     */
    public function actionActivatePosition()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (Yii::$app->request->isAjax) {
            $id = Yii::$app->request->post('id');
            
            if ($id) {
                $model = $this->findPositionModel((int)$id);
                $model->active = 1;
                $model->modifiedby = Yii::$app->user->identity->id;
                
                if ($model->save()) {
                    return ['status' => 'success', 'data' => null];
                }
            }
        }
        
        return ['status' => 'error', 'data' => null];
    }

    /**
     * Deactivate a vicar position
     * @return array
     */
    public function actionDeactivatePosition()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (Yii::$app->request->isAjax) {
            $id = Yii::$app->request->post('id');
            
            if ($id) {
                $model = $this->findPositionModel((int)$id);
                $model->active = 0;
                $model->modifiedby = Yii::$app->user->identity->id;
                
                if ($model->save()) {
                    return ['status' => 'success', 'data' => null];
                }
            }
        }
        
        return ['status' => 'error', 'data' => null];
    }

    /**
     * Manage Vicar Directory (Assign members to positions)
     * @return mixed
     */
    public function actionIndex()
    {
        $institutionId = $this->currentUser()->institutionid;
        $model = new ExtendedVicarDirectory();
        
        // Get filter parameters from request
        $filters = [];
        if ($search = Yii::$app->request->get('search')) {
            $filters['search'] = $search;
        }
        if ($status = Yii::$app->request->get('status')) {
            $filters['status'] = $status;
        }
        if ($position = Yii::$app->request->get('position')) {
            $filters['position'] = $position;
        }
        
        // Setup pagination
        $totalCount = ExtendedVicarDirectory::getVicarDirectoryCount($institutionId, false, $filters);
        $pagination = new \yii\data\Pagination([
            'totalCount' => $totalCount,
            'pageSize' => 20,
        ]);
        
        $vicars = ExtendedVicarDirectory::getVicarDirectoryWithDetails(
            $institutionId, 
            false, 
            $pagination->limit, 
            $pagination->offset,
            $filters
        );
        $positions = ExtendedVicarPositions::getActivePositions($institutionId);
        
        // Get active members for dropdown
        $members = ExtendedMember::find()
            ->joinWith('membertitle0')
            ->where(['member.institutionid' => $institutionId])
            ->andWhere(['!=', 'member.active', 0])
            ->orderBy(['member.firstName' => SORT_ASC, 'member.lastName' => SORT_ASC])
            ->all();

        if (Yii::$app->request->isPost) {
            return $this->actionCreateVicar();
        }

        return $this->render('index', [
            'model' => $model,
            'vicars' => $vicars,
            'positions' => $positions,
            'members' => $members,
            'pagination' => $pagination,
            'filters' => $filters,
        ]);
    }

    /**
     * Create new Vicar assignment
     * @return mixed
     */
    public function actionCreateVicar()
    {
        $model = new ExtendedVicarDirectory();
        
        if ($model->load(Yii::$app->request->post())) {
            $model->institution_id = $this->currentUser()->institutionid;
            $model->createdby = Yii::$app->user->identity->id;
            $model->is_active = 1;
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Vicar assigned successfully.');
            } else {
                Yii::$app->session->setFlash('error', 'Failed to assign vicar.');
            }
            
            return $this->redirect(['index']);
        }

        return $this->redirect(['index']);
    }

    /**
     * Update existing Vicar assignment
     * @param integer $id
     * @return mixed
     */
    public function actionUpdateVicar($id)
    {
        $model = $this->findVicarModel($id);
        $institutionId = $this->currentUser()->institutionid;
        
        // Get filter parameters from request
        $filters = [];
        if ($search = Yii::$app->request->get('search')) {
            $filters['search'] = $search;
        }
        if ($status = Yii::$app->request->get('status')) {
            $filters['status'] = $status;
        }
        if ($position = Yii::$app->request->get('position')) {
            $filters['position'] = $position;
        }
        
        // Setup pagination
        $totalCount = ExtendedVicarDirectory::getVicarDirectoryCount($institutionId, false, $filters);
        $pagination = new \yii\data\Pagination([
            'totalCount' => $totalCount,
            'pageSize' => 20,
        ]);
        
        $vicars = ExtendedVicarDirectory::getVicarDirectoryWithDetails(
            $institutionId, 
            false,
            $pagination->limit,
            $pagination->offset,
            $filters
        );
        $positions = ExtendedVicarPositions::getActivePositions($institutionId);
        
        $members = ExtendedMember::find()
            ->joinWith('membertitle0')
            ->where(['member.institutionid' => $institutionId])
            ->andWhere(['!=', 'member.active', 0])
            ->orderBy(['member.firstName' => SORT_ASC, 'member.lastName' => SORT_ASC])
            ->all();

        if ($model->load(Yii::$app->request->post())) {
            $model->modifiedby = Yii::$app->user->identity->id;
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Vicar updated successfully.');
            } else {
                Yii::$app->session->setFlash('error', 'Failed to update vicar.');
            }
            
            return $this->redirect(['index']);
        }

        return $this->render('index', [
            'model' => $model,
            'vicars' => $vicars,
            'positions' => $positions,
            'members' => $members,
            'pagination' => $pagination,
            'filters' => $filters,
            'update' => true,
        ]);
    }

    /**
     * Deactivate a vicar assignment
     * @return array
     */
    public function actionDeactivateVicar()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (Yii::$app->request->isAjax) {
            $id = Yii::$app->request->post('id');
            
            if ($id) {
                $model = $this->findVicarModel((int)$id);
                $model->is_active = 0;
                $model->end_date = date('Y-m-d');
                $model->modifiedby = Yii::$app->user->identity->id;
                
                if ($model->save()) {
                    return ['status' => 'success', 'data' => null];
                }
            }
        }
        
        return ['status' => 'error', 'data' => null];
    }

    /**
     * Activate a vicar assignment
     * @return array
     */
    public function actionActivateVicar()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (Yii::$app->request->isAjax) {
            $id = Yii::$app->request->post('id');
            
            if ($id) {
                $model = $this->findVicarModel((int)$id);
                $model->is_active = 1;
                $model->end_date = null;
                $model->modifiedby = Yii::$app->user->identity->id;
                
                if ($model->save()) {
                    return ['status' => 'success', 'data' => null];
                }
            }
        }
        
        return ['status' => 'error', 'data' => null];
    }

    /**
     * Finds the VicarPositions model based on its primary key value.
     * @param integer $id
     * @return ExtendedVicarPositions
     * @throws NotFoundHttpException
     */
    protected function findPositionModel($id)
    {
        if (($model = ExtendedVicarPositions::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Finds the VicarDirectory model based on its primary key value.
     * @param integer $id
     * @return ExtendedVicarDirectory
     * @throws NotFoundHttpException
     */
    protected function findVicarModel($id)
    {
        if (($model = ExtendedVicarDirectory::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
