<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "institution".
 *
 * @property int $id
 * @property string $name
 * @property string $address1
 * @property string $address2
 * @property string $address3
 * @property string $district
 * @property string $state
 * @property string $pin
 * @property string $phone1_countrycode
 * @property string $phone1_areacode
 * @property string $phone1
 * @property string $location
 * @property string $lattitude
 * @property string $institutionlogo
 * @property string $phone2_countrycode
 * @property string $phone2
 * @property string $email
 * @property string $longitude
 * @property int $active
 * @property int $createduser
 * @property string $createddate
 * @property int $modifieduser
 * @property string $modifieddate
 * @property string $timezone
 * @property int $feedbackenabled
 * @property string $feedbackemail
 * @property int $paymentoptionenabled
 * @property int $paymenttypeid
 * @property int $prayerrequestenabled
 * @property int $institutiontype
 * @property string $prayeremail
 * @property string $url
 * @property int $countryid
 * @property int $moreenabled
 * @property string $moreurl
 * @property int $isrotary
 * @property int $tagcloud
 * @property int $advancedsearchenabled
 * @property int $demo
 * @property string $demo_expiry
 *
 * @property Affiliatedinstitution[] $affiliatedinstitutions
 * @property Attendance[] $attendances
 * @property BillReceipt[] $billReceipts
 * @property Bills[] $bills
 * @property Billseendetails[] $billseendetails
 * @property BirthdayAnniversarySeendetails[] $birthdayAnniversarySeendetails
 * @property Cart[] $carts
 * @property Committee[] $committees
 * @property CommitteePeriod[] $committeePeriods
 * @property Committeegroup[] $committeegroups
 * @property Conversationtopic[] $conversationtopics
 * @property DeleteMember[] $deleteMembers
 * @property DeleteUsercredentials[] $deleteUsercredentials
 * @property DeleteUsermember[] $deleteUsermembers
 * @property Designation[] $designations
 * @property Devicedetails[] $devicedetails
 * @property Events[] $events
 * @property Eventseendetails[] $eventseendetails
 * @property Eventsentdetails[] $eventsentdetails
 * @property Familyunit[] $familyunits
 * @property Feedback[] $feedbacks
 * @property Country $country
 * @property Usercredentials $createduser0
 * @property Institutiontype $institutiontype0
 * @property Usercredentials $modifieduser0
 * @property InstitutionPaymentGateways[] $institutionPaymentGateways
 * @property Institutioncontactfilters[] $institutioncontactfilters
 * @property Institutiondashboard[] $institutiondashboards
 * @property Institutionfeedbacktype[] $institutionfeedbacktypes
 * @property Institutionprivilege[] $institutionprivileges
 * @property Institutionstaffdesignation[] $institutionstaffdesignations
 * @property Member[] $members
 * @property Notificationlog[] $notificationlogs
 * @property Notificationsentdetails[] $notificationsentdetails
 * @property Orders[] $orders
 * @property Prayerrequest[] $prayerrequests
 * @property Property[] $properties
 * @property Propertycategory[] $propertycategories
 * @property Role[] $roles
 * @property Rolecategory[] $rolecategories
 * @property Roleprivilege[] $roleprivileges
 * @property Successfullalbumsent[] $successfullalbumsents
 * @property Successfulleventsent[] $successfulleventsents
 * @property Survey[] $surveys
 * @property Surveycredentials $surveycredentials
 * @property Tax[] $taxes
 * @property Tempmember[] $tempmembers
 * @property Tempmembermail[] $tempmembermails
 * @property Testusermember[] $testusermembers
 * @property Title[] $titles
 * @property Usercredentials[] $usercredentials
 * @property Usermember[] $usermembers
 * @property Userprofile[] $userprofiles
 */
class Institution extends \yii\db\ActiveRecord
{
    public $facebook;
    public $instagram;
    public $twitter;
    public $youtube;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'institution';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lattitude', 'longitude'], 'number'],
            [['createduser', 'modifieduser', 'paymenttypeid', 'institutiontype', 'countryid','demo'], 'integer'],
            [['createddate', 'modifieddate','demo_expiry','social_media'], 'safe'],
            [['institutiontype'], 'required'],
            [['name', 'district', 'state', 'pin', 'phone1', 'location', 'phone2'], 'string', 'max' => 45],
            [['address1', 'address2', 'address3', 'email', 'url', 'moreurl'], 'string', 'max' => 100],
            [['phone1_countrycode', 'phone2_countrycode'], 'string', 'max' => 5],
            [['phone1_areacode', 'feedbackenabled', 'paymentoptionenabled', 'prayerrequestenabled', 'moreenabled', 'isrotary', 'tagcloud', 'advancedsearchenabled'], 'string', 'max' => 4],
            [['institutionlogo'], 'string', 'max' => 200],
            [['timezone', 'feedbackemail', 'prayeremail'], 'string', 'max' => 250],
            [['countryid'], 'exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['countryid' => 'countryid']],
            [['createduser'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['createduser' => 'id']],
            [['institutiontype'], 'exist', 'skipOnError' => true, 'targetClass' => Institutiontype::className(), 'targetAttribute' => ['institutiontype' => 'institutiontypeid']],
            [['modifieduser'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['modifieduser' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'address1' => 'Address1',
            'address2' => 'Address2',
            'address3' => 'Address3',
            'district' => 'District',
            'state' => 'State',
            'pin' => 'Pin',
            'phone1_countrycode' => 'Phone1 Countrycode',
            'phone1_areacode' => 'Phone1 Areacode',
            'phone1' => 'Phone1',
            'location' => 'Location',
            'lattitude' => 'Lattitude',
            'institutionlogo' => 'Institutionlogo',
            'phone2_countrycode' => 'Phone2 Countrycode',
            'phone2' => 'Phone2',
            'email' => 'Email',
            'longitude' => 'Longitude',
            'active' => 'Active',
            'createduser' => 'Createduser',
            'createddate' => 'Createddate',
            'modifieduser' => 'Modifieduser',
            'modifieddate' => 'Modifieddate',
            'timezone' => 'Timezone',
            'feedbackenabled' => 'Feedbackenabled',
            'feedbackemail' => 'Feedbackemail',
            'paymentoptionenabled' => 'Paymentoptionenabled',
            'paymenttypeid' => 'Paymenttypeid',
            'prayerrequestenabled' => 'Prayerrequestenabled',
            'institutiontype' => 'Institutiontype',
            'prayeremail' => 'Prayeremail',
            'url' => 'Url',
            'countryid' => 'Countryid',
            'moreenabled' => 'Moreenabled',
            'moreurl' => 'Moreurl',
            'isrotary' => 'Isrotary',
            'tagcloud' => 'Tagcloud',
            'advancedsearchenabled' => 'Advancedsearchenabled',
            'demo' => 'Demo',
        ];
    }

    public function afterFind()
    {
        parent::afterFind();
        $socialMedia = is_string($this->social_media) ? json_decode($this->social_media, true) : $this->social_media;
        $this->facebook = $socialMedia['facebook'] ?? null;
        $this->instagram = $socialMedia['instagram'] ?? null;
        $this->twitter = $socialMedia['twitter'] ?? null;
        $this->youtube = $socialMedia['youtube'] ?? null;
    }
}
