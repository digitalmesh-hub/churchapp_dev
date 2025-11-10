<?php
namespace common\components;

use Exception;
use Yii;
use yii\base\Component;
use yii\web\NotFoundHttpException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;


class ExcelHandler extends Component {
	/**
	 * To make excel report using PHPExcel
     * 
     * Important Note:-
     * This function can do 3 things with the report.
     * 1. Download Only
     * 2. Save Only
     * 3. Save and Download
     *
     * The example syntax for these functionalities is given below
     * 
     * Example Syntax - Download Only:
     * -------------------------------
     *     $title['data'] = ['a', 'b', 'c'];
     *     $title['style'] = ['font' => ['bold' => true]];
     *     $content['data'] = [[1,2,4],[3,4,5]];
     *     $content['style'] = ['font' => ['size' => 7]];
     *     $extra['defaultStyle'] = ['alignment' => ['wrap' => true]];
     *     $extra['defaultBorderStyle'] = ['borders' => [
     *         'allborders' => ['style' => \PHPExcel_Style_Border::BORDER_THIN]]
     *     ];
     *     Yii::$app->ExcelHandler->makeExcelReport($title, $content, $extra);
     *
     * Example Syntax - Save Only:
     * ---------------------------
     *     $title['data'] = ['a', 'b', 'c'];
     *     $title['style'] = ['font' => ['bold' => true]];
     *     $content['data'] = [[1,2,4],[3,4,5]];
     *     $content['style'] = ['font' => ['size' => 7]];
     *     $extra['defaultStyle'] = ['alignment' => ['wrap' => true]];
     *     $extra['defaultBorderStyle'] = ['borders' => [
     *         'allborders' => ['style' => \PHPExcel_Style_Border::BORDER_THIN]]
     *     ];
     *     // Path to save excel file
     *     $savePath = sys_get_temp_dir().'/test'.time().".xlsx";
     *     // Saves Excel file
     *     Yii::$app->ExcelHandler->makeExcelReport($title, $content, $extra, $savePath);
     *     
     * Example Syntax - Save and Download:
     * -----------------------------------
     *     $title['data'] = ['a', 'b', 'c'];
     *     $title['style'] = ['font' => ['bold' => true]];
     *     $content['data'] = [[1,2,4],[3,4,5]];
     *     $content['style'] = ['font' => ['size' => 7]];
     *     $extra['defaultStyle'] = ['alignment' => ['wrap' => true]];
     *     $extra['defaultBorderStyle'] = ['borders' => [
     *         'allborders' => ['style' => \PHPExcel_Style_Border::BORDER_THIN]]
     *     ];
     *     // Path to save excel file
     *     $savePath = sys_get_temp_dir().'/test'.time().".xlsx";
     *     // Saves Excel file
     *     Yii::$app->ExcelHandler->makeExcelReport($title, $content, $extra, $savePath);
     *     // Download excel from saved location
     *     Yii::$app->ExcelHandler->downloadExcelReport($savePath);
     *
     * Reference links:
     * ----------------
     * PHPExcel Function Reference developer documentation:
     * https://it-solutions.schultz.ch/images/schultz.ch/it-solutions/support/PHPexcel/PHPExcel_Function_Reference_developer_documentation.pdf
     * 
     * PHPExcel Style Reference:
     * http://www.bainweb.com/2012/01/phpexcel-style-reference-complete-list.html
     *     
	 * @param array   $title      Excel title data
	 * @param array   $content    Excel content
     * @param array   $extra      Extra non required data
	 * @param string  $savePath   Directory to save the excel file
	 * @return none
	 */
	public function makeExcelReport(
		$title, $content, $extra = null, $savePath = null
	) {	
		// Validating required argument data
		if (empty($title)) {
			Yii::error(
				'Empty $title data passed in'.
				'ExcelHandler::makeExcelReport()'
			);
			throw new NotFoundHttpException(Yii::$app->params['excel']['excelReportError']);
		}

		if (empty($content)) {
			Yii::error(
				'Empty $content data passed in'.
				'ExcelHandler::makeExcelReport()'
			);
			throw new NotFoundHttpException(Yii::$app->params['excel']['excelReportError']);
		}

		$objPHPExcel= new Spreadsheet();  /*----Spreadsheet object-----*/
		try {
        	$sheet = 0;
			$objPHPExcel->setActiveSheetIndex($sheet);

			if (!empty($extra) && !empty($extra['workSheetTitle'])) {
				//$objPHPExcel->getActiveSheet()->getCell('A0')->setValue($extra['workSheetTitle']);
			}

			$titleCount = count($title['data']);
			$contentCount = count($content['data']);

			// Gets the excel column index names
			$columnIndex = $this->getAllColumnIndex($titleCount);
			// Setting common excel parameters like styles, alignment, etc
            if (!empty($extra)) {
                $this->setCommonExcelParams(
                    $objPHPExcel, 
                    $extra,
                    $columnIndex,
                    $titleCount,
                    $contentCount
                );
            }
			// Writes provided titles to the excel
			$this->writeExcelTitles($objPHPExcel, $columnIndex, $title);

			// Writes provided content data to the excel
            $this->writeExcelContents($objPHPExcel, $columnIndex, $content);
            if (!empty($savePath)) {
                // Savethe excel according to the input
                return $this->downloadExcelReport($savePath, true, $objPHPExcel);
            } else {
                return $this->downloadExcelReport(null, false, $objPHPExcel);
            } 
        } catch( Exception $e ){
   			// $objPHPExcel->disconnectWorksheets();
			// unset($objPHPExcel)
            return [
            	'status' => 'failed',
            	'error' => $e->getMessage(),
            	'message' => Yii::$app->params['excel']['excelReportError']
            ];
        }
	}

