<?php
namespace common\components;

use Exception;
use Yii;
use yii\base\Component;
use common\helpers\Underscore as _;

class BillExcelHandler extends Component
{

    public function importExcelBill($fileName, $fields, $model, $formData, $required = [])
    {
        return $this->excel_to_array($fileName, $fields, $model, $formData, $required);
    }

    protected function excel_to_array($fileName, $fields, $model, $formData, $required = [])
    {
        if (! file_exists($fileName) || ! is_readable($fileName))
            return false;
        
        ini_set('memory_limit', '1024M');
        try {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
            try {
                $reader->setReadDataOnly(TRUE);
                $spreadsheet = $reader->load($fileName);
                $worksheet = $spreadsheet->getActiveSheet();
                // Get the highest row and column numbers referenced in the worksheet
                $highestRow = $worksheet->getHighestDataRow(); // e.g. 10
                $highestColumn = $worksheet->getHighestDataColumn(); // e.g 'F'
                $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn); // e.g. 5
            } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $ex) {
                yii::error("Error while reading excel file" . $ex->getMessage());
                return [
                    "status" => "failed",
                    'error' => $ex->getMessage(),
                    'message' => Yii::$app->params['excel']['excelOpeningError']
                ];
            }
            
            $headings = [];
            $errHeadings = [];
            $data = [];
            $excelData = [];
            $_excelData = [];
            
            // for accounts digital
            
            $rowData = $worksheet->ToArray(); // the excel converted to array
            
