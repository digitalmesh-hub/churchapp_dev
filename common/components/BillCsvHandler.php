<?php

namespace common\components;

use Yii;
use yii\base\Component;
use common\helpers\Utility;
use Exception;

class BillCsvHandler extends Component 
{
	
	public function importCsvBill($fileName, $fields, $model, $formData, $required = [])
	{
		if($formData->institutionid == 74) {
			return $this->custom_csv_to_array($fileName, $fields, $model, $formData, $required);
		}
        return $this->csv_to_array($fileName, $fields, $model, $formData, $required);
	}

	protected function csv_to_array($fileName, $fields, $model, $formData, $required = [])
	{ 
		if(!file_exists($fileName) || !is_readable($fileName))
			return false;
		
		$header = NULL;
		$data = array();
		$headings = [];
		$errHeadings = [];
		$csvData = [];
		$errorData = [];
		$patterns = array();
		$patterns[0] = "/'/";
		$patterns[1] = "/\\\\/";
		$replacements = array();
		$replacements[1] = "''";
		$replacements[0] = "";
		
		try{
			if (($handle = fopen($fileName, 'r')) !== FALSE) {
				while (($row = fgetcsv($handle)) !== FALSE) {
					if(!$header){
						$header = array_map('strtolower', Utility::utf8Cleanup($row));
					} else {
						$data[] = array_combine($header, $row);
					}
				}
				fclose($handle);
			}
		} catch(Exception $e) { 
			yii::error("Error while reading csv".$e->getMessage());
			return ["status"=>"failed", 'error'=>$e->getMessage(), 'message' => Yii::$app->params['csv']['csvOpeningError']]; 
		}
 		
		foreach ($header as $h) {
			if (isset($fields[$h])) {
				$headings[] = $fields[$h];
			} else {
				$errHeadings[]= $h;
			}
		}

		//to enable extra header fields 
		/*if (!empty($errHeadings)) {
			return ["status"=>"failed", 'message' => Yii::$app->params['csv']['csvMismatchError']." : ".implode(', ', $errHeadings)];
			Yii::$app->end();
		}*/
		if (!(array_intersect($required, $headings) === $required)) {
			return ["status"=>"failed", 'message' => Yii::$app->params['csv']['csvRequiredFieldsError']];
			Yii::$app->end();
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
	            		 $d["transactiondate"] = date(yii::$app->params['dateFormat']['sqlDateFromatWithDotSep'],strtotime($eachData["trans_date"]));	
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
	            	}  elseif ($d['type'] == 1 && empty($eachData['cr_amt']) && empty($eachData['dr_amt']) ) {
	            		$d['debit'] = 0;
	            		$d["credit"] = 0;
	            	}
	            	else {
	            		$e['data'] = $eachData;
	            		$e['message'] = "A transaction should have debit or credit amount";
	            		array_push($errorData, $e);
	            		continue;	
	            	}
	            array_push($csvData, $d);
	           
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
			$response = $formData->bulkInsertBill($model, $csvData, $formData, "csv", $tableFields);
			if($response['status'] == "success") {
				return [
						"status" => "success", 
						"errorData" => $errorData, 
						"heading" => $headings, 
						"totalRows" => count($data), 
						"insertedRows"=> count($csvData)
				];
			} else {
				return $response;       
			}

		} else {
			return ["status"=>"failed", 'message' => Yii::$app->params['csv']['csvRequiredFieldsError']];
			Yii::$app->end();
		}
	}

	private function custom_csv_to_array($fileName, $fields, $model, $formData, $required = [])
	{ 
		if(!file_exists($fileName) || !is_readable($fileName))
			return false;
		
		ini_set('memory_limit', '1024M');
		$rowData = [];
		$headings = [];
		$errHeadings = [];
		$excelData = [];
		$_excelData = [];

		try{
			if (($handle = fopen($fileName, 'r')) !== FALSE) {
				while (($row = fgetcsv($handle)) !== FALSE) {
					$rowData[] = $row;
				}
				fclose($handle);
			}
		} catch(Exception $e) { 
			yii::error("Error while reading csv".$e->getMessage());
			return ["status"=>"failed", 'error'=>$e->getMessage(), 'message' => Yii::$app->params['csv']['csvOpeningError']]; 
		}

		$highestColumnIndex = count($rowData[0]);
		$highestRowIndex = count($rowData);

		// exclusive code for reading bill from cochin club
		if ($highestColumnIndex == 10) {
			$required = yii::$app->params['cochinClubExcelRequiredFields'];
			$fields = yii::$app->params['cochinClubExcelFields'];
			$memberNo = "";
			$transactiondate = "";
			for ($row = 0; $row < $highestRowIndex; $row ++) {
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
						$transactiondate = date(yii::$app->params['dateFormat']['sqlDandTFormat'],strtotimeNew($transactiondate));
					} else {
						$info = "Transaction Date cannot be blank for Member No: " . $memberNo . "";
						return [
							"status" => "failed",
							'error' => "",
							'message' => $info
						];
					}

					$debit = (float) trim($this->removeNonNumeric($rowData[$row][7]));
					$credit = (float) trim($this->removeNonNumeric($rowData[$row][8]));
				   
					if ($debit && $credit) {
						 $info = "A transaction cannot have debit and credit amount together for Member No: " . $memberNo . "";
						return [
							"status" => "failed",
							'error' => "",
							'message' => $info
						];

					} elseif ($debit || $credit) {
						if (
							strstr(strtolower($rowData[$row][7]), 'dr') == 'dr'
							|| strstr(strtolower($rowData[$row][8]), 'cr') == 'cr'
						) {
							$debit = $credit;
							$credit = $debit;
						}            
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

						$debit = (float)trim($this->removeNonNumeric($rowData[$row][7]));
						$credit = (float)trim($this->removeNonNumeric($rowData[$row][8]));

						if ($credit || $debit) {
							$excelData['debit'] = $debit;
							$excelData['credit'] = $credit;

							if (
								strstr(strtolower($rowData[$row][7]), 'dr') == 'dr'
								|| strstr(strtolower($rowData[$row][8]), 'cr') == 'cr'
							) {
								$excelData['debit'] = $credit;
								$excelData['credit'] = $debit;
							}   
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
		return [
			"status"=>"failed", 
			'error'=> "Invalid file format. Please upload a valid csv file.", 
			'message' => "Invalid file format. Please upload a valid csv file."
		]; 
	}

	private function removeNonNumeric($string)
	{
		// Use regular expression to match digits and a decimal point
		// and replace everything else with an empty string
		$cleanedString = preg_replace('/[^0-9.]/', '', $string);
		
		// If the string starts with a decimal point, add a leading zero
		if (substr($cleanedString, 0, 1) === '.') {
			$cleanedString = '0' . $cleanedString;
		}
		
		// If the string ends with a decimal point, add a trailing zero
		if (substr($cleanedString, -1) === '.') {
			$cleanedString .= '0';
		}
	
		return $cleanedString;
	}
}
