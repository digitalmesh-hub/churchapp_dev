<?php
namespace api\modules\v3\models;

use yii;
use yii\base\Model;
use yii\base\ErrorException;
use common\components\EmailHandlerComponent;

class GalleryManager extends Model
{
    /**
     * Adds the album image.
     * @param $albumImageList array
     * @return boolean value
    */
    public function addAlbumImage($albumImageList){
        try {
            Yii::$app->db->createCommand("INSERT INTO albumimage(imageid,albumid,imageurl, caption,iscover,createdby,createddatetime,thumbnail)
                VALUES(:imageid, :albumid, :imageurl, :caption, :isalbumcover, 
                :createdby, :createddate, :thumbnail)")
                ->bindValue(':imageid', $albumImageList['pendingImageId'])
                ->bindValue(':albumid', $albumImageList['albumId'])
                ->bindValue(':imageurl', $albumImageList['ImageUrl'])
                ->bindValue(':caption', $albumImageList['caption'])
                ->bindValue(':isalbumcover', $albumImageList['isAlbumCover'])
                ->bindValue(':createdby', $albumImageList['createdBy'])
                ->bindValue(':createddate', $albumImageList['createdDateTime'])
                ->bindValue(':thumbnail', $albumImageList['thumbnail'])
                ->execute();
            return true;
        } 
        catch (ErrorException $e) {
            yii::error($e->getMessage());
            return false;
        }
    }

    /**
     * Get pending images
     * @param $photoId string
     * @param $albumId int
     * @return $pendingImages array
     */
    public function getPendingImagesByAlbumId($photoId, $albumId)
    {   
        $pendingImages  = [];
        try {
            $pendingImages = Yii::$app->db->createCommand("SELECT * FROM pending_album_image WHERE albumid = :albumid AND pending_imageid= :photoid")
                ->bindValue(':albumid', $albumId)
                ->bindValue(':photoid', $photoId)
                ->queryOne();      
        } catch (\Exception $e) {
            yii::error($e->getMessage());
        }    
        return $pendingImages; 
    }

    /**
     * Add album cover pic
     * @param coverPhotoId string
     * @param $albumId int
     * @return boolean value
     */
    public function addAlbumCoverPic($albumId, $coverPhotoId, $userId, $createdDateTime)
    {
        try {
            Yii::$app->db->createCommand("CALL setcoverpic(:coverPhotoId, :albumId, :modifiedDateTime, :createdDateTime, :userId)")
                ->bindValue(':albumId', (int)$albumId)
                ->bindValue(':coverPhotoId', $coverPhotoId)
                ->bindValue(':userId', $userId)
                ->bindValue(':createdDateTime', $createdDateTime)
                ->bindValue(':modifiedDateTime', $createdDateTime)
                ->execute();
            return true;     
        } catch (ErrorException $e) {
            yii::error($e->getMessage());
            return false;
        }   
    }

    /**
     * Update album modifiedDateTime
     * @param coverPhotoId string
     * @param $albumId int
     * @return boolean value
     */
    public function updateAlbumByAlbumId($albumId, $createdDateTime)
    {
        try {
            Yii::$app->db->createCommand("UPDATE album SET modifieddatetime = :modifiedDate WHERE albumid = :albumId")
                ->bindValue(':albumId', (int)$albumId)
                ->bindValue(':modifiedDate', $createdDateTime)
                ->execute();
            return true;     
        } catch (ErrorException $e) {
            yii::error($e->getMessage());
            return false;
        }   
    }

     /**
     * Get userId of member
     * @param $memberId int
     * @return userId int
     */
    public function getUserIdByMemberId($memberId)
    {
        try {
            $userId = Yii::$app->db->createCommand("SELECT userid FROM usermember WHERE memberid = :memberid")
                ->bindValue(':memberid', (int)$memberId)
                ->queryOne();

            if(!empty($userId)){
                return $userId['userid'];
            }
            else{
                return false;
            }
        } catch (ErrorException $e) {
            return false;
        }   
    }

    public function sentApprovedAlbum($userId, $eventId){

        $note = '';
        $activityDate = '';

        try{
            $adminEmail = Yii::$app->params['tempEmail'];
            $institutionId = Yii::$app->user->identity->institutionid;
            $institutionDetails = Yii::$app->user->identity->institution;
            $institutionName = $institutionDetails['name'];
            $logo = $institutionDetails['institutionlogo'];
            //Institution logo
            if(!empty($logo)){
                $logo = Yii::$app->params['imagePath'].$logo;
            }
            else{
                $logo = Yii::$app->params['imagePath'].'/institution/institution-icon-grey.png';
            }
            $memberDetails = $this->getMemberDetails($userId, $institutionId);
            $eventDetails = $this->getEventDetails($eventId);
            if($eventDetails){
                $activityDate = date_format(date_create($eventDetails['activitydate']),Yii::$app->params['dateFormat']['viewDateFormat']);
                $note = $eventDetails['notehead'];
            }

            if($memberDetails && is_array($memberDetails)){
                $subject ='Album Approval '.$note.' '.$activityDate;
                $attach = '';
                $ccAddress = '';
                $receiver = $memberDetails['emailid'];
                //$receiver = 'midhun@dmmail.com'; Hard coded
                $mailContent['template'] = 'album-approval-mail';
                $mailContent['name'] = $memberDetails['username'];
                $mailContent['institutionname'] = $institutionName;
                $mailContent['logo'] = $logo;
                $mailobj = new EmailHandlerComponent();
                $temp =  $mailobj->sendEmail($adminEmail, $receiver, $ccAddress, $subject, $mailContent, $attach);
                return $temp;
            }
        }
        catch(ErrorException $e){
            yii::error($e->getMessage());
            return false;
        }
    }

    /**
     * Get member details
     * @param $institutionId int
     * @param $userId int
     * @return $memberDetails array
     */
    public function getMemberDetails($userId, $institutionId)
    {
        try {
            //$userId=1861; Hard coded
            $memberDetails = Yii::$app->db->createCommand("CALL get_member_email(:userid, :institutionid)")
                ->bindValue(':userid', (int)$userId)
                ->bindValue(':institutionid', (int)$institutionId)
                ->queryOne();
            if(!empty($memberDetails)){
                return $memberDetails;
            }     
            else{
                return true;
            }
        } catch (ErrorException $e) {
            yii::error($e->getMessage());
            return false;
        }   
    }

    /**
     * Get event details
     * @param $eventId int
     * @return $eventDetails array
     */
    public function getEventDetails($eventId)
    {
        try {
            $eventDetails = Yii::$app->db->createCommand("SELECT * FROM events WHERE id = :eventid")
                ->bindValue(':eventid', $eventId)
                ->queryOne();

            if(!empty($eventDetails)){
                return $eventDetails;
            }     
            else{
                return false;
            }
        } catch (ErrorException $e) {
            return false;
        }   
    }

    /**
     * Delete pending images
     * @param $photoId string
     * @param $albumId int
     * @return boolean value
     */
    public function deletePendingImages($photoId, $albumId)
    {
        try {
            Yii::$app->db->createCommand("SET sql_safe_updates=0; DELETE from pending_album_image where albumid = :albumid and pending_imageid= :photoid")
                ->bindValue(':albumid', $albumId)
                ->bindValue(':photoid', $photoId)
                ->execute();
            return true;
        } catch (ErrorException $e) {
            return false;
        }   
    }

    /**
     * Delete pending image notification
     * @param $createdBy int
     * @param $albumId int
     * @return boolean value
     */
    public function deletePendingImageNotification($albumId, $createdBy)
    {
        try {
            Yii::$app->db->createCommand("SET sql_safe_updates=0; DELETE FROM pendingimagenotification WHERE albumid= :albumid and uploadedby= :createdby")
                ->bindValue(':albumid', $albumId)
                ->bindValue(':createdby', $createdBy)
                ->execute();
            return true;
        } catch (ErrorException $e) {
            return false;
        }   
    }

    /**
     * Delete pending images sent notification
     * @param $createdBy int
     * @param $albumId int
     * @return boolean value
     */
    public function deletePendingImageSentNotification($albumId, $createdBy)
    {
        try {
            Yii::$app->db->createCommand("SET sql_safe_updates=0; DELETE FROM pendingimagenotificationsent WHERE albumid= :albumid and uploadedby = :createdby")
                ->bindValue(':albumid', $albumId)
                ->bindValue(':createdby', $createdBy)
                ->execute();
            return true;
        } catch (ErrorException $e) {
            return false;
        }  
    }
}