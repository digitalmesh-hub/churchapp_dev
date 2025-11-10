<?php

namespace common\models\extendedmodels;

use Yii;
use yii\web\UploadedFile;
use common\models\basemodels\Institution;
use common\models\extendedmodels\ExtendedCountry;
use common\models\extendedmodels\ExtendedTimeZoneInfo;
use common\models\extendedmodels\ExtendedInstitutionType;
use common\models\extendedmodels\ExtendedInstitutioncontactfilters;
use common\models\extendedmodels\ExtendedContactfilters;
use common\models\extendedmodels\ExtendedFeedbacktype; 
use common\models\extendedmodels\ExtendedInstitutionfeedbacktype;
use yii\helpers\ArrayHelper;
use yii\imagine\Image;
use Exception;

class ExtendedInstitution extends Institution
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    
    const INSTITUTION_TYPE_CHURCH = 2;
    const INSTITUTION_TYPE_CLUB = 1;
    const INSTITUTION_TYPE_APPARTMENT = 3;
    const INSTITUTION_TYPE_EDUCATION = 4;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lattitude', 'longitude'], 'number'],
            [['createduser', 'modifieduser', 'paymenttypeid', 'institutiontype', 'countryid', 'demo'], 'integer'],
            [['createddate', 'modifieddate', 'demo_expiry', 'social_media'], 'safe'],
            [['institutiontype','name','address1','state','countryid','district','pin','timezone','tagcloud'], 'required'],
            [['name','state','district','pin','timezone','phone1','phone1_countrycode','phone2_countrycode','phone1_areacode','phone2'], 'trim'],
            [['name', 'district', 'state', 'location'], 'string', 'max' => 45],
            [['address1'], 'string', 'max' => 100, 'tooLong' => 'Address Line1 should contain at most 100 characters'],
            [['address2'], 'string', 'max' => 100, 'tooLong' => 'Address Line2 should contain at most 100 characters'],
            [['address3'], 'string', 'max' => 100, 'tooLong' => 'Address Line3 should contain at most 100 characters'],
            [['email'], 'string', 'max' => 100, 'tooLong' => 'Email should contain at most 100 characters'],
            ['email','email', 'message' => 'Please enter a valid email address'],
            [['name'], 'unique'],
            [['email'],'unique'],
            [['url', 'moreurl', 'facebook', 'instagram', 'twitter', 'youtube'], 'url', 'defaultScheme' => 'http', 'message' => 'Please enter a valid URL.'],
            [['phone2'], 'match', 'pattern' => '/^\d{10}$/'],
            [['phone1'],'match','pattern' => '/^\d{7,10}$/'],
            [['pin'], 'match', 'pattern' => '/^([0-9]+-)*[0-9]+$/'],
            [['phone1_areacode', 'phone2_countrycode','phone1_countrycode'],'match' , 'pattern' => '/^(\+(?:\d{2,3}$))|(\+(?:\d{1})$)|(?:\d{2,3}$)|(?:\d{1}$)/', 'message'=> ""],
