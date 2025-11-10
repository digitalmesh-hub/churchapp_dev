<?php
namespace common\components;

use Yii;
use yii\base\Component;
use yii\base\ErrorException;
use yii\data\SqlDataProvider;
use common\models\extendedmodels\ExtendedPropertycategory;

class RestaurantComponent extends component
{

    /**
     * getInstitutionProperties
     * @param integer $institutionId
     * @param integer $isActive
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
    */
    public function getInstitutionProperties($institutionId, $isActive, $isService=false)
    {
        try{
            $propertyList = Yii::$app->db->createCommand("CALL get_institution_properties(:institutionid,:isactive)")
            ->bindValue(':institutionid' , $institutionId)
            ->bindValue(':isactive', $isActive)
            ->queryAll();
            if($isService){
                if(!empty($propertyList)){
                    return $propertyList;
                }
                else{
                    return true;
                }
            }
            else{
                return $propertyList;
            }
        }
        catch(ErrorException $e){
            return false;
        }     
    }

    /**
     * getAllPropertyCategoryByInstitutionId
     * @param integer $institutionId
     * @param integer $isActive
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
    */
    public function getAllPropertyCategoryByInstitutionId($institutionid, $isActive){
        $categoryList = Yii::$app->db->createCommand('SELECT propertycategoryid,category,active FROM propertycategory WHERE institutionid = :institutionId AND(active = :isActive OR :isActive is null) ORDER BY category',['institutionId' => $institutionid, 'isActive' => $isActive])
            ->queryAll();
        return $categoryList;
    }

    /**
     * savePropertyCategory
     * @param integer $institutionId
     * @param integer propertyGroupId
     * @param string $categoryName
     * @param integer $userId
     * @param integer $isActive
     * @param dateTime $createdDateTime
     * @param integer $$categoryId
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
    */
    public function savePropertyCategory($institutionId,$propertyGroupId,$categoryName,$userId,$isActive,$createdDateTime,$categoryId){
        Yii::$app->db->createCommand('INSERT INTO propertycategory(category,institutionid,propertygroupid,active,createdby,createddatetime) VALUES(:categoryName,:institutionId,:propertyGroupId,1,:createdBy,:createdDateTime)',['categoryName' => $categoryName,'institutionId' => $institutionid, 'propertyGroupId' => $propertyGroupId, 'createdBy' => $userId, 'createdDateTime' => $createdDateTime])
            ->queryAll();
        return $categoryList;
    }
}
	