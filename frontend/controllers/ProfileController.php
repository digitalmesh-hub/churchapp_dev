<?php
namespace frontend\controllers;
use Yii;
use yii\web\Controller;
// use yii\db\Expression;
use common\models\extendedmodels\ExtendedMember;
use common\models\extendedmodels\ExtendedEditmember;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\helpers\Url;


class ProfileController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
        ];
    }

	public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

	public function actionView($id)
    {
        if ($id) {
            $memberType = substr($id, 0, 2); 
            $key = substr($id, 2); 
            $member = [];
            
    		if (true) {
    		    if (($editMemberModel = ExtendedEditmember::find()->where(['tempmemberid'=>$key])->one())!=null) {
    			$memberId = $editMemberModel->memberid;
    			if ($memberId) {
        				$model = $this->findModel($memberId);
                        if($memberType == 'MM'){
                            $member['imageOrginal'] = $model->member_pic ?: "/Member/default-user.png";
        				    $member['imageThumbnail'] = $model->memberImageThumbnail;
        				    $member['name'] = (($model->firstName)?$model->firstName.' ':'').(($model->middleName)?$model->middleName.' ':'').(($model->lastName)?$model->lastName:'');
                        }
                        else{
                            $member['imageOrginal'] = $model->spouse_pic ?: "/Member/default-user.png";
        				    $member['imageThumbnail'] = $model->spouseImageThumbnail;
        				    $member['name'] = (($model->spouse_firstName)?$model->spouse_firstName.' ':'').(($model->spouse_middleName)?$model->spouse_middleName.' ':'').(($model->spouse_lastName)?$model->spouse_lastName:'');
                        }
                        $member['residence_address1'] = $model->residence_address1;
                        $member['residence_address2'] = $model->residence_address2;
                        $member['residence_pincode'] = $model->residence_pincode;
                        $member['residence_district'] = $model->residence_district;
                        $member['residence_state'] = $model->residence_state;      				

    				$this->layout = 'main';
                    // echo "<pre>";print_r($model);
    				return $this->render('index', ['member' =>  $member,]);
    			} else {
    				return $this->render('no-member',['type'=>"error"]);
    			}
    		} else {
    			return $this->render('no-member',['type'=>"error"]);
    		}
    		} else{
    			return $this->render('no-member',['type'=>"error"]);
    		}
    	}
    }

         /**
     * Finds the ExtendedMember model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ExtendedMember the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ExtendedMember::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    
       
  
}
