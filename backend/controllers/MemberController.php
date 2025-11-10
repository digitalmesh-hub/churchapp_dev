<?php

namespace backend\controllers;

use Yii;
use common\models\extendedmodels\ExtendedMember;
use common\models\extendedmodels\ExtendedDependant;
use common\models\extendedmodels\ExtendedSettings;
use common\models\extendedmodels\ExtendedFamilyunit;
use common\models\extendedmodels\ExtendedTitle;
use common\models\extendedmodels\ExtendedAddresstype;
use common\models\extendedmodels\ExtendedUserCredentials;
use common\models\extendedmodels\ExtendedUserMember;
use common\models\extendedmodels\ExtendedMemberadditionalinfo;
use common\models\extendedmodels\ExtendedTempmemberadditionalinfomail;
use common\models\extendedmodels\ExtendedDeleteDependant;
use common\models\extendedmodels\ExtendedEditmember;
use common\models\extendedmodels\ExtendedTempmember;
use common\models\extendedmodels\ExtendedTempdependantmail;
use common\models\extendedmodels\ExtendedTempdependant;
use common\models\extendedmodels\ExtendedTempmemberadditionalinfo;
use common\models\extendedmodels\ExtendedProfileupdatenotification;
use common\models\searchmodels\ExtendedMemberSearch;
use common\models\extendedmodels\ExtendedStaffdesignation;
use common\models\searchmodels\ExtendedStaffSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ArrayDataProvider;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\helpers\BaseUrl;
use common\components\FileuploadComponent;
use yii\data\ActiveDataProvider;
use common\components\EmailHandlerComponent;
use common\models\extendedmodels\ExtendedInstitution;
use common\models\basemodels\CustomRoleModel;
use yii\filters\AccessControl;
use common\models\extendedmodels\ExtendedTempmembermail;
use common\models\basemodels\BaseModel;
use Exception;
use common\models\basemodels\UserOtp;
use common\models\searchmodels\NewMemberRegistrationSearch;
use common\helpers\CacheHelper;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * MemberController implements the CRUD actions for ExtendedMember model.
 */