	/**
	 * Writes the titles data excel
	 * @param object $objPHPExcel PHPExcel object
	 * @param array  $columnIndex Main column index names
	 * @param array  $title       Row title names
	 * @return none
	 */
	private function writeExcelTitles($objPHPExcel, $columnIndex, $title)
	{	
		$colTotal = count($columnIndex);
		$rowCount = 1;
		for ($i=0; $i < $colTotal ; $i++) { 
			$objPHPExcel->getActiveSheet()
				->setCellValue($columnIndex[$i].$rowCount, $title['data'][$i]);
		}

        // Applying styles to title cells
		$fistTitleIndex = $columnIndex[0].$rowCount;
		$lastTitleIndex = $columnIndex[count($columnIndex)-1].$rowCount;

		$objPHPExcel->getActiveSheet()
			->getStyle("{$fistTitleIndex}:{$lastTitleIndex}")
			->applyFromArray($title['style']);
	}
    
	/**
	 * Writes the content data to excel 
	 * @param object $objPHPExcel PHPExcel object
	 * @param array  $columnIndex Main column index names
	 * @param array  $title       Row title names
	 * @return none
	 */
	private function writeExcelContents($objPHPExcel, $columnIndex, $content)
	{  
        date_default_timezone_set('Asia/Kolkata');
		$colTotal = count($columnIndex);
		$rowCount = 1;
		foreach ($content['data'] as $data) {
            unset($data['userid']);
            unset($data['memberid']);
            unset($data['institutionid']);
            $data['lastlogintime'] = date('Y-m-d H:i:s',strtotimeNew($data['lastlogintime']));
			$rowCount++;
			$colCount = 0;
			foreach ($data as $key => $cellData) {
				$objPHPExcel->getActiveSheet()->setCellValue(
						$columnIndex[$colCount].$rowCount, 
						$cellData
				);
				$colCount++;
			}
		}
	}

	/**
	 * Gets the excel string column index names 
	 * from the given total columns.
	 * column index names example: A, B, AJ, etc
	 * @param  integer $totalColums [description]
	 * @return array
	 */
	public function getAllColumnIndex($totalColums)
	{	
		$columnIndex = array();
		for ($i=1; $i <= $totalColums; $i++) { 
			$columnIndex[] = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
		}
		return $columnIndex;
	}

