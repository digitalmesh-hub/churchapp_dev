<?php
namespace common\models\formmodels;

use yii;
use yii\base\Model;
use common\models\extendedmodels\ExtendedInstitution;
use common\models\extendedmodels\ExtendedBills;
use yii\web\UploadedFile;

/**
 * Signup form
 */
class BillFormModel extends Model
{
    public $year;
    public $month;
    public $invoice;
    public $institutionid;

    public $members = [];

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['year', 'month'], 'trim'],
            [['institutionid','year'],'integer'],
            [['month'],'integer', 'min' => 1],
            [['year', 'month', 'institutionid'], 'required'],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedInstitution::className(), 'targetAttribute' => ['institutionid' => 'id']],
            [['invoice'], 'file', 'skipOnEmpty' => false,
                'extensions' => 'xlsx, csv',
               'checkExtensionByMimeType' => false,
                'maxSize' => \Yii::$app->params['fileUploadSize']['billSize'],
                'tooBig' => \Yii::$app->params['fileUploadSize']['billSizeMsg']
            ],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'year' => 'Year',
            'month' => 'Month',
            'invoice' => 'Bill',
        ];
    }

    public function saveBill()
    {   
        $import = [];
        $batchData = [];
        $fileType = $this->invoice->getExtension();
        $temp = $this->invoice->tempName;
        $newFileName = $this->invoice->baseName.time().".".$fileType;
        $path = Yii::getAlias('@backend'.Yii::$app->params['fileUploadPath']['billUploadFilePath']);   
        $uploadFolder = $this->year."_".$this->month;       
        if (!file_exists($path.$uploadFolder)) {
            mkdir($path.$uploadFolder, 0777, true); //Make directory with year and month.
        }
        $upload = $this->upload($path, $uploadFolder,$newFileName);
        if ($upload) {
            $upFileName = Yii::getAlias('@backend'.$this->invoice);
            $billModel = new ExtendedBills();
            $this->getAllMembers();
            if ($fileType == "xlsx") {
                $import = Yii::$app->BillExcelHandler->importExcelBill($upFileName, Yii::$app->params['billExcelFields'], $billModel, $this, Yii::$app->params['billExcelRequiredFields']);
            } elseif ($fileType == "csv") {
                $import = Yii::$app->BillCsvHandler->importCsvBill($upFileName, Yii::$app->params['billCsvFields'], $billModel, $this, Yii::$app->params['billCsvRequiredFields']);
            }

            if(isset($import['status']) && $import['status'] == 'success') {
                $this->unlinkUploadedBill($upFileName);
                return [
                    'success' => true,
                    "institutionid" => $this->institutionid,
                    "year" => $this->year,
                    "month" => $this->month, 
                    'errors' => $import
                ];
            } else {
                // unlink the file
                return ['success' => false, 'errors' => $import]; 
            }
        } else {
            return ['success' => false, 'errors' => 'File upload failed.Please try again'];
        }  
    }

    protected function getAllMembers()
    {   
        if(!$this->members) {
            $this->members =  (new \yii\db\Query())
                ->select(['memberid', 'REPLACE(UCASE(newmembernum)," ","") as newmembernum'])
                ->from('member')
                ->indexBy('newmembernum')
                ->where(['institutionid' => $this->institutionid])
                ->all();
        }
    }
    
    /**
    *This function will upload the file to specified path.
    *@return boolean
    */
    protected function upload($path, $uploadFolder, $newFileName)
    {  
        if ($this->validate()) {
            $uploadPath = $path.$uploadFolder."/";
            $this->invoice->saveAs($uploadPath . $newFileName);
            $this->invoice = Yii::$app->params['fileUploadPath']['billUploadFilePath'].$uploadFolder."/".$newFileName;
            return true;
        } else {
            yii::error(" Error occured While uploading file".var_export($this->getErrors(),true));
            return false;
        }
    }
    
    public function bulkInsertBill($model, $rows,  $formData, $type ="", $tableFields =[])
    {   
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        $params = [
            ":institutionid" => $formData->institutionid,
            ":month" => $formData->month,
            ":year" => $formData->year
        ];
        $sql = "DELETE FROM billseendetails WHERE month = :month And year= :year And institutionid=:institutionid";
        try{
            //deletes related record from cheque table and receipt table
            $db->createCommand("CALL deletebills_receipt_cheque(:month, :year, :institutionid)")->bindValues($params)->execute();
            //deletes bills seen details
            $db->createCommand($sql)->bindValues($params)->execute();
            //deletes records from bills table for the same month and year,if exists for that institution
            $db->createCommand("CALL deletebills(:month, :year, :institutionid)")->bindValues($params)->execute();
            //inserts new row to bills table
            $db->createCommand()->batchInsert($model->tableName(), $tableFields, $rows)->execute();
            //updates memberid to each records 
            //handled in code itself
            //$db->createCommand("CALL setmemberid(:institutionid, :month, :year)")->bindValues($params)->execute();
            $transaction->commit();
            return['status' => "success", 'error' => "", "message" => "Successfull insertion"];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            yii::error('Error While saving bill'.$e->getMessage());
            return ["status"=>"failed", 'error'=>$e->getMessage(), 'message' => ($type == "csv") ? Yii::$app->params['csv']['csvImportTableError'] : Yii::$app->params['excel']['excelImportTableError'] ]; 
        }
        
    }
    protected function unlinkUploadedBill($filename)
    {
        if ($filename) {
            try {
                if (file_exists($filename)) {
                    chmod($filename, 0777); 
                    unlink($filename);
                }   
            } catch (\Exception $e) {
                yii::error($e->getMessage());
            }
        }

    }

    protected function cleanNewMemNumber($str)
    {
        return strtoupper(str_replace(" ","", $str));
    }

    public function getMemberId($newMemberNo)
    { 
        $key = $this->cleanNewMemNumber($newMemberNo);
        if(array_key_exists($key, $this->members)){
            return (int)$this->members[$key]['memberid'];
        }
        return null;
    }
    public static function checkBillAlreadyExist($data) {
        try{
            $sql = "SELECT 
                    * 
                FROM 
                    `uploaded_bills`
                WHERE 
                    `institution_id` = :institution_id
                AND
                    `month` = :month
                AND
                    `year` = :year";
            return Yii::$app->db->createCommand($sql)
            ->bindValue(":institution_id", $data['institutionid'])
            ->bindValue(":month", $data['month'])
            ->bindValue(":year", $data['year'])
            ->queryOne();
        } catch (\yii\db\Exception $e) {
            yii::error('Error While checking bill already exist '.$e->getMessage());
            return false;
        } catch (\Exception $e) {
            yii::error('Error While checking bill already exist '.$e->getMessage());
        }
       
    }

    public static function insertUploadedBills($data) {
        try{
           
            $uuid = $data['institutionid'].$data['month'].$data['year'].time();
            $params= [
                ':institution_id' => $data['institutionid'],
                ':month' => $data['month'],
                ':year' => $data['year'],
                ':uuid' => $uuid,
            ];
            
            $sql = "INSERT INTO 
            `uploaded_bills`
                (`institution_id`,
                `month`,
                `year`,
                `uuid`)
            VALUES 
            (
                :institution_id,
                :month,
                :year,
                :uuid
            )";
            return Yii::$app->db->createCommand($sql)
            ->bindValues($params)
            ->execute();
        } catch (\yii\db\Exception $e) {
            yii::error('Error While new bill notification data insert '.$e->getMessage());
            return false;
        } catch (\Exception $e) {
            yii::error('Error While new bill notification data insert '.$e->getMessage());
        }
    }

    public static function updateUploadedBills($data) {
        try{
            
            $uuid = $data['institutionid'].$data['month'].$data['year'].time();
            $params= [
                ':institution_id' => $data['institutionid'],
                ':month' => $data['month'],
                ':year' => $data['year'],
                ':uuid' => $uuid,
                ':remote_sync' => 0,
                ':send_notification' => 0,
                ':send_mail' => 0,
                ':send_sms' => 0,
                ':created' => $data['created'],
                ':updated' => date("Y-m-d H:i:s")
            ];
            
            $sql = "UPDATE 
                        `uploaded_bills`
                    SET
                        `institution_id` = :institution_id,
                        `month` = :month,
                        `year` = :year,
                        `uuid` = :uuid,
                        `remote_sync` = :remote_sync,
                        `send_notification` = :send_notification,
                        `send_mail` = :send_mail,
                        `send_sms` = :send_sms,
                        `created` = :created,
                        `updated` = :updated
                    WHERE 
                        `institution_id` = :institution_id
                    AND
                        `month` = :month
                    AND
                        `year` = :year";
            return Yii::$app->db->createCommand($sql)
            ->bindValues($params)
            ->execute();

        } catch (\yii\db\Exception $e) {
            yii::error('Error While new bill notification data update '.$e->getMessage());
            return false;
        } catch (\Exception $e) {
            yii::error('Error While new bill notification data update '.$e->getMessage());
        }
    }
                
}