class MemberController extends BaseController
{
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only' => [
					'index',
					'view',
					'create',
					'update',
					'member-approvel',
					'new-registered-members',
					'export-member-list',
					'export-members-filtered'
				],
				'rules' => [
					[
						'allow' => true,
						'actions' => ['index', 'new-registered-members', 'export-member-list', 'export-members-filtered'],
						'roles' => ['fe083df2-ec49-11e6-b48e-000c2990e707'] //view member list
					],
					[
						'allow' => true,
						'actions' => [
							'create',
							'update',
						],
						'roles' => ['1092473e-ec4a-11e6-b48e-000c2990e707'] // Add/edit member
					],
					[
						'allow' => true,
						'actions' => [
							'view'
						],
						'roles' => ['1d5bd81c-ec4a-11e6-b48e-000c2990e707'] //view member
					],
					[
						'allow' => true,
						'actions' => [
							'staff-list'
						],
						'roles' => ['27122647-ec4a-11e6-b48e-000c2990e707'] //view staff list
					],
					[
						'allow' => true,
						'actions' => [
							'staff-update, staff-register'
						],
						'roles' => ['316c0865-ec4a-11e6-b48e-000c2990e707'] //add/edit staff
					],
					[
						'allow' => true,
						'actions' => ['member-approvel'],
						'roles' => ['1092473e-ec4a-11e6-b48e-000c2990e707'],
						'denyCallback' => function ($rule, $action) {
							//to redirect to  home
							return $this->redirect('/');
						}
					]
				],
			],
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'delete' => ['POST'],
				],
			],
		];
	}

	public function beforeAction($action)
	{
		if (yii::$app->controller->action->id === "store-pending-details") {
			$this->enableCsrfValidation = false;
		}
		return parent::beforeAction($action);
	}
	/**
	 * Lists all ExtendedMember models also doing searching.
	 * @return mixed
	 */
	public function actionIndex()
	{
		$tempMember = new ExtendedTempmember();
		$institutionId =  $this->currentUser()->institutionid;
		$tempMemberDataProvider = ExtendedTempmember::find()->where(['temp_approved' => 0, 'temp_institutionid' => $institutionId])
			->orderBy('temp_createddate desc');
		$dataProvider1 = new ActiveDataProvider([
			'query' => $tempMemberDataProvider,
		]);

		$pendingRequests = $tempMember->getPendingMembers($institutionId);
		$pendingRequest = 0;
		if (isset($pendingRequests[0]['pendingmemmbercount'])) {
			$pendingRequest = $pendingRequests[0]['pendingmemmbercount'];
		}
		$searchModel = new ExtendedMemberSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams, $institutionId);

		$memberList =  $this->renderPartial('member_list', ['dataProvider' => $dataProvider, 'isStaff' => false]);

		$pendingMemberList = $this->renderPartial('pending_member_list', ['dataProvider' => $dataProvider1]);

		return $this->render('index', [
			'pendingMemberList' => $pendingMemberList,
			'memberList' => $memberList,
			'typeList'   => 'Member List',
			'pendingRequest' => $pendingRequest,
			'searchModel' => $searchModel,
			'action' => 'index',

		]);
	}

	/**
	 * Displays a single ExtendedMember model.
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionView($id)
	{
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
	}

	/**
	 * Creates a new ExtendedMember model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate()
	{
		$model           = new ExtendedMember();
		$settingsModel   = new ExtendedSettings();
		$familyUnitmodel = new ExtendedFamilyunit();
		$titleModel      = new ExtendedTitle();
		$memberadditionalModal = new ExtendedMemberadditionalinfo();
		//default setting
		$settingsModel->membernotification = 1;
		$settingsModel->birthday = 1;
		$settingsModel->anniversary = 1;
		$institusionId = $this->currentUser()->institutionid;
		$model->institutionid = $institusionId;
		$familyUnits = $familyUnitmodel->getActiveFamilyUnits($institusionId);

		$roleGroupId = $this->getMemberRoleGroupID("Member");
		$roleCategories = ArrayHelper::map(
			CustomRoleModel::getRoleCategories($roleGroupId, $institusionId),
			"RoleCategoryID",
			"Description"
		);

		$batches = BaseModel::getBatches(true);

		$familyUnitsArray = [];
		if (!empty($familyUnits)) {
			$familyUnitsArray =	ArrayHelper::map($familyUnits, 'familyunitid', 'description');
		}

		$titles = CacheHelper::getTitles($institusionId, function() use ($titleModel, $institusionId) {
			return $titleModel->getActiveTitles($institusionId);
		});
		$titlesArray = [];
		if (!empty($titles)) {
			$titlesArray =	ArrayHelper::map($titles, 'TitleId', 'Description');
		}
		$isMarried = ['1' => 'Single', '2' => "Married"];
		$bloodGroup = $this->getBloodGroup();
		$relations  = $this->getRelation();

		$dependantDetails  = $this->addDependantDetails();
		$addressTypes      = $this->getAddressTypes();

		if ($model->load(Yii::$app->request->post())) {

			$spouseImages = [];
			$memberImages = [];
			$imageType  = 'member';

			if (UploadedFile::getInstance($model, 'memberImageThumbnail')) {

				$memberImage = UploadedFile::getInstance($model, 'memberImageThumbnail');
				$targetPath = Yii::$app->params['image']['member']['main'] . '/' . Yii::$app->params['image']['member']['memberImage'];
				$thumbnail = Yii::$app->params['image']['member']['main'] . '/' . Yii::$app->params['image']['member']['memberthumbnailImage'];
				$memberImages = $this->fileUpload($memberImage, $targetPath, $thumbnail, $imageType);
			} else {
				$memberImages['orginal'] = '';
				$memberImages['thumbnail'] = '';
			}


			if (UploadedFile::getInstance($model, 'spouseImageThumbnail')) {

				$spouseImage = UploadedFile::getInstance($model, 'spouseImageThumbnail');
				$targetPath = Yii::$app->params['image']['member']['main'] . '/' . Yii::$app->params['image']['member']['spouseImage'];
				$thumbnail = Yii::$app->params['image']['member']['main'] . '/' . Yii::$app->params['image']['member']['spousethumbnailImage'];
				$spouseImages = $this->fileUpload($spouseImage, $targetPath, $thumbnail, $imageType);
			} else {
				$spouseImages['orginal'] = '';
				$spouseImages['thumbnail'] = '';
			}

			$response = $this->saveMemberdetails(Yii::$app->request->post(), $memberImages, $spouseImages, 'create');

			if (!$response['status']) {
				$this->sessionAddFlashArray('error', $response['msg'], true);
				return $this->render('create', [
					'model'            =>  $model,
					'dependantDetails' =>  $dependantDetails,
					//'spouseModel'      =>  $spouseModel,
					'settingsModel'    =>  $settingsModel,
					'addressTypes'     =>  $addressTypes,
					'relations'	 	   =>  $relations,
					'bloodGroup' 	   =>  $bloodGroup,
					'titlesArray'      =>  $titlesArray,
					'familyUnitsArray' =>  $familyUnitsArray,
					'isMarried'	       => $isMarried,
					'memberadditionalModal' => $memberadditionalModal,
					'type' => 'Member',
					'roleCategories' => $roleCategories,
					'batches' => $batches
				]);
			}
			if ($response['status']) {
				$this->sessionAddFlashArray('success', $response['msg'], true);
				return $this->redirect(['index']);
			}
		}
		$model->member_mobile1_countrycode = $model->spouse_mobile1_countrycode = "+91";
		return $this->render('create', [
			'model'            =>  $model,
			'dependantDetails'  =>  $dependantDetails,
			//'spouseModel'      =>  $spouseModel,
			'settingsModel'    =>  $settingsModel,
			'addressTypes'     =>  $addressTypes,
			'relations'	 	   =>  $relations,
			'bloodGroup' 	   =>  $bloodGroup,
			'titlesArray'      =>  $titlesArray,
			'familyUnitsArray' =>  $familyUnitsArray,
			'isMarried'	       => $isMarried,
			'memberadditionalModal' => $memberadditionalModal,
			'type' => 'Member',
			'roleCategories' => $roleCategories,
			'batches' => $batches
		]);
	}

	/**
	 * Updates an existing ExtendedMember model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate($id)
	{
		Yii::$app->session->removeAllFlashes();
		$model = $this->findModel($id);
		$institusionId = $this->currentUser()->institutionid;
		$selectedSpouseCat = "";
		$selectedMemberCat = "";
		$roleGroupId = $this->getMemberRoleGroupID("Member");
		$roleCategories = ArrayHelper::map(
			CustomRoleModel::getRoleCategories($roleGroupId, $institusionId),
			"RoleCategoryID",
			"Description"
		);
		$batches = BaseModel::getBatches(true);

		$settingsModel =  ExtendedSettings::find()->where(['memberid' => $id])->one();
		if (!$settingsModel) {
			$settingsModel = new ExtendedSettings();
		}

		$member_userid =  $userMemberId = ExtendedUserMember::getUserMemberId($id, $this->currentUser()->institutionid, ExtendedMember::USER_TYPE_MEMBER);
		$spouse_userid =  $userMemberId = ExtendedUserMember::getUserMemberId($id, $this->currentUser()->institutionid, ExtendedMember::USER_TYPE_SPOUSE);
		if ($member_userid) {
			$memberRoleData = CustomRoleModel::loadMemberRole($member_userid);
			if (!empty($memberRoleData)) {
				$selectedMemberCat = $memberRoleData['RoleCategoryID'];
			}
		}

		if ($spouse_userid) {
			$spouseRoleData = CustomRoleModel::loadMemberRole($spouse_userid);
			if (!empty($spouseRoleData)) {
				$selectedSpouseCat = $spouseRoleData['RoleCategoryID'];
			}
		}
		if ($model && $model->institutionid == $institusionId && $model->membertype == 0) {
			// settingsModel already loaded at line 322, no need to query again
			$familyUnitmodel = new ExtendedFamilyunit();
			$titleModel      = new ExtendedTitle();
			$spouseImages = [];
			$memberImages = [];

			$memberadditionalModal = ExtendedMemberadditionalinfo::find()->where(['memberid' => $id])->one();
			if ($memberadditionalModal == null) {
				$memberadditionalModal      = new ExtendedMemberadditionalinfo();
			}

			$memberImages['orginal'] = $model->member_pic;
			$memberImages['thumbnail'] = $model->memberImageThumbnail;
			$spouseImages['orginal'] = $model->spouse_pic;
			$spouseImages['thumbnail'] = $model->spouseImageThumbnail;

			$familyUnits = $familyUnitmodel->getActiveFamilyUnits($institusionId);

			$familyUnitsArray = [];
			if (!empty($familyUnits)) {
				$familyUnitsArray =	ArrayHelper::map($familyUnits, 'familyunitid', 'description');
			}

			// Cache titles for 1 hour (3600 seconds) to reduce database queries
			$titles = CacheHelper::getTitles($institusionId, function() use ($titleModel, $institusionId) {
				return $titleModel->getActiveTitles($institusionId);
			});
			$titlesArray = [];
			if (!empty($titles)) {
				$titlesArray =	ArrayHelper::map($titles, 'TitleId', 'Description');
			}
			
			$bloodGroup = $this->getBloodGroup();
			$relations = $this->getRelation();
			$addressTypes = $this->getAddressTypes();
			
			$dependantDetails  = 	$this->addDependantDetails($id);
			$isMarried = ['1' => 'Single', '2' => "Married"];
			if ($model->load(Yii::$app->request->post())) {

				$imageType  = 'member';
				if (UploadedFile::getInstance($model, 'memberImageThumbnail')) {
					$this->unlinkFile($memberImages['orginal'], $memberImages['thumbnail']);
					$memberImage = UploadedFile::getInstance($model, 'memberImageThumbnail');
					$targetPath = Yii::$app->params['image']['member']['main'] . '/' . Yii::$app->params['image']['member']['memberImage'];
					$thumbnail = Yii::$app->params['image']['member']['main'] . '/' . Yii::$app->params['image']['member']['memberthumbnailImage'];
					$memberImages = $this->fileUpload($memberImage, $targetPath, $thumbnail, $imageType);
				}
				if (UploadedFile::getInstance($model, 'spouseImageThumbnail')) {

					$this->unlinkFile($spouseImages['orginal'], $spouseImages['thumbnail']);
					$spouseImage = UploadedFile::getInstance($model, 'spouseImageThumbnail');
					$targetPath = Yii::$app->params['image']['member']['main'] . '/' . Yii::$app->params['image']['member']['spouseImage'];
					$thumbnail = Yii::$app->params['image']['member']['main'] . '/' . Yii::$app->params['image']['member']['spousethumbnailImage'];
					$spouseImages = $this->fileUpload($spouseImage, $targetPath, $thumbnail, $imageType);
				}


				$response = $this->saveMemberdetails(Yii::$app->request->post(), $memberImages, $spouseImages, 'update', $id);

				if ($response['status']) {

					$this->sessionAddFlashArray('success', $response['msg'], true);

					return $this->redirect(['index']);
				} else {
					$this->sessionAddFlashArray('error', $response['msg'], true);
					return $this->render('update', [
						'model'            =>  $model,
						'dependantDetails'	       =>  $dependantDetails,
						//'spouseModel'      =>  $spouseModel,
						'settingsModel'    =>  $settingsModel,
						'addressTypes'     =>  $addressTypes,
						'relations'	 	   =>  $relations,
						'bloodGroup' 	   =>  $bloodGroup,
						'titlesArray'      =>  $titlesArray,
						'familyUnitsArray' =>  $familyUnitsArray,
						'isMarried'	       => $isMarried,
						'memberadditionalModal' => $memberadditionalModal,
						'type' => 'Member',
						'roleCategories' => $roleCategories,
						'selectedSpouseCat' => $selectedSpouseCat,
						'selectedMemberCat' => $selectedMemberCat,
						'batches' => $batches
					]);
				}
			}
			return $this->render('update', [
				'model'            =>  $model,
				'dependantDetails'	       =>  $dependantDetails,
				//'spouseModel'      =>  $spouseModel,
				'settingsModel'    =>  $settingsModel,
				'addressTypes'     =>  $addressTypes,
				'relations'	 	   =>  $relations,
				'bloodGroup' 	   =>  $bloodGroup,
				'titlesArray'      =>  $titlesArray,
				'familyUnitsArray' =>  $familyUnitsArray,
				'isMarried'	       => $isMarried,
				'memberadditionalModal' => $memberadditionalModal,
				'type' => 'Member',
				'roleCategories' => $roleCategories,
				'selectedSpouseCat' => $selectedSpouseCat,
				'selectedMemberCat' => $selectedMemberCat,
				'batches' => $batches
			]);
		} else {
			$this->sessionAddFlashArray('error', "Member details not available !", true);
			return $this->redirect(['index']);
		}
	}

	/**
	 * Updates an existing ExtendedMember model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionStaffUpdate($id)
	{

		$model           = $this->findModel($id);
		$institusionId = $this->currentUser()->institutionid;
		$roleGroupId = $this->getMemberRoleGroupID("Staff");
		$selectedSpouseCat = "";
		$selectedMemberCat = "";
		$roleCategories = ArrayHelper::map(
			CustomRoleModel::getRoleCategories($roleGroupId, $institusionId),
			"RoleCategoryID",
			"Description"
		);

		$batches = BaseModel::getBatches(true);

		$settingsModel =  ExtendedSettings::find()->where(['memberid' => $id])->one();
		if (!$settingsModel) {
			$settingsModel = new ExtendedSettings();
		}

		$member_userid =  $userMemberId = ExtendedUserMember::getUserMemberId($id, $this->currentUser()->institutionid, ExtendedMember::USER_TYPE_MEMBER);
		$spouse_userid =  $userMemberId = ExtendedUserMember::getUserMemberId($id, $this->currentUser()->institutionid, ExtendedMember::USER_TYPE_SPOUSE);

		if ($member_userid) {
			$memberRoleData = CustomRoleModel::loadMemberRole($member_userid);
			if (!empty($memberRoleData)) {
				$selectedMemberCat = $memberRoleData['RoleCategoryID'];
			}
		}
		if ($spouse_userid) {
			$spouseRoleData = CustomRoleModel::loadMemberRole($spouse_userid);
			if (!empty($spouseRoleData)) {
				$selectedSpouseCat = $spouseRoleData['RoleCategoryID'];
			}
		}
		if ($model && $model->institutionid == $institusionId && $model->membertype == 1) {
			// settingsModel already loaded at line 322, no need to query again
			$familyUnitmodel = new ExtendedFamilyunit();
			$titleModel      = new ExtendedTitle();
			$spouseImages = [];
			$memberImages = [];

			$memberadditionalModal = ExtendedMemberadditionalinfo::find()->where(['memberid' => $id])->one();
			if ($memberadditionalModal == null) {
				$memberadditionalModal      = new ExtendedMemberadditionalinfo();
			}

			$memberImages['orginal'] = $model->member_pic;
			$memberImages['thumbnail'] = $model->memberImageThumbnail;
			$spouseImages['orginal'] = $model->spouse_pic;
			$spouseImages['thumbnail'] = $model->spouseImageThumbnail;

			$institusionId = $this->currentUser()->institutionid;
			$familyUnits = $familyUnitmodel->getActiveFamilyUnits($institusionId);
			$familyUnitsArray = [];
			if (!empty($familyUnits)) {
				$familyUnitsArray =	ArrayHelper::map($familyUnits, 'familyunitid', 'description');
			}
			
			// Cache titles for 1 hour to reduce database queries
			$titles = CacheHelper::getTitles($institusionId, function() use ($titleModel, $institusionId) {
				return $titleModel->getActiveTitles($institusionId);
			});

			$titlesArray = [];

			if (!empty($titles)) {
				$titlesArray =	ArrayHelper::map($titles, 'TitleId', 'Description');
			}
			
			$bloodGroup = $this->getBloodGroup();
			$relations = $this->getRelation();
			$addressTypes = $this->getAddressTypes();
			
			$staffDesignationList = ExtendedStaffdesignation::getStaffList($institusionId);
			$staffDesignationListArray = [];
			if (!empty($staffDesignationList)) {
				$staffDesignationListArray = ArrayHelper::map($staffDesignationList, 'staffdesignationid', 'designation');
			}
			$dependantDetails  = 	$this->addDependantDetails($id);
			$isMarried = ['1' => 'Single', '2' => "Married"];
			if ($model->load(Yii::$app->request->post())) {
				$imageType  = 'member';
				if (UploadedFile::getInstance($model, 'memberImageThumbnail')) {
					$this->unlinkFile($memberImages['orginal'], $memberImages['thumbnail']);
					$memberImage = UploadedFile::getInstance($model, 'memberImageThumbnail');
					$targetPath = Yii::$app->params['image']['member']['main'] . '/' . Yii::$app->params['image']['member']['memberImage'];
					$thumbnail = Yii::$app->params['image']['member']['main'] . '/' . Yii::$app->params['image']['member']['memberthumbnailImage'];
					$memberImages = $this->fileUpload($memberImage, $targetPath, $thumbnail, $imageType);
				}
				if (UploadedFile::getInstance($model, 'spouseImageThumbnail')) {
					$this->unlinkFile($spouseImages['orginal'], $spouseImages['thumbnail']);
					$spouseImage = UploadedFile::getInstance($model, 'spouseImageThumbnail');
					$targetPath = Yii::$app->params['image']['member']['main'] . '/' . Yii::$app->params['image']['member']['spouseImage'];
					$thumbnail = Yii::$app->params['image']['member']['main'] . '/' . Yii::$app->params['image']['member']['spousethumbnailImage'];
					$spouseImages = $this->fileUpload($spouseImage, $targetPath, $thumbnail, $imageType);
				}
				$response = $this->saveMemberdetails(Yii::$app->request->post(), $memberImages, $spouseImages, 'update', $id, 'staff');
				if ($response['status']) {
					$this->sessionAddFlashArray('success', $response['msg'], true);
					return $this->redirect(['staff-list']);
				} else {
					$this->sessionAddFlashArray('success', $response['msg'], true);
					//blood group change to staf designation for staff
					return $this->render('update', [
						'model'            =>  $model,
						'dependantDetails'	       =>  $dependantDetails,
						//'spouseModel'      =>  $spouseModel,
						'settingsModel'    =>  $settingsModel,
						'addressTypes'     =>  $addressTypes,
						'relations'	 	   =>  $relations,
						'bloodGroup' 	   =>  $staffDesignationListArray,
						'titlesArray'      =>  $titlesArray,
						'familyUnitsArray' =>  $familyUnitsArray,
						'isMarried'	       => $isMarried,
						'memberadditionalModal' => $memberadditionalModal,
						'type' => 'Staff',
						'roleCategories' => $roleCategories,
						'selectedSpouseCat' => $selectedSpouseCat,
						'selectedMemberCat' => $selectedMemberCat,
						'batches' => $batches
					]);
				}
			}
			return $this->render('update', [
				'model'            =>  $model,
				'dependantDetails'	       =>  $dependantDetails,
				//'spouseModel'      =>  $spouseModel,
				'settingsModel'    =>  $settingsModel,
				'addressTypes'     =>  $addressTypes,
				'relations'	 	   =>  $relations,
				'bloodGroup' 	   =>  $staffDesignationListArray,
				'titlesArray'      =>  $titlesArray,
				'familyUnitsArray' =>  $familyUnitsArray,
				'isMarried'	       => $isMarried,
				'memberadditionalModal' => $memberadditionalModal,
				'type' => 'Staff',
				'roleCategories' => $roleCategories,
				'selectedSpouseCat' => $selectedSpouseCat,
				'selectedMemberCat' => $selectedMemberCat,
				'batches' => $batches
			]);
		} else {
			$this->sessionAddFlashArray('error', "Member details not available!", true);
			return $this->redirect(['staff-list']);
		}
	}


	/**
	 * Deletes an existing ExtendedMember model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionDelete()
	{

		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		if (yii::$app->request->isAjax) {
			$memberId = yii::$app->request->post('memberId');
			$member = $this->findModel($memberId);
			if ($member) {
				$institutionId = $this->currentUser()->institutionid;
				try {
					$response = $member->deleteMember($memberId, $institutionId);
					if ($response) {
						return ['status' => "success", 'msg' => "Member deleted successfully"];
					}
				} catch (\Exception $e) {
					return ['status' => "error", 'msg' => $e->getMessage()];
				}
			}
			return ['status' => "error", 'msg' => "Unable to proccess the request"];
		}
		return $this->redirect(['index']);
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
		// Use eager loading to reduce database queries
		if (($model = ExtendedMember::find()
			->where(['memberid' => $id])
			->with(['membertitle0', 'spousetitle0', 'institution', 'familyunit'])
			->one()) !== null) {
			return $model;
		}
		throw new NotFoundHttpException('The requested page does not exist.');
	}

	/*
     * to sent the generated link
     */

	public function actionSendGeneratedlink()
	{

		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		if (yii::$app->request->isAjax) {
			$genaratedUrl = yii::$app->request->post('genratedUrl');
			$memberId = yii::$app->request->post('memberId');
			try {

				$model = $this->findModel($memberId);
				if ($model->member_email == '' || $model->member_email == null) {
					return 'email_error';
				}
				$institusionId = $this->currentUser()->institutionid;
				if ($model && $model->institutionid == $institusionId && $model->membertype == 0) {
					$mailContent = [];
					$from = yii::$app->params['re-memberEmail'];
					$to = $model->member_email;
					$subject = "Update membership information";
					$title = '';
					$emailModal = 	new EmailHandlerComponent();
					$mailContent['template'] = 'link-email';
					$mailContent['generatedlink'] = $genaratedUrl;
					$srTitle      =  $model->membertitle0['Description'] ? $model->membertitle0['Description'] : '';
					$firstName  =  $model->firstName ? $model->firstName : ' ';
					$middleName =  $model->middleName ? $model->middleName : ' ';
					$lastName   =  $model->lastName ? $model->lastName : '';
					$displayName = $srTitle . ' ' . $firstName . ' ' . $middleName . ' ' . $lastName;
					$mailContent['name'] = $firstName;
					$mailContent['institutionname'] = $model->institution->name;
					$institutionImage = empty($model->institution->institutionlogo) ? '/institution/institution-icon-grey.png' : $model->institution->institutionlogo;
					$mailContent['logo'] = Yii::$app->params['imagePath'] . $institutionImage;
					$attach = '';
					$temp = $emailModal->sendEmail($from, $to, $title, $subject, $mailContent, $attach);
					if ($temp) {
						return 'success';
					} else {
						return 'Error';
					}
				} else {
					return 'Error';
				}
			} catch (Exception $e) {
				return 'Error';
			}
		}
	}
	/*
     * To store the dependant details
     * 
     */
	public function actionStoreDependantDetails()
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		if (yii::$app->request->isAjax) {
			//          $rawData = yii::$app->request->post('postData');
			//     		$reqDetails = (array)json_decode($rawData);
			//     	    $memberId = $reqDetails['memberId'];
			//     		$formType = $reqDetails['formType'];
			$reqDetails = yii::$app->request->post();
			$memberId = yii::$app->request->post('memberId');
			$formType = yii::$app->request->post('formType');
			$reqDetails = (array)json_decode($reqDetails['data']);

			$dependantImage = !empty($_FILES['dependantImage']) ? $_FILES['dependantImage'] : null;
			$dependantSpouseImage = !empty($_FILES['dependantSpouseImage']) ? $_FILES['dependantSpouseImage'] : null;
			$imageType  = 'member';
			$dependantImages['orginal'] = '';
			$dependantImages['thumbnail'] = '';
			if ($reqDetails['dependantId'] != '' || !empty($reqDetails['dependantId'])) {
				$dependantModel = ExtendedDependant::findOne($reqDetails['dependantId']);
				$dependantImages['orginal'] = $dependantModel->image;
				$dependantImages['thumbnail'] = $dependantModel->thumbnailimage;
			} else {
				$dependantModel = new ExtendedDependant();
			}
			if (!empty($dependantImage['name'])) {
				$this->unlinkFile($dependantImages['orginal'], $dependantImages['thumbnail']);
				$targetPath = Yii::$app->params['image']['member']['main'] . '/' . Yii::$app->params['image']['member']['dependantImage'];
				$thumbnail = Yii::$app->params['image']['member']['main'] . '/' . Yii::$app->params['image']['member']['thumbnailDepentant'];
				$dependantImages = $this->fileUpload($dependantImage, $targetPath, $thumbnail, $imageType, true);
			}
			$reqDetails['dependantId'] = '';
			if ($formType == 'create') {
				$response = $this->storeDependant($dependantModel, $reqDetails, $dependantImages, null, $memberId);
			} else {
				$response = $this->storeDependant($dependantModel, $reqDetails, $dependantImages, $memberId, null);
			}
			if ($response) {
				$reqDetails['dependantId'] = $response->id;
				$reqDetails['relation'] = '';
				$reqDetails['dependantName'] = $reqDetails['dependantspousename'];
				$reqDetails['dependantdob'] = $reqDetails['dependantspousedob'];

				if ($reqDetails['dependantMartialStatus'] == 2) {

					$imageType  = 'member';
					$dependantSpouseImages['orginal'] = '';
					$dependantSpouseImages['thumbnail'] = '';

					if ($reqDetails['spouseDependantId'] != '' || !empty($reqDetails['spouseDependantId'])) {

						$dependantSpouseModel = ExtendedDependant::findOne($reqDetails['spouseDependantId']);
						$dependantSpouseImages['orginal'] = $dependantSpouseModel->image;
						$dependantSpouseImages['thumbnail'] = $dependantSpouseModel->thumbnailimage;
					} else {
						$dependantSpouseModel = new ExtendedDependant();
					}

					$reqDetails['dependantMartialStatus'] = '';
					$reqDetails['dependantTitleId'] = $reqDetails['dependantspousetitleid'];
					$reqDetails['dependantMobileCountryCode'] = $reqDetails['dependantSpouseMobileCountryCode'];
					$reqDetails['dependantMobile'] = $reqDetails['dependantSpouseMobile'];
					$reqDetails['dependantOccupation'] = $reqDetails['dependantspouseoccupation'] ?? '';
					$reqDetails['dependantActive'] = $reqDetails['dependantspouseactive'] ?? 0;
					$reqDetails['dependantConfirmed'] = $reqDetails['dependantspouseconfirmed'] ?? 0;

					if (!empty($dependantSpouseImage['name'])) {

						$this->unlinkFile($dependantSpouseImages['orginal'], $dependantSpouseImages['thumbnail']);

						$targetPath = Yii::$app->params['image']['member']['main'] . '/' . Yii::$app->params['image']['member']['dependantSpouse'];
						$thumbnail = Yii::$app->params['image']['member']['main'] . '/' . Yii::$app->params['image']['member']['thumbnailDependantSpouse'];
						$dependantSpouseImages = $this->fileUpload($dependantSpouseImage, $targetPath, $thumbnail, $imageType, true);
					}

					if ($formType == 'create') {
						$response = $this->storeDependant($dependantSpouseModel, $reqDetails, $dependantSpouseImages, null, $memberId);
					} else {
						$response = $this->storeDependant($dependantSpouseModel, $reqDetails, $dependantSpouseImages, $memberId, null);
					}
				} else {
					if ($reqDetails['spouseDependantId'] != '' || !empty($reqDetails['spouseDependantId'])) {
						$dependantSpouseModel = ExtendedDependant::findOne($reqDetails['spouseDependantId']);
						$this->deleteDepentant($dependantSpouseModel);
					}
				}
			}
		}
		if ($formType == 'create') {
			yii::error("memberId " . $memberId);
			$modifiedDependantRecords = $this->addDependantDetails(null, $memberId, true);
		} else {
			$modifiedDependantRecords = $this->addDependantDetails($memberId, null, true);
		}
		return $modifiedDependantRecords;
	}

	/**
	 * To delete dependant
	 * @return string
	 */
	public function actionDeleteDependant()
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		if (yii::$app->request->isAjax) {
			$dependantId = yii::$app->request->post('id');
			$memberId = yii::$app->request->post('memberId');
			$formType = yii::$app->request->post('formType');
			$dependantModel = ExtendedDependant::findOne($dependantId);
			if ($dependantModel) {
				$sql1 = 'DELETE FROM `tempdependant` WHERE dependantid = :dependantId';
				$sql2 = 'DELETE FROM `tempdependantmail` WHERE dependantid = :dependantId';
				$db = Yii::$app->db;
				$transaction = $db->beginTransaction();
				try {
					$db->createCommand($sql1)->bindValue(':dependantId', $dependantId)->execute();
					$db->createCommand($sql2)->bindValue(':dependantId', $dependantId)->execute();
					$transaction->commit();
				} catch (\Exception $e) {
					$transaction->rollBack();
					yii::error($e->getMessage());
				}
				$this->deleteDepentant($dependantModel);
			}
			$newExtended = new ExtendedDependant();
			$spouseModelObj = $newExtended->find()->where(['dependantid' => $dependantId])->one();
			if ($spouseModelObj) {
				$this->deleteDepentant($spouseModelObj);
			}
			if ($formType == 'create') {
				$modifiedDependantRecords = $this->addDependantDetails(null, $memberId, true);
			} else {
				$modifiedDependantRecords = $this->addDependantDetails($memberId, null, true);
			}
			return $modifiedDependantRecords;
		}
	}

	/**
	 * To get the dependant edit record's html
	 * @return string
	 */
	public function actionEditDependant()
	{

		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		if (yii::$app->request->isAjax) {
			$dependantId = yii::$app->request->post('id');
			$memberId = yii::$app->request->post('memberId');
			$dependantModel = ExtendedDependant::findOne($dependantId);
			$spouseModel     = new \yii\base\DynamicModel(['id', 'spouse_title', 'spouse_name', 'wedding_anniversary', 'photo', 'dob', 'weddinganniversary', 'spouseImage', 'spouse_mobile_country_code', 'spouse_mobile', 'spouse_occupation', 'active', 'confirmed']);
			//$dependantDetails = $dependantModel->getDependants(338);
			if ($dependantModel->ismarried == 2) {
				$newExtended = new ExtendedDependant();
				$spouseModelObj = $newExtended->find()->where(['dependantid' => $dependantId])->one();
				if (!empty($spouseModelObj)) {
					$spouseModel->spouse_title = $spouseModelObj->titleid;
					$spouseModel->spouse_name = $spouseModelObj->dependantname;
					$spouseModel->wedding_anniversary = $spouseModelObj->weddinganniversary;
					$spouseModel->dob = $spouseModelObj->dob;
					$spouseModel->spouseImage = $spouseModelObj->image;
					$spouseModel->id = $spouseModelObj->id;
					$spouseModel->spouse_mobile_country_code = $spouseModelObj->dependantmobilecountrycode;
					$spouseModel->spouse_mobile = $spouseModelObj->dependantmobile;
					$spouseModel->spouse_occupation = $spouseModelObj->occupation ?? '';
					$spouseModel->active = $spouseModelObj->active ?? 0;
					$spouseModel->confirmed = $spouseModelObj->confirmed ?? 0;
				}
			}
			$titleModel      = new ExtendedTitle();
			$institusionId = $this->currentUser()->institutionid;
			$titles = CacheHelper::getTitles($institusionId, function() use ($titleModel, $institusionId) {
				return $titleModel->getActiveTitles($institusionId);
			});
			$relations  = $this->getRelation();
			$isMarried = ['1' => 'Single', '2' => "Married"];
			$titlesArray = [];
			if (!empty($titles)) {
				$titlesArray =	ArrayHelper::map($titles, 'TitleId', 'Description');
			}
			$memberDependantForm =  $this->renderAjax('dependantcreate', [
				'dependantModel' => $dependantModel,
				'spouseModel'  => $spouseModel,
				'titlesArray'  => $titlesArray,
				'relations'	   =>  $relations,
				'isMarried'    => $isMarried,
				'memberId'       =>  $memberId,
			]);
			return  $memberDependantForm;
		}
	}
	/**
	 * To listing the staff
	 * @return Ambigous <string, string>
	 */
	public function actionStaffList()
	{
		$searchModel = new ExtendedStaffSearch();
		$institutionId =  $this->currentUser()->institutionid;
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams, $institutionId);
		$memberList =  $this->renderPartial('member_list', ['dataProvider' => $dataProvider, 'isStaff' => true]);
		$pendingMemberList = '';
		return $this->render('index', [
			'pendingMemberList' => $pendingMemberList,
			'memberList' => $memberList,
			'typeList'   => 'Staff List',
			'searchModel' => $searchModel,
			'action' => 'staff-list',
		]);
	}

	/**
	 * Staff creationS
	 * @return Ambigous <string, string>
	 */
	public function actionStaffRegister()
	{

		$model           = new ExtendedMember();
		$settingsModel   = new ExtendedSettings();
		$familyUnitmodel = new ExtendedFamilyunit();
		$titleModel      = new ExtendedTitle();
		$memberadditionalModal = new ExtendedMemberadditionalinfo();
		$institusionId = $this->currentUser()->institutionid;
		$model->institutionid = $institusionId;
		$familyUnitsArray = [];
		$roleGroupId = $this->getMemberRoleGroupID("Staff");
		$roleCategories = ArrayHelper::map(
			CustomRoleModel::getRoleCategories($roleGroupId, $institusionId),
			"RoleCategoryID",
			"Description"
		);

		$batches = BaseModel::getBatches(true);

		$settingsModel->membernotification = 1;
		$settingsModel->birthday = 1;
		$settingsModel->anniversary = 1;

		$titles = CacheHelper::getTitles($institusionId, function() use ($titleModel, $institusionId) {
			return $titleModel->getActiveTitles($institusionId);
		});
		$titlesArray = [];
		if (!empty($titles)) {
			$titlesArray =	ArrayHelper::map($titles, 'TitleId', 'Description');
		}
		$bloodGroup = $this->getBloodGroup();
		$relations  = $this->getRelation();
		$staffDesignationList = ExtendedStaffdesignation::getStaffList($institusionId);
		$staffDesignationListArray = [];
		if (!empty($staffDesignationList)) {
			$staffDesignationListArray = ArrayHelper::map($staffDesignationList, 'staffdesignationid', 'designation');
		}
		$dependantDetails  = 	'';
		$addressTypes = $this->getAddressTypes();
		$isMarried = ['1' => 'Single', '2' => "Married"];
		if ($model->load(Yii::$app->request->post())) {
			$spouseImages = [];
			$memberImages = [];
			$imageType  = 'member';
			if (UploadedFile::getInstance($model, 'memberImageThumbnail')) {
				$memberImage = UploadedFile::getInstance($model, 'memberImageThumbnail');
				$targetPath = Yii::$app->params['image']['member']['main'] . '/' . Yii::$app->params['image']['member']['memberImage'];
				$thumbnail = Yii::$app->params['image']['member']['main'] . '/' . Yii::$app->params['image']['member']['memberthumbnailImage'];
				$memberImages = $this->fileUpload($memberImage, $targetPath, $thumbnail, $imageType);
			} else {
				$memberImages['orginal'] = '';
				$memberImages['thumbnail'] = '';
			}
			if (UploadedFile::getInstance($model, 'spouseImageThumbnail')) {
				$spouseImage = UploadedFile::getInstance($model, 'spouseImageThumbnail');
				$targetPath = Yii::$app->params['image']['member']['main'] . '/' . Yii::$app->params['image']['member']['spouseImage'];
				$thumbnail = Yii::$app->params['image']['member']['main'] . '/' . Yii::$app->params['image']['member']['spousethumbnailImage'];
				$spouseImages = $this->fileUpload($spouseImage, $targetPath, $thumbnail, $imageType);
			} else {
				$spouseImages['orginal'] = '';
				$spouseImages['thumbnail'] = '';
			}
			$response = $this->saveMemberdetails(Yii::$app->request->post(), $memberImages, $spouseImages, 'create', null, 'staff');
			if (!$response['status']) {
				$this->sessionAddFlashArray('error', $response['msg'], true);
				//Blood group array change to staffDesignationListArray for staff designationn
				return $this->render('create', [
					'model'            =>  $model,
					'dependantDetails' =>  $dependantDetails,
					//'spouseModel'      =>  $spouseModel,
					'settingsModel'    =>  $settingsModel,
					'addressTypes'     =>  $addressTypes,
					'relations'	 	   =>  $relations,
					'bloodGroup' 	   =>  $staffDesignationListArray,
					'titlesArray'      =>  $titlesArray,
					'familyUnitsArray' =>  $familyUnitsArray,
					'isMarried'	       => $isMarried,
					'memberadditionalModal' => $memberadditionalModal,
					'type' => 'Staff',
					'roleCategories' => $roleCategories,
					'batches' => $batches
				]);
			}
			if ($response['status']) {
				$this->sessionAddFlashArray('success', $response['msg'], true);
				return $this->redirect(['staff-list']);
			}
		}
		return $this->render('create', [
			'model'            =>  $model,
			'dependantDetails'  =>  $dependantDetails,
			//'spouseModel'      =>  $spouseModel,
			'settingsModel'    =>  $settingsModel,
			'addressTypes'     =>  $addressTypes,
			'relations'	 	   =>  $relations,
			'bloodGroup' 	   =>  $staffDesignationListArray,
			'titlesArray'      =>  $titlesArray,
			'familyUnitsArray' =>  $familyUnitsArray,
			'isMarried'	       => $isMarried,
			'memberadditionalModal' => $memberadditionalModal,
			'type' => 'Staff',
			'roleCategories' => $roleCategories,
			'batches' => $batches
		]);
	}

	/**
	 * To upload the images
	 */
	protected  function fileUpload($image, $targetPath, $thumbnail, $imageType, $isArray = false)
	{

		$fileHandlerObj = new FileuploadComponent();
		if ($isArray) {
			$tempName = $image['tmp_name'];
			$uploadFilename = $image['name'];
		} else {
			$tempName = $image->tempName;
			$uploadFilename = $image->name;
		}
		$uploadImages = $fileHandlerObj->uploader($uploadFilename, $targetPath, $tempName, $thumbnail, $imageType, false, false);
		return $uploadImages;
	}
	/**
	 * To generate url for member edit
	 * @return string|NULL
	 */
	public function actionGenerateMemberUrl()
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		if (yii::$app->request->isAjax) {
			$memberId = yii::$app->request->post('memberId');
			if (($model = ExtendedMember::findOne($memberId)) !== null) {
				$uniqueId = Yii::$app->getSecurity()->generateRandomString();
				$uniqueId = str_replace("-", "_", $uniqueId);
				if (($editMemberModel = ExtendedEditmember::find()->where(['memberid' => $memberId])->one()) != null) {
					$editMemberModel->tempmemberid = $uniqueId;
				} else {
					$editMemberModel = new ExtendedEditmember();
					$editMemberModel->memberid = $memberId;
					$editMemberModel->tempmemberid = $uniqueId;
				}
				if ($editMemberModel->save()) {
					return BaseUrl::base(true) . '/member/member-edit/' . $uniqueId;
				} else {
					return null;
				}
			}
		}
	}

	public function actionGetMemberProfileUrl()
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		if (yii::$app->request->isAjax) {
			$memberId = yii::$app->request->post('memberId');
			$memberType = yii::$app->request->post('memberType');
			$memParam = ($memberType == 'member') ? 'MM' : 'SP';
			if (($model = ExtendedMember::findOne($memberId)) !== null) {
				$uniqueId = Yii::$app->getSecurity()->generateRandomString();
				$uniqueId = str_replace("-", "_", $uniqueId);
				if (($editMemberModel = ExtendedEditmember::find()->where(['memberid' => $memberId])->one()) != null) {
					$editMemberModel->tempmemberid = $uniqueId;
				} else {
					$editMemberModel = new ExtendedEditmember();
					$editMemberModel->memberid = $memberId;
					$editMemberModel->tempmemberid = $uniqueId;
				}
				if ($editMemberModel->save()) {
					return yii::$app->params['memberProfileUrl'] . $memParam . $uniqueId;
				} else {
					return null;
				}
			}
		}
	}

	/**
	 * To generate public url for member edit
	 * @return string|NULL
	 */
	public function actionEditMemberDetails()
	{
		$otp = '';
		$showOTPBox = false;
		$errorMessage = '';
		$successMessage = '';
		$memberPhoneNumber = '';
		if (Yii::$app->request->post()) {
			$memberPhoneNumber = yii::$app->request->post('memberMobileNumber');
			$otp = yii::$app->request->post('otp');
			/* $memberDetails = ExtendedMember::find()
				->where([
					'member_mobile1' => $memberPhoneNumber, 
					'institutionid' => 48
				])->one(); */
			if ($memberPhoneNumber == 9400984372) {
				$memberDetails = ExtendedMember::find()
					->where([
						'member_mobile1' => $memberPhoneNumber,
						'institutionid' => 66
					])->one();
			} else {
				$memberDetails = ExtendedMember::find()
					->where([
						'member_mobile1' => $memberPhoneNumber,
						'institutionid' => 48
					])->one();
			}
			if ($memberDetails !== null) {
				$memberId = $memberDetails->memberid;
				if (!empty($otp)) {
					$showOTPBox = true;
					$model = UserOtp::find()
						->where([
							'otp' => $otp,
							'mobile_number' => $memberPhoneNumber,
							'request_type' => 'profile-edit'
						])->one();
					if ($model !== null) {
						$fdate = new \DateTime($model->updated_at);
						$fdate->modify("+10 minutes");
						if ($fdate > new \DateTime()) {
							$uniqueId = Yii::$app->getSecurity()->generateRandomString();
							$uniqueId = str_replace("-", "_", $uniqueId);
							if (($editMemberModel = ExtendedEditmember::find()->where(['memberid' => $memberId])->one()) != null) {
								$editMemberModel->tempmemberid = $uniqueId;
							} else {
								$editMemberModel = new ExtendedEditmember();
								$editMemberModel->memberid = $memberId;
								$editMemberModel->tempmemberid = $uniqueId;
							}
							if ($editMemberModel->save()) {
								$this->redirect(BaseUrl::base(true) . '/member/member-edit/' . $uniqueId);
							} else {
								$errorMessage = "Sorry, something went wrong. Please try again.";
							}
						} else {
							$showOTPBox = false;
							$errorMessage = 'OTP is expired';
						}
					} else {
						$errorMessage = "OTP is not valid.";
					}
				} else {
					$response = yii::$app->communicationManager->generateOtpNew($memberPhoneNumber, 'profile-edit');
					if ($response->ErrorCode == 502) {
						$errorMessage = $response->ErrorMessage;
					} else {
						$showOTPBox = true;
						$successMessage = $response->otpInfoText;
					}
				}
			} else {
				$errorMessage = "Sorry, we couldn't find any member with this phone number.";
			}
		}
		$this->layout = 'memberEdit';
		return $this->render('public-member-edit', [
			'otp' => $otp,
			'showOTPBox' => $showOTPBox,
			'errorMessage' => $errorMessage,
			'successMessage' => $successMessage,
			'memberPhoneNumber' => $memberPhoneNumber,
		]);
	}

	/**
	 * Lists all newly registered members.
	 * @return mixed
	 */
	public function actionNewRegisteredMembers()
	{

		$searchModel = new NewMemberRegistrationSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		return $this->render('new-registered-members', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}


	/**
	 * To store the member edit details
	 * @param unknown $id
	 * @return Ambigous <string, string>
	 */
	public function actionMemberEdit($id)
	{
		if ($id) {
			$this->layout = 'memberEdit';
			if (true) {
				if (($editMemberModel = ExtendedEditmember::find()->where(['tempmemberid' => $id])->one()) != null) {
					$memberId = $editMemberModel->memberid;
					if ($memberId) {
						$model = $this->findModel($memberId);
						$memberNo = $model->memberno;
						$settingsModel   = new ExtendedSettings();
						$settingsModel =  $settingsModel->find()->where(['memberid' => $memberId])->one();
						$familyUnitmodel = new ExtendedFamilyunit();
						$titleModel      = new ExtendedTitle();
						$dependantModel  = new ExtendedDependant();
						$dynamicImageManageModel     = new \yii\base\DynamicModel(['memberpic', 'spousepic']);
						$memberImages['orginal'] = $model->member_pic;
						$memberImages['thumbnail'] = $model->memberImageThumbnail;
						$spouseImages['orginal'] = $model->spouse_pic;
						$spouseImages['thumbnail'] = $model->spouseImageThumbnail;
						$memberadditionalModal = ExtendedMemberadditionalinfo::find()->where(['memberid' => $memberId])->one();
						if ($memberadditionalModal == null) {
							$memberadditionalModal      = new ExtendedMemberadditionalinfo();
						}
						$institusionId = $model->institutionid;
						$familyUnits = [];
						$familyUnitsArray = [];
						if (!empty($familyUnits)) {
							$familyUnitsArray =	ArrayHelper::map($familyUnits, 'familyunitid', 'description');
						}
						$titles = CacheHelper::getTitles($institusionId, function() use ($titleModel, $institusionId) {
							return $titleModel->getActiveTitles($institusionId);
						});
						$titlesArray = [];
						if (!empty($titles)) {
							$titlesArray =	ArrayHelper::map($titles, 'TitleId', 'Description');
						}
						$bloodGroup = $this->getBloodGroup();
						$relations  = $this->getRelation();
						$relations[""] = "Please select";
						$dependantDetails = $dependantModel->getDependants($memberId);
						$dependantIds = $this->getDepentantIds($dependantDetails);
						$addressTypes      = $this->getAddressTypes();
						$isMarried = ['' => 'Please Select', '1' => 'Single', '2' => "Married"];
						$this->layout = 'memberEdit';
						if (Yii::$app->request->post()) {
							$postDetails = Yii::$app->request->post();
							$savableEntry = $this->checkMemberDataIsUpdated($postDetails, $model, $dependantDetails);

							if (!$savableEntry) {
								$this->sessionAddFlashArray('error', "Error: Please update at least one field before submitting.", true);
								return $this->redirect(['member/member-edit/' . $id]);
							}

							$tempDependantImages = $_FILES;
							$tempMemberModel = ExtendedTempmember::find()->where(['temp_memberid' => $memberId])->one();
							if ($tempMemberModel == null) {
								$tempMemberModel = new ExtendedTempmember();
							}
							$tempMemberMailModel = ExtendedTempmembermail::find()->where(['temp_memberid' => $memberId])->one();
							if ($tempMemberMailModel == null) {
								$tempMemberMailModel = new ExtendedTempmembermail();
							}
							$imageType  = 'member';
							if (UploadedFile::getInstance($model, 'memberImageThumbnail')) {
								$memberImage = UploadedFile::getInstance($model, 'memberImageThumbnail');
								$targetPath = Yii::$app->params['image']['member']['main'] . '/' . Yii::$app->params['image']['member']['memberImage'];
								$thumbnail = Yii::$app->params['image']['member']['main'] . '/' . Yii::$app->params['image']['member']['memberthumbnailImage'];
								$memberImages = $this->fileUpload($memberImage, $targetPath, $thumbnail, $imageType);
							} else {
								if ($postDetails['DynamicModel']['spousepic'] == 'removed') {
									$memberImages['orginal'] = '';
									$memberImages['thumbnail'] = '';
								} elseif (empty($memberImages)) {
									$memberImages['orginal'] = '';
									$memberImages['thumbnail'] = '';
								}
							}
							if (UploadedFile::getInstance($model, 'spouseImageThumbnail')) {
								$spouseImage = UploadedFile::getInstance($model, 'spouseImageThumbnail');
								$targetPath = Yii::$app->params['image']['member']['main'] . '/' . Yii::$app->params['image']['member']['spouseImage'];
								$thumbnail = Yii::$app->params['image']['member']['main'] . '/' . Yii::$app->params['image']['member']['spousethumbnailImage'];
								$spouseImages = $this->fileUpload($spouseImage, $targetPath, $thumbnail, $imageType);
							} else {
								if ($postDetails['DynamicModel']['memberpic'] == 'removed') {
									$memberImages['orginal'] = '';
									$memberImages['thumbnail'] = '';
								} elseif (empty($spouseImages)) {
									$spouseImages['orginal'] = '';
									$spouseImages['thumbnail'] = '';
								}
							}

							$response = $this->storeTempMemeberDetails(Yii::$app->request->post(), $tempMemberModel, $tempMemberMailModel, $memberImages, $spouseImages, $tempDependantImages, $titlesArray, 'editmember');

							//no need to call again both are done at first call

							/*$tempMemberMail = $this->storeTempMemeberDetails(Yii::$app->request->post(),$tempMemberMailModel,$memberImages,$spouseImages,'editmember',$tempDependantImages, $titlesArray,'mail');*/
							if (!$response['status']) {
								$this->sessionAddFlashArray('error', $response['msg'], true);
								return $this->render('editmember', [
									'model'            =>  $model,
									'dependantDetails'	       =>  $dependantDetails,
									//'spouseModel'      =>  $spouseModel,
									"dynamicImageManageModel" => $dynamicImageManageModel,
									'settingsModel'    =>  $settingsModel,
									'addressTypes'     =>  $addressTypes,
									'relations'	 	   =>  $relations,
									'bloodGroup' 	   =>  $bloodGroup,
									'titlesArray'      =>  $titlesArray,
									'familyUnitsArray' =>  $familyUnitsArray,
									'isMarried'	       => $isMarried,
									'memberadditionalModal' => $memberadditionalModal,
									'type' => 'Member',
									'dependantIds'  => $dependantIds,
								]);
							}
							if ($response['status']) {
								//Sending notification
								ExtendedProfileupdatenotification::profileUpdateNotification($model);
								$srTitle    =  $model->membertitle0['Description'] ? $model->membertitle0['Description'] : '';
								$firstName  =  $model->firstName ? $model->firstName : ' ';
								$middleName =  $model->middleName ? $model->middleName : ' ';
								$lastName   =  $model->lastName ? $model->lastName : '';
								$displayName = $firstName . ' ' . $middleName . ' ' . $lastName;
								$adminMail = ExtendedInstitution::find()
									->where('id = :institutionid', [':institutionid' => $institusionId])
									->one();
								$toEmailId = [];
								$extendedUserModel = new ExtendedUserCredentials();
								$emailIds = $extendedUserModel->getUserEmail($institusionId);
								if ($emailIds) {
									if (is_array($emailIds)) {
										foreach ($emailIds as $email) {
											$toEmailId[] = $email['emailid'];
										}
									}
								}

								if (!empty($model->institution->institutionlogo)) {
									$logo = Yii::$app->params['imagePath'] . $model->institution->institutionlogo;
								} else {
									$logo = Yii::$app->params['imagePath'] . '/institution/institution-icon-grey.png';
								}
								$institutionLogo = $logo;
								$institutionName = $model->institution->name;

								$contentToMember = "I have submitted a request to edit my membership data. Kindly approve and let me know.";

								$fromName = '';
								$this->toSendMsg('ADMIN', $displayName, $toEmailId, $fromName, $contentToMember, $institutionLogo, $institutionName, $memberId, $memberNo);
								$toMail = $model->member_email;
								$fromMail =  $adminMail->email;
								$institutionName = $adminMail->name;
								$contentToMember = "We have received your request for updating your directory information. We will review and let you know shortly";
								$this->toSendMsg($displayName, $institutionName, $toMail, $fromMail, $contentToMember, $institutionLogo, $institutionName, $memberId, $memberNo);
								return $this->render('modifiedmemberedit', ['id' => $model->memberid]);
							}
						}
						return $this->render('editmember', [
							'model'            =>  $model,
							'dependantDetails'	       =>  $dependantDetails,
							//'spouseModel'      =>  $spouseModel,
							"dynamicImageManageModel" => $dynamicImageManageModel,
							'settingsModel'    =>  $settingsModel,
							'addressTypes'     =>  $addressTypes,
							'relations'	 	   =>  $relations,
							'bloodGroup' 	   =>  $bloodGroup,
							'titlesArray'      =>  $titlesArray,
							'familyUnitsArray' =>  $familyUnitsArray,
							'isMarried'	       => $isMarried,
							'memberadditionalModal' => $memberadditionalModal,
							'type' => 'Member',
							'dependantIds'  => $dependantIds,
						]);
					} else {
						return $this->render('modifiedmemberedit', ['type' => "error"]);
					}
				} else {
					return $this->render('modifiedmemberedit', ['type' => "error"]);
				}
			} else {
				return $this->render('modifiedmemberedit', ['type' => "error"]);
			}
		}
	}

	private function checkMemberDataIsUpdated(array $postData, $memberModel, $dependantDetails)
	{
		// Temporarily disable for all institutions except DM Challengers
		/* $institusionId = $memberModel->institutionid;
		if ($institusionId != 66 ) {
			return true;
		} */
		$availableDepentantIdslist = !empty($postData['availableDepentantIdslist']) ? explode(',', $postData['availableDepentantIdslist']) : [];
		$depentantIdslist = !empty($postData['depentantIdslist']) ? explode(',', $postData['depentantIdslist']) : [];

		// new dependant added and or removed
		if (!empty($postData['removedDependants']) || (!empty($availableDepentantIdslist) &&
			count(array_diff($availableDepentantIdslist, $depentantIdslist)) > 0
		)) {
			return true;
		}

		// spouse or member profile pic removed
		if ($postData['DynamicModel']['memberpic'] == 'removed' || $postData['DynamicModel']['spousepic'] == 'removed') {
			return true;
		}

		// spouse or member profile pic changed
		if (
			!empty($_FILES['ExtendedMember']['name']['memberImageThumbnail']) ||
			!empty($_FILES['ExtendedMember']['name']['spouseImageThumbnail'])
		) {
			return true;
		}

		$memberDob = !empty($memberModel->member_dob) ? date('d F Y', strtotimeNew($memberModel->member_dob)) : '';
		if ($this->isDiffer($memberDob, $postData['member_dob'])) {
			return true;
		}

		$memberSpouseDob = !empty($memberModel->spouse_dob) ? date('d F Y', strtotimeNew($memberModel->spouse_dob)) : '';
		if ($this->isDiffer($memberSpouseDob, $postData['spouse_dob'])) {
			return true;
		}

		$memberDom = !empty($memberModel->dom) ? date('d F Y', strtotimeNew($memberModel->dom)) : '';
		if ($this->isDiffer($memberDom, $postData['dom'])) {
			return true;
		}

		// check member data is updated
		foreach ($postData['ExtendedMember'] as $key => $value) {
			if (in_array($key, ['memberImageThumbnail', 'spouseImageThumbnail'])) {
				continue;
			}
			if ($this->isDiffer($value, $memberModel->$key)) {
				return true;
			}
		}

		// check dependant data is updated
		foreach ($dependantDetails as $depentant) {

			if (empty($postData['dependantname_' . $depentant['id']])) {
				continue;
			}

			if (
				$postData['dependantpic_' . $depentant['id']] == 'removed' ||
				$postData['dependantspousepic_' . $depentant['id']] == 'removed'
			) {
				return true;
			}

			if ($this->isDiffer($postData['dependantname_' . $depentant['id']], $depentant['dependantname'])) {
				return true;
			}
			if ($this->isDiffer($postData['dependantmobilecountrycode_' . $depentant['id']], $depentant['dependantmobilecountrycode'])) {
				return true;
			}
			if ($this->isDiffer($postData['dependantmobile_' . $depentant['id']], $depentant['dependantmobile'])) {
				return true;
			}

			if ($this->isDiffer($postData['dependanttitle_' . $depentant['id']], $depentant['dependanttitleid'])) {
				return true;
			}

			if ($this->isDiffer($postData['dependantrelation_' . $depentant['id']], $depentant['relation'])) {
				return true;
			}

			$dependantDob = !empty($depentant['dob']) ? date('d F Y', strtotimeNew($depentant['dob'])) : '';
			if ($this->isDiffer($postData['dependantdob_' . $depentant['id']], $dependantDob)) {
				return true;
			}

			if ($this->isDiffer($postData['dependantmartialstatus_' . $depentant['id']], $depentant['ismarried'])) {
				return true;
			}

			$weddingAnniversary = !empty($depentant['weddinganniversary']) ?
				date('d F Y', strtotimeNew($depentant['weddinganniversary'])) : '';
			if ($this->isDiffer($postData['tempDependantwdate_' . $depentant['id']], $weddingAnniversary)) {
				return true;
			}

			if (empty($postData['tempDependantSpousName_' . $depentant['id']]) && empty($depentant['spousename'])) {
				continue;
			}

			if ($this->isDiffer($postData['tempDependantSpousName_' . $depentant['id']], $depentant['spousename'])) {
				return true;
			}
			if ($this->isDiffer($postData['tempDependantSpouseMobileCountryCode_' . $depentant['id']], $depentant['dependantspousemobilecountrycode'])) {
				return true;
			}
			if ($this->isDiffer($postData['tempDependantSpouseMobile_' . $depentant['id']], $depentant['dependantspousemobile'])) {
				return true;
			}

			$spouseDob = !empty($depentant['spousedob']) ? date('d F Y', strtotimeNew($depentant['spousedob'])) : '';
			if ($this->isDiffer($postData['tempDependantSpouseDob_' . $depentant['id']], $spouseDob)) {
				return true;
			}

			if ($this->isDiffer($postData['tempdependantspousetitleid_' . $depentant['id']], $depentant['spousetitleid'])) {
				return true;
			}

			// dependant or dependant spouse profile pic changed
			if (
				!empty($_FILES['dependantfile_' . $depentant['id']]['name']) ||
				!empty($_FILES['dependantspousefile_' . $depentant['id']]['name'])
			) {
				return true;
			}
		}

		return false;
	}

	/**
	 * View - Member approvel details
	 * @param unknown $id
	 * @return Ambigous <string, string>
	 */
	public function actionMemberApprovel($id)
	{

		$model           = $this->findModel($id);
		$tempMemberModel = ExtendedTempmember::find()->where(['temp_memberid' => $id])->one();
		if (empty($tempMemberModel) || $tempMemberModel == null) {

			$this->sessionAddFlashArray('success', "Member Details Already Approved ", true);
			return $this->redirect(['index']);
		}

		$settingsModel   = new ExtendedSettings();
		$settingsModel =  $settingsModel->find()->where(['memberid' => $id])->one();
		$familyUnitmodel = new ExtendedFamilyunit();
		$titleModel      = new ExtendedTitle();
		$dependantModel  = new ExtendedDependant();
		$memberadditionalModal = new ExtendedMemberadditionalinfo();
		$tempMemberModelDao = new ExtendedTempmember();
		$dynamicDepentantModel  = new \yii\base\DynamicModel(['dependantname', 'dependantmobilecountrycode', 'dependantmobile', 'relation', 'ismarried', 'wedding_anniversary', 'spousename', 'dependantspousemobilecountrycode', 'dependantspousemobile', 'dob', 'weddinganniversary', 'title', 'spousetitle']);
		$institusionId = $this->currentUser()->institutionid;
		$memberadditionalModal = ExtendedMemberadditionalinfo::find()->where(['memberid' => $id])->one();
		if ($memberadditionalModal == null) {
			$memberadditionalModal      = new ExtendedMemberadditionalinfo();
		}
		$tempMemberadditionalModal = ExtendedTempmemberadditionalinfo::find()->where(['memberid' => $id])->one();
		if ($tempMemberadditionalModal == null) {
			$tempMemberadditionalModal = new ExtendedTempmemberadditionalinfo();
		}
		$familyUnits = [];
		$familyUnitsArray = [];
		if (!empty($familyUnits)) {
			$familyUnitsArray =	ArrayHelper::map($familyUnits, 'familyunitid', 'description');
		}

		$titles = CacheHelper::getTitles($institusionId, function() use ($titleModel, $institusionId) {
			return $titleModel->getActiveTitles($institusionId);
		});
		$titlesArray = [];
		if (!empty($titles)) {
			$titlesArray =	ArrayHelper::map($titles, 'TitleId', 'Description');
		}
		$titlesArray[''] = '';
		$bloodGroup = $this->getBloodGroup();
		$relations  = $this->getRelation();
		$dependantDetails = $dependantModel->getDependants($id);
		$tempDepentantDetails = $tempMemberModelDao->getTempDependantDetails($id);

		$tempDepentantDetails = ArrayHelper::index($tempDepentantDetails, null, 'dependantid');
		$dependantIds = $this->getDepentantIds($dependantDetails);
		$countDependant = count($dependantDetails);
		$addressTypes      = $this->getAddressTypes();
		$isMarried = ['' => '', 0 => '', '1' => 'Single', '2' => "Married"];
		$totalMemberAndSpouseDiffers = $this->getTotalMemberAndSpouseDiffers($model, $tempMemberModel);
		$totalMemberAdditionalDiffers = $this->getTotalMemberAdditionalDiffers($model, $memberadditionalModal, $tempMemberadditionalModal);
		$totalDependentDiffers = $this->getTotalDependentDiffers($dependantDetails, $tempDepentantDetails);
		if ($model->load(Yii::$app->request->post())) {
			return $this->render('adminmemberapprovelform', [
				'model'            =>  $model,
				'dependantDetails'  =>  $dependantDetails,
				'tempDepentantDetails' =>  $tempDepentantDetails,
				//'settingsModel'    =>  $settingsModel,
				'tempMember' =>     $tempMemberModel,
				'addressTypes'     =>  $addressTypes,
				'relations'	 	   =>  $relations,
				'bloodGroup' 	   =>  $bloodGroup,
				'titlesArray'      =>  $titlesArray,
				'familyUnitsArray' =>  $familyUnitsArray,
				'isMarried'	       => $isMarried,
				'memberadditionalModal' => $memberadditionalModal,
				'tempMemberadditionalModal' => $tempMemberadditionalModal,
				'type' => 'Member',
				'dependantIds'  => $dependantIds,
				'dynamicDepentantModel' => $dynamicDepentantModel,
				'countDependant' => $countDependant,
				'totalModified' => $totalMemberAndSpouseDiffers + $totalMemberAdditionalDiffers + $totalDependentDiffers,
				//'approval' => $approval,
			]);
		}
		return $this->render('adminmemberapprovelform', [
			'model'            =>  $model,
			'dependantDetails'  =>  $dependantDetails,
			'tempDepentantDetails' =>  $tempDepentantDetails,
			//     			'settingsModel'    =>  $settingsModel,
			'tempMember' =>     $tempMemberModel,
			'addressTypes'     =>  $addressTypes,
			'relations'	 	   =>  $relations,
			'bloodGroup' 	   =>  $bloodGroup,
			'titlesArray'      =>  $titlesArray,
			'familyUnitsArray' =>  $familyUnitsArray,
			'isMarried'	       => $isMarried,
			'memberadditionalModal' => $memberadditionalModal,
			'tempMemberadditionalModal' => $tempMemberadditionalModal,
			'type' => 'Member',
			'dependantIds'  => $dependantIds,
			'dynamicDepentantModel' => $dynamicDepentantModel,
			'countDependant' => $countDependant,
			'totalModified' => $totalMemberAndSpouseDiffers + $totalMemberAdditionalDiffers + $totalDependentDiffers,
			//'approval' => $approval,
		]);
	}

	private function getTotalMemberAndSpouseDiffers($model, $tempMemberModel)
	{
		$difference = 0;
		if ($this->isDiffer($model->membertitle, $tempMemberModel->temp_membertitle)) {
			$difference++;
		}
		if ($this->isDiffer($model->spousetitle, $tempMemberModel->temp_spousetitle)) {
			$difference++;
		}
		if ($this->isDiffer($model->firstName, $tempMemberModel->temp_firstName)) {
			$difference++;
		}
		if ($this->isDiffer($model->middleName, $tempMemberModel->temp_middleName)) {
			$difference++;
		}
		if ($this->isDiffer($model->lastName, $tempMemberModel->temp_lastName)) {
			$difference++;
		}
		if ($this->isDiffer($model->spouse_firstName, $tempMemberModel->temp_spouse_firstName)) {
			$difference++;
		}
		if ($this->isDiffer($model->spouse_middleName, $tempMemberModel->temp_spouse_middleName)) {
			$difference++;
		}
		if ($this->isDiffer($model->spouse_lastName, $tempMemberModel->temp_spouse_lastName)) {
			$difference++;
		}
		if ($this->isDiffer($model->membernickname, $tempMemberModel->temp_membernickname)) {
			$difference++;
		}
		if ($this->isDiffer($model->spousenickname, $tempMemberModel->temp_spousenickname)) {
			$difference++;
		}
		if ($this->isDiffer($model->member_email, $tempMemberModel->temp_member_email)) {
			$difference++;
		}
		if ($this->isDiffer($model->spouse_email, $tempMemberModel->temp_spouse_email)) {
			$difference++;
		}
		if ($this->isDiffer($model->member_dob, $tempMemberModel->temp_member_dob)) {
			$difference++;
		}
		if ($this->isDiffer($model->spouse_dob, $tempMemberModel->temp_spouse_dob)) {
			$difference++;
		}
		if (
			$this->isDiffer($model->spouse_mobile1_countrycode, $tempMemberModel->temp_spouse_mobile1_countrycode) ||
			$this->isDiffer($model->spouse_mobile1, $tempMemberModel->temp_spouse_mobile1)
		) {
			$difference++;
		}
		if ($this->isDiffer($model->homechurch, $tempMemberModel->temp_homechurch)) {
			$difference++;
		}
		if ($this->isDiffer($model->occupation, $tempMemberModel->temp_occupation)) {
			$difference++;
		}
		if ($this->isDiffer($model->spouseoccupation, $tempMemberModel->temp_spouseoccupation)) {
			$difference++;
		}
		if ($this->isDiffer($model->memberbloodgroup, $tempMemberModel->tempmemberBloodGroup)) {
			$difference++;
		}
		if ($this->isDiffer($model->spousebloodgroup, $tempMemberModel->tempspouseBloodGroup)) {
			$difference++;
		}
		if ($this->isDiffer($model->dom, $tempMemberModel->temp_dom)) {
			$difference++;
		}
		if ($this->isDiffer($model->member_pic, $tempMemberModel->temp_member_pic)) {
			$difference++;
		}
		if ($this->isDiffer($model->spouse_pic, $tempMemberModel->temp_spouse_pic)) {
			$difference++;
		}
		if ($this->isDiffer($model->business_address1, $tempMemberModel->temp_business_address1)) {
			$difference++;
		}
		if ($this->isDiffer($model->business_address2, $tempMemberModel->temp_business_address2)) {
			$difference++;
		}
		if ($this->isDiffer($model->business_pincode, $tempMemberModel->temp_business_pincode)) {
			$difference++;
		}
		if ($this->isDiffer($model->business_district, $tempMemberModel->temp_business_district)) {
			$difference++;
		}
		if ($this->isDiffer($model->business_state, $tempMemberModel->temp_business_state)) {
			$difference++;
		}
		if ($this->isDiffer($model->businessemail, $tempMemberModel->temp_businessemail)) {
			$difference++;
		}
		if (
			$this->isDiffer($model->member_business_phone1_countrycode, $tempMemberModel->temp_member_business_phone1_countrycode) ||
			$this->isDiffer($model->member_business_phone1_areacode, $tempMemberModel->temp_member_business_phone1_areacode) ||
			$this->isDiffer($model->member_musiness_Phone1, $tempMemberModel->temp_member_business_Phone1)
		) {
			$difference++;
		}
		if (
			$this->isDiffer($model->member_business_phone3_countrycode, $tempMemberModel->temp_member_business_phone3_countrycode) ||
			$this->isDiffer($model->member_business_phone3_areacode, $tempMemberModel->temp_member_business_phone3_areacode) ||
			$this->isDiffer($model->member_business_Phone3, $tempMemberModel->temp_member_business_Phone3)
		) {
			$difference++;
		}
		if ($this->isDiffer($model->residence_address1, $tempMemberModel->temp_residence_address1)) {
			$difference++;
		}
		if ($this->isDiffer($model->residence_address2, $tempMemberModel->temp_residence_address2)) {
			$difference++;
		}
		if ($this->isDiffer($model->residence_pincode, $tempMemberModel->temp_residence_pincode)) {
			$difference++;
		}
		if ($this->isDiffer($model->residence_district, $tempMemberModel->temp_residence_district)) {
			$difference++;
		}
		if ($this->isDiffer($model->residence_state, $tempMemberModel->temp_residence_state)) {
			$difference++;
		}
		if (!empty($tempMemberModel->location['latitude']) || !empty($tempMemberModel->location['longitude']) || !empty($model->location)) {
			if ($this->isDifferArray($model->location, $tempMemberModel->location)) {
				$difference++;
			}
		}
		if (
			$this->isDiffer($model->member_residence_Phone1_countrycode, $tempMemberModel->temp_member_residence_Phone1_countrycode) ||
			$this->isDiffer($model->member_residence_phone1_areacode, $tempMemberModel->temp_member_residence_Phone1_areacode) ||
			$this->isDiffer($model->member_residence_Phone1, $tempMemberModel->temp_member_residence_Phone1)
		) {
			$difference++;
		}
		return $difference;
	}

	private function getTotalMemberAdditionalDiffers($model, $memberadditionalModal, $tempMemberadditionalModal)
	{
		$difference = 0;
		if ($model->institution->tagcloud) {
			if ($this->isDiffer($memberadditionalModal->tagcloud, $tempMemberadditionalModal->temptagcloud)) {
				$difference++;
			}
		}
		return $difference;
	}

	private function getTotalDependentDiffers($dependantDetails, $tempDepentantDetails)
	{
		$difference = 0;
		foreach ($dependantDetails as $dependent) {
			if (empty($tempDepentantDetails[$dependent['id']][0])) {
				$difference += $this->getDeletedDependentDiffers($dependent);
				continue;
			}
			$tempDependent = $tempDepentantDetails[$dependent['id']][0];
			if ($this->isDiffer($dependent['dependanttitleid'], $tempDependent['dependanttitleid'])) {
				$difference++;
			}
			if ($this->isDiffer($dependent['dependantname'], $tempDependent['dependantname'])) {
				$difference++;
			}
			$dependantMobileOld = $dependent['dependantmobilecountrycode'] . $dependent['dependantmobile'];
			$dependantMobileNew = $tempDependent['dependantmobilecountrycode'] . $tempDependent['dependantmobile'];
			if ($this->isDiffer($dependantMobileOld, $dependantMobileNew)) {
				$difference++;
			}
			if ($this->isDiffer($dependent['dependantimage'], $tempDependent['tempimage'])) {
				$difference++;
			}
			$oldDate = !empty($dependent['dob']) ? date('y-m-d', strtotimeNew($dependent['dob'])) : '';
			$newDate = !empty($tempDependent['dob']) ? date('y-m-d', strtotimeNew($tempDependent['dob'])) : '';
			if ($this->isDiffer($oldDate, $newDate)) {
				$difference++;
			}
			if ($this->isDiffer($dependent['relation'], $tempDependent['relation'])) {
				$difference++;
			}
			if ($this->isDiffer($dependent['ismarried'], $tempDependent['ismarried'])) {
				$difference++;
			}

			if ($this->isDiffer($dependent['spousetitleid'], $tempDependent['spousetitleid'])) {
				$difference++;
			}
			if ($this->isDiffer($dependent['spousename'], $tempDependent['spousename'])) {
				$difference++;
			}
			$dependantSpouseMobileOld = $dependent['dependantspousemobilecountrycode'] . $dependent['dependantspousemobile'];
			$dependantSpouseMobileNew = $tempDependent['dependantspousemobilecountrycode'] . $tempDependent['dependantspousemobile'];
			if ($this->isDiffer($dependantSpouseMobileOld, $dependantSpouseMobileNew)) {
				$difference++;
			}
			if ($this->isDiffer($dependent['dependantspouseimage'], $tempDependent['tempdependantspouseimage'])) {
				$difference++;
			}
			$oldDate = !empty($dependent['spousedob']) ? date('y-m-d', strtotimeNew($dependent['spousedob'])) : '';
			$newDate = !empty($tempDependent['spousedob']) ? date('y-m-d', strtotimeNew($tempDependent['spousedob'])) : '';
			if ($this->isDiffer($oldDate, $newDate)) {
				$difference++;
			}
			$oldDate = !empty($dependent['weddinganniversary']) ? date('y-m-d', strtotimeNew($dependent['weddinganniversary'])) : '';
			$newDate = !empty($tempDependent['weddinganniversary']) ? date('y-m-d', strtotimeNew($tempDependent['weddinganniversary'])) : '';
			if ($this->isDiffer($oldDate, $newDate)) {
				$difference++;
			}
		}
		return $difference;
	}

	private function getDeletedDependentDiffers($dependent)
	{
		$difference = 0;
		if (!empty($dependent['dependanttitleid'])) {
			$difference++;
		}
		if (!empty($dependent['dependantname'])) {
			$difference++;
		}
		if (!empty($dependent['dependantimage'])) {
			$difference++;
		}
		if (!empty($dependent['dob'])) {
			$difference++;
		}
		if (!empty($dependent['relation'])) {
			$difference++;
		}
		if (!empty($dependent['ismarried'])) {
			$difference++;
		}
		if (!empty($dependent['spousetitleid'])) {
			$difference++;
		}
		if (!empty($dependent['spousename'])) {
			$difference++;
		}
		if (!empty($dependent['dependantspouseimage'])) {
			$difference++;
		}
		if (!empty($dependent['spousedob'])) {
			$difference++;
		}
		if (!empty($dependent['weddinganniversary'])) {
			$difference++;
		}
		return $difference;
	}

	/**
	 * to store the pending approval
	 */
	public function actionStorePendingDetails()
	{

		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		if (yii::$app->request->isAjax) {
			$approvalDetails  = yii::$app->request->post();
			$memberDetails = $approvalDetails['memberViewModel'];
			$dependantModel  = new ExtendedDependant();
			$tempMemberModelDao = new ExtendedTempmember();
			if (!empty($memberDetails)) {
				$memberId = $memberDetails['MemberID'];
				$dependantDetails = $dependantModel->getDependants($memberId);
				$tempDepentantDetails = $tempMemberModelDao->getTempDependantDetails($memberId);
				$countDependant = isset($memberDetails['DependantLst']) ? count($memberDetails['DependantLst']) : 0;

				$depentantResponse = [];
				$depentantResponse['isApproved'] = [];
				if ($countDependant >= 1) {
					$depentantResponse = $this->storeApprovalDependantMember($dependantDetails, $tempDepentantDetails, $memberDetails['DependantLst'], $memberId);
					if (!$depentantResponse) {
						return false;
					}
				}
				$memberadditionalModal = ExtendedMemberadditionalinfo::find()->where(['memberid' => $memberId])->one();
				if ($memberadditionalModal == null) {
					$memberadditionalModal      = new ExtendedMemberadditionalinfo();
				}
				$tempMemberadditionalModal = ExtendedTempmemberadditionalinfo::find()->where(['memberid' => $memberId])->one();
				if ($tempMemberadditionalModal != null) {

					if ($this->isDiffer($memberadditionalModal->tagcloud, $tempMemberadditionalModal->temptagcloud)) {

						if ($this->isDiffer($memberDetails['MemberTag'], $tempMemberadditionalModal->temptagcloud)) {
						} else {
							$memberadditionalModal->tagcloud = $memberDetails['MemberTag'];
							$memberadditionalModal->memberid = $memberId;
							$memberadditionalModal->save();
						}
					}
				}
				$approvalMemberModal = $this->getMemberModal($memberDetails);
				$memberModal = $this->findModel($memberDetails['MemberID']);
				$tempMemberModal = ExtendedTempmember::findOne(['temp_memberid' => $memberDetails['MemberID']]);
				$approvalMemberModal = $this->toSetImagesApproval($approvalMemberModal, $memberDetails, $memberModal, $tempMemberModal);
				if ($approvalMemberModal && $memberModal && $tempMemberModal) {
					$memberModal->lastupdated =  date(yii::$app->params['dateFormat']['sqlDandTFormat']);
					$memberResponse = $this->storeAndSendApprovaldetails($approvalMemberModal, $memberModal, $tempMemberModal);
				}
				$memberModal = $this->findModel($memberDetails['MemberID']);
				if (!isset($memberDetails['DependantLst'])) {
					$memberDetails['DependantLst'] = [];;
				}
				$this->sentApprovalEmailNotification($depentantResponse['isApproved'], $memberResponse['isApproved'], $memberModal, $memberDetails['DependantLst']);
				ExtendedMember::profileApprovedNotification($memberId, 'M', $memberModal->institutionid, $memberModal->institution->name);

				BaseModel::deleteTempMemberDeatils($memberId);
				return [
					'hasError' => 0,
					'url' => "/",
				];
			}
		}
	}

	/**
	 * to remove the spouse
	 */
	public function actionRemoveSpouse()
	{
		$memberModel = new ExtendedMember();
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		if (yii::$app->request->isAjax) {
			$memberId  = yii::$app->request->post('memberId');
			try {
				$respose = $memberModel->deleteSpouseDetails($memberId);
				return ['status' => 'success'];
			} catch (Exception $e) {
				return ['status' => 'error'];
			}
		}
	}

	/**
	 * To remove primary member
	 * @return multitype:string |NULL
	 */
	public function actionRemovePrimaryMember()
	{

		$memberModel = new ExtendedMember();
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		if (yii::$app->request->isAjax) {
			$memberDetails  = yii::$app->request->post();
			$memberDetails = $memberDetails['data'];
			$institutionId = $this->currentUser()->institutionid;
			try {
				$respose = $memberModel->removePrimaryMemberDetails($memberDetails);
				$manageUserCount = ExtendedUserMember::find()->where(['memberid' => $memberDetails['MemberID']])->count();
				if ($manageUserCount > 1) {
					$memberUser = ExtendedUserMember::find()->where(['memberid' => $memberDetails['MemberID'], 'institutionid' => $institutionId, 'usertype' => 'M'])->one();
					if ($memberUser != null) {
						$memberUser->delete();
					}
					$spouseUser = ExtendedUserMember::find()->where(['memberid' => $memberDetails['MemberID'], 'institutionid' => $institutionId, 'usertype' => 'S'])->one();
					if ($spouseUser != null) {
						$spouseUser->usertype = "M";
						$spouseUser->save();
					}
				}
				return [
					'status' => 'success',
					'url' => "/",
				];
			} catch (Exception $e) {
				yii::error($e->getMessage());
				return null;
			}
		}
	}
	/**
	 * to assing the memebr values into member model
	 * @param unknown $memberDetails
	 */
	public function getMemberModal($memberDetails)
	{

		$memberModel = new ExtendedMember();
		$titleModel      = new ExtendedTitle();
		$institusionId = $this->currentUser()->institutionid;
		$titles = CacheHelper::getTitles($institusionId, function() use ($titleModel, $institusionId) {
			return $titleModel->getActiveTitles($institusionId);
		});
		$titlesArray = [];
		if (!empty($titles)) {
			$titlesArray =	ArrayHelper::map($titles, 'Description', 'TitleId');
		}
		$memberModel->firstName 	= $memberDetails['FirstName'];
		$memberModel->middleName 	= $memberDetails['MiddleName'];
		$memberModel->lastName 	= $memberDetails['LastName'];
		$memberModel->business_address1 	= $memberDetails['BusinessAddress1'];
		$memberModel->business_address2 	= $memberDetails['BusinessAddress2'];
		$memberModel->business_address3 	= null;
		$memberModel->business_district 	= $memberDetails['BusinessDistrict'];
		$memberModel->business_state 	= $memberDetails['BusinessState'];
		$memberModel->business_pincode 	= $memberDetails['BusinessPincode'];
		$memberModel->member_dob 	   = $this->sqlDateConversion($memberDetails['varmemberDOB']);
		$memberModel->member_mobile2 	= null;
		$memberModel->member_musiness_Phone1 	= $memberDetails['MemberBusinessPhone1'];
		$memberModel->member_business_Phone2 	= null;
		$memberModel->member_residence_Phone1 	= $memberDetails['MemberResidencePhone1'];
		$memberModel->member_residence_Phone2 	= '';
		$memberModel->member_email 	= $memberDetails['MemberEmail'];
		$memberModel->spouse_firstName 	= $memberDetails['SpouseFirstName'];
		$memberModel->spouse_middleName 	= $memberDetails['SpouseMiddleName'];
		$memberModel->spouse_lastName 	= $memberDetails['SpouseLastName'];
		$memberModel->spouse_dob 	= $this->sqlDateConversion($memberDetails['varspouseDOB']);
		$memberModel->dom 	=  $this->sqlDateConversion($memberDetails['varDOM']);
		$memberModel->spouse_mobile1 	= $memberDetails['spousemobile1'];
		$memberModel->spouse_mobile2 	= '';
		$memberModel->spouse_email 	= $memberDetails['spouseemail'];
		$memberModel->residence_address1 	= $memberDetails['Residenceaddress1'];
		$memberModel->residence_address2 	= $memberDetails['Residenceaddress2'];
		$memberModel->residence_address3 	= '';
		$memberModel->residence_district 	= $memberDetails['Residencedistrict'];
		$memberModel->residence_state 	= $memberDetails['Residencestate'];
		$memberModel->residence_pincode 	= $memberDetails['Residence_pincode'];
		if (!empty($memberDetails['location'])) {
			list($latitude, $longitude) = explode(',', $memberDetails['location']);
			$locationData = [
				'latitude' => $latitude,
				'longitude' => $longitude,
			];
		}
		$memberModel->location 	= $locationData ?? NULL;
		$memberModel->app_reg_member 	= '';
		$memberModel->app_reg_spouse 	= '';
		$memberModel->active 	= 1;
		$memberModel->businessemail 	= $memberDetails['BusinessEmail'];
		$memberModel->membertitle 	= $titlesArray[$memberDetails['MemberTitleDescription']];
		$memberModel->spousetitle 	= empty($memberDetails['SpouseTitleDescription']) ? null : $titlesArray[$memberDetails['SpouseTitleDescription']];
		$memberModel->membernickname 	= $memberDetails['MemberNickName'];
		$memberModel->spousenickname 	= $memberDetails['SpouceNickName'];
		//	$memberModel->createddate 	= date('Y-m-d H:i:s');
		$memberModel->homechurch 	= isset($memberDetails['HomeChurch']) ? $memberDetails['HomeChurch'] : '';
		$memberModel->occupation 	= $memberDetails['Occupation'];
		$memberModel->spouseoccupation 	= $memberDetails['SpouseOccupation'];
		//$memberModel->member_mobile1_countrycode 	= $memberDetails['member_mobile1_countrycode'];
		$memberModel->spouse_mobile1_countrycode 	= $memberDetails['Spouse_Mobile1_Countrycode'];
		$memberModel->member_business_phone1_countrycode 	= $memberDetails['Member_Business_Phone1_Countrycode'];
		$memberModel->member_business_phone1_areacode 	= $memberDetails['Member_Business_Phone1_Areacode'];
		$memberModel->member_business_phone2_countrycode 	= '';
		$memberModel->membertype 	= 0;
		$memberModel->staffdesignation 	= null;
		$memberModel->member_business_Phone3 	= $memberDetails['MemberBusinessPhone3'];
		$memberModel->member_business_phone3_areacode 	= $memberDetails['Member_Business_Phone3_Areacode'];
		$memberModel->member_business_phone3_countrycode 	= $memberDetails['Member_Business_Phone3_Countrycode'];
		//	$memberModel->newmembernum 	= $memberDetails['memberno'];

		$memberModel->memberbloodgroup 	= $memberDetails['MemberBloodGroup'];
		$memberModel->spousebloodgroup 	= $memberDetails['SpouseBloodGroup'];
		$memberModel->member_residence_phone1_areacode 	= $memberDetails['MemberResidencePhone1AreaCode'];
		$memberModel->member_residence_Phone1_countrycode  	= $memberDetails['MemberResidencePhone1CountryCode'];
		return $memberModel;
	}

	public function storeAndSendApprovaldetails($approvalMemberModal, $memberModal, $tempMemberModal)
	{

		$isApproved = [];
		$allAccept = 0;
		$allReject = 0;
		$total = 0;
		$acceptMailContent = '';
		$userMemberModel = new ExtendedUserMember();
		$spousePhoneAccept = false;
		//first 
		if ($this->isDiffer($tempMemberModal->temp_firstName, $memberModal->firstName)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->firstName, $tempMemberModal->temp_firstName)) {
				$isApproved['firstName'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_firstName];
				$allReject++;
			} else {
				$memberModal->firstName = $approvalMemberModal->firstName;
				$isApproved['firstName'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_firstName];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['firstName'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_firstName];
		}
		//MiddleName
		if ($this->isDiffer($tempMemberModal->temp_middleName, $memberModal->middleName)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->middleName, $tempMemberModal->temp_middleName)) {
				$isApproved['middleName'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_middleName];
				$allReject++;
			} else {
				$memberModal->middleName = $approvalMemberModal->middleName;
				$isApproved['middleName'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_middleName];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['middleName'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_middleName];
		}
		// last name
		if ($this->isDiffer($tempMemberModal->temp_lastName, $memberModal->lastName)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->lastName, $tempMemberModal->temp_lastName)) {
				$isApproved['lastName'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_lastName];
				$allReject++;
			} else {
				$memberModal->lastName = $approvalMemberModal->lastName;
				$isApproved['lastName'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_lastName];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['lastName'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_lastName];
		}
		//location
		if ($this->isDifferArray($tempMemberModal->location, $memberModal->location)) {

			$total++;
			if ($this->isDifferArray($approvalMemberModal->location, $tempMemberModal->location)) {
				$isApproved['location'] = ['isApproved' => false, 'value' => $tempMemberModal->location];
				$allReject++;
			} else {
				$memberModal->location = $approvalMemberModal->location;
				$isApproved['location'] = ['isApproved' => true, 'value' => $tempMemberModal->location];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['location'] = ['isApproved' => true, 'value' => $tempMemberModal->location];
		}

		if ($this->isDiffer($tempMemberModal->temp_business_address1, $memberModal->business_address1)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->business_address1, $tempMemberModal->temp_business_address1)) {
				$isApproved['business_address1'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_business_address1];
				$allReject++;
			} else {
				$memberModal->business_address1 = $approvalMemberModal->business_address1;
				$isApproved['business_address1'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_business_address1];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['business_address1'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_business_address1];
		}
		// business_address2
		if ($this->isDiffer($tempMemberModal->temp_business_address2, $memberModal->business_address2)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->business_address2, $tempMemberModal->temp_business_address2)) {
				$isApproved['business_address2'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_business_address2];
				$allReject++;
			} else {
				$memberModal->business_address2 = $approvalMemberModal->business_address2;
				$isApproved['business_address2'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_business_address2];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['business_address2'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_business_address2];
		}
		// business_district
		if ($this->isDiffer($tempMemberModal->temp_business_district, $memberModal->business_district)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->business_district, $tempMemberModal->temp_business_district)) {
				$isApproved['business_district'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_business_district];
				$allReject++;
			} else {
				$memberModal->business_district = $approvalMemberModal->business_district;
				$isApproved['business_district'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_business_district];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['business_district'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_business_district];
		}
		// business_state
		if ($this->isDiffer($tempMemberModal->temp_business_state, $memberModal->business_state)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->business_state, $tempMemberModal->temp_business_state)) {
				$isApproved['business_state'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_business_state];
				$allReject++;
			} else {
				$memberModal->business_state = $approvalMemberModal->business_state;
				$isApproved['business_state'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_business_state];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['business_state'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_business_state];
		}
		// business_pincode
		if ($this->isDiffer($tempMemberModal->temp_business_pincode, $memberModal->business_pincode)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->business_pincode, $tempMemberModal->temp_business_pincode)) {
				$isApproved['business_pincode'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_business_pincode];
				$allReject++;
			} else {
				$memberModal->business_pincode = $approvalMemberModal->business_pincode;
				$isApproved['business_pincode'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_business_pincode];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['business_pincode'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_business_pincode];
		}
		// member_dob
		if ($this->isDiffer($tempMemberModal->temp_member_dob, $memberModal->member_dob)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->member_dob, $tempMemberModal->temp_member_dob)) {
				$isApproved['member_dob'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_member_dob];
				$allReject++;
			} else {
				$memberModal->member_dob = $approvalMemberModal->member_dob;
				$isApproved['member_dob'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_member_dob];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['member_dob'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_member_dob];
		}
		// member_business_Phone1
		if ($this->isDiffer($tempMemberModal->temp_member_business_Phone1, $memberModal->member_musiness_Phone1)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->member_musiness_Phone1, $tempMemberModal->temp_member_business_Phone1)) {
				$isApproved['member_musiness_Phone1'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_member_business_Phone1];
				$allReject++;
			} else {
				$memberModal->member_musiness_Phone1 = $approvalMemberModal->member_musiness_Phone1;
				$isApproved['member_musiness_Phone1'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_member_business_Phone1];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['member_musiness_Phone1'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_member_business_Phone1];
		}
		// member_business_Phone2
		if ($this->isDiffer($tempMemberModal->temp_member_business_Phone2, $memberModal->member_business_Phone2)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->member_business_Phone2, $tempMemberModal->temp_member_business_Phone2)) {
				$isApproved['member_business_Phone2'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_member_business_Phone2];
				$allReject++;
			} else {
				$memberModal->member_business_Phone2 = $approvalMemberModal->member_business_Phone2;
				$isApproved['member_business_Phone2'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_member_business_Phone2];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['member_business_Phone2'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_member_business_Phone2];
		}
		// member_residence_Phone1
		if ($this->isDiffer($tempMemberModal->temp_member_residence_Phone1, $memberModal->member_residence_Phone1)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->member_residence_Phone1, $tempMemberModal->temp_member_residence_Phone1)) {
				$isApproved['member_residence_Phone1'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_member_residence_Phone1];
				$allReject++;
			} else {
				$memberModal->member_residence_Phone1 = $approvalMemberModal->member_residence_Phone1;
				$isApproved['member_residence_Phone1'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_member_residence_Phone1];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['member_residence_Phone1'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_member_residence_Phone1];
		}
		// member_residence_Phone2
		if ($this->isDiffer($tempMemberModal->temp_member_residence_Phone2, $memberModal->member_residence_Phone2)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->member_residence_Phone2, $tempMemberModal->temp_member_residence_Phone2)) {
				$isApproved['member_residence_Phone2'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_member_residence_Phone2];
				$allReject++;
			} else {
				$memberModal->member_residence_Phone2 = $approvalMemberModal->member_residence_Phone2;
				$isApproved['member_residence_Phone2'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_member_residence_Phone2];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['member_residence_Phone2'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_member_residence_Phone2];
		}
		// member_email
		if ($this->isDiffer($tempMemberModal->temp_member_email, $memberModal->member_email)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->member_email, $tempMemberModal->temp_member_email)) {
				$isApproved['member_email'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_member_email];
				$allReject++;
			} else {
				$memberModal->member_email = $approvalMemberModal->member_residence_Phone2;
				$isApproved['member_email'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_member_email];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['member_email'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_member_email];
		}
		// spouse_firstName
		if ($this->isDiffer($tempMemberModal->temp_spouse_firstName, $memberModal->spouse_firstName)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->spouse_firstName, $tempMemberModal->temp_spouse_firstName)) {
				$isApproved['spouse_firstName'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_spouse_firstName];
				$allReject++;
			} else {
				$memberModal->spouse_firstName = $approvalMemberModal->spouse_firstName;
				$isApproved['spouse_firstName'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_spouse_firstName];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['spouse_firstName'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_spouse_firstName];
		}
		// spouse_middleName
		if ($this->isDiffer($tempMemberModal->temp_spouse_middleName, $memberModal->spouse_middleName)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->spouse_middleName, $tempMemberModal->temp_spouse_middleName)) {
				$isApproved['spouse_middleName'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_spouse_middleName];
				$allReject++;
			} else {
				$memberModal->spouse_middleName = $approvalMemberModal->spouse_middleName;
				$isApproved['spouse_middleName'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_spouse_middleName];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['spouse_middleName'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_spouse_middleName];
		}
		// spouse_lastName
		if ($this->isDiffer($tempMemberModal->temp_spouse_lastName, $memberModal->spouse_lastName)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->spouse_lastName, $tempMemberModal->temp_spouse_lastName)) {
				$isApproved['spouse_lastName'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_spouse_lastName];
				$allReject++;
			} else {
				$memberModal->spouse_lastName = $approvalMemberModal->spouse_lastName;
				$isApproved['spouse_lastName'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_spouse_lastName];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['spouse_lastName'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_spouse_lastName];
		}
		// spouse_dob
		if ($this->isDiffer($tempMemberModal->temp_spouse_dob, $memberModal->spouse_dob)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->spouse_dob, $tempMemberModal->temp_spouse_dob)) {
				$isApproved['spouse_dob'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_spouse_dob];
				$allReject++;
			} else {
				$memberModal->spouse_dob = $approvalMemberModal->spouse_dob;
				$isApproved['spouse_dob'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_spouse_dob];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['spouse_dob'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_spouse_dob];
		}
		// dom
		if ($this->isDiffer($tempMemberModal->temp_dom, $memberModal->dom)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->dom, $tempMemberModal->temp_dom)) {
				$isApproved['dom'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_dom];
				$allReject++;
			} else {
				$memberModal->dom = ($approvalMemberModal->dom) ? date('Y-m-d h:i:s', strtotimeNew($approvalMemberModal->dom)) : NULL;
				$isApproved['dom'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_dom];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['dom'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_dom];
		}
		// spouse_mobile1
		if ($this->isDiffer($tempMemberModal->temp_spouse_mobile1, $memberModal->spouse_mobile1)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->spouse_mobile1, $tempMemberModal->temp_spouse_mobile1)) {
				$isApproved['spouse_mobile1'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_spouse_mobile1];
				$allReject++;
			} else {
				$spousePhoneAccept = true;
				$memberModal->spouse_mobile1 = $approvalMemberModal->spouse_mobile1;
				$isApproved['spouse_mobile1'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_spouse_mobile1];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['spouse_mobile1'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_spouse_mobile1];
		}
		// spouse_mobile2
		if ($this->isDiffer($tempMemberModal->temp_spouse_mobile2, $memberModal->spouse_mobile2)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->spouse_mobile2, $tempMemberModal->temp_spouse_mobile2)) {
				$isApproved['spouse_mobile2'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_spouse_mobile2];
				$allReject++;
			} else {
				$memberModal->spouse_mobile2 = $approvalMemberModal->spouse_mobile2;
				$isApproved['spouse_mobile2'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_spouse_mobile2];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['spouse_mobile2'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_spouse_mobile2];
		}
		// spouse_email
		if ($this->isDiffer($tempMemberModal->temp_spouse_email, $memberModal->spouse_email)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->spouse_email, $tempMemberModal->temp_spouse_email)) {
				$isApproved['spouse_email'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_spouse_email];
				$allReject++;
			} else {
				$memberModal->spouse_email = $approvalMemberModal->spouse_email;
				$isApproved['spouse_email'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_spouse_email];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['spouse_email'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_spouse_email];
		}
		// residence_address1
		if ($this->isDiffer($tempMemberModal->temp_residence_address1, $memberModal->residence_address1)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->residence_address1, $tempMemberModal->temp_residence_address1)) {
				$isApproved['residence_address1'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_residence_address1];
				$allReject++;
			} else {
				$memberModal->residence_address1 = $approvalMemberModal->residence_address1;
				$isApproved['residence_address1'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_residence_address1];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['residence_address1'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_residence_address1];
		}
		// residence_address2
		if ($this->isDiffer($tempMemberModal->temp_residence_address2, $memberModal->residence_address2)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->residence_address2, $tempMemberModal->temp_residence_address2)) {
				$isApproved['residence_address2'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_residence_address2];
				$allReject++;
			} else {
				$memberModal->residence_address2 = $approvalMemberModal->residence_address2;
				$isApproved['residence_address2'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_residence_address2];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['residence_address2'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_residence_address2];
		}
		// residence_district
		if ($this->isDiffer($tempMemberModal->temp_residence_district, $memberModal->residence_district)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->residence_district, $tempMemberModal->temp_residence_district)) {
				$isApproved['residence_district'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_residence_district];
				$allReject++;
			} else {
				$memberModal->residence_district = $approvalMemberModal->residence_district;
				$isApproved['residence_district'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_residence_district];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['residence_district'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_residence_district];
		}
		// residence_state
		if ($this->isDiffer($tempMemberModal->temp_residence_state, $memberModal->residence_state)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->residence_state, $tempMemberModal->temp_residence_state)) {
				$isApproved['residence_state'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_residence_state];
				$allReject++;
			} else {
				$memberModal->residence_state = $approvalMemberModal->residence_state;
				$isApproved['residence_state'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_residence_state];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['residence_state'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_residence_state];
		}
		// residence_pincode
		if ($this->isDiffer($tempMemberModal->temp_residence_pincode, $memberModal->residence_pincode)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->residence_pincode, $tempMemberModal->temp_residence_pincode)) {
				$isApproved['residence_pincode'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_residence_pincode];
				$allReject++;
			} else {
				$memberModal->residence_pincode = $approvalMemberModal->residence_pincode;
				$isApproved['residence_pincode'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_residence_pincode];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['residence_pincode'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_residence_pincode];
		}
		// member_pic
		if ($this->isDiffer($tempMemberModal->temp_member_pic, $memberModal->member_pic)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->member_pic, $tempMemberModal->temp_member_pic, true)) {
				$isApproved['member_pic'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_member_pic];
				$allReject++;
			} else {
				$memberModal->member_pic = $approvalMemberModal->member_pic;
				$isApproved['member_pic'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_member_pic];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['member_pic'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_member_pic];
		}
		// spouse_pic
		yii::error($tempMemberModal->temp_spouse_pic . 'temp image');
		yii::error($memberModal->spouse_pic . ' image');
		if ($this->isDiffer($tempMemberModal->temp_spouse_pic, $memberModal->spouse_pic)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->spouse_pic, $tempMemberModal->temp_spouse_pic)) {
				$isApproved['spouse_pic'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_spouse_pic];
				$allReject++;
			} else {
				yii::error($memberModal->spouse_pic . 'Approved image');
				$memberModal->spouse_pic = $approvalMemberModal->spouse_pic;
				$isApproved['spouse_pic'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_spouse_pic];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['spouse_pic'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_spouse_pic];
		}
		// businessemail
		if ($this->isDiffer($tempMemberModal->temp_businessemail, $memberModal->businessemail)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->businessemail, $tempMemberModal->temp_businessemail)) {
				$isApproved['businessemail'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_businessemail];
				$allReject++;
			} else {
				$memberModal->businessemail = $approvalMemberModal->businessemail;
				$isApproved['businessemail'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_businessemail];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['businessemail'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_businessemail];
		}
		// membertitle
		if ($this->isDiffer($tempMemberModal->temp_membertitle, $memberModal->membertitle)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->membertitle, $tempMemberModal->temp_membertitle)) {
				$isApproved['membertitle'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_membertitle];
				$allReject++;
			} else {
				$memberModal->membertitle = $approvalMemberModal->membertitle;
				$isApproved['membertitle'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_membertitle];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['membertitle'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_membertitle];
		}
		// spousetitle
		if ($this->isDiffer($tempMemberModal->temp_spousetitle, $memberModal->spousetitle)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->spousetitle, $tempMemberModal->temp_spousetitle)) {
				$isApproved['spousetitle'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_spousetitle];
				$allReject++;
			} else {
				$memberModal->spousetitle = $approvalMemberModal->spousetitle;
				$isApproved['spousetitle'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_spousetitle];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['spousetitle'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_spousetitle];
		}
		// membernickname
		if ($this->isDiffer($tempMemberModal->temp_membernickname, $memberModal->membernickname)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->membernickname, $tempMemberModal->temp_membernickname)) {
				$isApproved['membernickname'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_membernickname];
				$allReject++;
			} else {
				$memberModal->membernickname = $approvalMemberModal->membernickname;
				$isApproved['membernickname'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_membernickname];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['membernickname'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_membernickname];
		}
		// spousenickname
		if ($this->isDiffer($tempMemberModal->temp_spousenickname, $memberModal->spousenickname)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->spousenickname, $tempMemberModal->temp_spousenickname)) {
				$isApproved['spousenickname'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_spousenickname];
				$allReject++;
			} else {
				$memberModal->spousenickname = $approvalMemberModal->spousenickname;
				$isApproved['spousenickname'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_spousenickname];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['spousenickname'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_spousenickname];
		}
		// homechurch
		if ($this->isDiffer($tempMemberModal->temp_homechurch, $memberModal->homechurch)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->homechurch, $tempMemberModal->temp_homechurch)) {
				$isApproved['homechurch'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_homechurch];
				$allReject++;
			} else {
				$memberModal->homechurch = $approvalMemberModal->homechurch;
				$isApproved['homechurch'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_homechurch];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['homechurch'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_homechurch];
		}
		// occupation
		if ($this->isDiffer($tempMemberModal->temp_occupation, $memberModal->occupation)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->occupation, $tempMemberModal->temp_occupation)) {
				$isApproved['occupation'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_occupation];
				$allReject++;
			} else {
				$memberModal->occupation = $approvalMemberModal->occupation;
				$isApproved['occupation'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_occupation];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['occupation'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_occupation];
		}
		// spouseoccupation
		if ($this->isDiffer($tempMemberModal->temp_spouseoccupation, $memberModal->spouseoccupation)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->spouseoccupation, $tempMemberModal->temp_spouseoccupation)) {
				$isApproved['spouseoccupation'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_spouseoccupation];
				$allReject++;
			} else {
				$memberModal->spouseoccupation = $approvalMemberModal->spouseoccupation;
				$isApproved['spouseoccupation'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_spouseoccupation];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['spouseoccupation'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_spouseoccupation];
		}
		// spouse_mobile1_countrycode
		if ($this->isDiffer($tempMemberModal->temp_spouse_mobile1_countrycode, $memberModal->spouse_mobile1_countrycode)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->spouse_mobile1_countrycode, $tempMemberModal->temp_spouse_mobile1_countrycode)) {
				$isApproved['spouse_mobile1_countrycode'] = [
					'isApproved' => false,
					'value' => $tempMemberModal->temp_spouse_mobile1_countrycode
				];
				$allReject++;
			} else {
				$memberModal->spouse_mobile1_countrycode = $approvalMemberModal->spouse_mobile1_countrycode;
				$isApproved['spouse_mobile1_countrycode'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_spouse_mobile1_countrycode];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['spouse_mobile1_countrycode'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_spouse_mobile1_countrycode];
		}
		// member_business_phone1_countrycode
		if ($this->isDiffer($tempMemberModal->temp_member_business_phone1_countrycode, $memberModal->member_business_phone1_countrycode)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->member_business_phone1_countrycode, $tempMemberModal->temp_member_business_phone1_countrycode)) {
				$isApproved['member_business_phone1_countrycode'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_member_business_phone1_countrycode];
				$allReject++;
			} else {
				$memberModal->member_business_phone1_countrycode = $approvalMemberModal->member_business_phone1_countrycode;
				$isApproved['member_business_phone1_countrycode'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_member_business_phone1_countrycode];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['member_business_phone1_countrycode'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_member_business_phone1_countrycode];
		}
		// member_business_phone1_countrycode
		if ($this->isDiffer($tempMemberModal->temp_member_business_phone1_areacode, $memberModal->member_business_phone1_areacode)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->member_business_phone1_areacode, $tempMemberModal->temp_member_business_phone1_areacode)) {
				$isApproved['member_business_phone1_areacode'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_member_business_phone1_areacode];
				$allReject++;
			} else {
				$memberModal->member_business_phone1_areacode = $approvalMemberModal->member_business_phone1_areacode;
				$isApproved['member_business_phone1_areacode'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_member_business_phone1_areacode];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['member_business_phone1_areacode'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_member_business_phone1_areacode];
		}

		// member_business_phone1_countrycode

		if ($this->isDiffer($tempMemberModal->temp_member_business_phone1_areacode, $memberModal->member_business_phone1_areacode)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->member_business_phone1_areacode, $tempMemberModal->temp_member_business_phone1_areacode)) {
				$isApproved['member_business_phone1_areacode'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_member_business_phone1_areacode];
				$allReject++;
			} else {
				$memberModal->member_business_phone1_areacode = $approvalMemberModal->member_business_phone1_areacode;
				$isApproved['member_business_phone1_areacode'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_member_business_phone1_areacode];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['member_business_phone1_areacode'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_member_business_phone1_areacode];
		}
		// memberImageThumbnail
		if ($this->isDiffer($tempMemberModal->temp_memberImageThumbnail, $memberModal->memberImageThumbnail)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->memberImageThumbnail, $tempMemberModal->temp_memberImageThumbnail)) {
				$isApproved['memberImageThumbnail'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_memberImageThumbnail];
				$allReject++;
			} else {
				$memberModal->memberImageThumbnail = $approvalMemberModal->memberImageThumbnail;
				$isApproved['memberImageThumbnail'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_memberImageThumbnail];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['memberImageThumbnail'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_memberImageThumbnail];
		}
		// spouseImageThumbnail
		if ($this->isDiffer($tempMemberModal->temp_spouseImageThumbnail, $memberModal->spouseImageThumbnail)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->spouseImageThumbnail, $tempMemberModal->temp_spouseImageThumbnail)) {
				$isApproved['spouseImageThumbnail'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_spouseImageThumbnail];
				$allReject++;
			} else {
				$memberModal->spouseImageThumbnail = $approvalMemberModal->spouseImageThumbnail;
				$isApproved['spouseImageThumbnail'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_spouseImageThumbnail];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['spouseImageThumbnail'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_spouseImageThumbnail];
		}
		// member_business_Phone3
		if ($this->isDiffer($tempMemberModal->temp_member_business_Phone3, $memberModal->member_business_Phone3)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->member_business_Phone3, $tempMemberModal->temp_member_business_Phone3)) {
				$isApproved['member_business_Phone3'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_member_business_Phone3];
				$allReject++;
			} else {
				$memberModal->member_business_Phone3 = $approvalMemberModal->member_business_Phone3;
				$isApproved['member_business_Phone3'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_member_business_Phone3];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['member_business_Phone3'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_member_business_Phone3];
		}

		// member_business_phone3_countrycode
		if ($this->isDiffer($tempMemberModal->temp_member_business_phone3_countrycode, $memberModal->member_business_phone3_countrycode)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->member_business_phone3_countrycode, $tempMemberModal->temp_member_business_phone3_countrycode)) {
				$isApproved['member_business_phone3_countrycode'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_member_business_phone3_countrycode];
				$allReject++;
			} else {
				$memberModal->member_business_phone3_countrycode = $approvalMemberModal->member_business_phone3_countrycode;
				$isApproved['member_business_phone3_countrycode'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_member_business_phone3_countrycode];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['member_business_phone3_countrycode'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_member_business_phone3_countrycode];
		}
		// member_business_phone3_areacode
		if ($this->isDiffer($tempMemberModal->temp_member_business_phone3_areacode, $memberModal->member_business_phone3_areacode)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->member_business_phone3_areacode, $tempMemberModal->temp_member_business_phone3_areacode)) {
				$isApproved['member_business_phone3_areacode'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_member_business_phone3_areacode];
				$allReject++;
			} else {
				$memberModal->member_business_phone3_areacode = $approvalMemberModal->member_business_phone3_areacode;
				$isApproved['member_business_phone3_areacode'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_member_business_phone3_areacode];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['member_business_phone3_areacode'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_member_business_phone3_areacode];
		}

		// memberbloodgroup

		if ($this->isDiffer($tempMemberModal->tempmemberBloodGroup, $memberModal->memberbloodgroup)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->memberbloodgroup, $tempMemberModal->tempmemberBloodGroup)) {
				$isApproved['memberbloodgroup'] = ['isApproved' => false, 'value' => $tempMemberModal->tempmemberBloodGroup];
				$allReject++;
			} else {
				$memberModal->memberbloodgroup = $approvalMemberModal->memberbloodgroup;
				$isApproved['memberbloodgroup'] = ['isApproved' => true, 'value' => $tempMemberModal->tempmemberBloodGroup];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['memberbloodgroup'] = ['isApproved' => true, 'value' => $tempMemberModal->tempmemberBloodGroup];
		}
		// spousebloodgroup
		if ($this->isDiffer($tempMemberModal->tempspouseBloodGroup, $memberModal->spousebloodgroup)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->spousebloodgroup, $tempMemberModal->tempspouseBloodGroup)) {
				$isApproved['spousebloodgroup'] = ['isApproved' => false, 'value' => $tempMemberModal->tempspouseBloodGroup];
				$allReject++;
			} else {
				$memberModal->spousebloodgroup = $approvalMemberModal->spousebloodgroup;
				$isApproved['spousebloodgroup'] = ['isApproved' => true, 'value' => $tempMemberModal->tempspouseBloodGroup];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['spousebloodgroup'] = ['isApproved' => true, 'value' => $tempMemberModal->tempspouseBloodGroup];
		}
		// member_residence_phone1_areacode
		if ($this->isDiffer($tempMemberModal->temp_member_residence_Phone1_areacode, $memberModal->member_residence_phone1_areacode)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->member_residence_phone1_areacode, $tempMemberModal->temp_member_residence_Phone1_areacode)) {
				$isApproved['member_residence_phone1_areacode'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_member_residence_Phone1_areacode];
				$allReject++;
			} else {
				$memberModal->member_residence_phone1_areacode = $approvalMemberModal->member_residence_phone1_areacode;
				$isApproved['member_residence_phone1_areacode'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_member_residence_Phone1_areacode];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['member_residence_phone1_areacode'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_member_residence_Phone1_areacode];
		}
		// member_residence_Phone1_countrycode
		if ($this->isDiffer($tempMemberModal->temp_member_residence_Phone1_countrycode, $memberModal->member_residence_Phone1_countrycode)) {
			$total++;
			if ($this->isDiffer($approvalMemberModal->member_residence_Phone1_countrycode, $tempMemberModal->temp_member_residence_Phone1_countrycode)) {
				$isApproved['member_residence_Phone1_countrycode'] = ['isApproved' => false, 'value' => $tempMemberModal->temp_member_residence_Phone1_countrycode];
				$allReject++;
			} else {
				$memberModal->member_residence_Phone1_countrycode = $approvalMemberModal->member_residence_Phone1_countrycode;
				$isApproved['member_residence_Phone1_countrycode'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_member_residence_Phone1_countrycode];
				$allAccept++;
			}
		} else {
			$total++;
			$allAccept++;
			$isApproved['member_residence_Phone1_countrycode'] = ['isApproved' => true, 'value' => $tempMemberModal->temp_member_residence_Phone1_countrycode];
		}
		if ($memberModal->save(false)) {
			$spousePhoneAccept = true;
			// add spouse details in creadential
			$spouseMobleNo = $approvalMemberModal->spouse_mobile1;
			$spouseEmail = $approvalMemberModal->spouse_email;
			if (!empty($spouseMobleNo) && $spousePhoneAccept) {
				$userCredentialModel = new ExtendedUserCredentials();
				$spouseUserCredentialExist = $userCredentialModel->memberCredentialExist($spouseMobleNo, $spouseEmail);
				if (!empty($spouseUserCredentialExist)) {
					$spouseUserCredentialId = $spouseUserCredentialExist['id'];
				} else {
					$spouseUserCredentialId = $this->addUserCredential($this->currentUser()->institutionid, $spouseEmail, 'remember', 'S', $spouseMobleNo);
				}
				$userMemberList = $userMemberModel->userMemberExist($spouseUserCredentialId, $memberModal->memberid, $this->currentUser()->institutionid, 'S');
				if ($userMemberList == null || count($userMemberList) <= 0) {
					$this->addUserModel($spouseUserCredentialId, $memberModal->memberid, $this->currentUser()->institutionid, 'S');
				}
			}
			$tempMemberModal->temp_approved = 1;
			if ($tempMemberModal->save(false)) {
				return array('isApproved' => $isApproved, 'allAccept' => $allAccept, 'allReject' => $allReject);
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	/**
	 * check the differnce is available
	 * @param unknown $newData
	 * @param unknown $oldData
	 * @return string
	 */
	protected function isDiffer($newData, $oldData)
	{
		$pendinginfo = false;
		if ($newData != $oldData) {
			$pendinginfo = true;
		}
		return $pendinginfo;
	}

	public function isDifferArray($array1, $array2)
	{
		return $array1 != $array2;
	}
	/**
	 * to get the bloob groups
	 * @return multitype:string
	 */
	protected  function getBloodGroup()
	{
		return [
			"O -ve"  => "O -ve",
			"O +ve"  => "O +ve",
			"A -ve" => "A -ve",
			"A +ve" => "A +ve",
			"B -ve" => "B -ve",
			"B +ve" => "B +ve",
			"AB -ve" => "AB -ve",
			"AB +ve" => "AB +ve"
		];
	}

	protected function getRelation()
	{

		return [

			"Son"  => "Son",
			"Daughter"  => "Daughter",
			"Father" => "Father",
			"Mother" => "Mother",
			"Brother" => "Brother",
			"Sister" => "Sister",
			"Grand son" => "Grand son",
			"Grand daughter" => "Grand daughter",
			"Grand father" => "Grand father",
			"Grand mother" => "Grand mother"
		];
	}

	protected  function getAddressTypes()
	{

		$addressTypeModel = new ExtendedAddresstype();
		$typeAddress = $addressTypeModel::find()->all();
		$typeAddressArray = [];
		foreach ($typeAddress as $communicationAddres) {
			$typeAddressArray[$communicationAddres->id] = $communicationAddres->type;
		}
		return $typeAddressArray;
	}

	/**
	 * to store member details
	 */
	protected function saveMemberdetails($postDetails, $memberImages, $spouseImages, $type, $memberId = null, $memberType = null)
	{
		$userCredentialModel = new ExtendedUserCredentials();
		$userMemberModel     = new ExtendedUserMember();
		$dependantModel      = new ExtendedDependant();
		if (!empty($postDetails)) {
			$membersince = null;
			if ($memberType != 'staff') {
				$memberSinceVal = isset($postDetails['membersince']) ? $postDetails['membersince'] : null;
				$membersince = $this->sqlDateConversion($memberSinceVal);
			}
			$memberDob   = isset($postDetails['member_dob']) ? $this->sqlDateConversion($postDetails['member_dob']) : null;
			$spouseDob   = isset($postDetails['spouse_dob']) ? $this->sqlDateConversion($postDetails['spouse_dob']) : null;
			$dom         = isset($postDetails['dom']) ? $this->sqlDateConversion($postDetails['dom']) : null;

			$spouseMobleNo = isset($postDetails['ExtendedMember']['spouse_mobile1']) ? trim($postDetails['ExtendedMember']['spouse_mobile1']) : null;
			$spouseEmail   = isset($postDetails['ExtendedMember']['spouse_email']) ? trim($postDetails['ExtendedMember']['spouse_email']) : null;
			$mobileNo      = isset($postDetails['ExtendedMember']['member_mobile1']) ? trim($postDetails['ExtendedMember']['member_mobile1']) : null;;
			$email         = isset($postDetails['ExtendedMember']['member_email']) ? trim($postDetails['ExtendedMember']['member_email']) : null;
			$membership    = isset($postDetails['ExtendedMember']['memberno']) ? trim($postDetails['ExtendedMember']['memberno']) : null;

			$member_role = isset($postDetails['ExtendedSettings']['member_role']) ? trim($postDetails['ExtendedSettings']['member_role']) : null;
			$spouse_role = isset($postDetails['ExtendedSettings']['spouse_role']) ? trim($postDetails['ExtendedSettings']['spouse_role']) : null;

			$user = Yii::$app->user->identity;
			$currentInstitution = $user->institution;
			$institutionType 	= $currentInstitution->institutiontype;

			$institusionId = $this->currentUser()->institutionid;
			if ($memberId) {
				$memberModel = $this->findModel($memberId);
				$memberModel->lastupdated = date(yii::$app->params['dateFormat']['sqlDandTFormat']);
			} else {
				$memberModel = new ExtendedMember();
			}

			$checkingDeatils = $memberModel->isMobleNumberExists($institusionId, $mobileNo);
			if (!empty($checkingDeatils)) {
				if ($memberId) {
					if ($checkingDeatils['memberid'] != $memberId) {
						return ['status' => false, 'msg' => "Member mobile number already exists"];
					}
				} else {
					return ['status' => false, 'msg' => "Member mobile number already exists"];
				}
			}
			if (!empty($spouseMobleNo)) {
				$checkingDeatils = $memberModel->isMobleNumberExists($institusionId, $spouseMobleNo);
				if (!empty($checkingDeatils)) {
					if ($memberId) {
						if ($checkingDeatils['memberid'] != $memberId) {
							return ['status' => false, 'msg' => "Spouse mobile no already exists"];
						}
					} else {
						return ['status' => false, 'msg' => "Spouse mobile no already exists"];
					}
				}
			}
			$checkingDeatils = $memberModel->isMemberShipExists($institusionId, $membership);
			if (!empty($checkingDeatils)) {
				if ($memberId) {
					if ($checkingDeatils['memberid'] != $memberId) {
						return ['status' => false, 'msg' => "Membership number already exists"];
					}
				} else {
					return ['status' => false, 'msg' => "Membership number already exists"];
				}
			}

			$memberUserCredentialId = null;
			$currrentInstitutionId = $this->currentUser()->institutionid;
			$userCredentialExist = $userCredentialModel->memberCredentialExist($mobileNo, $email);
			if (!empty($userCredentialExist)) {
				$memberUserCredentialId = $userCredentialExist['id'];
				$memberUserInstitutionId = $userCredentialExist['institutionid'];
				if ($currrentInstitutionId != $memberUserInstitutionId) {
					$checkingDeatils = $memberModel->isMobleNumberExists($memberUserInstitutionId, $mobileNo);
					if (empty($checkingDeatils)) {
						$userCredentialExist = $userCredentialModel->updateInstitutionId($currrentInstitutionId, $memberUserCredentialId);
					}
				}
			} else {
				$memberUserCredentialId   = 	$this->addUserCredential($currrentInstitutionId, $email, 'remember', 'M', $mobileNo);
				if (!$memberUserCredentialId) {
					return ['status' => false, 'msg' => "Unable to save member details"];
				}
			}
			$member = $this->addMember($postDetails['ExtendedMember'], $memberModel, $membersince, $memberDob, $spouseDob, $dom, $memberImages, $spouseImages, $memberType, $member_role);
			if (is_object($member)) {
				if ($member->memberid) {
					if ($memberId) {
						$additionalInfo = ExtendedMemberadditionalinfo::find()->where(['memberid' => $member->memberid])->one();
						if ($additionalInfo == null) {
							$additionalInfo = new ExtendedMemberadditionalinfo();
						}
					} else {
						$additionalInfo      = new ExtendedMemberadditionalinfo();
					}
					if (!empty($postDetails['ExtendedMemberadditionalinfo']['tagcloud'])) {
						$additionalInfo->tagcloud = $postDetails['ExtendedMemberadditionalinfo']['tagcloud'];
						$additionalInfo->memberid = $member->memberid;
						if ($additionalInfo->save()) {
						} else {
							return ['status' => false, 'msg' => "Unable to save member details"];
						}
					}
					$memberId = $member->memberid;

					// Handle member active status and UserMember connection
					$memberActive = isset($postDetails['ExtendedMember']['active']) ? (int)$postDetails['ExtendedMember']['active'] : 1;
					$userMemberList = $userMemberModel->userMemberExist($memberUserCredentialId, $member->memberid, $this->currentUser()->institutionid, 'M');

					// Check if we should manage UserMember based on active status
					$shouldRemoveIfInactive = \Yii::$app->params['removeUserMemberOnInactive'] === true;
					$shouldManageUserMember = ($memberActive == 1) || !$shouldRemoveIfInactive;

					if ($shouldManageUserMember) {
						// Create or maintain UserMember connection
						if ($userMemberList == null || count($userMemberList) <= 0) {
							$ll= $this->addUserModel($memberUserCredentialId, $member->memberid, $this->currentUser()->institutionid, 'M', $member_role);
						} else {
							// UserMember exists - update role if provided
							if ($member_role) {
								$userMemberId = ExtendedUserMember::getUserMemberId($member->memberid, $this->currentUser()->institutionid, 'M');
								if ($userMemberId) {
									$this->setRole($member_role, $userMemberId);
								}
							}
						}
					} elseif ($memberActive == 0 && $shouldRemoveIfInactive) {
						// Member is inactive and feature is enabled - remove UserMember connection
						if ($userMemberList != null && count($userMemberList) > 0) {
							ExtendedUserMember::deleteAll([
								'userid' => $memberUserCredentialId,
								'memberid' => $member->memberid,
								'institutionid' => $this->currentUser()->institutionid,
								'usertype' => 'M'
							]);
						}
					}

					$dependantIds = isset($postDetails['dependantIds']) ? $postDetails['dependantIds'] : null;
					if ($dependantIds) {
						if ($dependantModel->updateMemberId($dependantIds, $memberId)) {
						} else {
							return ['status' => false, 'msg' => "Unable to save member details"];
						}
					}
				}
			} else {
				return ['status' => false, 'msg' => "Unable to save member details"];
			}

			// Handle spouse active status and UserMember connection
			if (!empty($spouseMobleNo)) {
				$spouseActive = isset($postDetails['ExtendedMember']['active_spouse']) ? (int)$postDetails['ExtendedMember']['active_spouse'] : 1;

				$spouseUserCredentialExist = $userCredentialModel->memberCredentialExist($spouseMobleNo, $spouseEmail);
				if (!empty($spouseUserCredentialExist)) {
					$spouseUserCredentialId = $spouseUserCredentialExist['id'];
				} else {
					$spouseUserCredentialId   = $this->addUserCredential($this->currentUser()->institutionid, $spouseEmail, 'remember', 'S', $spouseMobleNo, $spouse_role);
				}

				$userMemberList = $userMemberModel->userMemberExist($spouseUserCredentialId, $member->memberid, $this->currentUser()->institutionid, 'S');

				// Check if we should manage UserMember based on active status
				$shouldRemoveIfInactive = \Yii::$app->params['removeUserMemberOnInactive'] === true;
				$shouldManageUserMember = ($spouseActive == 1) || !$shouldRemoveIfInactive;

				if ($shouldManageUserMember) {
					// Create or maintain UserMember connection
					if ($userMemberList == null || count($userMemberList) <= 0) {
						$this->addUserModel($spouseUserCredentialId, $member->memberid, $this->currentUser()->institutionid, 'S', $spouse_role);
					} else {
						// UserMember exists - update role if provided
						if ($spouse_role) {
							$userMemberId = ExtendedUserMember::getUserMemberId($member->memberid, $this->currentUser()->institutionid, 'S');
							if ($userMemberId) {
								$this->setRole($spouse_role, $userMemberId);
							}
						}
					}
				} elseif ($spouseActive == 0 && $shouldRemoveIfInactive) {
					// Spouse is inactive and feature is enabled - remove UserMember connection
					if ($userMemberList != null && count($userMemberList) > 0) {
						ExtendedUserMember::deleteAll([
							'userid' => $spouseUserCredentialId,
							'memberid' => $member->memberid,
							'institutionid' => $this->currentUser()->institutionid,
							'usertype' => 'S'
						]);
					}
				}
			}
			$this->storeSettings($postDetails['ExtendedSettings'], $member->memberid);
			if ($memberId) {
				// Update user credentials for mobile/email changes and conflict resolution
				// The problematic UserMember deletion logic has been disabled in BaseModel
				BaseModel::updateUserCredentials($memberModel);
			}
			return ['status' => true, 'msg' => "Member details stored successfully"];
		}
	}
	protected function storeTempMemeberDetails($postDetails, $tempMemberModel, $tempMemberMailModel, $memberImages, $spouseImages, $tempDependantImages, $titlesArray, $type = null)
	{
		$memberModel = new ExtendedMember();
		if ($postDetails) {
			if (!empty($postDetails['ExtendedMember']['spouse_mobile1'])) {
				if ($memberModel->isMobleNumberAlreadyExists($postDetails['ExtendedMember']['institutionid'], $postDetails['ExtendedMember']['spouse_mobile1'], $postDetails['ExtendedMember']['memberid'])) {
					return ['status' => false, 'msg' => "Spouse mobile no already exists"];
				}
			}
			$memberDob   = $this->sqlDateConversion($postDetails['member_dob']);
			$spouseDob   = $this->sqlDateConversion($postDetails['spouse_dob']);
			$dom         = $this->sqlDateConversion($postDetails['dom']);
			$member = $this->addTempMember($postDetails['ExtendedMember'], $tempMemberModel, $memberDob, $spouseDob, $dom, $memberImages, $spouseImages, $type, $titlesArray);
			$_member = $this->addTempMember($postDetails['ExtendedMember'], $tempMemberMailModel, $memberDob, $spouseDob, $dom, $memberImages, $spouseImages, $type, $titlesArray);
			if (!$member || !$_member) {
				return ['status' => false, 'msg' => "Unable to save member details"];
			}
			if (!$this->addTempDependantMember($postDetails, $tempDependantImages)) {
				return ['status' => false, 'msg' => "Unable to save dependant details"];
			}
			if (!empty($postDetails['ExtendedMemberadditionalinfo']['tagcloud'])) {
				$this->addTempAdditionalInfo($postDetails['ExtendedMemberadditionalinfo']['tagcloud'], $postDetails['ExtendedMember']['memberid']);
			}
			if (!empty($postDetails['ExtendedMemberadditionalinfo']['tagcloud'])) {
				$this->addTempAdditionalInfoMail($postDetails['ExtendedMemberadditionalinfo']['tagcloud'], $postDetails['ExtendedMember']['memberid']);
			}
			return ['status' => true, 'msg' => "Member details stored successfully"];
		}
	}

	protected function addTempAdditionalInfo($tags, $memberId)
	{
		$additionalInfo = ExtendedMemberadditionalinfo::find()->where(['memberid' => $memberId])->one();
		if ($additionalInfo == null) {
			$additionalInfo      = new ExtendedMemberadditionalinfo();
		}
		$additionalInfo->memberid = $memberId;
		$additionalInfo->save();
		$tempAdditionalInfo = ExtendedTempmemberadditionalinfo::find()->where(['memberid' => $memberId])->one();
		if ($tempAdditionalInfo == null) {
			$tempAdditionalInfo = new ExtendedTempmemberadditionalinfo();
		}
		if (!empty($tags)) {
			$tempAdditionalInfo->temptagcloud = $tags;
			$tempAdditionalInfo->isapproved  = '0';
			$tempAdditionalInfo->memberid = $memberId;
			if ($tempAdditionalInfo->save()) {
				return true;
			} else {
				return false;
			}
		}
	}

	protected function addTempAdditionalInfoMail($tags, $memberId)
	{
		$tempAdditionalInfoMail = ExtendedTempmemberadditionalinfomail::find()->where(['memberid' => $memberId])->one();
		if ($tempAdditionalInfoMail == null) {
			$tempAdditionalInfoMail = new ExtendedTempmemberadditionalinfomail();
		}
		if (!empty($tags)) {
			$tempAdditionalInfoMail->temptagcloud = $tags;
			$tempAdditionalInfoMail->isapproved  = '0';
			$tempAdditionalInfoMail->memberid = $memberId;
			if ($tempAdditionalInfoMail->save()) {
				return true;
			} else {
				return false;
			}
		}
	}
	/**
	 * To store the member settings
	 * @param unknown $postSettingDetails
	 * @param unknown $memberId
	 * @return boolean
	 */
	protected function storeSettings($postSettingDetails, $memberId)
	{
		if (($settingsModel = ExtendedSettings::findOne(['memberid' => $memberId])) != null) {
		} else {
			$settingsModel = new ExtendedSettings();
		}
		if (!empty($postSettingDetails)) {
			$settingsModel->memberid = $memberId;
			$settingsModel->addresstypeid = !empty($postSettingDetails['addresstypeid']) ? $postSettingDetails['addresstypeid'] : null;
			$settingsModel->membernotification  = $postSettingDetails['membernotification'] ? 1 : 0;
			$settingsModel->birthday = $postSettingDetails['birthday'] ? 1 : 0;
			$settingsModel->anniversary = $postSettingDetails['anniversary'] ? 1 : 0;
			$settingsModel->memberemail = $postSettingDetails['memberemail'] ? 1 : 0;
			$settingsModel->membersms = 0;
			$settingsModel->spousenotification = $postSettingDetails['spousenotification'] ? 1 : 0;
			$settingsModel->spousebirthday = $postSettingDetails['spousebirthday'] ? 1 : 0;
			$settingsModel->spouseanniversary = $postSettingDetails['spouseanniversary'] ? 1 : 0;
			$settingsModel->spouseemail = $postSettingDetails['spouseemail'] ? 1 : 0;
			$settingsModel->spousesms  = 0;

			$settingsModel->membermobilePrivacyEnabled = !empty($postSettingDetails['membermobilePrivacyEnabled']) ? 1 : 0;
			$settingsModel->spousemobilePrivacyEnabled = !empty($postSettingDetails['spousemobilePrivacyEnabled']) ? 1 : 0;
			if ($settingsModel->save()) {
				return true;
			} else {
				return false;
			}
		}
	}
	/**
	 * add details into usermodel table
	 * @param unknown $memberUserCredentialId
	 * @param unknown $memberId
	 * @param unknown $institusionId
	 * @param unknown $type
	 */

	protected function addUserModel($memberUserCredentialId, $memberId, $institusionId, $type, $roleId = null)
	{
		$userMemberModel = ExtendedUserMember::find()
			->where(['memberid' => $memberId])
			->andWhere(['institutionid' => $institusionId])
			->andWhere(['usertype' => $type])->one();
		if (!$userMemberModel) {
			$userMemberModel = new ExtendedUserMember();
		}
		$userMemberModel->userid = $memberUserCredentialId;
		$userMemberModel->memberid = $memberId;
		$userMemberModel->institutionid = $institusionId;
		$userMemberModel->usertype = $type;
		if ($userMemberModel->save()) {
			if ($roleId) {
				$this->setRole($roleId, $userMemberModel->id);
			}
			return true;
		} else {
			return false;
		}
	}
	/**
	 * To add user creadential
	 * @param unknown $institutionId
	 * @param unknown $email
	 * @param unknown $password
	 * @param unknown $userType
	 * @param unknown $mobileNo
	 */
	protected  function addUserCredential($institutionId, $email, $password, $userType, $mobileNo)
	{
		$userCredentialModel = new ExtendedUserCredentials();
		$userCredentialModel->institutionid = $institutionId;
		$userCredentialModel->emailid = $email;
		$userCredentialModel->password = Yii::$app->getSecurity()->generatePasswordHash($password);
		$userCredentialModel->initiallogin = false;
		$userCredentialModel->usertype = $userType;
		$userCredentialModel->mobileno = $mobileNo;
		$userCredentialModel->created_at = date('Y-m-d H:i:s');
		$userCredentialModel->generateAuthKey();
		if ($userCredentialModel->save(false)) {
			return $userCredentialModel->id;
		} else {
			return false;
		}
	}
	/*
    *Sets role to memeber
    *@input:$role id,userid
    */
	protected function setRole($roleId, $memberId)
	{
		try {
			$auth = Yii::$app->authManager;
			$role = $auth->getRole($roleId);
			$ifRoleExist = $auth->getRolesByUser($memberId);
			if ($ifRoleExist) {
				$auth->revokeAll($memberId);
			}
			if ($role) {
				$auth->assign($role, $memberId);
			}
		} catch (Exception $e) {
			yii::error($e->getMessage());
		}
	}
	/**
	 * To store member details
	 * @param unknown $memberDetails
	 */
	protected function addMember(
		$memberDetails,
		$memberModel,
		$membersince,
		$memberDob,
		$spouseDob,
		$dom,
		$memberImages,
		$spouseImages,
		$isStaff = false,
		$memberRole = null
	) {

		if (!$isStaff) {
			$memberModel->familyunitid 	= isset($memberDetails['familyunitid']) ? trim($memberDetails['familyunitid']) : '';
			$memberModel->memberno = isset($memberDetails['memberno']) ? trim($memberDetails['memberno']) : null;
			$memberModel->membershiptype 	= isset($memberDetails['membershiptype']) ? trim($memberDetails['membershiptype']) : null;
			$memberModel->membersince 	= $membersince;
		} else {
			$memberModel->memberno = '000-00-000';
			$memberModel->membershiptype = '';
		}
		$memberModel->institutionid   	= $this->currentUser()->institutionid;
		$memberModel->firstName 	= trim($memberDetails['firstName']);
		$memberModel->middleName 	= trim($memberDetails['middleName']);
		$memberModel->lastName 	= trim($memberDetails['lastName']);
		$memberModel->business_address1 	= trim($memberDetails['business_address1']);
		$memberModel->business_address2 	= trim($memberDetails['business_address2']);
		$memberModel->business_address3 	= null;
		$memberModel->business_district 	= trim($memberDetails['business_district']);
		$memberModel->business_state 	= trim($memberDetails['business_state']);
		$memberModel->business_pincode 	= trim($memberDetails['business_pincode']);
		$memberModel->member_dob 	= $memberDob;
		$memberModel->member_mobile1 	= trim($memberDetails['member_mobile1']);
		$memberModel->member_mobile2 	= null;
		$memberModel->member_musiness_Phone1 	= trim($memberDetails['member_musiness_Phone1']);
		$memberModel->member_business_Phone2 	= null;
		$memberModel->member_residence_Phone1 	= trim($memberDetails['member_residence_Phone1']);
		$memberModel->member_residence_Phone2 	= '';
		$memberModel->member_email 	= trim($memberDetails['member_email']);
		$memberModel->spouse_firstName 	= trim($memberDetails['spouse_firstName']);
		$memberModel->spouse_middleName 	= trim($memberDetails['spouse_middleName']);
		$memberModel->spouse_lastName 	= trim($memberDetails['spouse_lastName']);
		$memberModel->spouse_dob 	= $spouseDob;
		$memberModel->dom 	= $dom;
		$memberModel->spouse_mobile1 	= trim($memberDetails['spouse_mobile1']);
		$memberModel->spouse_mobile2 	= '';
		$memberModel->spouse_email 	= trim($memberDetails['spouse_email']);
		$memberModel->residence_address1 	= trim($memberDetails['residence_address1']);
		$memberModel->residence_address2 	= trim($memberDetails['residence_address2']);
		$memberModel->residence_address3 	= '';
		$memberModel->residence_district 	= trim($memberDetails['residence_district']);
		$memberModel->residence_state 	= trim($memberDetails['residence_state']);
		$memberModel->residence_pincode 	= trim($memberDetails['residence_pincode']);
		$memberModel->member_pic 	= $memberImages['orginal'];
		$memberModel->spouse_pic 	= $spouseImages['orginal'];
		$memberModel->memberImageThumbnail 	= $memberImages['thumbnail'];
		$memberModel->spouseImageThumbnail 	= $spouseImages['thumbnail'];
		$memberModel->app_reg_member 	= '';
		$memberModel->app_reg_spouse 	= '';
		$memberModel->active 	= 1;
		$memberModel->businessemail 	= trim($memberDetails['businessemail']);
		$memberModel->membertitle 	= trim($memberDetails['membertitle']);
		$memberModel->spousetitle 	= trim($memberDetails['spousetitle']);
		$memberModel->membernickname 	= isset($memberDetails['membernickname']) ? trim($memberDetails['membernickname']) : '';
		$memberModel->spousenickname 	= trim($memberDetails['spousenickname']);
		$memberModel->createddate 	= date('Y-m-d H:i:s');
		$memberModel->homechurch 	= isset($memberDetails['homechurch']) ? trim($memberDetails['homechurch']) : '';
		$memberModel->occupation 	= trim($memberDetails['occupation']);
		$memberModel->spouseoccupation 	= trim($memberDetails['spouseoccupation']);
		$memberModel->member_mobile1_countrycode 	= trim($memberDetails['member_mobile1_countrycode']);
		if (!empty($memberModel->spouse_mobile1)) {
			$memberModel->spouse_mobile1_countrycode 	= trim($memberDetails['spouse_mobile1_countrycode']);
		} else {
			$memberModel->spouse_mobile1_countrycode 	= '';
		}
		$memberModel->member_business_phone1_countrycode 	= trim($memberDetails['member_business_phone1_countrycode']);
		$memberModel->member_business_phone1_areacode 	= trim($memberDetails['member_business_phone1_areacode']);
		$memberModel->member_business_phone2_countrycode 	= '';
		$memberModel->batch = !empty($memberDetails['batch']) ? $memberDetails['batch'] : '';
		$memberModel->setLatitude($memberDetails['latitude'] ?? NULL); // Set latitude
		$memberModel->setLongitude($memberDetails['longitude'] ?? NULL);

		if (!$isStaff) {
			$memberModel->membertype 	= 0;
		} else {
			$memberModel->membertype 	= 1;
		}
		if ($isStaff) {
			$memberModel->staffdesignation 	= trim($memberDetails['staffdesignation']);
		} else {
			$memberModel->staffdesignation 	= null;
		}
		$memberModel->member_business_Phone3 	= trim($memberDetails['member_business_Phone3']);
		$memberModel->member_business_phone3_areacode 	= trim($memberDetails['member_business_phone3_areacode']);
		$memberModel->member_business_phone3_countrycode 	= trim($memberDetails['member_business_phone3_countrycode']);
		if (!$isStaff) {
			$memberModel->newmembernum 	= trim($memberDetails['memberno']);
			$memberModel->memberbloodgroup 	= trim($memberDetails['memberbloodgroup']);
			$memberModel->spousebloodgroup 	= trim($memberDetails['spousebloodgroup']);
		}
		$memberModel->member_residence_phone1_areacode 	= trim($memberDetails['member_residence_phone1_areacode']);
		$memberModel->member_residence_Phone1_countrycode  	= trim($memberDetails['member_residence_Phone1_countrycode']);

		// Handle new fields for member
		if (!$isStaff) {
			$memberModel->active = isset($memberDetails['active']) ? (int)$memberDetails['active'] : 1;
			$memberModel->confirmed = isset($memberDetails['confirmed']) ? (int)$memberDetails['confirmed'] : 1;
			$memberModel->active_spouse = isset($memberDetails['active_spouse']) ? (int)$memberDetails['active_spouse'] : 1;
			$memberModel->confirmed_spouse = isset($memberDetails['confirmed_spouse']) ? (int)$memberDetails['confirmed_spouse'] : 1;
			$memberModel->head_of_family = isset($memberDetails['head_of_family']) ? $memberDetails['head_of_family'] : 'm';
		}

		if ($memberModel->save(false)) {
			return $memberModel;
		} else {
			return false;
		}
	}

	/**
	 * To store temp member details
	 * @param unknown $memberDetails
	 */
	protected function addTempMember(
		$memberDetails,
		$memberModel,
		$memberDob,
		$spouseDob,
		$dom,
		$memberImages,
		$spouseImages,
		$formType = null,
		$titleArray = []
	) {

		$memberModel->temp_memberid         = trim($memberDetails['memberid']);
		$memberModel->temp_institutionid    = trim($memberDetails['institutionid']);
		$memberModel->temp_approved         = 0;
		//	$memberModel->temp_memberno         = $memberDetails['memberno'];
		//	$memberModel->temp_membershiptype 	= $memberDetails['membershiptype'];
		//	$memberModel->temp_membersince 	= $membersince;
		$memberModel->temp_firstName 	= trim($memberDetails['firstName']);
		$memberModel->temp_middleName 	= trim($memberDetails['middleName']);
		$memberModel->temp_lastName 	= trim($memberDetails['lastName']);
		$memberModel->temp_business_address1 	= trim($memberDetails['business_address1']);
		$memberModel->temp_business_address2 	= trim($memberDetails['business_address2']);
		$memberModel->temp_business_address3 	= null;
		$memberModel->temp_business_district 	= trim($memberDetails['business_district']);
		$memberModel->temp_business_state 	= trim($memberDetails['business_state']);
		$memberModel->temp_business_pincode 	= trim($memberDetails['business_pincode']);
		$memberModel->temp_member_dob 	= $memberDob;
		//to do
		//	$memberModel->temp_member_mobile1 	= $memberDetails['member_mobile1'];
		$memberModel->temp_member_mobile2 	= null;
		$memberModel->temp_member_business_Phone1 	= trim($memberDetails['member_musiness_Phone1']);
		$memberModel->temp_member_business_Phone2 	= null;
		$memberModel->temp_member_residence_Phone1 	= trim($memberDetails['member_residence_Phone1']);
		$memberModel->temp_member_residence_Phone2 	= '';
		$memberModel->temp_member_email 	= trim($memberDetails['member_email']);
		$memberModel->temp_spouse_firstName 	= trim($memberDetails['spouse_firstName']);
		$memberModel->temp_spouse_middleName 	= trim($memberDetails['spouse_middleName']);
		$memberModel->temp_spouse_lastName 	= trim($memberDetails['spouse_lastName']);
		$memberModel->temp_spouse_dob 	= $spouseDob;
		$memberModel->temp_dom 	= $dom;
		$memberModel->temp_spouse_mobile1 	= trim($memberDetails['spouse_mobile1']);
		$memberModel->temp_spouse_mobile2 	= '';
		$memberModel->temp_spouse_email 	= trim($memberDetails['spouse_email']);
		$memberModel->temp_residence_address1 	= trim($memberDetails['residence_address1']);
		$memberModel->temp_residence_address2 	= trim($memberDetails['residence_address2']);
		$memberModel->temp_residence_address3 	= '';
		$memberModel->temp_residence_district 	= trim($memberDetails['residence_district']);
		$memberModel->temp_residence_state 	= trim($memberDetails['residence_state']);
		$memberModel->temp_residence_pincode 	= trim($memberDetails['residence_pincode']);
		$memberModel->temp_member_pic 	= $memberImages['orginal'];
		$memberModel->temp_spouse_pic 	= $spouseImages['orginal'];
		$memberModel->temp_memberImageThumbnail 	= $memberImages['thumbnail'];
		$memberModel->temp_spouseImageThumbnail 	= $spouseImages['thumbnail'];
		$memberModel->temp_app_reg_member 	= '';
		$memberModel->temp_app_reg_spouse 	= '';
		$memberModel->temp_homechurch = !empty($memberDetails['homechurch']) ? trim($memberDetails['homechurch']) : null;
		$memberModel->temp_active 	= 1;
		$memberModel->temp_businessemail 	= trim($memberDetails['businessemail']);
		$memberModel->temp_membertitle 	= trim($memberDetails['membertitle']);
		$spouseTitle = !empty($memberDetails['spousetitle']) ? $memberDetails['spousetitle'] : null;
		if ($spouseTitle) {
			$memberModel->temp_spousetitle 	= $spouseTitle;
		}
		$memberModel->temp_membernickname 	= trim($memberDetails['membernickname']);
		$memberModel->temp_spousenickname 	= trim($memberDetails['spousenickname']);
		$memberModel->temp_createddate 	= date('Y-m-d H:i:s');
		$memberModel->temp_occupation 	= trim($memberDetails['occupation']);
		$memberModel->temp_spouseoccupation 	= trim($memberDetails['spouseoccupation']);
		//to do
		//$memberModel->temp_member_mobile1_countrycode 	= $memberDetails['member_mobile1_countrycode'];

		$memberModel->temp_spouse_mobile1_countrycode 	= trim($memberDetails['spouse_mobile1_countrycode']);

		$memberModel->temp_member_business_phone1_countrycode 	= trim($memberDetails['member_business_phone1_countrycode']);
		$memberModel->temp_member_business_phone1_areacode 	= trim($memberDetails['member_business_phone1_areacode']);
		$memberModel->temp_member_business_phone2_countrycode 	= '';
		$memberModel->temp_member_business_Phone3 	= trim($memberDetails['member_business_Phone3']);
		$memberModel->temp_member_business_phone3_areacode 	= trim($memberDetails['member_business_phone3_areacode']);
		$memberModel->temp_member_business_phone3_countrycode 	= trim($memberDetails['member_business_phone3_countrycode']);
		$memberModel->tempmemberBloodGroup 	= trim($memberDetails['memberbloodgroup']);
		$memberModel->tempspouseBloodGroup 	= trim($memberDetails['spousebloodgroup']);
		$memberModel->temp_member_residence_Phone1_areacode 	= trim($memberDetails['member_residence_phone1_areacode']);
		$memberModel->temp_member_residence_Phone1_countrycode  	= trim($memberDetails['member_residence_Phone1_countrycode']);
		$memberModel->setLatitude($memberDetails['latitude'] ?? NULL);
		$memberModel->setLongitude($memberDetails['longitude'] ?? NULL);

		if ($memberModel->save(false)) {
			return $memberModel;
		} else {
			return false;
		}
	}

	/**
	 * To add the dependants
	 * @param string $memberId
	 * @param string $tempMemberId
	 * @return string
	 */

	public function addDependantDetails($memberId = null, $tempMemberId = null, $isAjax = false)
	{

		$dependantModel = new ExtendedDependant();
		$spouseModel     = new \yii\base\DynamicModel(['id', 'spouse_title', 'spouse_name', 'wedding_anniversary', 'photo', 'dob', 'weddinganniversary', 'spouseImage', 'spouse_mobile_country_code', 'spouse_mobile', 'spouse_occupation', 'active', 'confirmed']);

		if (!$memberId && !$tempMemberId) {

			$memberId = time();
		}

		if ($memberId) {
			$dependantDetailCorrection = [];
			$dependantDetails = $dependantModel->getDependants($memberId);
			foreach ($dependantDetails as $key => $value) {
				if ($value['dependantname'] == '' || $value['dependantname'] == null) {
					continue;
				}
				$dependantDetailCorrection[$key] = $value;
			}
			$dependantDetails  = $dependantDetailCorrection;
		}
		if ($tempMemberId) {

			$dependantDetails = $dependantModel->getMemberNotDependants($tempMemberId);
			$memberId = $tempMemberId;
		}

		$titleModel      = new ExtendedTitle();

		$institusionId = $this->currentUser()->institutionid;

		$titles = CacheHelper::getTitles($institusionId, function() use ($titleModel, $institusionId) {
			return $titleModel->getActiveTitles($institusionId);
		});

		$relations  = $this->getRelation();
		$isMarried = ['1' => 'Single', '2' => "Married"];

		$titlesArray = [];

		if (!empty($titles)) {
			$titlesArray =	ArrayHelper::map($titles, 'TitleId', 'Description');
		}

		// Ensure dependantDetails is always an array
		if (!is_array($dependantDetails)) {
			$dependantDetails = [];
		}

		$dataProvider = new ArrayDataProvider([
			'allModels' => $dependantDetails,
		]);
		$dependantIds = $this->getDepentantIds($dependantDetails);
		if ($isAjax) {
			// Debug logging - uncomment if needed
			// Yii::debug("AJAX CALL - Loading dependant form", __METHOD__);
			// Yii::debug($dependantDetails, __METHOD__);
			$memberDependantForm =  $this->renderAjax('dependantcreate', [
				'dependantModel' => $dependantModel,
				'spouseModel'    => $spouseModel,
				'titlesArray'    => $titlesArray,
				'relations'      =>  $relations,
				'isMarried'      =>  $isMarried,
				'memberId'       =>  $memberId,
			]);
			$dependantDetails = $this->renderPartial('dependantindex', [
				'dataProvider' => $dataProvider,
				'memberDependantForm'   => $memberDependantForm,
				'dependantIds' => $dependantIds,
			]);
		} else {
			$memberDependantForm =  $this->renderPartial('dependantcreate', [
				'dependantModel' => $dependantModel,
				'spouseModel'    => $spouseModel,
				'titlesArray'    => $titlesArray,
				'relations'      =>  $relations,
				'isMarried'      =>  $isMarried,
				'memberId'       =>  $memberId,
			]);
			$dependantDetails = $this->renderPartial('dependantindex', [
				'dataProvider' => $dataProvider,
				'memberDependantForm'   => $memberDependantForm,
				'dependantIds' => $dependantIds,
			]);
		}


		return $dependantDetails;
	}
	/*
     * To delete depentant details
     */
	protected function deleteDepentant($model)
	{

		$deleteDepentantModel = new ExtendedDeleteDependant();
		$deleteDepentantModel->memberid = $model->memberid;
		$deleteDepentantModel->dependantname = $model->dependantname;
		$deleteDepentantModel->dob = $model->dob;
		$deleteDepentantModel->relation = $model->relation;
		$deleteDepentantModel->save();
		$model->delete();
	}

	/**
	 * store the dependant details
	 * @param unknown $model
	 * @param unknown $reqDetails
	 * @param string $memberId
	 * @param string $tempMemberid
	 * @return unknown|boolean
	 */
	protected function storeDependant($model, $reqDetails, $images, $memberId = null, $tempMemberid = null)
	{

		if ($tempMemberid) {
			$model->tempmemberid = $tempMemberid;
		}
		if ($memberId) {
			$model->memberid = $memberId;
		}
		$model->titleid = trim($reqDetails['dependantTitleId']);
		$model->dependantname = trim($reqDetails['dependantName']);
		$model->dob =  !empty($reqDetails['dependantdob']) ? date(yii::$app->params['dateFormat']['sqlDandTFormat'], strtotimeNew($reqDetails['dependantdob'])) : '';
		$model->relation = trim($reqDetails['relation']);
		$model->dependantid = $reqDetails['dependantId'] ? $reqDetails['dependantId'] : '';
		$model->weddinganniversary =  !empty($reqDetails['dependantweddingdate']) ? date(yii::$app->params['dateFormat']['sqlDandTFormat'], strtotimeNew($reqDetails['dependantweddingdate'])) : '';

		$model->ismarried = trim($reqDetails['dependantMartialStatus']) ? trim($reqDetails['dependantMartialStatus']) : 0;
		$model->image = $images['orginal'];
		$model->thumbnailimage = $images['thumbnail'];
		$model->dependantmobilecountrycode = $reqDetails['dependantMobileCountryCode'] ?? NULL;
		$model->dependantmobile = $reqDetails['dependantMobile'] ?? NULL;
		$model->occupation = $reqDetails['dependantOccupation'] ?? '';
		$model->active = isset($reqDetails['dependantActive']) ? (int)$reqDetails['dependantActive'] : 0;
		$model->confirmed = isset($reqDetails['dependantConfirmed']) ? (int)$reqDetails['dependantConfirmed'] : 0;
		if ($model->save()) {
			return $model;
		} else {
			return false;
		}
	}

	/*
   *get the depebnding ids 
   */
	protected function getDepentantIds($dependantDetails)
	{

		$dependantIds = '';
		if (!empty($dependantDetails)) {
			foreach ($dependantDetails as $data) {
				$dependantIds .= $data['id'] . ',';
			}
			$dependantIds = rtrim($dependantIds, ',');
		}
		return $dependantIds;
	}
	/*
    * to conver the date into sql formate
    */
	protected function sqlDateConversion($date)
	{
		return  !empty($date) ? date(yii::$app->params['dateFormat']['sqlDandTFormat'], strtotimeNew($date)) : '';
	}

	/**
	 * To store the used edit dependant details
	 * @param unknown $postdetails
	 * @return boolean
	 */
	protected function addTempDependantMember($postdetails, $tempDependantImages)
	{

		$model =  new ExtendedTempdependantmail();
		$dependantModel =  new ExtendedDependant();
		if ($postdetails) {
			if ($postdetails['ExtendedMember']['memberid']) {
				$memberId = $postdetails['ExtendedMember']['memberid'];
				$dependantModel->getDependants($memberId);
				if (isset($postdetails['depentantIdslist'])) {
					$tempdepndantIds = explode(',', $postdetails['depentantIdslist']);
					$bachInsert = [];
					$fields     = ['tempmemberid', 'dependantid', 'titleid', 'dependantname', 'dependantmobilecountrycode', 'dependantmobile', 'dob', 'relation', 'weddinganniversary', 'spousedependantid', 'ismarried', 'isapproved', 'tempimage', 'tempimagethumbnail'];
					foreach ($tempdepndantIds as $tempdepndantId) {
						$tempImage = '';
						$tempImageThumb = '';
						$imageType = 'member';
						if (!empty($tempDependantImages['dependantfile_' . $tempdepndantId]['name']) && !$tempDependantImages['dependantfile_' . $tempdepndantId]['error']) {
							$dependantImage = $tempDependantImages['dependantfile_' . $tempdepndantId];
							$targetPath = Yii::$app->params['image']['member']['main'] . '/' . Yii::$app->params['image']['member']['dependantImage'];
							$thumbnail = Yii::$app->params['image']['member']['main'] . '/' . Yii::$app->params['image']['member']['thumbnailDepentant'];
							$dependantImages = $this->fileUpload($dependantImage, $targetPath, $thumbnail, $imageType, true);
							$tempImage = $dependantImages['orginal'];
							$tempImageThumb = $dependantImages['thumbnail'];
						} else {
							$dependantModelImage = ExtendedDependant::findOne(['id' => $tempdepndantId]);
							if ($dependantModelImage != null) {
								if ($postdetails["dependantpic_" . $tempdepndantId] == "removed") {
									$tempImage = '';
									$tempImageThumb = '';
								} else {
									$tempImage = $dependantModelImage->image;
									$tempImageThumb = $dependantModelImage->thumbnailimage;
								}
							}
						}

						$bachInsert[] = [
							$memberId,
							$tempdepndantId,
							$postdetails["dependanttitle_" . $tempdepndantId],
							$postdetails["dependantname_" . $tempdepndantId],
							$postdetails["dependantmobilecountrycode_" . $tempdepndantId],
							$postdetails["dependantmobile_" . $tempdepndantId],
							$this->sqlDateConversion($postdetails["dependantdob_" . $tempdepndantId]),
							$postdetails["dependantrelation_" . $tempdepndantId],
							$this->sqlDateConversion($postdetails["tempDependantwdate_" . $tempdepndantId]),
							null,
							$postdetails["dependantmartialstatus_" . $tempdepndantId],
							0,
							$tempImage,
							$tempImageThumb
						];
						if ($postdetails["dependantmartialstatus_" . $tempdepndantId] == 1 || $postdetails["dependantmartialstatus_" . $tempdepndantId] == '') {
							continue;
						}
						if (!empty($postdetails["spousedependantid_" . $tempdepndantId])) {
							$tempImage = '';
							$tempImageThumb = '';
							if (!empty($tempDependantImages['dependantspousefile_' . $tempdepndantId]['name'])) {
								$spouseImage = $tempDependantImages['dependantspousefile_' . $tempdepndantId];
								$targetPath = Yii::$app->params['image']['member']['main'] . '/' . Yii::$app->params['image']['member']['spouseImage'];
								$thumbnail = Yii::$app->params['image']['member']['main'] . '/' . Yii::$app->params['image']['member']['spousethumbnailImage'];
								$spouseImages = $this->fileUpload($spouseImage, $targetPath, $thumbnail, $imageType, true);
								$tempImage = $spouseImages['orginal'];
								$tempImageThumb = $spouseImages['thumbnail'];
							} else {
								$dependantSpouseModelImage = ExtendedDependant::findOne(['id' => $postdetails["spousedependantid_" . $tempdepndantId]]);
								if ($dependantSpouseModelImage != null) {
									if ($postdetails["dependantspousepic_" . $tempdepndantId] == "removed") {
										$tempImage = '';
										$tempImageThumb = '';
									} else {
										$tempImage = $dependantSpouseModelImage->image;
										$tempImageThumb = $dependantSpouseModelImage->thumbnailimage;
									}
								}
							}
							$bachInsert[] = [
								$memberId,
								$postdetails["spousedependantid_" . $tempdepndantId],
								$postdetails["tempdependantspousetitleid_" . $tempdepndantId],
								$postdetails["tempDependantSpousName_" . $tempdepndantId],
								$postdetails["tempDependantSpouseMobileCountryCode_" . $tempdepndantId],
								$postdetails["tempDependantSpouseMobile_" . $tempdepndantId],
								$this->sqlDateConversion($postdetails["tempDependantSpouseDob_" . $tempdepndantId]),
								null,
								$this->sqlDateConversion($postdetails["tempDependantwdate_" . $tempdepndantId]),
								$tempdepndantId,
								$postdetails["dependantmartialstatus_" . $tempdepndantId],
								0,
								$tempImage,
								$tempImageThumb
							];
						} else {

							$tempImage = '';
							$tempImageThumb = '';
							if (!empty($tempDependantImages['dependantspousefile_' . $tempdepndantId]['name'])) {
								$spouseImage = $tempDependantImages['dependantspousefile_' . $tempdepndantId];
								$targetPath = Yii::$app->params['image']['member']['main'] . '/' . Yii::$app->params['image']['member']['spouseImage'];
								$thumbnail = Yii::$app->params['image']['member']['main'] . '/' . Yii::$app->params['image']['member']['spousethumbnailImage'];
								$spouseImages = $this->fileUpload($spouseImage, $targetPath, $thumbnail, $imageType, true);
								$tempImage = $spouseImages['orginal'];
								$tempImageThumb = $spouseImages['thumbnail'];
							} else {
								$dependantSpouseModelImage = ExtendedDependant::findOne(['id' => $postdetails["spousedependantid_" . $tempdepndantId]]);
								if ($dependantSpouseModelImage != null) {
									if ($postdetails["dependantspousepic_" . $tempdepndantId] == "removed") {
										$tempImage = '';
										$tempImageThumb = '';
									} else {
										$tempImage = $dependantSpouseModelImage->image;
										$tempImageThumb = $dependantSpouseModelImage->thumbnailimage;
									}
								}
							}
							$bachInsert[] = [
								$memberId,
								null,
								$postdetails["tempdependantspousetitleid_" . $tempdepndantId],
								$postdetails["tempDependantSpousName_" . $tempdepndantId],
								$postdetails["tempDependantSpouseMobileCountryCode_" . $tempdepndantId],
								$postdetails["tempDependantSpouseMobile_" . $tempdepndantId],
								$this->sqlDateConversion($postdetails["tempDependantSpouseDob_" . $tempdepndantId]),
								null,
								$this->sqlDateConversion($postdetails["tempDependantwdate_" . $tempdepndantId]),
								$tempdepndantId,
								$postdetails["dependantmartialstatus_" . $tempdepndantId],
								0,
								$tempImage,
								$tempImageThumb
							];
						}
					}
					if (count($bachInsert) > 0) {
						$flag = Yii::$app->db->createCommand()->batchInsert('tempdependant', $fields, $bachInsert)->execute();
						$flag &= Yii::$app->db->createCommand()->batchInsert('tempdependantmail', $fields, $bachInsert)->execute();
						if ($flag) {
							return true;
						} else {
							return false;
						}
					}
				}
				return true;
			}
			return false;
		}
		return false;
	}

	/**
	 * to store the approval details
	 * @param unknown $dependantDetails
	 * @param unknown $tempDepentantDetails
	 * @param unknown $dependantList
	 * @param unknown $memberId
	 * @return boolean|multitype:boolean multitype:multitype:boolean unknown
	 */

	protected function storeApprovalDependantMember($dependantDetails, $tempDepentantDetails, $dependantList, $memberId)
	{
		$isApproved = [];
		$allAccept = 0;
		$allReject = 0;
		$total = 0;
		if (!empty($dependantList)) {
			$titleModel      = new ExtendedTitle();
			if (is_array($tempDepentantDetails)) {
				$tempDepentantDetails = ArrayHelper::index($tempDepentantDetails, null, 'dependantid');
			}

			$dependantDetails = ArrayHelper::index($dependantDetails, null, 'id');
			$institusionId = $this->currentUser()->institutionid;
			$titles = CacheHelper::getTitles($institusionId, function() use ($titleModel, $institusionId) {
				return $titleModel->getActiveTitles($institusionId);
			});
			$titlesArray = [];
			if (!empty($titles)) {
				$titlesArray =	ArrayHelper::map($titles, 'TitleId', 'Description');
			}
			foreach ($dependantList as $dependant) {
				$modal = new ExtendedTempdependant();
				if (isset($dependant['DependantId'])) {
					$dependantId = $dependant['DependantId'];
					if (isset($dependantDetails[$dependantId])) {
						if (isset($tempDepentantDetails[$dependantId])) {
							$tempDepentantDetail = $tempDepentantDetails[$dependantId];
							$tempDepentantDetail = $tempDepentantDetail[0];
						} else {
							$tempDepentantDetail = [
								'dependantid' => $dependantId,
								'tempmemberid' => "",
								'dependanttitleid' => "",
								'dependanttitle' => "",
								'dependantname' => "",
								'dependantmobilecountrycode' => "",
								'dependantmobile' => "",
								'relation' => "",
								'dob' => "",
								'ismarried' => "",
								'weddinganniversary' => "",
								'DependantSpouseId' => "",
								'spousedependantid' => "",
								'spousetitleid' => "",
								'spousetitle' => "",
								'spousename' => "",
								'dependantspousemobilecountrycode' => "",
								'dependantspousemobile' => "",
								'spousedob' => "",
								'tempimage' => "",
								'tempdependantspouseimage' => "",
								'tempimagethumbnail' => "",
								'tempdependantspouseimagethumbnail' => ""
							];
						}
						$dependantDetail     = $dependantDetails[$dependantId];
						$dependantDetail     = $dependantDetail[0];
						if (($modal = ExtendedDependant::find()->where(['id' => $dependantId])->one()) !== null) {
							if ($this->isDiffer($tempDepentantDetail['dependanttitleid'], $dependantDetail['dependanttitleid'])) {
								$total++;
								if ($this->isDiffer($dependant['DependantTitle'], $tempDepentantDetail['dependanttitle'])) {
									$isApproved['dependanttitle_' . $dependantId] = ['isApproved' => false, 'value' => $tempDepentantDetail['dependanttitle']];
									$allReject++;
								} else {
									$modal->titleid = $tempDepentantDetail['dependanttitleid'];
									$isApproved['dependanttitle_' . $dependantId] = ['isApproved' => true, 'value' => $tempDepentantDetail['dependanttitle']];
									$allAccept++;
								}
							} else {
								$total++;
								$allAccept++;
								$isApproved['dependanttitle_' . $dependantId] = ['isApproved' => true, 'value' => $tempDepentantDetail['dependanttitle']];
							}
							$dependantPic =  $this->getCorrectPic($dependant['Image']);
							if ($this->isDiffer($tempDepentantDetail['tempimage'], $dependantDetail['dependantimage'])) {
								$total++;
								if ($this->isDiffer($dependantPic, $tempDepentantDetail['tempimage'])) {
									$isApproved['dependantPic_' . $dependantId] = ['isApproved' => false, 'value' => $tempDepentantDetail['tempimage']];
									$allReject++;
								} else {
									$modal->image   = $tempDepentantDetail['tempimage'];
									$modal->thumbnailimage   = $tempDepentantDetail['tempimagethumbnail'];
									$isApproved['dependantPic_' . $dependantId] = ['isApproved' => true, 'value' => $tempDepentantDetail['tempimage']];
									$allAccept++;
								}
							} else {
								$total++;
								$allAccept++;
								$isApproved['dependantPic_' . $dependantId] = ['isApproved' => true, 'value' => $tempDepentantDetail['tempimage']];
							}
							if ($this->isDiffer($tempDepentantDetail['dependantname'], $dependantDetail['dependantname'])) {
								$total++;
								if ($this->isDiffer($dependant['DependantName'], $tempDepentantDetail['dependantname'])) {
									$isApproved['dependantname_' . $dependantId] = ['isApproved' => false, 'value' => $tempDepentantDetail['dependantname']];
									$allReject++;
								} else {
									$modal->dependantname = $tempDepentantDetail['dependantname'];
									$isApproved['dependantname_' . $dependantId] = ['isApproved' => true, 'value' => $tempDepentantDetail['dependantname']];
									$allAccept++;
								}
							} else {
								$total++;
								$allAccept++;
								$isApproved['dependantname_' . $dependantId] = ['isApproved' => true, 'value' => $tempDepentantDetail['dependantname']];
							}
							if ($this->isDiffer($tempDepentantDetail['dependantmobilecountrycode'], $dependantDetail['dependantmobilecountrycode'])) {
								$total++;
								if ($this->isDiffer($dependant['DependantMobileCountryCode'], $tempDepentantDetail['dependantmobilecountrycode'])) {
									$isApproved['dependantmobilecountrycode_' . $dependantId] = ['isApproved' => false, 'value' => $tempDepentantDetail['dependantmobilecountrycode']];
									$allReject++;
								} else {
									$modal->dependantmobilecountrycode = $tempDepentantDetail['dependantmobilecountrycode'];
									$isApproved['dependantmobilecountrycode_' . $dependantId] = ['isApproved' => true, 'value' => $tempDepentantDetail['dependantmobilecountrycode']];
									$allAccept++;
								}
							} else {
								$total++;
								$allAccept++;
								$isApproved['dependantmobilecountrycode_' . $dependantId] = ['isApproved' => true, 'value' => $tempDepentantDetail['dependantmobilecountrycode']];
							}
							if ($this->isDiffer($tempDepentantDetail['dependantmobile'], $dependantDetail['dependantmobile'])) {
								$total++;
								if ($this->isDiffer($dependant['DependantMobile'], $tempDepentantDetail['dependantmobile'])) {
									$isApproved['dependantmobile_' . $dependantId] = ['isApproved' => false, 'value' => $tempDepentantDetail['dependantmobile']];
									$allReject++;
								} else {
									$modal->dependantmobile = $tempDepentantDetail['dependantmobile'];
									$isApproved['dependantmobile_' . $dependantId] = ['isApproved' => true, 'value' => $tempDepentantDetail['dependantmobile']];
									$allAccept++;
								}
							} else {
								$total++;
								$allAccept++;
								$isApproved['dependantmobile_' . $dependantId] = ['isApproved' => true, 'value' => $tempDepentantDetail['dependantmobile']];
							}

							if ($this->isDiffer($tempDepentantDetail['relation'], $dependantDetail['relation'])) {
								$total++;
								if ($this->isDiffer($dependant['Relation'], $tempDepentantDetail['relation'])) {
									$isApproved['relation_' . $dependantId] = ['isApproved' => false, 'value' => $tempDepentantDetail['relation']];
									$allReject++;
								} else {
									$modal->relation = $tempDepentantDetail['relation'];
									$isApproved['relation_' . $dependantId] = ['isApproved' => true, 'value' => $tempDepentantDetail['relation']];
									$allAccept++;
								}
							} else {
								$total++;
								$allAccept++;
								$isApproved['relation_' . $dependantId] = ['isApproved' => true, 'value' => $tempDepentantDetail['relation']];
							}
							if ($this->isDiffer($tempDepentantDetail['dob'], $dependantDetail['dob'])) {
								$total++;
								$dependantDate = $this->sqlDateConversion($dependant['Dob']);
								if ($this->isDiffer($dependantDate, $tempDepentantDetail['dob'])) {
									$isApproved['dob_' . $dependantId] = ['isApproved' => false, 'value' => $tempDepentantDetail['dob']];
									$allReject++;
								} else {
									$modal->dob = !empty($tempDepentantDetail['dob']) ? date('Y-m-d h:i:s', strtotimeNew($tempDepentantDetail['dob'])) : null;
									$isApproved['dob_' . $dependantId] = ['isApproved' => true, 'value' => $tempDepentantDetail['dob']];
									$allAccept++;
								}
							} else {
								$total++;
								$allAccept++;
								$isApproved['dob_' . $dependantId] = ['isApproved' => true, 'value' => $tempDepentantDetail['dob']];
							}
							$isMarried = ['' => 0, 'Single' => '1', 'Married' => "2"];
							$spouseModal = null;
							if ($this->isDiffer($tempDepentantDetail['ismarried'], $dependantDetail['ismarried'])) {
								$total++;
								if ($this->isDiffer($isMarried[$dependant['IsMarried']], $tempDepentantDetail['ismarried'])) {
									$isApproved['ismarried_' . $dependantId] = ['isApproved' => false, 'value' => $dependant['IsMarried']];
									if ($dependant['IsMarried'] == 'Married') {
										if (($spouseModal = ExtendedDependant::find()->where(['dependantid' => $dependantId])->one()) !== null) {
										} else {
											$spouseModal = new ExtendedDependant();
											$spouseModal->dependantid = $dependantId;
											$spouseModal->memberid = $memberId;
										}
									}
									$allReject++;
								} else {
									if ($dependant['IsMarried'] == 'Married') {
										if (($spouseModal = ExtendedDependant::find()->where(['dependantid' => $dependantId])->one()) !== null) {
										} else {
											$spouseModal = new ExtendedDependant();
											$spouseModal->dependantid = $dependantId;
											$spouseModal->memberid = $memberId;
											$spouseModal->ismarried = $tempDepentantDetail['ismarried'];
										}
									} elseif ($dependant['IsMarried'] == 'Single') {
										if (($spouseModal = ExtendedDependant::find()->where(['dependantid' => $dependantId])->one()) !== null) {
											$this->deleteDepentant($spouseModal);
											$spouseModal = null;
										}
									}
									$modal->ismarried = $tempDepentantDetail['ismarried'];
									$modal->relation = $tempDepentantDetail['relation'];
									$isApproved['ismarried_' . $dependantId] = ['isApproved' => true, 'value' => $dependant['IsMarried']];
									$allAccept++;
								}
							} else {
								if ($dependant['IsMarried'] == 'Married') {
									if (($spouseModal = ExtendedDependant::find()->where(['dependantid' => $dependantId])->one()) !== null) {
									} else {
										$spouseModal = new ExtendedDependant();
										$spouseModal->dependantid = $dependantId;
										$spouseModal->memberid = $memberId;
										$spouseModal->ismarried = $tempDepentantDetail['ismarried'];
									}
								} elseif ($dependant['IsMarried'] == 'Single') {
									if (($spouseModal = ExtendedDependant::find()->where(['dependantid' => $dependantId])->one()) !== null) {
										$this->deleteDepentant($spouseModal);
										$spouseModal = null;
									}
								}
								$total++;
								$allAccept++;
								$isApproved['ismarried_' . $dependantId] = ['isApproved' => true, 'value' => $dependant['IsMarried']];
							}
							if ($spouseModal) {
								if ($this->isDiffer($tempDepentantDetail['spousetitleid'], $dependantDetail['spousetitleid'])) {
									$total++;
									if ($this->isDiffer($dependant['SpouseTitle'], $tempDepentantDetail['spousetitle'])) {
										$isApproved['spousetitle_' . $dependantId] = ['isApproved' => false, 'value' => $tempDepentantDetail['spousetitle']];
										$allReject++;
									} else {
										$spouseModal->titleid = $tempDepentantDetail['spousetitleid'];
										$isApproved['spousetitle_' . $dependantId] = ['isApproved' => true, 'value' => $tempDepentantDetail['spousetitle']];
										$allAccept++;
									}
								} else {
									$total++;
									$allAccept++;
									$isApproved['spousetitle_' . $dependantId] = ['isApproved' => true, 'value' => $tempDepentantDetail['spousetitle']];
								}
								$dependantSpousePic =  $this->getCorrectPic($dependant['SpouseImage']);

								if ($this->isDiffer($tempDepentantDetail['tempdependantspouseimage'], $dependantDetail['dependantspouseimage'])) {
									$total++;
									if ($this->isDiffer($dependantSpousePic, $tempDepentantDetail['tempdependantspouseimage'])) {

										$isApproved['dependantSpousePic_' . $dependantId] = ['isApproved' => false, 'value' => $tempDepentantDetail['tempdependantspouseimage']];
										$allReject++;
									} else {

										$spouseModal->image   = $tempDepentantDetail['tempdependantspouseimage'];
										$spouseModal->thumbnailimage   = $tempDepentantDetail['tempdependantspouseimagethumbnail'];
										$isApproved['dependantSpousePic_' . $dependantId] = ['isApproved' => true, 'value' => $tempDepentantDetail['tempdependantspouseimage']];
										$allAccept++;
									}
								} else {

									$total++;
									$allAccept++;
									$isApproved['dependantSpousePic_' . $dependantId] = ['isApproved' => true, 'value' => $tempDepentantDetail['tempdependantspouseimage']];
								}

								if ($this->isDiffer($tempDepentantDetail['spousename'], $dependantDetail['spousename'])) {
									$total++;
									if ($this->isDiffer($dependant['DependantSpouseName'], $tempDepentantDetail['spousename'])) {
										$isApproved['spousename_' . $dependantId] = ['isApproved' => false, 'value' => $tempDepentantDetail['spousename']];
										$allReject++;
									} else {
										$spouseModal->dependantname = $tempDepentantDetail['spousename'];
										$isApproved['spousename_' . $dependantId] = ['isApproved' => true, 'value' => $tempDepentantDetail['spousename']];
										$allAccept++;
									}
								} else {
									$total++;
									$allAccept++;
									$isApproved['spousename_' . $dependantId] = ['isApproved' => true, 'value' => $tempDepentantDetail['spousename']];
								}
								if ($this->isDiffer($tempDepentantDetail['dependantspousemobilecountrycode'], $dependantDetail['dependantspousemobilecountrycode'])) {
									$total++;
									if ($this->isDiffer($dependant['DependantSpouseMobileCountryCode'], $tempDepentantDetail['dependantspousemobilecountrycode'])) {
										$isApproved['dependantspousemobilecountrycode_' . $dependantId] = ['isApproved' => false, 'value' => $tempDepentantDetail['dependantspousemobilecountrycode']];
										$allReject++;
									} else {
										$spouseModal->dependantmobilecountrycode = $tempDepentantDetail['dependantspousemobilecountrycode'];
										$isApproved['dependantspousemobilecountrycode_' . $dependantId] = ['isApproved' => true, 'value' => $tempDepentantDetail['dependantspousemobilecountrycode']];
										$allAccept++;
									}
								} else {
									$total++;
									$allAccept++;
									$isApproved['dependantspousemobilecountrycode_' . $dependantId] = ['isApproved' => true, 'value' => $tempDepentantDetail['dependantspousemobilecountrycode']];
								}
								if ($this->isDiffer($tempDepentantDetail['dependantspousemobile'], $dependantDetail['dependantspousemobile'])) {
									$total++;
									if ($this->isDiffer($dependant['DependantSpouseMobile'], $tempDepentantDetail['dependantspousemobile'])) {
										$isApproved['dependantspousemobile_' . $dependantId] = ['isApproved' => false, 'value' => $tempDepentantDetail['dependantspousemobile']];
										$allReject++;
									} else {
										$spouseModal->dependantmobile = $tempDepentantDetail['dependantspousemobile'];
										$isApproved['dependantspousemobile_' . $dependantId] = ['isApproved' => true, 'value' => $tempDepentantDetail['dependantspousemobile']];
										$allAccept++;
									}
								} else {
									$total++;
									$allAccept++;
									$isApproved['dependantspousemobile_' . $dependantId] = ['isApproved' => true, 'value' => $tempDepentantDetail['dependantspousemobile']];
								}
								if ($this->isDiffer($tempDepentantDetail['spousedob'], $dependantDetail['spousedob'])) {
									$total++;
									$dependantDate = $this->sqlDateConversion($dependant['DependantSpouseDOB']);
									if ($this->isDiffer($dependantDate, $tempDepentantDetail['spousedob'])) {
										$isApproved['spousedob_' . $dependantId] = ['isApproved' => false, 'value' => $tempDepentantDetail['spousedob']];
										$allReject++;
									} else {
										$spouseModal->dob = !empty($tempDepentantDetail['spousedob']) ? date('Y-m-d h:i:s', strtotimeNew($tempDepentantDetail['spousedob'])) : null;
										$isApproved['spousedob_' . $dependantId] = ['isApproved' => true, 'value' => $tempDepentantDetail['spousedob']];
										$allAccept++;
									}
								} else {
									$total++;
									$allAccept++;
									$isApproved['spousedob_' . $dependantId] = ['isApproved' => true, 'value' => $tempDepentantDetail['spousedob']];
								}
								if ($this->isDiffer($tempDepentantDetail['weddinganniversary'], $dependantDetail['weddinganniversary'])) {
									$total++;
									$dependantDate = $this->sqlDateConversion($dependant['WeddingAnniversary']);
									if ($this->isDiffer($dependantDate, $tempDepentantDetail['spousedob'])) {
										$isApproved['weddinganniversary_' . $dependantId] = ['isApproved' => false, 'value' => $tempDepentantDetail['weddinganniversary']];
										$allReject++;
									} else {
										$modal->weddinganniversary = !empty($tempDepentantDetail['weddinganniversary']) ? date('Y-m-d h:i:s', strtotimeNew($tempDepentantDetail['weddinganniversary'])) : null;
										$isApproved['weddinganniversary_' . $dependantId] = ['isApproved' => true, 'value' => $tempDepentantDetail['weddinganniversary']];
										$allAccept++;
									}
								} else {
									$total++;
									$allAccept++;
									$isApproved['weddinganniversary_' . $dependantId] = ['isApproved' => true, 'value' => $tempDepentantDetail['weddinganniversary']];
								}
								if (!$spouseModal->save()) {
									yii::error(print_r($spouseModal->getErrors(), true));
									return false;
								}
								if ($spouseModal->dependantname == '' || $spouseModal->dependantname == null) {
									$spouseModal->delete();
								}
							}
							if (!$modal->save()) {
								yii::error(print_r($modal->getErrors(), true));
								return false;
							}
							if ($modal->dependantname == '' || $modal->dependantname == null) {
								$modal->delete();
							}
						}
					}
				}
			}
			return array('isApproved' => $isApproved, 'allAccept' => $allAccept, 'allReject' => $allReject);
		}
	}

	/**
	 * To remove server image path
	 * @param string $data
	 * @return Ambigous <>|NULL
	 */
	protected function getCorrectPic(string $data)
	{
		if (!empty($data)) {
			$pic = explode(Yii::$app->params['imagePath'], $data);
			if (isset($pic[1])) {
				return trim($pic[1]);
			} else {
				return null;
			}
		}
		return null;
	}
	/**
	 * To set the images in approval object 
	 * @param unknown $approvalMemberModal
	 * @param unknown $memberDetails
	 */
	protected function toSetImagesApproval($approvalMemberModal, $memberDetails, $memberModal, $tempMemberModal)
	{

		$approvalMemberModal->member_pic 			= '';
		$approvalMemberModal->spouse_pic 			= '';
		$approvalMemberModal->memberImageThumbnail 	= '';
		$approvalMemberModal->spouseImageThumbnail 	= '';
		if (isset($memberDetails['memberpic'])) {
			$memberPic = $this->getCorrectPic($memberDetails['memberpic']);
			$approvalMemberModal->member_pic 			= $memberPic;
			if ($memberPic == $memberModal->member_pic) {
				$approvalMemberModal->memberImageThumbnail 	= $memberModal->memberImageThumbnail;
			} elseif ($memberPic == $tempMemberModal->temp_member_pic) {
				$approvalMemberModal->memberImageThumbnail = $tempMemberModal->temp_memberImageThumbnail;
			}
		}
		if (isset($memberDetails['spousepic'])) {
			$spousepic =  $this->getCorrectPic($memberDetails['spousepic']);
			$approvalMemberModal->spouse_pic = $spousepic;
			if ($spousepic == $memberModal->spouse_pic) {
				$approvalMemberModal->spouseImageThumbnail 	= $memberModal->spouseImageThumbnail;
			} elseif ($spousepic == $tempMemberModal->temp_spouse_pic) {
				$approvalMemberModal->spouseImageThumbnail = $tempMemberModal->temp_spouseImageThumbnail;
			}
		}
		return $approvalMemberModal;
	}

	/**
	 * To sent the approval details to the member
	 * @param unknown $depentantResponse
	 * @param unknown $memberResponse
	 * @param unknown $model
	 * @param unknown $depentandList
	 * @return string
	 */
	protected function sentApprovalEmailNotification($depentantResponse, $memberResponse, $model, $depentandList)
	{

		$mailContent = [];
		$from = yii::$app->params['re-memberEmail'];
		$to = $model->member_email;
		$institutionName =   $this->currentUser()->institution->name;
		$subject = "Your request for updation of membership data - " . $institutionName;
		$title = '';
		$emailModal = 	new EmailHandlerComponent();
		$mailContent['template'] = 'pending-approvel';
		$srTitle      =  $model->membertitle0['Description'] ? $model->membertitle0['Description'] : '';
		$firstName  =  $model->firstName ? $model->firstName : ' ';
		$middleName =  $model->middleName ? $model->middleName : ' ';
		$lastName   =  $model->lastName ? $model->lastName : '';
		$displayName = $srTitle . ' ' . $firstName . ' ' . $middleName . ' ' . $lastName;

		if (!empty($memberResponse['member_dob']['value'])) {
			$memberResponse['member_dob']['value'] = date('d-F-Y', strtotimeNew($memberResponse['member_dob']['value']));
		}
		if (!empty($memberResponse['location']['value'])) {
			$data = $memberResponse['location']['value'];
			$latitude = $data['latitude'] ?? '';
			$longitude = $data['longitude'] ?? '';
			$location = "latitide :  $latitude longitude : $longitude";
			$memberResponse['location']['value'] = $location;
		}
		if (!empty($memberResponse['spouse_dob']['value'])) {
			$memberResponse['spouse_dob']['value'] = date('d-F-Y', strtotimeNew($memberResponse['spouse_dob']['value']));
		}
		if (!empty($memberResponse['dom']['value'])) {
			$memberResponse['dom']['value'] = date('d-F-Y', strtotimeNew($memberResponse['dom']['value']));
		}
		if (!empty($memberResponse['member_pic']['value'])) {
			$memberResponse['member_pic']['value'] =  Yii::$app->params['imagePath'] . $memberResponse['member_pic']['value'];
		} else {
			$memberResponse['member_pic']['value'] = Yii::$app->params['imagePath'] . '/Member/default-user.png';
		}
		if (!empty($memberResponse['spouse_pic']['value'])) {
			$memberResponse['spouse_pic']['value'] =  Yii::$app->params['imagePath'] . $memberResponse['spouse_pic']['value'];
		} else {
			$memberResponse['spouse_pic']['value'] = Yii::$app->params['imagePath'] . '/Member/default-user.png';
		}
		if (!empty($memberResponse['memberImageThumbnail']['value'])) {
			$memberResponse['memberImageThumbnail']['value'] =  Yii::$app->params['imagePath'] . $memberResponse['memberImageThumbnail']['value'];
		} else {
			$memberResponse['memberImageThumbnail']['value'] = Yii::$app->params['imagePath'] . '/Member/default-user.png';
		}
		if (!empty($memberResponse['spouseImageThumbnail']['value'])) {
			$memberResponse['spouseImageThumbnail']['value'] =  Yii::$app->params['imagePath'] . $memberResponse['spouseImageThumbnail']['value'];
		} else {
			$memberResponse['spouseImageThumbnail']['value'] = Yii::$app->params['imagePath'] . '/Member/default-user.png';
		}

		$mailContent['name'] = $displayName;
		$mailContent['approvelDependantDetails'] = $depentantResponse;
		$mailContent['approvelMemberDetails'] = $memberResponse;
		$mailContent['approvelDepentandList'] = $depentandList;
		$mailContent['institutionname'] = $institutionName;

		//Institution logo
		if (!empty($this->currentUser()->institution->institutionlogo)) {
			$logo = Yii::$app->params['imagePath'] . $this->currentUser()->institution->institutionlogo;
		} else {
			$logo = Yii::$app->params['imagePath'] . '/institution/institution-icon-grey.png';
		}
		$mailContent['logo'] = $logo;
		$attach = array();
		$temp = $emailModal->sendEmail($from, $to, $title, $subject, $mailContent, $attach);
		if ($temp) {
			return 'success';
		} else {
			return 'Error';
		}
	}

	/**
	 * 
	 */
	protected function toSendMsg($toadmin, $toName, $toEmailId, $fromName, $content, $institutionLogo, $institutionName, $memberId, $memberNo)
	{
		$mailContent = [];
		$from = yii::$app->params['re-memberEmail'];
		if ($toadmin == "ADMIN") {
			$subject  = "Request for updating membership data from " . $toName;
			$mailContent['template'] = 'profile-request-mail';
			$link = Yii::$app->urlManager->createAbsoluteUrl(
				[
					'member/member-approvel',
					'id' => $memberId
				]
			);
			$mailContent['updateLink'] = $link;
		} else {
			$subject = "Your request for updation of membership data - " . $institutionName;
			$mailContent['template'] = 'email-message';
		}

		$title = '';
		$emailModal = 	new EmailHandlerComponent();
		$mailContent['content'] = $content;
		$mailContent['name'] = $toName;
		$mailContent['toname'] = $toadmin;
		$mailContent['logo'] = !empty($institutionLogo) ? $institutionLogo : '';
		$mailContent['memberno'] = $memberNo;

		$attach = '';

		$temp = $emailModal->sendEmail($from, $toEmailId, $title, $subject, $mailContent, $attach);
		if ($temp) {
			return 'success';
		} else {
			return 'Error';
		}
	}

	/*  protected function profileUpdateNotification($model){

  	$pushNotificationSender = Yii::$app->PushNotificationHandler;
  	
  	$memberId = $model->memberid;
  	$profileupdateNotificationModel = new ExtendedProfileupdatenotification();
  	$profileupdateNotificationsentModel = new ExtendedProfileupdatenotificationsent;
  	$deviceDetailsModel = new ExtendedDevicedetails();
  	
  	//deleting from notification tables
  	$profileupdateNotificationModel->deleteFromProfileNotification($memberId);
  	$profileupdateNotificationsentModel->deleteFromProfileNotificationSent($memberId);
  	
  	$institutionId = $model->institutionid;
  	$prayerrequestPrivilegeId= ExtendedPrivilege::APPROVE_PENDING_MEMBER;
  	$deviceDetails = $deviceDetailsModel->getDeviceDetails($institutionId, $prayerrequestPrivilegeId);
  	
  	$fields     = ['userid','memberid','createddatetime'];
  	$bachInsert = [];
  	$notificationSent =[];
  	foreach ($deviceDetails as $deviceDetail){
  		$userId =		   	$deviceDetail[ 'id'];
	  	$deviceid 	= 	$deviceDetail['deviceid'];
	    $devicetype =   $deviceDetail['devicetype'];
	  	//$memberId	=   $deviceDetail['memberid'];
	  	$usertype	=  $deviceDetail['membertype'];
	  	$institutionId = $deviceDetail['institutionid'];
	  	$institutionName =	$deviceDetail ['institutionname'];
	  	$membernotification = $deviceDetail['membernotification'];
	  	$spousenotification = $deviceDetail['spousenotification'];
	  	
	  	
	  	$sentMemberId = null;
	  	if (strtolower($usertype) == "m" && $membernotification == 1)
	  	{
	  		$sentMemberId = $deviceid;
	  	}
	  	if (strtolower($usertype) == "s" && $spousenotification == 1)
	  	{
	  		$sentMemberId = $deviceid;
	  	}
	  	if ($sentMemberId){
	  	$bachInsert[] = [$userId,$memberId,gmdate('Y-m-d H:i:s')];
	  	
	  	$firstName  =  $model->firstName ? $model->firstName:' ';
	  	$middleName =  $model->middleName ? $model->middleName:' ';
	  	$lastName   =  $model->lastName ? $model->lastName :'';
	  	$memberName = $firstName . ' '.$middleName. ' '. $lastName;
	  	$message    = $memberName + " requested for a profile update.";
	  	
	  	$notificationType = 'profile-approval';
	  	$requestData  = $pushNotificationSender->setPushNotificationRequest($sentMemberId,$message,$notificationType,$institutionId,$memberId,$institutionName,strtolower($devices['devicetype']));
	  	$response     = $pushNotificationSender->sendNotification(strtolower($devices['devicetype']), $registrationid, $requestData);
	  	
	  	if($response){
	  		
	  		$notificationSent[] = [$userId,$memberId,gmdate('Y-m-d H:i:s')];
	  	}
	  	}
  	}
  	if (count($bachInsert)>0){
  		 
  		Yii::$app->db->createCommand()->batchInsert('profileupdatenotification', $fields,$bachInsert )->execute();

  	}
  	
  	if (count($notificationSent)>0){
  			
  		Yii::$app->db->createCommand()->batchInsert('profileupdatenotification', $fields,$notificationSent )->execute();
  	
  	}
  	
  } */
	protected function getMemberRoleGroupID($type)
	{
		try {
			return  Yii::$app->db->createCommand(
				'
            SELECT RoleGroupID FROM rolegroup where description = :type'
			)->bindValue(':type', $type)->queryScalar();
		} catch (Exception $e) {
			yii::error($e->getMessage());
		}
		return false;
	}
	public function actionRoleDepDrop()
	{
		$request = Yii::$app->request;
		$institutionId = yii::$app->user->identity->institution->id;
		$roleCategoryId = $request->post('roleCategoryId');
		$memberId = $request->post('memberId');
		$userType = $request->post('userType');
		$userMemberId = null;
		if ($memberId && $userType) {
			switch ($userType) {
				case ExtendedMember::USER_TYPE_MEMBER:
					$userMemberId = ExtendedUserMember::getUserMemberId($memberId, $institutionId, ExtendedMember::USER_TYPE_MEMBER);
					break;
				case ExtendedMember::USER_TYPE_SPOUSE:
					$userMemberId = ExtendedUserMember::getUserMemberId($memberId, $institutionId, ExtendedMember::USER_TYPE_SPOUSE);
					break;
			}
		}
		if ($userMemberId) {
			$previousData = CustomRoleModel::loadMemberRole($userMemberId);
		}
		if (($institutionId && $roleCategoryId)) {
			$roles = CustomRoleModel::getselectedRoles($roleCategoryId, $institutionId);
			if (!empty($roles)) {
				echo "<option value>Please select</option>";
				foreach ($roles as $Key => $role) {
					if (!empty($previousData) && $previousData['roleid'] == $role['roleid']) {
						echo "<option value='" . $role['roleid'] . "' selected>" . $role['roledescription'] . "</option>";
					} else {
						echo "<option value='" . $role['roleid'] . "'>" . $role['roledescription'] . "</option>";
					}
				}
			} else {
				echo "<option value selected>Please select</option>";
			}
		}
	}
	protected function unlinkFile($original = null, $thumbnail = null)
	{
		$path =  Yii::getAlias('@service');
		try {
			if ($original) {
				$originalPath = $path . $original;
				if (file_exists($originalPath)) {
					unlink($originalPath);
				}
			}
			if ($thumbnail) {
				$thumbnailPath = $path . $thumbnail;
				if (file_exists($thumbnailPath)) {
					unlink($thumbnailPath);
				}
			}
		} catch (Exception $e) {
			yii::error($e->getMessage());
		}
	}
	/*
    *@param memberid
    *@return member data
    *function return data for text editor in event and news reg:
    */
	public function actionMemberDataForEditor()
	{

		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		if (yii::$app->request->isAjax) {
			$memberId = yii::$app->request->post('id');
			if ($memberId) {
				$sql = "SELECT memberno FROM member WHERE memberid =:memberid";
				try {
					$memberNo = yii::$app->db->createCommand($sql)->bindValue(':memberid', $memberId)->queryScalar();
					if ($memberNo) {
						return [
							'status' => 'success',
							'data' => [
								'memberNo' => $memberNo
							]
						];
					} else {
						return [
							'status' => 'error',
							'data' => null
						];
					}
				} catch (Exception $e) {
					return [
						'status' => 'error',
						'data' => null
					];
				}
			} else {
				return [
					'status' => 'error',
					'data' => null
				];
			}
		}
	}
	/**
	 * to remove member pic
	 */
	public function actionRemoveMemberPic()
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		if (yii::$app->request->isAjax) {
			$memberId = yii::$app->request->post('memberId');
			$model = $this->findModel($memberId);
			if ($model) {
				$this->unlinkFile($model->member_pic, $model->memberImageThumbnail);
				$model->member_pic = null;
				$model->memberImageThumbnail = null;
				$model->save(false);
			}
			return;
		}
	}
	/**
	 * To remove spouse pic
	 */

	public function actionRemoveSpousepic()
	{

		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		if (yii::$app->request->isAjax) {
			$memberId = yii::$app->request->post('memberId');
			$model = $this->findModel($memberId);
			if ($model) {
				$this->unlinkFile($model->spouse_pic, $model->spouseImageThumbnail);
				$model->spouse_pic 	= null;
				$model->spouseImageThumbnail = null;
				$model->save(false);
			}
			return;
		}
	}

	public function actionExportMemberListRaw()
	{
		ini_set('memory_limit', '-1');
		set_time_limit(300);
		try {
			$startTime = date('H:i:s');
			$institutionId = yii::$app->user->identity->institution->id;
			$titleModel      = new ExtendedTitle();
			$titles = CacheHelper::getTitles($institutionId, function() use ($titleModel, $institutionId) {
				return $titleModel->getActiveTitles($institutionId);
			});
			$titlesArray = [];
			if (!empty($titles)) {
				$titlesArray =	ArrayHelper::map($titles, 'TitleId', 'Description');
			}
			$members = Yii::$app->db->createCommand(
				"SELECT * FROM member
				LEFT JOIN dependant ON member.memberid = dependant.memberid
				WHERE institutionid = :institutionId order by member.memberid"
			)
				->bindValue(':institutionId', $institutionId)
				->queryAll();

			$memberId = 0;

			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->setCellValue('A1', 'Name')
				->setCellValue('B1', 'Member Date of Birth')
				->setCellValue('C1', 'Spouse Name')
				->setCellValue('D1', 'Spouse Date of Birth')
				->setCellValue('E1', 'Member Mobile')
				->setCellValue('F1', 'Spouse Mobile')
				->setCellValue('G1', 'Residential Address')
				->setCellValue('H1', 'Business Address')
				->setCellValue('I1', 'Member Wedding Anniversary')
				->setCellValue('J1', 'Dependent Name')
				->setCellValue('K1', 'Dependent Relation')
				->setCellValue('L1', 'Dependent Date of Birth')
				->setCellValue('M1', 'Dependent Spouse Name')
				->setCellValue('N1', 'Dependent Spouse Date of Birth')
				->setCellValue('O1', 'Dependent Wedding Anniversary');

			// Set row counter to 2
			$row = 2;
			$mergeLastColumn = 'I';
			$lastColumn = 'O';

			$dependants = [];

			foreach ($members as $key => $member) {

				if (empty($member['memberid'])) {
					continue;
				}

				if ($memberId != $member['memberid']) {
					$memberId = $member['memberid'];
					// Write member details to the sheet
					$memberFullName = trim(implode(' ', [($titlesArray[$member['membertitle']] ?? ''), $member['firstName'], $member['middleName'], $member['lastName']]));
					$spouseFullName = trim(implode(' ', [($titlesArray[$member['spousetitle']] ?? ''), $member['spouse_firstName'], $member['spouse_middleName'], $member['spouse_lastName']]));

					$residentialAddress = trim(implode(' ', [$member['residence_address1'], $member['residence_address2'], $member['residence_address3'], $member['residence_district'], $member['residence_state'], $member['residence_pincode']]));

					$bussinessAddress = trim(implode(' ', [$member['business_address1'], $member['business_address2'], $member['business_address3'], $member['business_district'], $member['business_state'], $member['business_pincode']]));

					$sheet->setCellValue('A' . $row, $memberFullName)
						->setCellValue('B' . $row, (!empty($member['member_dob']) ? date_format(date_create($member['member_dob']), Yii::$app->params['dateFormat']['viewDateFormat']) : ''))
						->setCellValue('C' . $row, $spouseFullName)
						->setCellValue('D' . $row, (!empty($member['spouse_dob']) ? date_format(date_create($member['spouse_dob']), Yii::$app->params['dateFormat']['viewDateFormat']) : ''))
						->setCellValue('E' . $row, trim($member['member_mobile1_countrycode'] . $member['member_mobile1']))
						->setCellValue('F' . $row, trim($member['spouse_mobile1_countrycode'] . $member['spouse_mobile1']))
						->setCellValue('G' . $row, $residentialAddress)
						->setCellValue('H' . $row, $bussinessAddress)
						->setCellValue('I' . $row, (!empty($member['dom']) ? date_format(date_create($member['dom']), Yii::$app->params['dateFormat']['viewDateFormat']) : ''));

					$dependants = [];
				}

				if (empty($member['dependantid'])) {
					$dependants[$member['id']]['name'] = trim(implode(' ', [($titlesArray[$member['titleid']] ?? ''), $member['dependantname']]));
					$dependants[$member['id']]['relation'] = $member['relation'];
					$dependants[$member['id']]['dob'] = (!empty($member['dob']) ? date_format(date_create($member['dob']), Yii::$app->params['dateFormat']['viewDateFormat']) : '');
					$dependants[$member['id']]['titleid'] = $member['titleid'];
					$dependants[$member['id']]['weddinganniversary'] = (!empty($member['weddinganniversary']) ? date_format(date_create($member['weddinganniversary']), Yii::$app->params['dateFormat']['viewDateFormat']) : '');
				} else {

					$dependants[$member['dependantid']]['spouse_name'] = trim(implode(' ', [($titlesArray[$member['titleid']] ?? ''), $member['dependantname']]));
					$dependants[$member['dependantid']]['spouse_dob'] = (!empty($member['dob']) ? date_format(date_create($member['dob']), Yii::$app->params['dateFormat']['viewDateFormat']) : '');
					$dependants[$member['dependantid']]['spouse_titleid'] = $member['titleid'];
				}

				if (empty($members[$key + 1]['memberid']) || $members[$key + 1]['memberid'] != $memberId) {

					$i = 0;

					// Write dependant details to the sheet
					foreach ($dependants as $dependant) {

						if (empty($dependant['name'])) {
							continue;
						}

						$sheet->setCellValue('J' . ($row + $i), $dependant['name'])
							->setCellValue('K' . ($row + $i), $dependant['relation'])
							->setCellValue('L' . ($row + $i), $dependant['dob'])
							->setCellValue('M' . ($row + $i), ($dependant['spouse_name'] ?? ''))
							->setCellValue('N' . ($row + $i), ($dependant['spouse_dob'] ?? ''))
							->setCellValue('O' . ($row + $i), $dependant['weddinganniversary']);
						$i++;
					}


					if ($i) {
						foreach (range('A', $mergeLastColumn) as $columnID) {
							$sheet->mergeCells($columnID . $row . ':' . $columnID . ($row + $i - 1));
						}
					}

					$row += max(1, $i);
				}
			}

			/* foreach (range('A', $lastColumn) as $columnID) {

				$sheet->getColumnDimension($columnID)
					->setAutoSize(true);
			} */

			foreach (range('A', $lastColumn) as $columnID) {
				if (in_array($columnID, ['N', 'O'])) {
					$sheet->getColumnDimension($columnID)->setWidth(30);
					continue;
				}
				$sheet->getColumnDimension($columnID)->setWidth(25);
			}

			foreach (range(0, ($row - 1)) as $rowId) {
				foreach (range('A', $lastColumn) as $columnID) {
					$sheet->getStyle($columnID . $rowId)
						->getBorders()->getOutline()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)
						->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF808080'));
				}
			}


			// Set the file name and type
			$fileName = 'members.xlsx';
			// $fileName = $startTime.date('H:i:s').$fileName;
			$writer = new Xlsx($spreadsheet);
			// Create a temporary file in the system
			$filePath = sys_get_temp_dir() . '/' . $fileName;
			// Save our workbook as this file name
			$writer->save($filePath);
			// Return the file as an attachment
			return Yii::$app->response->sendFile($filePath, $fileName, ['inline' => true]);

			/* ob_start();

			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="members.xlsx"');
            header('Cache-Control: max-age=0');
			$writer->save('php://output');
			ob_end_flush(); */
		} catch (\Exception $e) {
			print_r($e->getMessage());
		}
	}



	public function actionExportMemberList()
	{
		ini_set('memory_limit', '-1');
		set_time_limit(300);
		$institutionId = yii::$app->user->identity->institution->id;
		$titleModel      = new ExtendedTitle();
		$titles = CacheHelper::getTitles($institutionId, function() use ($titleModel, $institutionId) {
			return $titleModel->getActiveTitles($institutionId);
		});
		$titlesArray = [];
		if (!empty($titles)) {
			$titlesArray =	ArrayHelper::map($titles, 'TitleId', 'Description');
		}

		$members = ExtendedMember::find()->where(['institutionid' => $institutionId])->all();
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'Name')
			->setCellValue('B1', 'Member Date of Birth')
			->setCellValue('C1', 'Spouse Name')
			->setCellValue('D1', 'Spouse Date of Birth')
			->setCellValue('E1', 'Member Mobile')
			->setCellValue('F1', 'Spouse Mobile')
			->setCellValue('G1', 'Residential Address')
			->setCellValue('H1', 'Business Address')
			->setCellValue('I1', 'Member Wedding Anniversary')
			->setCellValue('J1', 'Dependent Name')
			->setCellValue('K1', 'Dependent Mobile')
			->setCellValue('L1', 'Dependent Relation')
			->setCellValue('M1', 'Dependent Date of Birth')
			->setCellValue('N1', 'Dependent Spouse Name')
			->setCellValue('O1', 'Dependent Spouse Mobile')
			->setCellValue('P1', 'Dependent Spouse Date of Birth')
			->setCellValue('Q1', 'Dependent Wedding Anniversary');

		// Set row counter to 2
		$row = 2;
		$mergeLastColumn = 'I';
		$lastColumn = 'O';

		foreach ($members as $member) {

			// Write member details to the sheet
			$memberFullName = trim(implode(' ', [($titlesArray[$member->membertitle] ?? ''), $member->firstName, $member->middleName, $member->lastName]));
			$spouseFullName = trim(implode(' ', [($titlesArray[$member->spousetitle] ?? ''), $member->spouse_firstName, $member->spouse_middleName, $member->spouse_lastName]));

			$residentialAddress = trim(implode(' ', [$member->residence_address1, $member->residence_address2, $member->residence_address3, $member->residence_district, $member->residence_state, $member->residence_pincode]));

			$bussinessAddress = trim(implode(' ', [$member->business_address1, $member->business_address2, $member->business_address3, $member->business_district, $member->business_state, $member->business_pincode]));
			$memberMobileNumber = ($member->member_mobile1_countrycode ?? '') . ($member->member_mobile1 ?? '');
			$memberSpouseNumber = ($member->spouse_mobile1_countrycode ?? '') . ($member->spouse_mobile1 ?? '');

			$sheet->setCellValue('A' . $row, $memberFullName)
				->setCellValue('B' . $row, (!empty($member->member_dob) ? date_format(date_create($member->member_dob), Yii::$app->params['dateFormat']['viewDateFormat']) : ''))
				->setCellValue('C' . $row, $spouseFullName)
				->setCellValue('D' . $row, (!empty($member->spouse_dob) ? date_format(date_create($member->spouse_dob), Yii::$app->params['dateFormat']['viewDateFormat']) : ''))
				->setCellValue('E' . $row, !empty($memberMobileNumber) ? "\t" . $memberMobileNumber : '')
				->setCellValue('F' . $row, !empty($memberSpouseNumber) ? "\t" . $memberSpouseNumber : '')
				->setCellValue('G' . $row, $residentialAddress)
				->setCellValue('H' . $row, $bussinessAddress)
				->setCellValue('I' . $row, (!empty($member->dom) ? date_format(date_create($member->dom), Yii::$app->params['dateFormat']['viewDateFormat']) : ''));

			$dependants = [];
			foreach ($member->dependants as $dependant) {
				if (empty($dependant->dependantid)) {
					$dependants[$dependant->id]['name'] = trim(implode(' ', [($titlesArray[$dependant->titleid] ?? ''), $dependant->dependantname]));
					$mobileNumber = ($dependant->dependantmobilecountrycode ?? '') . ($dependant->dependantmobile ?? '');
					$dependants[$dependant->id]['mobile'] = !empty($mobileNumber) ? "\t" . $mobileNumber : '';
					$dependants[$dependant->id]['relation'] = $dependant->relation;
					$dependants[$dependant->id]['dob'] = (!empty($dependant->dob) ? date_format(date_create($dependant->dob), Yii::$app->params['dateFormat']['viewDateFormat']) : '');
					$dependants[$dependant->id]['titleid'] = $dependant->titleid;
					$dependants[$dependant->id]['weddinganniversary'] = (!empty($dependant->weddinganniversary) ? date_format(date_create($dependant->weddinganniversary), Yii::$app->params['dateFormat']['viewDateFormat']) : '');
				} else {
					if (empty($dependants[$dependant->dependantid])) {
						$dependants[$dependant->dependantid] = [];
					}
					$dependants[$dependant->dependantid]['spouse_name'] = trim(implode(' ', [($titlesArray[$dependant->titleid] ?? ''), $dependant->dependantname]));
					$mobileNumber = ($dependant->dependantmobilecountrycode ?? '') . ($dependant->dependantmobile ?? '');
					$dependants[$dependant->dependantid]['spouse_mobile'] = !empty($mobileNumber) ? "\t" . $mobileNumber : '';
					$dependants[$dependant->dependantid]['spouse_dob'] = (!empty($dependant->dob) ? date_format(date_create($dependant->dob), Yii::$app->params['dateFormat']['viewDateFormat']) : '');
					$dependants[$dependant->dependantid]['spouse_titleid'] = $dependant->titleid;
				}
			}

			$i = 0;
			foreach ($dependants as $dependant) {

				if (empty($dependant['name'])) {
					continue;
				}
				$sheet->setCellValue('J' . ($row + $i), $dependant['name'])
					->setCellValue('K' . ($row + $i), $dependant['mobile'])
					->setCellValue('L' . ($row + $i), $dependant['relation'])
					->setCellValue('M' . ($row + $i), $dependant['dob'])
					->setCellValue('N' . ($row + $i), ($dependant['spouse_name'] ?? ''))
					->setCellValue('O' . ($row + $i), ($dependant['spouse_mobile'] ?? ''))
					->setCellValue('P' . ($row + $i), ($dependant['spouse_dob'] ?? ''))
					->setCellValue('Q' . ($row + $i), $dependant['weddinganniversary']);
				$i++;
			}
			if ($i) {
				foreach (range('A', $mergeLastColumn) as $columnID) {
					$sheet->mergeCells($columnID . $row . ':' . $columnID . ($row + $i - 1));
				}
			}

			$row += max(1, $i);
		}

		foreach (range('A', $lastColumn) as $columnID) {
			if (in_array($columnID, ['P', 'Q', 'K', 'O'])) {
				$sheet->getColumnDimension($columnID)->setWidth(30);
				continue;
			}
			$sheet->getColumnDimension($columnID)->setWidth(25);
		}

		foreach (range(1, ($row)) as $rowId) {
			foreach (range('A', $lastColumn) as $columnID) {
				$sheet->getStyle($columnID . $rowId)
					->getBorders()->getOutline()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)
					->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF808080'));
			}
		}

		// Set the file name and type
		$fileName = 'members.xlsx';
		$writer = new Xlsx($spreadsheet);
		// Create a temporary file in the system
		$filePath = sys_get_temp_dir() . '/' . $fileName;
		// Save our workbook as this file name
		ob_end_clean();
		$writer->save($filePath);
		// Return the file as an attachment
		return Yii::$app->response->sendFile($filePath, $fileName, ['inline' => true]);
	}

	/**
	 * Export members with advanced filtering options
	 * Filters: Member since date range, marriage date (month/full date), birthday (month/full date), include dependants
	 */
	public function actionExportMembersFiltered()
	{
		ini_set('memory_limit', '-1');
		set_time_limit(300);

		$institutionId = Yii::$app->user->identity->institution->id;

		// Get filter parameters from request
		$memberSinceFrom = Yii::$app->request->get('member_since_from');
		$memberSinceTo = Yii::$app->request->get('member_since_to');
		$membershipType = Yii::$app->request->get('membership_type');
		$marriageMonth = Yii::$app->request->get('marriage_month'); // 1-12 or empty
		$marriageDateFrom = Yii::$app->request->get('marriage_date_from');
		$marriageDateTo = Yii::$app->request->get('marriage_date_to');
		$birthdayMonth = Yii::$app->request->get('birthday_month'); // 1-12 or empty
		$birthdayDateFrom = Yii::$app->request->get('birthday_date_from');
		$birthdayDateTo = Yii::$app->request->get('birthday_date_to');
		// Handle checkbox - when unchecked, parameter is not sent at all, so default to 0
		$includeDependants = (int) Yii::$app->request->get('include_dependants', 0); // 1 or 0

		// Build query with filters
		$query = ExtendedMember::find()->where(['institutionid' => $institutionId]);

		// Filter by membership type
		if (!empty($membershipType)) {
			$query->andWhere(['membershiptype' => $membershipType]);
		}

		// Filter by member since date range
		if (!empty($memberSinceFrom)) {
			$query->andWhere(['>=', 'memberdate', $memberSinceFrom]);
		}
		if (!empty($memberSinceTo)) {
			$query->andWhere(['<=', 'memberdate', $memberSinceTo]);
		}

		// Filter by marriage month only
		if (!empty($marriageMonth)) {
			if ($includeDependants) {
				// Include dependant wedding anniversaries in the search
				$query->joinWith('dependants')->andWhere([
					'or',
					['MONTH(member.dom)' => $marriageMonth],
					['MONTH(dependant.weddinganniversary)' => $marriageMonth]
				]);
			} else {
				// Only check member marriage date
				$query->andWhere(['MONTH(dom)' => $marriageMonth]);
			}
		}

		// Filter by marriage date range
		if (!empty($marriageDateFrom)) {
			if ($includeDependants) {
				$query->joinWith('dependants')->andWhere([
					'or',
					['>=', 'member.dom', $marriageDateFrom],
					['>=', 'dependant.weddinganniversary', $marriageDateFrom]
				]);
			} else {
				$query->andWhere(['>=', 'dom', $marriageDateFrom]);
			}
		}
		if (!empty($marriageDateTo)) {
			if ($includeDependants) {
				$query->joinWith('dependants')->andWhere([
					'or',
					['<=', 'member.dom', $marriageDateTo],
					['<=', 'dependant.weddinganniversary', $marriageDateTo]
				]);
			} else {
				$query->andWhere(['<=', 'dom', $marriageDateTo]);
			}
		}

		// Filter by birthday month only
		if (!empty($birthdayMonth)) {
			if ($includeDependants) {
				// Include dependant birthdays in the search
				$query->joinWith('dependants')->andWhere([
					'or',
					['MONTH(member.member_dob)' => $birthdayMonth],
					['MONTH(member.spouse_dob)' => $birthdayMonth],
					['MONTH(dependant.dob)' => $birthdayMonth]
				]);
			} else {
				// Only check member and spouse birthdays
				$query->andWhere([
					'or',
					['MONTH(member_dob)' => $birthdayMonth],
					['MONTH(spouse_dob)' => $birthdayMonth]
				]);
			}
		}

		// Filter by birthday date range
		if (!empty($birthdayDateFrom) && !empty($birthdayDateTo)) {
			if ($includeDependants) {
				// Include dependant birthdays in the search
				$query->joinWith('dependants')->andWhere([
					'or',
					['between', 'member.member_dob', $birthdayDateFrom, $birthdayDateTo],
					['between', 'member.spouse_dob', $birthdayDateFrom, $birthdayDateTo],
					['between', 'dependant.dob', $birthdayDateFrom, $birthdayDateTo]
				]);
			} else {
				// Only check member and spouse birthdays
				$query->andWhere([
					'or',
					['between', 'member_dob', $birthdayDateFrom, $birthdayDateTo],
					['between', 'spouse_dob', $birthdayDateFrom, $birthdayDateTo]
				]);
			}
		}

		// Filter by marriage month only
		if (!empty($marriageMonth)) {
			if ($includeDependants) {
				// Include dependant wedding anniversaries in the search
				$query->joinWith('dependants')->andWhere([
					'or',
					['MONTH(member.dom)' => $marriageMonth],
					['MONTH(dependant.weddinganniversary)' => $marriageMonth]
				]);
			} else {
				// Only check member marriage date
				$query->andWhere(['MONTH(dom)' => $marriageMonth]);
			}
		}

		// Filter by marriage date range
		if (!empty($marriageDateFrom) && !empty($marriageDateTo)) {
			if ($includeDependants) {
				// Include dependant wedding anniversaries in the search
				$query->joinWith('dependants')->andWhere([
					'or',
					['between', 'member.dom', $marriageDateFrom, $marriageDateTo],
					['between', 'dependant.weddinganniversary', $marriageDateFrom, $marriageDateTo]
				]);
			} else {
				// Only check member marriage date
				$query->andWhere(['>=', 'dom', $marriageDateFrom])
					->andWhere(['<=', 'dom', $marriageDateTo]);
			}
		}

		$members = $query->distinct()->all();

		// Get titles
		$titleModel = new ExtendedTitle();
		$titles = CacheHelper::getTitles($institutionId, function() use ($titleModel, $institutionId) {
			return $titleModel->getActiveTitles($institutionId);
		});
		$titlesArray = [];
		if (!empty($titles)) {
			$titlesArray = ArrayHelper::map($titles, 'TitleId', 'Description');
		}

		// Create spreadsheet
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		// Set headers based on whether dependants are included
		if ($includeDependants) {
			$sheet->setCellValue('A1', 'Member Name')
				->setCellValue('B1', 'Member Since')
				->setCellValue('C1', 'Member DOB')
				->setCellValue('D1', 'Member Age')
				->setCellValue('E1', 'Member Mobile')
				->setCellValue('F1', 'Member Email')
				->setCellValue('G1', 'Member Occupation')
				->setCellValue('H1', 'Member Active')
				->setCellValue('I1', 'Member Confirmed')
				->setCellValue('J1', 'Spouse Name')
				->setCellValue('K1', 'Spouse DOB')
				->setCellValue('L1', 'Spouse Age')
				->setCellValue('M1', 'Spouse Mobile')
				->setCellValue('N1', 'Spouse Occupation')
				->setCellValue('O1', 'Spouse Active')
				->setCellValue('P1', 'Spouse Confirmed')
				->setCellValue('Q1', 'Head of Family')
				->setCellValue('R1', 'Wedding Anniversary')
				->setCellValue('S1', 'Residential Address')
				->setCellValue('T1', 'Business Address')
				->setCellValue('U1', 'Dependant Name')
				->setCellValue('V1', 'Dependant Relation')
				->setCellValue('W1', 'Dependant DOB')
				->setCellValue('X1', 'Dependant Age')
				->setCellValue('Y1', 'Dependant Mobile')
				->setCellValue('Z1', 'Dependant Occupation')
				->setCellValue('AA1', 'Dependant Active')
				->setCellValue('AB1', 'Dependant Confirmed')
				->setCellValue('AC1', 'Dependant Spouse Name')
				->setCellValue('AD1', 'Dependant Spouse DOB')
				->setCellValue('AE1', 'Dependant Spouse Age')
				->setCellValue('AF1', 'Dependant Spouse Mobile')
				->setCellValue('AG1', 'Dependant Spouse Occupation')
				->setCellValue('AH1', 'Dependant Spouse Active')
				->setCellValue('AI1', 'Dependant Spouse Confirmed')
				->setCellValue('AJ1', 'Dependant Wedding Anniversary');
		} else {
			$sheet->setCellValue('A1', 'Member Name')
				->setCellValue('B1', 'Member Since')
				->setCellValue('C1', 'Member DOB')
				->setCellValue('D1', 'Member Age')
				->setCellValue('E1', 'Member Mobile')
				->setCellValue('F1', 'Member Email')
				->setCellValue('G1', 'Member Occupation')
				->setCellValue('H1', 'Member Active')
				->setCellValue('I1', 'Member Confirmed')
				->setCellValue('J1', 'Spouse Name')
				->setCellValue('K1', 'Spouse DOB')
				->setCellValue('L1', 'Spouse Age')
				->setCellValue('M1', 'Spouse Mobile')
				->setCellValue('N1', 'Spouse Occupation')
				->setCellValue('O1', 'Spouse Active')
				->setCellValue('P1', 'Spouse Confirmed')
				->setCellValue('Q1', 'Head of Family')
				->setCellValue('R1', 'Wedding Anniversary')
				->setCellValue('S1', 'Residential Address')
				->setCellValue('T1', 'Business Address');
		}

		$row = 2;
		$mergeLastColumn = $includeDependants ? 'T' : 'T';
		$lastColumn = $includeDependants ? 'AJ' : 'T';

		foreach ($members as $member) {
			// Calculate ages
			$memberAge = !empty($member->member_dob) ? $this->calculateAge($member->member_dob) : '';
			$spouseAge = !empty($member->spouse_dob) ? $this->calculateAge($member->spouse_dob) : '';

			// Build full names
			$memberFullName = trim(implode(' ', [
				($titlesArray[$member->membertitle] ?? ''),
				$member->firstName,
				$member->middleName,
				$member->lastName
			]));

			$spouseFullName = trim(implode(' ', [
				($titlesArray[$member->spousetitle] ?? ''),
				$member->spouse_firstName,
				$member->spouse_middleName,
				$member->spouse_lastName
			]));

			// Build addresses
			$residentialAddress = trim(implode(' ', [
				$member->residence_address1,
				$member->residence_address2,
				$member->residence_address3,
				$member->residence_district,
				$member->residence_state,
				$member->residence_pincode
			]));

			$businessAddress = trim(implode(' ', [
				$member->business_address1,
				$member->business_address2,
				$member->business_address3,
				$member->business_district,
				$member->business_state,
				$member->business_pincode
			]));

			// Format phone numbers
			$memberMobileNumber = ($member->member_mobile1_countrycode ?? '') . ($member->member_mobile1 ?? '');
			$memberSpouseNumber = ($member->spouse_mobile1_countrycode ?? '') . ($member->spouse_mobile1 ?? '');

			// Format active/confirmed status
			$memberActive = isset($member->active) ? ($member->active ? 'Yes' : 'No') : '';
			$memberConfirmed = isset($member->confirmed) ? ($member->confirmed ? 'Yes' : 'No') : '';
			$spouseActive = isset($member->active_spouse) ? ($member->active_spouse ? 'Yes' : 'No') : '';
			$spouseConfirmed = isset($member->confirmed_spouse) ? ($member->confirmed_spouse ? 'Yes' : 'No') : '';
			$headOfFamily = ($member->head_of_family === 's') ? 'Spouse' : 'Member';

			// Write member details
			$sheet->setCellValue('A' . $row, $memberFullName)
				->setCellValue('B' . $row, !empty($member->memberdate) ? date_format(date_create($member->memberdate), Yii::$app->params['dateFormat']['viewDateFormat']) : '')
				->setCellValue('C' . $row, !empty($member->member_dob) ? date_format(date_create($member->member_dob), Yii::$app->params['dateFormat']['viewDateFormat']) : '')
				->setCellValue('D' . $row, $memberAge)
				->setCellValue('E' . $row, !empty($memberMobileNumber) ? "\t" . $memberMobileNumber : '')
				->setCellValue('F' . $row, $member->member_email ?? '')
				->setCellValue('G' . $row, $member->occupation ?? '')
				->setCellValue('H' . $row, $memberActive)
				->setCellValue('I' . $row, $memberConfirmed)
				->setCellValue('J' . $row, $spouseFullName)
				->setCellValue('K' . $row, !empty($member->spouse_dob) ? date_format(date_create($member->spouse_dob), Yii::$app->params['dateFormat']['viewDateFormat']) : '')
				->setCellValue('L' . $row, $spouseAge)
				->setCellValue('M' . $row, !empty($memberSpouseNumber) ? "\t" . $memberSpouseNumber : '')
				->setCellValue('N' . $row, $member->spouseoccupation ?? '')
				->setCellValue('O' . $row, $spouseActive)
				->setCellValue('P' . $row, $spouseConfirmed)
				->setCellValue('Q' . $row, $headOfFamily)
				->setCellValue('R' . $row, !empty($member->dom) ? date_format(date_create($member->dom), Yii::$app->params['dateFormat']['viewDateFormat']) : '')
				->setCellValue('S' . $row, $residentialAddress)
				->setCellValue('T' . $row, $businessAddress);

			// Process dependants if included
			if ($includeDependants) {
				$dependants = [];
				foreach ($member->dependants as $dependant) {
					if (empty($dependant->dependantid)) {
						// Primary dependant
						$dependantAge = !empty($dependant->dob) ? $this->calculateAge($dependant->dob) : '';
						$dependants[$dependant->id]['name'] = trim(implode(' ', [
							($titlesArray[$dependant->titleid] ?? ''),
							$dependant->dependantname
						]));
						$mobileNumber = ($dependant->dependantmobilecountrycode ?? '') . ($dependant->dependantmobile ?? '');
						$dependants[$dependant->id]['mobile'] = !empty($mobileNumber) ? "\t" . $mobileNumber : '';
						$dependants[$dependant->id]['relation'] = $dependant->relation;
						$dependants[$dependant->id]['dob'] = !empty($dependant->dob) ? date_format(date_create($dependant->dob), Yii::$app->params['dateFormat']['viewDateFormat']) : '';
						$dependants[$dependant->id]['age'] = $dependantAge;
						$dependants[$dependant->id]['occupation'] = $dependant->occupation ?? '';
						$dependants[$dependant->id]['active'] = isset($dependant->active) ? ($dependant->active ? 'Yes' : 'No') : '';
						$dependants[$dependant->id]['confirmed'] = isset($dependant->confirmed) ? ($dependant->confirmed ? 'Yes' : 'No') : '';
						$dependants[$dependant->id]['weddinganniversary'] = !empty($dependant->weddinganniversary) ? date_format(date_create($dependant->weddinganniversary), Yii::$app->params['dateFormat']['viewDateFormat']) : '';
					} else {
						// Dependant's spouse
						if (empty($dependants[$dependant->dependantid])) {
							$dependants[$dependant->dependantid] = [];
						}
						$spouseDependantAge = !empty($dependant->dob) ? $this->calculateAge($dependant->dob) : '';
						$dependants[$dependant->dependantid]['spouse_name'] = trim(implode(' ', [
							($titlesArray[$dependant->titleid] ?? ''),
							$dependant->dependantname
						]));
						$mobileNumber = ($dependant->dependantmobilecountrycode ?? '') . ($dependant->dependantmobile ?? '');
						$dependants[$dependant->dependantid]['spouse_mobile'] = !empty($mobileNumber) ? "\t" . $mobileNumber : '';
						$dependants[$dependant->dependantid]['spouse_dob'] = !empty($dependant->dob) ? date_format(date_create($dependant->dob), Yii::$app->params['dateFormat']['viewDateFormat']) : '';
						$dependants[$dependant->dependantid]['spouse_age'] = $spouseDependantAge;
						$dependants[$dependant->dependantid]['spouse_occupation'] = $dependant->occupation ?? '';
						$dependants[$dependant->dependantid]['spouse_active'] = isset($dependant->active) ? ($dependant->active ? 'Yes' : 'No') : '';
						$dependants[$dependant->dependantid]['spouse_confirmed'] = isset($dependant->confirmed) ? ($dependant->confirmed ? 'Yes' : 'No') : '';
					}
				}

				$i = 0;
				foreach ($dependants as $dependant) {
					if (empty($dependant['name'])) {
						continue;
					}
					$sheet->setCellValue('U' . ($row + $i), $dependant['name'])
						->setCellValue('V' . ($row + $i), $dependant['relation'])
						->setCellValue('W' . ($row + $i), $dependant['dob'])
						->setCellValue('X' . ($row + $i), $dependant['age'])
						->setCellValue('Y' . ($row + $i), $dependant['mobile'])
						->setCellValue('Z' . ($row + $i), $dependant['occupation'])
						->setCellValue('AA' . ($row + $i), $dependant['active'])
						->setCellValue('AB' . ($row + $i), $dependant['confirmed'])
						->setCellValue('AC' . ($row + $i), $dependant['spouse_name'] ?? '')
						->setCellValue('AD' . ($row + $i), $dependant['spouse_dob'] ?? '')
						->setCellValue('AE' . ($row + $i), $dependant['spouse_age'] ?? '')
						->setCellValue('AF' . ($row + $i), $dependant['spouse_mobile'] ?? '')
						->setCellValue('AG' . ($row + $i), $dependant['spouse_occupation'] ?? '')
						->setCellValue('AH' . ($row + $i), $dependant['spouse_active'] ?? '')
						->setCellValue('AI' . ($row + $i), $dependant['spouse_confirmed'] ?? '')
						->setCellValue('AJ' . ($row + $i), $dependant['weddinganniversary']);
					$i++;
				}

				if ($i) {
					foreach (range('A', $mergeLastColumn) as $columnID) {
						$sheet->mergeCells($columnID . $row . ':' . $columnID . ($row + $i - 1));
					}
				}

				$row += max(1, $i);
			} else {
				$row++;
			}
		}

		// Set column widths
		// Generate column range from A to lastColumn (handles multi-character columns like AA, AB, etc.)
		$columns = [];
		$currentCol = 'A';
		while (true) {
			$columns[] = $currentCol;
			if ($currentCol === $lastColumn) {
				break;
			}
			$currentCol++;
		}
		
		foreach ($columns as $columnID) {
			// Wide columns for addresses and longer text
			if (in_array($columnID, ['S', 'T', 'Y', 'AF'])) {
				$sheet->getColumnDimension($columnID)->setWidth(35);
				// Medium columns for phone numbers, anniversary, occupation
			} elseif (in_array($columnID, ['E', 'G', 'M', 'N', 'R', 'Z', 'AG', 'AJ'])) {
				$sheet->getColumnDimension($columnID)->setWidth(25);
				// Standard columns for everything else
			} else {
				$sheet->getColumnDimension($columnID)->setWidth(20);
			}
		}

		// Add borders
		foreach (range(1, ($row - 1)) as $rowId) {
			foreach ($columns as $columnID) {
				$sheet->getStyle($columnID . $rowId)
					->getBorders()->getOutline()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)
					->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF808080'));
			}
		}

		// Generate filename with filter info
		$filenameParts = ['members'];
		if (!empty($membershipType)) {
			$filenameParts[] = 'type_' . str_replace(' ', '_', $membershipType);
		}
		if (!empty($memberSinceFrom) || !empty($memberSinceTo)) {
			$filenameParts[] = 'member_since';
		}
		if (!empty($marriageMonth)) {
			$filenameParts[] = 'marriage_month_' . $marriageMonth;
		}
		if (!empty($birthdayMonth)) {
			$filenameParts[] = 'birthday_month_' . $birthdayMonth;
		}
		if ($includeDependants) {
			$filenameParts[] = 'with_dependants';
		}
		$filenameParts[] = date('Y-m-d');

		$fileName = implode('_', $filenameParts) . '.xlsx';
		$writer = new Xlsx($spreadsheet);
		$filePath = sys_get_temp_dir() . '/' . $fileName;

		ob_end_clean();
		$writer->save($filePath);

		return Yii::$app->response->sendFile($filePath, $fileName, ['inline' => true]);
	}

	/**
	 * Calculate age from date of birth
	 */
	private function calculateAge($dob)
	{
		try {
			$birthDate = new \DateTime($dob);
			$today = new \DateTime('today');
			$age = $birthDate->diff($today)->y;
			return $age;
		} catch (Exception $e) {
			return '';
		}
	}
}