            if ($highestColumnIndex == 7) {
                for ($row = 4; $row < $highestRow; $row ++) {
                    if ($row == 4) {
                        // all column heading values into an array
                       
                        foreach ($rowData[4] as $r) {
                            $r = trim($r);
                            if($r) {
                                if (isset($fields[$r])) {
                                    $headings[] = $fields[$r];
                                } else {
                                    $errHeadings[] = $r;
                                }
                            }
                        }
                        if (!empty($errHeadings)) {
                            return [
                                "status" => "failed",
                                'message' => Yii::$app->params['excel']['excelMismatchError'] . " : " . implode(', ', $errHeadings)
                            ];
                            Yii::$app->end();
                        }
                        if (!(array_intersect($required, $headings) === $required)) {
                            return [
                                "status" => "failed",
                                'message' => Yii::$app->params['excel']['excelRequiredFieldsError']
                            ];
                            Yii::$app->end();
                        }
                        continue;
                    }
                    
                    $colVal = trim($rowData[$row][2]);
                    $check = [
                        "SUNDRY DEBTORS-LIFE LONG MEMBERS",
                        "SUNDRY DEBTORS-INST. MEMBERS",
                        "SUNDRY DEBTORS-TEMP. MEMBERS",
                        "SUNDRY DEBTORS-SHORT TERM MEMBERS",
                        "SUNDRY DEBTORS-AFILIATED MEMBERS",
                        "SUNDRY DEBTORS ASSOCIATE MEMBER"       
                    ];
                    if (in_array($colVal, $check)) {
                        $tempMem = $rowData[$row + 1][1];
                        $c = strpos($tempMem, "D");
                        $memberNo = substr($tempMem, $c + 1);
                        $transactiondate = "";
                        continue;
                    }
                    
                    $ufdate = (string) trim($rowData[$row][0]);
                    if (strlen($ufdate) != 0) {
                        $cleanDate = preg_replace("/\"{2}/", "", $ufdate);
                        if ($cleanDate) {
                            $dateObj = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($cleanDate);
                            $transactiondate = $dateObj->format(yii::$app->params['dateFormat']['sqlDateFromatWithDotSep']);
                        } else {
                            $transactiondate = $transactiondate;
                        }
                        $description = preg_replace([
                            '/\\\\/',
                            '/\"/',
                            '/\'/',
                            '/\./'
                        ], [
                            "",
                            "''",
                            "",
                            ""
                        ], $rowData[$row][2]);
                        if (strpos($description, 'Opening Balance') !== false) {
                            $type = 1;
                        } else {
                            $type = 0;
                        }
                        $datecolm = preg_replace('/\s+/', "", $rowData[$row + 1][0]);
                        $debitcolm = preg_replace([
                            '/\s+/',
                            '/\,/'
                        ], "", $rowData[$row + 1][3]);
                        $creditcolm = preg_replace([
                            '/\s+/',
                            '/\,/'
                        ], "", $rowData[$row + 1][4]);
                        $debit = str_replace(",", "", trim($rowData[$row][3]));
                        $credit = str_replace(",", "", trim($rowData[$row][4]));
                        if (
                            ($rowData[$row + 1][2] != "" && strstr($datecolm, '"') == false) 
                            && strlen($datecolm) == 0 
                            && (empty($debitcolm) && empty($creditcolm))) {
                            $appendesc = trim(preg_replace([
                                '/\s+/'
                            ], "", str_replace("\\", "", $rowData[$row + 1][2])));
                            $td = $description ."-". $appendesc;
                            $description = preg_replace([
                                '/\s+/'
                            ], "", $td);
                        }
                        if ($highestColumnIndex == 7) {
                            if ($memberNo) {
                                $l = strpos($memberNo, "/");
                                $newmembernum = ($l > 0) ? substr($memberNo, 0, $l) : $memberNo;
                            } else {
                                $message = "Member number cannot be blank";
                                return [
                                    "status" => "failed",
                                    'error' => "",
                                    'message' => $message
                                ];
                            }
                            if ($debit && $credit) {
                                $message = "A transaction cannot have debit and credit amount together for Member No: " . $memberNo . "";
                                return [
                                    "status" => "failed",
                                    'error' => "",
                                    'message' => $message
                                ];

                            } elseif ($debit || $credit) {
                                $debit = ($debit) ? number_format((float)$debit, 2, '.', '') : '';
                                $credit = ($credit) ? number_format((float)$credit, 2, '.', '') : '';
                            } elseif ($type == 1 && !$debit && !$credit) {
                                $debit = ($debit) ? number_format((float)$debit, 2, '.', '') : '';
                                $credit = ($credit) ? number_format((float)$credit, 2, '.', '') : ''; 
                            } else {
                                $message = "A transaction should have debit or credit amount for Member No: " . $memberNo . "";
                                return [
                                    "status" => "failed",
                                    'error' => "",
                                    'message' => $message
                                ];
                            }
                            if (! $transactiondate) {
                                $message = "Transaction Date cannot be blank for Member No:" . $memberNo . "";
                                return [
                                    "status" => "failed",
                                    'error' => "",
                                    'message' => $message
                                ];
                            }
                        }
                        $excelData = [
                            "memberNo" => trim($memberNo),
                            "transactiondate" => $transactiondate,
                            "year" => $formData->year,
                            "userid" => yii::$app->user->identity->id,
                            "month" => $formData->month,
                            "institutionid" => $formData->institutionid,
                            "description" => trim($description),
                            "type" => $type,
                            "debit" => $debit,
                            "credit" => $credit,
                            "newmembernum" => trim($newmembernum),
                            "memberid" => $formData->getMemberId($newmembernum)
                        ];
                        array_push($_excelData, $excelData);
                    }
                }
                $tableFields = [
                    "memberNo",
                    "transactiondate",
                    "year",
                    "userid",
                    "month",
                    "institutionid",
                    "description",
                    "type",
                    "debit",
                    "credit",
                    "newmembernum",
                    "memberid"
                ];
                $response = $formData->bulkInsertBill($model, $_excelData, $formData, "excel", $tableFields);
                if ($response['status'] == "success") {
                    return [
                        "status" => "success",
                        "errorData" => [],
                        "heading" => $headings,
                        "totalRows" => count($_excelData),
                        "insertedRows" => count($_excelData)
                    ];
                } else {
                    return $response;
                }
            }
            
