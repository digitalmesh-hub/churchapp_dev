<?php

namespace backend\controllers;

use Yii;
use common\models\extendedmodels\ExtendedFeedback;
use common\models\extendedmodels\ExtendedFeedbacktype;
use common\models\extendedmodels\ExtendedInstitutionfeedbacktype;
use common\models\extendedmodels\ExtendedFeedbacknotificationsent;
use common\models\searchmodels\ExtendedFeedbackSearch;
use yii\web\Controller;
use common\components\EmailHandlerComponent;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use common\models\extendedmodels\ExtendedInstitution;

/**
 * FeedbackController implements the CRUD actions for ExtendedFeedback model.
 */
class FeedbackController extends BaseController
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
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
    
    function beforeAction($action)
    {   
        //if manage feedback privilage is set user has access.
        if (!Yii::$app->user->can('0f74458a-ec49-11e6-b48e-000c2990e707')) {
            throw new \yii\web\ForbiddenHttpException('You are not allowed to perform the requested action');
        }
        return parent::beforeAction($action);
    }

    /**
     * Lists all ExtendedFeedback models.
     * @return mixed
     */
    public function actionIndex()
    {  
        $dataProvider = array();
        $searchModel = new ExtendedFeedbackSearch();
        $searchModel->start_date = date('d M Y');
        $searchModel->end_date = date('d M Y');
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $feedbackarray =  ArrayHelper::map(ExtendedInstitutionfeedbacktype::find()
                ->select(['institutionfeedbacktype.feedbacktypeid', 'feedbacktype.description'])
                ->innerJoin('feedbacktype', '`feedbacktype`.`feedbacktypeid` = `institutionfeedbacktype`.`feedbacktypeid`')
                ->where(['institutionfeedbacktype.institutionid' => yii::$app->user->identity->institutionid])
                ->andWhere(['institutionfeedbacktype.active' => 1])
                ->orderby('feedbacktype.description')
                ->all(),'feedbacktypeid', 'feedbacktype.description');
        
        $getmail = ExtendedFeedback::getFeedbackEmail($this->currentUser()->institutionid);
        $institutionfeedbacktype = new ExtendedInstitutionfeedbacktype();
        $query = $institutionfeedbacktype->find()->where(['institutionid'=> $this->currentUser()->institutionid]);
        $provider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
              'defaultOrder' => [
               'order' => SORT_ASC
             ]
            ]
        ]);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'getmail' => $getmail,
            'institutionfeedback' => $provider,
            'feedbackarray' => $feedbackarray
        ]);
    }

    protected function findModel($id)
    {
        if (($model = ExtendedFeedback::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
   
    /**
     * [actionFeedbackmail description] for respond the feedback 
     * send mail action
     * @return [type] [description]
     */
    public function actionFeedbackmail()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (yii::$app->request->isAjax) {
		try {
			$mailobj = new EmailHandlerComponent();
			$from = yii::$app->user->identity->emailid;
			$institutionName =  yii::$app->user->identity->institution->name;
			$institutionLogo = Yii::$app->user->identity->institution->institutionlogo;
      $institutionLogo = (!empty($institutionLogo)) ? $institutionLogo : '/institution/institution-icon-grey.png';
			$email =explode(",",yii::$app->request->post('emailid'));
			$toemail = $email;
			$title ='';
			$subject ='Feedback Response';
			$mailContent['name'] = 'admin';
			$mailContent['content'] = yii::$app->request->post('data');
			$mailContent['institutionname'] = $institutionName;
			$mailContent['logo'] = yii::$app->params['imagePath'].$institutionLogo;
			$mailContent['template'] = 'feedback-reply';
			$attach = '';
			$temp =  $mailobj->sendEmail($from,$toemail,$title,$subject,$mailContent,$attach);
			
			if($temp){
				$id = yii::$app->request->post('feedbackid');
				$userid = yii::$app->request->post('userid');
				$insertnotification =  new ExtendedFeedbacknotificationsent();
				$insertnotification->feedbackid = $id ;
				$insertnotification->userid = $userid;
				$insertnotification->createddatetime = date(yii::$app->params['dateFormat']['sqlDandTFormat']);
				if($insertnotification->save(false)) {
					$feedbackrobj = new ExtendedFeedback();
					$feedbackdetails = $feedbackrobj::find()->where(['feedbackid' => $id])->one();
					$feedbackdetails->isresponded = '1';
					if($feedbackdetails->save(false)) {
						return [
								'status' => 'success',
								'data' => 'The feedback response has been sent successfully',
						];
					}else{
						return [
								'status' => 'success',
								'data' => "An error occured while processing the request",
						];
					}
			
				}else {
					return [
							'status' => 'success',
							'data' => 'An error occured while processing the request',
					];
				}
				 
			
			}else{
				return [
						'status' => 'error',
						'data' => 'An error occured while processing the request',
				];
			}
		} catch (\Exception $e) {
			return [
					'status' => 'error',
					'data' => 'An error occured while processing the request',
			];
		}
 
               
        }   
    }
    /**
     * [actionSaveFeedbackEmail description] for save feedback mail 
     * @return [type] [description]
     */
    public function actionSaveFeedbackEmail()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (yii::$app->request->isAjax) {
           $mail = yii::$app->request->post('data');
           $institutionId = $this->currentUser()->institutionid;
           $model = ExtendedInstitution::findOne($institutionId);
           if(!empty($model))
           {
           		$model->feedbackemail = $mail;
           		if($model->save()){
           			return true;
           		}
           		else {
           			yii::error($model->getErrors());
           		}
           }
           return false;
        }
    }
    /**
     * [actionAddFeedbackType description] add feedback type to db 
     * @return [type] [description]
     */
    public function actionAddFeedbackType()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (yii::$app->request->isAjax) {
            $feedbackdescription = yii::$app->request->post('feedbacktype');
            $feedbacktype = new ExtendedFeedbacktype();
            $institutionfeedbacktype = new ExtendedInstitutionfeedbacktype();
            $typearray = $feedbacktype->find()->where(['description' => $feedbackdescription])->one();
            if ($typearray) {
                $institutionfeedback = $institutionfeedbacktype->find()->where(['feedbacktypeid' => $typearray['feedbacktypeid'],'institutionid'=>$this->currentUser()->institutionid])->one();
                if($institutionfeedback){
                        return ['hasError' => 1,'message' =>  'feedback type already exist in the institution' ];
                } else {
                       $data = ExtendedFeedbacktype::getFeedbackOrder($this->currentUser()->institutionid);
                        $institutionfeedbacktype->feedbacktypeid = $typearray['feedbacktypeid'];
                        $institutionfeedbacktype->institutionid = $this->currentUser()->institutionid;
                        $institutionfeedbacktype->active = '1';
                        $institutionfeedbacktype->order = ($data['ordervalue'] == 0)? 1 : $data['ordervalue']+1 ;
                        if($institutionfeedbacktype->save()) {
                             return ['hasError' => 0,'message' =>  'done' ];
                        }
                } 
            }else {                
                $feedbacktype->description = $feedbackdescription;
                if($feedbacktype->save()) {
                     $data = ExtendedFeedbacktype::getFeedbackOrder($this->currentUser()->institutionid);
                    $newtypearray = $feedbacktype->find()->where(['description' => $feedbackdescription])->one();
                    $institutionfeedbacktype->feedbacktypeid = $newtypearray['feedbacktypeid'];
                    $institutionfeedbacktype->institutionid = $this->currentUser()->institutionid;
                    $institutionfeedbacktype->active = '1';
                    $institutionfeedbacktype->order = $institutionfeedbacktype->order = ($data['ordervalue'] == 0)? 1 : $data['ordervalue']+1 ;
                    if ($institutionfeedbacktype->save()) {
                      return ['hasError' => 0,'message' =>  'done' ];
                    }
                }
            }            
        }   
    }
    /**
     * [actionActivate description]for active and  feedbacktype
     * @return [type] [description]
     */
    public function actionActivate()
    {  
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (yii::$app->request->isAjax) {
            $id = yii::$app->request->post('id');

            if ($id) {
                $institutionfeedbacktype = new ExtendedInstitutionfeedbacktype();
                $institutionfeedback = $institutionfeedbacktype->find()->where(['feedbacktypeid' =>  $id,'institutionid'=>$this->currentUser()->institutionid])->one();
                
                
                if (!empty( $institutionfeedback)) {
                     $institutionfeedback->active = '1';
                    if ($institutionfeedback->update()) {
                            return [
                                'status' => 'success',
                                'data' => null,
                            ];
                    } else {
                        return [
                            'status' => 'error',
                            'data' => null
                        ];
                    }
                }
                 return [
                        'status' => 'error',
                        'data' => null,
                ];
            }
             return [
                        'status' => 'error',
                        'data' => null,
            ];
        }
    }
    /**
     * [actionDectivate description]for deactive and  feedbacktype
     * @return [type] [description]
     */
    public function actionDeactivate()
    {   
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (yii::$app->request->isAjax) {
            $id = yii::$app->request->post('id');

            if ($id) {
               $institutionfeedbacktype = new ExtendedInstitutionfeedbacktype();
               $institutionfeedback = $institutionfeedbacktype->find()->where(['feedbacktypeid' =>  $id,'institutionid'=>$this->currentUser()->institutionid])->one();
                if (!empty( $institutionfeedback)) {
                     $institutionfeedback->active = '0';
                    if ($institutionfeedback->update()) {
                            return [
                                'status' => 'success',
                                'data' => null,
                            ];
                    } else {

                        return [
                            'status' => 'error',
                            'data' => null
                        ];
                    }
                }

                 return [
                        'status' => 'error',
                        'data' => null,
                ];
            }
             return [
                        'status' => 'error',
                        'data' => null,
            ];
        }
    }
    /**
     * [actionSortFeedbackType description]to sort the feedback types 
     * @return [type] [description]
     */
    public function actionSortFeedbackType()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (yii::$app->request->isAjax) {
            $sort = yii::$app->request->post('sort');
             if($sort == 'up') {
              $InstitutionFeedbackTypeID = yii::$app->request->post('InstitutionFeedbackTypeID');
              $CurrentOrder=yii::$app->request->post('CurrentOrder');
              $PreviousOrder=yii::$app->request->post('PreviousOrder');
              $institutionfeedbacktype = new ExtendedInstitutionfeedbacktype();
              $feedbackorder = $institutionfeedbacktype->find()->where(['order' =>$PreviousOrder,'institutionid'=>$this->currentUser()->institutionid])->one();
              $institutionfeedback = $institutionfeedbacktype->find()->where(['institutionfeedbacktypeid' =>  $InstitutionFeedbackTypeID,'institutionid'=>$this->currentUser()->institutionid])->one();
              $institutionfeedback->order = $PreviousOrder;
              if($institutionfeedback->update(false)){
                  $feedbackorder->order = $CurrentOrder;
                  if($feedbackorder->update(false)){
                      return [
                        'status' => 'success',
                        'data' => null,
                      ];
                  }
                return [
                      'status' => 'error',
                      'data' => null,
                ];
              }
              return [
                    'status' => 'error',
                    'data' => null,
              ];

        }
        else if($sort == 'down'){
             $InstitutionFeedbackTypeID = yii::$app->request->post('InstitutionFeedbackTypeID');
              $CurrentOrder=yii::$app->request->post('CurrentOrder');
              $nextOrder=yii::$app->request->post('nextOrder');
              $institutionfeedbacktype = new ExtendedInstitutionfeedbacktype();
              $feedbackorder = $institutionfeedbacktype->find()->where(['order' =>$nextOrder,'institutionid'=>$this->currentUser()->institutionid])->one();
              $institutionfeedback = $institutionfeedbacktype->find()->where(['institutionfeedbacktypeid' =>  $InstitutionFeedbackTypeID,'institutionid'=>$this->currentUser()->institutionid])->one();
              $institutionfeedback->order = $nextOrder;
              if($institutionfeedback->update(false)){
                $feedbackorder->order = $CurrentOrder;
                if($feedbackorder->update(false)){

                return [
                                'status' => 'success',
                                'data' => null,
                       ];

                }
                return [
                                'status' => 'error',
                                'data' => null,
                       ];


              }
               return [
                                'status' => 'error',
                                'data' => null,
                      ];

            }
        }
    }
}
