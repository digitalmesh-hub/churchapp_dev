<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\DeleteMember;

/**
 * This is the model class for table "delete_member".
 *
 * @property int $memberid
 * @property int $institutionid
 * @property string $memberno
 * @property string $membershiptype
 * @property string $membersince
 * @property string $firstName
 * @property string $middleName
 * @property string $lastName
 * @property string $business_address1
 * @property string $business_address2
 * @property string $business_address3
 * @property string $business_district
 * @property string $business_state
 * @property string $business_pincode
 * @property string $member_dob
 * @property string $member_mobile1
 * @property string $member_mobile2
 * @property string $member_musiness_Phone1
 * @property string $member_business_Phone2
 * @property string $member_residence_Phone1
 * @property string $member_residence_Phone2
 * @property string $member_email
 * @property string $spouse_firstName
 * @property string $spouse_middleName
 * @property string $spouse_lastName
 * @property string $spouse_dob
 * @property string $dom
 * @property string $spouse_mobile1
 * @property string $spouse_mobile2
 * @property string $spouse_email
 * @property string $residence_address1
 * @property string $residence_address2
 * @property string $residence_address3
 * @property string $residence_district
 * @property string $residence_state
 * @property string $residence_pincode
 * @property string $member_pic
 * @property string $spouse_pic
 * @property string $app_reg_member
 * @property string $app_reg_spouse
 * @property int $active
 * @property string $businessemail
 * @property int $membertitle
 * @property int $spousetitle
 * @property string $membernickname
 * @property string $spousenickname
 * @property string $lastupdated
 * @property string $createddate
 *
 * @property Institution $institution
 * @property Title $membertitle0
 * @property Title $spousetitle0
 */
class ExtendedDeleteMember extends DeleteMember
{
	/**
	 * To get the deleted contacts
	 * @param unknown $institutionId
	 * @param unknown $lastUpdatedOn
	 * @return mixed
	 */
	public static function getDeletedContacts($institutionId,$lastUpdatedOn)
	{
		try {
			$deletedContacts = Yii::$app->db->createCommand("
					select memberid from delete_member where institutionid=:institutionid and (createddate >= :lastupdatedon or :lastupdatedon is null)")
					->bindValue(':institutionid', $institutionId)
					->bindValue(':lastupdatedon', $lastUpdatedOn)
					->queryAll();
			return $deletedContacts;
		} catch (\Exception $e) {
			return false;
		}
	}
}