            // exclusive code for reading bill from cochin club
            if ($highestColumnIndex == 10) {
                $required = yii::$app->params['cochinClubExcelRequiredFields'];
                $fields = yii::$app->params['cochinClubExcelFields'];
                for ($row = 0; $row < $highestRow; $row ++) {
                    if (! empty(preg_replace('/\s+/', "", $rowData[$row][0])) && ( strstr(strtolower($rowData[$row][1]), "ism") !== false || strstr(strtolower($rowData[$row][1]), "osm") !== false)) {
                        
                        $memberNo = strtolower(preg_replace('/\s+/', "", $rowData[$row][1]));
                        $memberNo = str_replace(array( '(', ')' ), '', $memberNo);
                        
                        if (strstr($memberNo, "ism-") != false) {
                            $c = strrpos($memberNo, "ism-") + 4;
                            $memberNo = trim(substr($memberNo, $c));
                            $memberNo = "ISM-" . $memberNo;
                        } else if (strstr($memberNo, "osm-") != false) {
                            $c = strrpos($memberNo, "osm-") + 4;
                            $memberNo = trim(substr($memberNo, $c));
                            $memberNo = "OSM-" . $memberNo;
                        } else {
                            $memberNo = "";
                        }
                        if ((strstr(strtolower($memberNo), "ism") == false) && (strstr(strtolower($memberNo), "osm") == false)) {
                            continue;
                        }
                        if ($memberNo) {
                            $attempts = 0;
                            do {
                                $row++;
                                $attempts++;
                            } while (strtolower($rowData[$row][0]) != "date" && $attempts < 4);
                            if (strtolower($rowData[$row][0]) != "date") {
                                $info = "Transaction Date cannot be blank for Member No: " . $memberNo . "";
                                return [
                                    "status" => "failed",
                                    'error' => "",
                                    'message' => $info
                                ];
                            }
                            $row++;
                        } else {
                            $info = "Member Number cannot be blank";
                            return [
                                "status" => "failed",
                                'error' => "",
                                'message' => $info
                            ];
                        }
                        $transactiondate = trim($rowData[$row][0]);
                      
                        if ($transactiondate) {
                            try {
                                $dateObj = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($transactiondate);
                                $transactiondate = $dateObj->format(yii::$app->params['dateFormat']['sqlDandTFormat']);
                            } catch (\Exception $e) {
                                $info = "Transaction Date cannot be blank for Member No: " . $memberNo . "";
                                return [
                                    "status" => "failed",
                                    'error' => "",
                                    'message' => $info
                                ];
                            }
                        } else {
                            $info = "Transaction Date cannot be blank for Member No: " . $memberNo . "";
                            return [
                                "status" => "failed",
                                'error' => "",
                                'message' => $info
                            ];
                        }

                        $debit = trim(str_replace("Dr", "", $rowData[$row][7]));
                        $credit = trim(str_replace("Cr", "", $rowData[$row][8]));
                       
                        if ($debit && $credit) {
                             $info = "A transaction cannot have debit and credit amount together for Member No: " . $memberNo . "";
                            return [
                                "status" => "failed",
                                'error' => "",
                                'message' => $info
                            ];

                        } elseif ($debit || $credit) {
                            $debit = (float)$debit;
                            $credit = (float)$credit;              
                        } else {
                            $info = "A transaction should have debit or credit amount for Member No: " . $memberNo . "";
                            return [
                                "status" => "failed",
                                'error' => "",
                                'message' => $info
                            ];              
                        }

                        if ($memberNo) {
                            $l = strpos($memberNo, "/");
                            $newmembernum = ($l > 0) ? substr($memberNo, 0, $l) : $memberNo;
                        }

                        $type = 1;
                        $description = "Opening Balance";
                        if(trim(strtolower(preg_replace('/\s+/', "", $rowData[$row][2]))) != 'openingbalance')
                        {
                            continue;
                        }
                        $excelData = [
                            "description"=> $description,
                            "debit" => $debit,
                            "credit" => $credit,
                            "type" => $type,
                            "memberNo" => $memberNo,
                            "year" => $formData->year,
                            "userid" => yii::$app->user->identity->id,
                            "month" => $formData->month,
                            "institutionid" => $formData->institutionid,
                            "transactiondate" => $transactiondate,
                            "newmembernum" => $newmembernum,
                            "memberid" => $formData->getMemberId($newmembernum)
                        ];
                        array_push($_excelData, $excelData);
                        continue;
                    } else if (strstr(trim(strtolower(preg_replace('/\s+/', "", $rowData[$row][2]))), "(asperdetails)") == false && strstr(trim(strtolower(preg_replace('/\s+/', "", $rowData[$row][2]))), "onaccount") == false && strstr(trim(strtolower(preg_replace('/\s+/', "", $rowData[$row][2]))), "closingbalance") == false && strstr(trim(strtolower(preg_replace('/\s+/', "", $rowData[$row][2]))), "monthlybill") == false && !empty(trim($rowData[$row][2])))
                        {

                            if (empty($memberNo) && (strstr(strtolower($memberNo), "ism") == false) && (strstr(strtolower($memberNo), "osm") == false)) {
                                continue;
                            }
                            $debit = (float)trim(str_replace("Dr", "", $rowData[$row][7]));
                            $credit = (float)trim(str_replace("Cr", "", $rowData[$row][8]));
                            if ($credit || $debit) {
                                // new changes as per the discussion with the client on the 11th of august 2022
                                /* $excelData['debit'] = $credit;
                                $excelData['credit'] = $debit; */
                                $excelData['debit'] = strstr($rowData[$row][7], 'Cr') ? $credit : $debit;
                                $excelData['credit'] = strstr($rowData[$row][7], 'Dr') ? $debit : $credit;
                            }  else {
                                continue;
                            }
                            $excelData['description'] = trim($rowData[$row][2]);
                            $excelData['type'] = 0;
                            $excelData['memberNo'] = $memberNo;
                            $excelData['year'] = $formData->year;
                            $excelData['userid'] = yii::$app->user->identity->id;
                            $excelData['month'] = $formData->month;
                            $excelData['institutionid'] = $formData->institutionid;
                            $excelData['transactiondate'] = $transactiondate;
                            $l = strpos($memberNo, "/");
                            $excelData['newmembernum'] = ($l > 0) ? substr($memberNo, 0, $l) : $memberNo;
                            $excelData['memberid'] = $formData->getMemberId($excelData['newmembernum']);
                            array_push($_excelData, $excelData);
                        } 
                }
                $tableFields = [
                    "description",
                    "debit",
                    "credit",
                    "type",
                    "memberNo",
                    "year",
                    "userid",
                    "month",
                    "institutionid",
                    "transactiondate",
                    "newmembernum",
                    "memberid"
                ];
                $response = $formData->bulkInsertBill($model, $_excelData, $formData, "excel", $tableFields);
                if ($response['status'] == "success") {
                    return [
                        "status" => "success",
                        "errorData" => [],
                        "heading" => $headings,
                        "totalRows" => count($_excelData),
                        "insertedRows" => count($_excelData)
                    ];
                } else {
                    return $response;
                }
            }