//             [['phone1_countrycode'],'match', 'pattern' => '/^[a-zA-Z]{2,3}(?:-[A-Z]{2,3}(?:-[a-zA-Z]{4})?)?$/','message' => ""],
            [['feedbackenabled',
             'paymentoptionenabled',
             'paymentoptionenabled',
             'prayerrequestenabled',
             'isrotary',
             'moreenabled',
             'active',
             'advancedsearchenabled'
             ], 'boolean'],
            [['institutionlogo'], 'file', 'skipOnEmpty' => true,
                'extensions' => 'png, jpg,  jpeg', 'skipOnEmpty' =>true,
                'maxSize' => \Yii::$app->params['fileUploadSize']['imageFileSize'],
                'tooBig' => \Yii::$app->params['fileUploadSize']['imageSizeMsg']],
            [['timezone', 'feedbackemail', 'prayeremail'], 'string', 'max' => 250],
            ['tagcloud', 'in', 'range' => [0, 1]],
            [['countryid'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedCountry::className(), 'targetAttribute' => ['countryid' => 'countryid']],
            [['createduser'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedUsercredentials::className(), 'targetAttribute' => ['createduser' => 'id']],
            [['institutiontype'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedInstitutiontype::className(), 'targetAttribute' => ['institutiontype' => 'institutiontypeid']],
            [['modifieduser'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedUsercredentials::className(), 'targetAttribute' => ['modifieduser' => 'id']],
            ['moreurl', 'required', 'when' => function ($model) {
                    return $model->moreenabled == 1;
                }, 'whenClient' => "function (attribute, value) {
                    return $('#checkbox-more').prop('checked') == true;
                }"
            ],
            ['demo_expiry', 'required', 'when' => function ($model) {
                return $model->demo == 1;
            }, 'whenClient' => "function (attribute, value) {
                return $('#checkbox-demo').prop('checked') == true;
            }"
        ],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Institution Name',
            'address1' => 'Address Line1',
            'address2' => 'Address Line2',
            'address3' => 'Address Line3',
            'district' => 'District',
            'state' => 'State',
            'pin' => 'Pin',
            'phone1_countrycode' => 'Countrycode',
            'phone1_areacode' => 'Areacode',
            'phone1' => 'Telephone Number',
            'location' => 'Location',
            'lattitude' => 'Lattitude',
            'institutionlogo' => 'Image',
            'phone2_countrycode' => 'Areacode',  
            'phone2' => 'Mobile Number',
            'email' => 'Email',
            'longitude' => 'Longitude',
            'active' => 'Active',
            'createduser' => 'Created user',
            'createddate' => 'Created date',
            'modifieduser' => 'Modified user',
            'modifieddate' => 'Modified date',
            'timezone' => 'Time zone',
            'feedbackenabled' => 'Enable Feedback',
            'feedbackemail' => 'Feedback email',
            'paymentoptionenabled' => 'Enable Payments',
            'paymenttypeid' => 'Paymenttypeid',
            'prayerrequestenabled' => 'Enable Prayer',
            'institutiontype' => 'Institution Type',
            'prayeremail' => 'Prayeremail',
            'url' => 'Website Url',
            'countryid' => 'Country',
            'moreenabled' => 'More',
            'moreurl' => 'More url',
            'isrotary' => 'Is Rotary Club',
            'tagcloud' => 'Tag cloud',
            'advancedsearchenabled' => 'Advanced Search',
            'demo' => 'Demo'
        ];
    }
    /**
     * To create an institution
     * @return boolean[]|string[]|boolean[]|boolean[]
     */
    public function createInstitution()
    {       

        $this->institutionlogo = UploadedFile::getInstance($this, 'institutionlogo'); 
        if (UploadedFile::getInstance($this, 'institutionlogo')) {
            
            $institutionLogo = UploadedFile::getInstance($this, 'institutionlogo');
            $filePath = 'institution/'.Yii::$app->params['image']['institution']['institutionImage'];
            $tempName = $institutionLogo->tempName;
            $uploadFilename = $institutionLogo->name;
            $thumbnailNamePath = 'institution/'.Yii::$app->params['image']['institution']['thumbnailImage'];
            $imageType = 'institution';

            if (!$uploadResponse = yii::$app->fileUploadHelper->uploader($uploadFilename, $filePath, $tempName, $thumbnailNamePath, $imageType )) {
                return ['success' => false, 'errors' => 'Upload failed,Please try again'];
            }
            $this->institutionlogo = $uploadResponse['thumbnail'];
        }
        $this->createduser = yii::$app->user->identity->id;
        $this->createddate = date('Y-m-d H:i:s');
        if($this->demo && $this->demo_expiry) {
            $this->demo_expiry = date('Y-m-d', strtotimeNew($this->demo_expiry));
        } else {
            $this->demo_expiry = null;
        }
        $this->active = true;
        if ($this->save(false)) {

            $allFilters = ExtendedContactfilters::find()->all();
            $feedBackType = ExtendedFeedbacktype::findOne(1); // to find type general feedback;
            if (!empty($feedBackType)) {
                $insFeedmodel = new ExtendedInstitutionfeedbacktype();
                $insFeedmodel->feedbacktypeid = $feedBackType->feedbacktypeid;
                $insFeedmodel->institutionid = $this->id;
                $insFeedmodel->active = 1;
                $insFeedmodel->order = 1; 
                if (!$insFeedmodel->save()) {
                    $this->delete();
                    yii::error('Failed inserting default feedback type'.print_r($insFeedmodel->getErros(),true));
                    return ['success' => false, 'errors' => 'Something went wrong please try again'];
                }
            }
            $institutioncontactfiltersArray = [];
            //print_r($allFilters);exit;
            if(!empty($allFilters)) {
                foreach ($allFilters as $t => $y) {
                    if($this->tagcloud == 1 && $y->contactfilterid == ExtendedInstitutioncontactfilters::CONTACT_FILTER_TAGSEARCH) {
                        $institutioncontactfiltersArray[$t]['institutionid'] = $this->id;
                        $institutioncontactfiltersArray[$t]['contactfilterid'] = $y->contactfilterid;
                    }
                    if($this->institutiontype == self::INSTITUTION_TYPE_CHURCH && $y->contactfilterid == ExtendedInstitutioncontactfilters::CONTACT_FILTER_FAMILYUNIT) {
                        $institutioncontactfiltersArray[$t]['institutionid'] = $this->id;
                        $institutioncontactfiltersArray[$t]['contactfilterid'] = $y->contactfilterid;
                    }
                    if($y->contactfilterid == ExtendedInstitutioncontactfilters::CONTACT_FILTER_BLOODGROUP) {
                        $institutioncontactfiltersArray[$t]['institutionid'] = $this->id;
                        $institutioncontactfiltersArray[$t]['contactfilterid'] = $y->contactfilterid;
                    }
                    if($y->contactfilterid == ExtendedInstitutioncontactfilters::CONTACT_FILTER_BATCH) {
                        $institutioncontactfiltersArray[$t]['institutionid'] = $this->id;
                        $institutioncontactfiltersArray[$t]['contactfilterid'] = $y->contactfilterid;
                    }
                }
            }
            
            $sql = 'select dashboardid from dashboard';
            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();
            try {
                $dashboardItem = $db->createCommand($sql)->queryColumn();
                $count = 1;
                $dataArray = [];
                foreach ($dashboardItem as $key => $value) {
                    $dataArray[$key]['dashboardid'] = $value;
                    $dataArray[$key]['institutionid'] = $this->id;
                    $dataArray[$key]['isactive'] = false;
                    $dataArray[$key]['sortorder'] = $count;
                    $count++;
                }
                $db->createCommand()->batchInsert('institutiondashboard', [
                    'dashboardid', 'institutionid', 'isactive', 'sortorder'], 
                        $dataArray)->execute();
                if(!empty($institutioncontactfiltersArray)) {
                    $db->createCommand()->batchInsert('institutioncontactfilters', [
                     'institutionid', 'contactfilterid'], 
                        $institutioncontactfiltersArray)->execute();
                }
                $transaction->commit();
            } catch (Exception $e) {
                $transaction->rollBack();
                $this->delete();
                 return ['success' => false, 'errors' => 'Something went wrong please try again'];
            }
                return ['success' => true];
        } else {
                return ['success' => false, 'errors' => $this->getErrors()];
        }
    }
    /**
     * To update the image of an institution
     * @param unknown $oldImage
     * @return boolean[]|string[]|boolean[]|boolean[]
     */
    public function updateInstitution($oldImage)
    {   
        $this->institutionlogo = UploadedFile::getInstance($this, 'institutionlogo');
        if (UploadedFile::getInstance($this, 'institutionlogo')) {
            
            $filePath = 'institution/'.Yii::$app->params['image']['institution']['institutionImage'];
            $tempName = $this->institutionlogo->tempName;
            $uploadFilename = $this->institutionlogo->name;
            $thumbnailNamePath = 'institution/'.Yii::$app->params['image']['institution']['thumbnailImage'];
            $imageType = 'institution';
            if (!$uploadResponse = yii::$app->fileUploadHelper->uploader($uploadFilename, $filePath, $tempName, $thumbnailNamePath, $imageType, true, $oldImage )) {
                return ['success' => false, 'errors' => 'Upload failed,Please try again'];
            }
            $this->institutionlogo = $uploadResponse['thumbnail'];
        } else {
            $this->institutionlogo = $oldImage;
        }
        if($this->demo && $this->demo_expiry) {
            $this->demo_expiry = date('Y-m-d', strtotimeNew($this->demo_expiry));
        } else {
            $this->demo_expiry = null;
        }
        if ($this->update(false)) {

            $allFilters = ExtendedContactfilters::find()->all();
            $institutioncontactfiltersArray = [];
            if(!empty($allFilters)) {
                foreach ($allFilters as $t => $y) {
                    if($this->tagcloud == 1 && $y->contactfilterid == ExtendedInstitutioncontactfilters::CONTACT_FILTER_TAGSEARCH) {
                        $institutioncontactfiltersArray[$t]['institutionid'] = $this->id;
                        $institutioncontactfiltersArray[$t]['contactfilterid'] = $y->contactfilterid;
                    }
                    if($this->institutiontype == self::INSTITUTION_TYPE_CHURCH && $y->contactfilterid == ExtendedInstitutioncontactfilters::CONTACT_FILTER_FAMILYUNIT) {
                        $institutioncontactfiltersArray[$t]['institutionid'] = $this->id;
                        $institutioncontactfiltersArray[$t]['contactfilterid'] = $y->contactfilterid;
                    }
                    if($y->contactfilterid == ExtendedInstitutioncontactfilters::CONTACT_FILTER_BLOODGROUP) {
                        $institutioncontactfiltersArray[$t]['institutionid'] = $this->id;
                        $institutioncontactfiltersArray[$t]['contactfilterid'] = $y->contactfilterid;
                    }
                    if($y->contactfilterid == ExtendedInstitutioncontactfilters::CONTACT_FILTER_BATCH) {
                        $institutioncontactfiltersArray[$t]['institutionid'] = $this->id;
                        $institutioncontactfiltersArray[$t]['contactfilterid'] = $y->contactfilterid;
                    }
                }
            }
            if(!empty($institutioncontactfiltersArray)) {
                $db = Yii::$app->db;
                $transaction = $db->beginTransaction();
                try {
                    
                        $db->createCommand('Delete from institutioncontactfilters where institutionid = :institutionid ')->bindValue(':institutionid',$this->id)->execute();
                        $db->createCommand()->batchInsert('institutioncontactfilters', [
                         'institutionid', 'contactfilterid'], 
                            $institutioncontactfiltersArray)->execute();
                    $transaction->commit();
                } catch (Exception $e) {
                    yii::error('Error occured while updating institutioncontactfilters');
                    $transaction->rollBack();
                    return ['success' => false, 'errors' => 'Something went wrong please try again'];
                }
            }
            return ['success' => true];
        }elseif (empty($this->getErrors)) {
           return ['success' => true];
        } else {
            return ['success' => false, 'errors' => $this->getErrors()];
        }
    }

    /**
     * To get the country
     */
    public function getCountry()
    {
        return ArrayHelper::map(
            ExtendedCountry::find()
            ->select('countryid, CountryName')
            ->orderBy('CountryName')->all(),
            'countryid',
            'CountryName'
        );

    }
    /**
     * To get the timezone
     */
    public function getTimeZone()
    {
        return ArrayHelper::map(
            ExtendedTimeZoneInfo::find()
            ->select(['displaystring', 'timezonename'])
            ->orderBy('displaystring')->all(),
            'displaystring',
            'timezonename'
        );

    }
    /**
     * To get the institution types
     */
    public function getInstitutionTypes()
    {
        return ArrayHelper::map(
            ExtendedInstitutionType::find()
            ->select(['institutiontypeid', 'institutiontype'])
            ->orderBy('institutiontype')->all(),
            'institutiontypeid',
            'institutiontype'
        );

    }
    /**
     * To upload a file
     * @param unknown $file
     * @return boolean
     */
    protected function upload($file)
    {
        if ($this->validate()) {
            $this->institutionlogo->saveAs($file);
            return true;
        } else {
            return false;
        }
    }
    protected function processImage($fileName, $toPath, $thumbnailName)
    {
        try {
            Image::thumbnail($fileName, 120, 120)
            ->save($toPath.$thumbnailName, ['quality' => 90]);
        } catch (Exception $e) {
            yii::error($e->getMessage());
        }
        
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAffiliatedinstitutions()
    {
        return $this->hasMany(ExtendedAffiliatedinstitution::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttendances()
    {
        return $this->hasMany(ExtendedAttendance::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBillReceipts()
    {
        return $this->hasMany(ExtendedBillReceipt::className(), ['institutionId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBills()
    {
        return $this->hasMany(ExtendedBills::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBillseendetails()
    {
        return $this->hasMany(ExtendedBillseendetails::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBirthdayAnniversarySeendetails()
    {
        return $this->hasMany(ExtendedBirthdayAnniversarySeendetails::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCarts()
    {
        return $this->hasMany(ExtendedCart::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommittees()
    {
        return $this->hasMany(ExtendedCommittee::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommitteePeriods()
    {
        return $this->hasMany(ExtendedCommitteePeriod::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommitteegroups()
    {
        return $this->hasMany(ExtendedCommitteegroup::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConversationtopics()
    {
        return $this->hasMany(ExtendedConversationtopic::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeleteMembers()
    {
        return $this->hasMany(ExtendedDeleteMember::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeleteUsercredentials()
    {
        return $this->hasMany(ExtendedDeleteUsercredentials::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeleteUsermembers()
    {
        return $this->hasMany(ExtendedDeleteUsermember::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDesignations()
    {
        return $this->hasMany(ExtendedDesignation::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDevicedetails()
    {
        return $this->hasMany(ExtendedDevicedetails::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvents()
    {
        return $this->hasMany(ExtendedEvents::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventseendetails()
    {
        return $this->hasMany(ExtendedEventseendetails::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventsentdetails()
    {
        return $this->hasMany(ExtendedEventsentdetails::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFamilyunits()
    {
        return $this->hasMany(ExtendedFamilyunit::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFeedbacks()
    {
        return $this->hasMany(ExtendedFeedback::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstituteCountry()
    {
        return $this->hasOne(ExtendedCountry::className(), ['countryid' => 'countryid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreateduser0()
    {
        return $this->hasOne(ExtendedUsercredentials::className(), ['id' => 'createduser']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitutiontype0()
    {
        return $this->hasOne(ExtendedInstitutiontype::className(), ['institutiontypeid' => 'institutiontype']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModifieduser0()
    {
        return $this->hasOne(ExtendedUsercredentials::className(), ['id' => 'modifieduser']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitutionPaymentGateways()
    {
        return $this->hasMany(ExtendedInstitutionPaymentGateways::className(), ['institutionId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitutioncontactfilters()
    {
        return $this->hasMany(ExtendedInstitutioncontactfilters::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitutiondashboards()
    {
        return $this->hasMany(ExtendedInstitutiondashboard::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitutionfeedbacktypes()
    {
        return $this->hasMany(ExtendedInstitutionfeedbacktype::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitutionprivileges()
    {
        return $this->hasMany(ExtendedInstitutionprivilege::className(), ['InstitutionID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitutionstaffdesignations()
    {
        return $this->hasMany(ExtendedInstitutionstaffdesignation::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMembers()
    {
        return $this->hasMany(ExtendedMember::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotificationlogs()
    {
        return $this->hasMany(ExtendedNotificationlog::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotificationsentdetails()
    {
        return $this->hasMany(ExtendedNotificationsentdetails::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(ExtendedOrders::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrayerrequests()
    {
        return $this->hasMany(ExtendedPrayerrequest::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProperties()
    {
        return $this->hasMany(ExtendedProperty::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPropertycategories()
    {
        return $this->hasMany(ExtendedPropertycategory::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoles()
    {
        return $this->hasMany(ExtendedRole::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRolecategories()
    {
        return $this->hasMany(ExtendedRolecategory::className(), ['InstitutionID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoleprivileges()
    {
        return $this->hasMany(ExtendedRoleprivilege::className(), ['InstitutionID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSuccessfullalbumsents()
    {
        return $this->hasMany(ExtendedSuccessfullalbumsent::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSuccessfulleventsents()
    {
        return $this->hasMany(ExtendedSuccessfulleventsent::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSurveys()
    {
        return $this->hasMany(ExtendedSurvey::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSurveycredentials()
    {
        return $this->hasOne(ExtendedSurveyCredentials::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaxes()
    {
        return $this->hasMany(ExtendedTax::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTempmembers()
    {
        return $this->hasMany(ExtendedTempmember::className(), ['temp_institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTempmembermails()
    {
        return $this->hasMany(ExtendedTempmembermail::className(), ['temp_institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTestusermembers()
    {
        return $this->hasMany(ExtendedTestusermember::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTitles()
    {
        return $this->hasMany(ExtendedTitle::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsercredentials()
    {
        return $this->hasMany(ExtendedUsercredentials::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsermembers()
    {
        return $this->hasMany(ExtendedUsermember::className(), ['institutionid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserprofiles()
    {
        return $this->hasMany(ExtendedUserprofile::className(), ['institutionid' => 'id']);
    }
    /**
     * To get the details of 
     * an institution
     * @param unknown $institutionId
     * @return boolean
     */
    public static function getInstitutionData($institutionId)
    {
    	try {
    		$institutionDetails = Yii::$app->db->createCommand(
    				"CALL get_institution(:institutionid)")
    				->bindValue(':institutionid' , $institutionId )
    				->queryAll();
    		return $institutionDetails;
    		
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * to get the details of
     * institution details of a user
     * @param $userId int
     */
    public static function getUserInstitutions($userId)
    {
    	try {
    		$userInstitutions = Yii::$app->db->createCommand(
    				"CALL getuserinstitutions(:userid)")
    				->bindValue(':userid' , $userId )
    				->queryAll();
    		return $userInstitutions;
    		
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * To get the details of 
     * an institution
     * @param unknown $institutionId
     * @return \yii\db\false|boolean
     */
    public static function getInstitutionDetails($institutionId)
    {
    	try {
    		$institutionDetails = Yii::$app->db->createCommand('SELECT * FROM institution WHERE id=:institutionid ')
						    		->bindValue(':institutionid',$institutionId)
						    		->queryOne();
    		return $institutionDetails;
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * Chnages the institution
     * @param $userId int
     * @param $institutionId int
     */
    public static function changeInstitution($userId,$institutionId)
    {
    	try {
    		$institutionSettings = ExtendedSettings::getUserSettings($userId, $institutionId);
    		return $institutionSettings;
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * update user institution details
     * @param $userId int
     * @param $institutionId int
     * @return boolean
     */
    public static function updateUserInstitution($userId,$institutionId,$newUsertype)
    {
    	try {
    		$updateInstitution = Yii::$app->db->createCommand('UPDATE usercredentials SET 
    								institutionid = :institutionid, initiallogin = 1, usertype=:type WHERE id = :userid')
    		    ->bindValue(':institutionid',$institutionId)
    			->bindValue(':userid',$userId)
                ->bindValue(':type',$newUsertype)
    			->execute();
    		return true;
    		
    		
    	} catch (Exception $e) {
    		return false;
    	}	
    }
    /**
     * update institution details
     * for device
     * @param $userId int
     * @param $institutionId int
     * @return boolean
     */
    public static function updateInstitutionForDevice($userId,$institutionId)
    {
    	try {
    		$updateInstitution = Yii::$app->db->createCommand('UPDATE devicedetails SET
    				institutionid = :institutionid WHERE userid = :userid AND active=1')
    		    	->bindValue(':institutionid',$institutionId)
    		    	->bindValue(':userid',$userId)
    		    	->execute();
    		
    	} catch (Exception $e) {
    		false;
    	}
    }
    
  /**
   * To get the institution feature settings
   * @param $institutionId int
   * @return $institutionFeatureData 
   */
    public static function getInstitutionFeatureDetails($institutionId)
    {
    	try {
    		$institutionFeatureData = Yii::$app->db->createCommand('SELECT feedbackenabled,paymentoptionenabled,
				    				prayerrequestenabled,moreenabled,advancedsearchenabled,tagcloud FROM  institution 
				    				WHERE id=:institutionid ')
    		->bindValue(':institutionid',$institutionId)
    		->queryOne();
    		return $institutionFeatureData;
    
    	} catch (Exception $e) {
    		return false;
    	}
    	
    }
    /**
     * Select institutions
     * @param $institutionId int
     * @return $institutionResponse
     */
    public static function getInstitution($institutionId)
    {
    	try {
    		$institutionResponse = Yii::$app->db->createCommand(
    				"CALL get_institution(:institutionid)")
    				->bindValue(':institutionid' , $institutionId )
    				->queryOne();
    		return $institutionResponse;
    		
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * To get feedback types
     * under institution
     * @param $institutionId int
     */
    public static function institutionFeedbackTypes($institutionId,$isActive)
    {
    	try {
    		$feedbackTypes = Yii::$app->db->createCommand(
    				"CALL institution_feedbacktype_get(:institutionid,:isactive)")
    				->bindValue(':institutionid' , $institutionId )
    				->bindValue(':isactive', $isActive)
    				->queryAll();
    				return $feedbackTypes;
    		
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * To get the user 
     * institution features
     * @param $instituionId int
     */
    public static function getInstitutionFeatures($institutionId)
    {
        $response = new \stdClass();
        $dataArr = [];
        try {
            $DashboardList = Yii::$app->db->createCommand("CALL getinstitutionfeatures(:institutionid)")
               ->bindValue(':institutionid' , $institutionId )
               ->queryAll();
            if (count($DashboardList) > 0) {
                $dataArr = $DashboardList;
            }
            $response->value = $dataArr;
            $response->Status = true;
        } catch (\Exception $e) {
            $response->ErrorMessage = "Institution Selection Failed!";
            $response->Status = false;
            $response->ErrorCode = 1;
            yii::error($e->getMessage());
        }
        
        return $response;
    }
   
   /**
    * To get institution name
    */
    public static function getInstitutionName($institutionId)
    {
    	try {
    		$institutionName = Yii::$app->db->createCommand('SELECT name FROM  institution
				    				WHERE id=:institutionid and active=1 ')
    						    				->bindValue(':institutionid', $institutionId)
    						    				->queryOne();
    		return $institutionName;
    		
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * To add dashboard items
     * @param unknown $dataArray
     * @param unknown $institutionId
     * @return boolean|number
     */
    public static function addDashBoardItems($dataArray, $institutionId) 
    {   
        $sql = 'Delete from institutiondashboard where institutionid = :institutionId';
        $db = Yii::$app->db;
        // the transaction is started on the master connection
        $transaction = $db->beginTransaction(); 
        try {
            $db->createCommand($sql)->bindValue(':institutionId', (int)$institutionId)->execute();
            $response = $db->createCommand()->batchInsert('institutiondashboard', [
                    'institutionid', 'dashboardid', 'sortorder', 'isactive', ], 
                        $dataArray)->execute();
            $transaction->commit();
        } catch (yii\db\Exception $e) {
            $transaction->rollBack();
            yii::error($e->getMessage());
            return false;
        }
        return $response;
          
    }
    /**
     * Returns institution type name
     * @param bool $status
     * @return array|mixed
     */
    public static function getInstitutionTypeNameFromCode($code = null)
    {
        
        $types = [
            self::INSTITUTION_TYPE_CHURCH => Yii::t('app', 'Church'),
            self::INSTITUTION_TYPE_CLUB => Yii::t('app', 'Club'),
            self::INSTITUTION_TYPE_APPARTMENT => Yii::t('app', 'Appartment'),
            self::INSTITUTION_TYPE_EDUCATION => Yii::t('app', 'Education')
 
        ];
        return isset($code) ? ArrayHelper::getValue($types, $code) : $types;
    }
    
  /**
   *  To get the list of the institutions
   * @return mixed
   */
    public static function getAllInstitutions(){
    	try {
    		$data = Yii::$app->db->createCommand(
    				"CALL getinstitutions() ")
    				->queryAll();
    		return $data;
    		 
    	} catch (Exception $e) {
    		return false;
    	}
    }
   /**
    * To get the associated
    * institution
    * @param unknown $currentMemberId
    * @param unknown $memberId
    * @param unknown $userType
    * @param unknown $institutionId
    * @param unknown $currentUserType
    * @return boolean|boolean
    */
 /*   public static function getAssociatedInstitutions($currentMemberId, $memberId, $userType, $institutionId, $currentUserType)
    {   

        $response = [];
    	try {
    		    $currentUserUserId = Yii::$app->db->createCommand("select userid from usermember where memberid=:memberid and usertype=:usertype and institutionid = :institutionId")
				    ->bindValue(':memberid', $currentMemberId)
				    ->bindValue(':usertype', $currentUserType)
                    ->bindValue(':institutionId', $institutionId)
				    ->queryScalar();
    		    $userUserId = Yii::$app->db->createCommand("select userid from usermember where memberid=:memberid and usertype=:usertype")
				    ->bindValue(':memberid', $memberId)
				    ->bindValue(':usertype', 'M')
				    ->queryScalar();
    		if($currentUserUserId  && $userUserId) {
    			$response = ExtendedInstitution::getAssociatedInstitutionDetails($currentUserUserId, $userUserId, $institutionId);
    		} elseif ($currentUserUserId && !$userUserId) {
    			$response = ExtendedInstitution::getInstName($memberId);
    		}
    	} catch (Exception $e) {
    		yii::error($e->getMessage());
    	}
        return $response;
    }*/

   /**
    * To get the associated
    * institution
    * @param unknown $currentMemberId
    * @param unknown $memberId
    * @param unknown $userType
    * @param unknown $institutionId
    * @param unknown $currentUserType
    * @return boolean|boolean
    */
    public static function getAssociatedInstitutions($currentMemberId, $memberId, $userType, $institutionId, $currentUserType)
    {   

        $response = [];
        $qry = "select userid from usermember where memberid=:memberid and usertype=:usertype and institutionid = :institutionId";

        $loggedUserId = Yii::$app->db->createCommand($qry)
                    ->bindValue(':memberid', $currentMemberId)
                    ->bindValue(':usertype', $currentUserType)
                    ->bindValue(':institutionId', $institutionId)
                    ->queryScalar();
        $qry2 = "select userid from usermember where memberid=:memberid and usertype=:usertype";

        $memberUserId = Yii::$app->db->createCommand($qry2)
                    ->bindValue(':memberid', $memberId)
                    ->bindValue(':usertype', $userType)
                    ->queryScalar();
        if($loggedUserId  && $memberUserId) {
            $a1 = self::getAssociatedInstitutionDetails($loggedUserId, $currentUserType);        
            $a2 = self::getAssociatedInstitutionDetails($memberUserId, $userType);
            $response = array_intersect_key($a1,$a2);
        } elseif ($loggedUserId && !$memberUserId) {
            $response = ExtendedInstitution::getInstName($memberId);
        }
        return $response;      
    }
    
    /**
     * Get the details of associated institution
     * institutions
     * @param unknown $currentUserUserId
     * @param unknown $userUserId
     * @return boolean
     */
    public static function getAssociatedInstitutionDetails($memberId, $usertype)
    {
         return (new \yii\db\Query())
            ->select("um.institutionid,i.name AS institutionname")
            ->from('usermember um')
            ->innerJoin('institution i', 'i.id = um.institutionid')
            ->where(['um.userid' => $memberId])
            ->andWhere(['i.active' => 1])
            ->indexBy('institutionid')
            ->all();
    }

    /**
     * Get the details of associated institution
     * institutions
     * @param unknown $currentUserUserId
     * @param unknown $userUserId
     * @return boolean
     */
   /* public static function getAssociatedInstitutionDetails($currentUserUserId, $userUserId, $institutionId)
    {
    	try {
    		$response = Yii::$app->db->createCommand("
    				CALL get_associatedinstitutions(:userid,:institutionId)")
        				->bindValue(':userid', $userUserId)
                        ->bindValue(':institutionId', $institutionId)
        				->queryAll();
        	return $response;
    	} catch (Exception $e) {
    		return false;
    	}
    }*/
   /**
    * To get the institution name
    * @param unknown $institutionId
    * @return boolean
    */
    public static function getInstName($memberId)
    {

    	try {
    		$institutionName = Yii::$app->db->createCommand('SELECT i.name as institutionname  FROM 
                                                             institution as i inner join 
                                                             member as m on i.id=m.institutionid
				    				                         WHERE m.memberid=:memberid '
                                                         )
    					    		->bindValue(':memberid', $memberId)
    					    		->queryAll();
    		return $institutionName;
    	
    	} catch (Exception $e) {
    		return false;
    	}
    }
   /**
    * To get institution details
    * by memberid
    * @param unknown $memberId
    * @return \yii\db\false|boolean
    */
    public static function getInstitutionDetailsByMemberId($memberId)
    {
    	try {
    		
    		$institutionDetails = Yii::$app->db->createCommand("
    				CALL get_institutiondetails_by_memberid(:memberid)")
        				->bindValue(':memberid', $memberId)
        				->queryOne();
    		return $institutionDetails;
    		
    	} catch (Exception $e) {
    		return false;
    	}
    }
}