	/**
	 * Setting common excel parameters like styles, alignment, etc
	 * @param object $objPHPExcel PHPExcel object
	 * @param array  $extra       Extra non required data
	 */
	private function setCommonExcelParams(
		$objPHPExcel, $extra, $columnIndex, $titleCount, $contentCount
	) {	

		// Applying default styles to work sheet
		if (!empty($extra['defaultStyle'])) {
			$objPHPExcel->getDefaultStyle()
				->applyFromArray($extra['defaultStyle']);
		}

		// Getting first and last column index values 
		// and setting the border
        if (!empty($extra['defaultBorderStyle'])) {
            $fistCellIndex = $columnIndex[0].'1'; // A1
        
            // Summing available content count and title count
            $totalCellCount = $contentCount + 1;
            $lastCellName = $columnIndex[$titleCount-1];

            $lastCellIndex = $lastCellName.$totalCellCount;

            $objPHPExcel->getActiveSheet()
                ->getStyle("{$fistCellIndex}:{$lastCellIndex}")
                ->applyFromArray($extra['defaultBorderStyle']);
        }

        // Sets Auto column width to fit the column text
        // To disable auto column width set $extra['disableColumnAutoSize'] = true
        if (empty($extra['disableColumnAutoSize'])) {
            foreach ($columnIndex as $columnID) {
                $objPHPExcel->getActiveSheet()
                    ->getColumnDimension($columnID)
                    ->setAutoSize(true);
            }
        }

        // Setting custom content column alignment
        if (!empty($extra['contentColumnAlignment'])) {
            $this->setContentColumnAlignment(
                $objPHPExcel, 
                $extra['contentColumnAlignment'],
                $contentCount
            );
        }
        
	}

    private function setContentColumnAlignment(
        $objPHPExcel, $cellAlignData, $contentCount
    ) { 
        $firstCellIndex = 2; // 1 is for title columns, so 2 eg: A2
        $lastCellIndex = $contentCount + 1; // +1 is for the title. eg: A11
        foreach ($cellAlignData as $alignData) {
            $cells = explode(',',$alignData['cells']);
            foreach ($cells as $cellName) {
                $range = $cellName.$firstCellIndex.':'.$cellName.$lastCellIndex;
                $objPHPExcel->getActiveSheet()
                    ->getStyle($range)
                    ->getAlignment()
                    ->setHorizontal($alignData['style']);
            }
        }
    }

     /**
     * Save and download the excel file
     * @param  string  $savePath    Path to save excel
     * @param  boolean $isDownload  Download
     * @param  object  $objPHPExcel PHPExcel object
     * @return none
     */
    public function downloadExcelReport(
        $savePath = null, $isSaveOnly = false ,$objPHPExcel = null
    ) {
        $filename = 'LoginReport'.time().".xlsx";
        $filePath = null;
        
        // Sets the file name and save path
        if (!empty($savePath)) {
            $savePathArray = explode('/', $savePath);
            $filename = $savePathArray[count($savePathArray)-1];
            $filePath = $savePath;    
        }
        
        // Download excel
        if (!empty($objPHPExcel) && $isSaveOnly) {
            //Generate Excel File and save file
            $objWriter = new Xls($objPHPExcel);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename='.$filename .' ');
            header('Cache-Control: max-age=0');
            $objWriter->save($filePath);
            return true;
        } elseif (!empty($objPHPExcel) && !$isSaveOnly && empty($savePath)) {
            $objWriter = new Xls($objPHPExcel);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename='.$filename .' ');
            header('Cache-Control: max-age=0');
            $objWriter->save('php://output');  //Download file
            Yii::info("Successfully downloaded - ".$filename);
            Yii::$app->end();
        } else {
            if(file_exists($filePath)){
                \Yii::$app->response->sendFile($filePath)->send();
                Yii::info("Successfully downloaded - ".$filePath);
            } else {
                throw new \yii\web\HttpException(404, 'The requested File could not be found.');
            }
        }
    }
}