            // exclusive code for reading bill from lotus club
            if ($highestColumnIndex == 11) {
                $header = NULL;
                $errorData = [];
                $patterns = array();
                $patterns[0] = "/'/";
                $patterns[1] = "/\\\\/";
                $replacements = array();
                $replacements[1] = "''";
                $replacements[0] = "";
                foreach ($rowData as $k => $v) {
                    if(!$header){
                        $header = array_map('strtolower', $v);
                    } else if(!_::first($v)){
                        continue;
                    } else {
                        $data[] = array_combine($header, $v);
                    }
                }

                $billLotusClubExcelFields = [
                    ["name" => "mem_alias", "required" => true],
                    ["name" => "mem_order",  "required" => true],
                    ["name" => "mem_name",  "required" => true], 
                    ["name" => "mem_type",  "required" => true],
                    ["name" => "bill_date",  "required" => true], 
                    ["name" => "item_no",  "required" => true],
                    ["name" => "item_desc",  "required" => true],
                    ["name" => "trans_date",  "required" => true],
                    ["name" => "trans_desc",  "required" => true],
                    ["name" => "db_amt",  "required" => false],
                    ["name" =>"cr_amt", "required" => true]
                ];
    
                $required = _::pluck(_::filter($billLotusClubExcelFields, function($val) { return $val['required'];}), 'name');
                
                if (!(array_intersect($required, $header) === $required)) {
                    return ['status' => 'failed', 'message' => Yii::$app->params['excel']['excelRequiredFieldsError']];
                }

                if(!empty($data)) {
                    $d = [];
                    $e = [];
                    foreach ($data as $k => $eachData) {
                        $d["transactiontype"] = $eachData["item_no"] ? (int) $eachData["item_no"] : 0;
                        $d["description"] = preg_replace(
                            $patterns,
                            $replacements,
                            trim($eachData["item_desc"])
                        );
                        if (strpos($d["description"], 'Opening Balance') !== false) {
                            $d['type'] = 1;
                        } else {
                            $d['type'] = 0;
                        }
                        $memberNo = $eachData['mem_alias'];
                        $d["memberNo"] = $memberNo;
                        $d["year"] = $formData->year;
                        $d["userid"] = yii::$app->user->identity->id;
                        $d["month"] = $formData->month;
                        $d["institutionid"] = $formData->institutionid;
                        $fields = count($eachData);
                      
                        $dateTime = \DateTime::createFromFormat('d.m.Y', $eachData["trans_date"]);
                        $eachData["trans_date"] = $dateTime
                            ? $dateTime->format(yii::$app->params['dateFormat']['sqlDandTFormat'])
                            : "";

                        $billDateTime = \DateTime::createFromFormat('d.m.Y', $eachData["bill_date"]);
                        $eachData["bill_date"] = $billDateTime
                                ? $billDateTime->format(yii::$app->params['dateFormat']['sqlDandTFormat'])
                                : "";                       
                        
                        if(!empty($memberNo)) {
                            $l = strpos($memberNo, "/");
                            $d["newmembernum"] = ($l > 0) ? substr($memberNo, 0, $l) : $memberNo;
                            $d["memberid"] = $formData->getMemberId($d["newmembernum"]);
                        } else {
                            $e['data'] = $eachData;
                            $e['message'] = "Member Number cannot be blank";
                            array_push($errorData, $e);
                            continue;
                        }
                        if(!empty($eachData["trans_date"])) {
                            $d["transactiondate"] = $eachData["trans_date"];
                        } else {
                            $e['data'] = $eachData;
                            $e['message'] = "Transaction Date cannot be blank";
                            array_push($errorData, $e);
                            continue;
                        }
                        if( (!empty($eachData['db_amt']) && $eachData['db_amt'] > 0) && (!empty($eachData['cr_amt']) && $eachData['cr_amt'] > 0 ) ) {
                            $e['data'] = $eachData;
                            $e['message'] = "A transaction cannot have debit and credit amount together";
                            array_push($errorData, $e);
                            continue;   
                        } elseif(!empty($eachData['db_amt']) && $eachData['db_amt'] > 0) {
                            $d["debit"] = !empty($eachData['db_amt']) ? number_format((float)$eachData['db_amt'], 2, '.', '') : '';
                            $d["credit"] = '';
                        } elseif (!empty($eachData['cr_amt']) && $eachData['cr_amt'] > 0 ) {
                            $d['debit'] = '';
                            $d["credit"] = !empty($eachData['cr_amt']) ? number_format((float)$eachData['cr_amt'], 2, '.', '') : '';
                        } else {
                            $e['data'] = $eachData;
                            $e['message'] = "A transaction should have debit or credit amount";
                            array_push($errorData, $e);
                            continue;   
                        }
                        array_push($_excelData, $d);    
                    }
                    $tableFields = [
                        "transactiontype",
                        "description",
                        "type",
                        "memberNo",
                        "year",
                        "userid",
                        "month",
                        "institutionid",
                        "newmembernum",
                        "memberid",
                        "transactiondate",
                        "debit",
                        "credit"
                    ];
                    $response = $formData->bulkInsertBill($model, $_excelData, $formData, "excel", $tableFields);
                    if($response['status'] == "success") {
                        return [
                            "status" => "success", 
                            "errorData" => $errorData, 
                            "heading" => $header, 
                            "totalRows" => count($data), 
                            "insertedRows"=> count($_excelData)
                        ];
                    } else {
                        return $response;       
                    }
                }
            }
        } catch (Exception $e) {
            return [
                "status" => "failed",
                'error' => $e->getMessage(),
                'message' => Yii::$app->params['excel']['excelImportTableError']
            ];
        }
        return [
            "status" => "failed",
            'error' => "",
            'message' => Yii::$app->params['excel']['excelImportTableError']
        ];
        Yii::$app->end();
    }
}
