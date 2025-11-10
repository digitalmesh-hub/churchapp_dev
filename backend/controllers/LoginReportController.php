<?php 

namespace backend\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\controllers\BaseController;
use common\models\extendedmodels\ExtendedInstitution;
use yii\helpers\ArrayHelper;

class LoginReportController extends BaseController{
	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
					'only' => ['index'],
						'rules' => [
							[
								'actions' => [ 'index'],
								'allow' => true,
								'roles' => ['superadmin']
							],
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
	public function actions()
	{
		return
		[
				'error' => [
						'class' => 'yii\web\ErrorAction',
				],
		];
	}
	public function actionIndex(){
		
		$model = new \yii\base\DynamicModel(['start_date', 'end_date', 'institutionId']);
		$model->addRule(['institutionId', 'start_date', 'end_date'], 'safe');
		$instituionModel = ArrayHelper::map(
        		ExtendedInstitution::find()
        		->select('id,name')
        		->all(),
        		'id',
        		'name'
        		);
		if ($model->load(Yii::$app->request->post())) {
			$dynamic = Yii::$app->request->post('DynamicModel');
			$institutionId = $dynamic['institutionId'] ? $dynamic['institutionId'] : 0;
			$start_date = Yii::$app->request->post('start_date');
			$start_date = date_format(date_create($start_date),Yii::$app->params['dateFormat']['sqlDateFormat']);
			$end_date = Yii::$app->request->post('end_date');
			$end_date = date_format(date_create($end_date),Yii::$app->params['dateFormat']['sqlDateFormat']);
			$result = $this->getResult($institutionId, $start_date, $end_date);
			$date = date('d-M-Y');
			$excelFile = $this->convertToExcel(
					$result,
					'LoginReport'
					.'_'.$start_date.' - '.$end_date,
					$start_date,
					$end_date
			);
		}
		return $this->render('index',
			[
				'institutionModel' => $instituionModel,
				'model' => $model
			]
		);
		
	}
	
	//Function to get the data
	protected function getResult($institutionId, $start_date, $end_date) 
	{
		$result = Yii::$app->db->createCommand("CALL login_report(:institutionId, :startdatetime, :enddatetime)")
		->bindValue(':institutionId' , $institutionId )
		->bindValue(':startdatetime', $start_date)
		->bindValue(':enddatetime', $end_date)
		->queryAll();
		return $result;
	}
	
	//Function to export array as excel
	protected function convertToExcel($data, $fileName, $start_date, $end_date)
	{
		
		ob_clean();
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename='.$fileName.'.csv');
		$fp = fopen('php://output', 'w');
		fputcsv($fp, ['Login Report from '.$start_date.' to ' .$end_date]);
		fputcsv($fp, ['Member Name','Institution','Last Login Time']);
		foreach ($data as $dd) {
			unset($dd['userid']);
			unset($dd['memberid']);
			unset($dd['institutionid']);
			fputcsv($fp, $dd);
		}
		fclose($fp);
		exit();
	}


	//Function to export array as excel
	/*protected function convertToExcel($data, $fileName, $start_date, $end_date)
	{
		$content = array();
		// Set the save path
        $savePath = '/runtime/Reports/'.$fileName.".xlsx";
        $headerTitle = 'Login Report from '.$start_date.' to ' .$end_date;
        $fullFilePath = Yii::getAlias('@app') . $savePath;
        // Gets the excel report titles
        $title['data'] = ['Member Name', 'Institution', 'Last Login Time'];
        // Gets the report data
        $content['data'] = $data;
        $excelStyle = $this->getSaleExcelReportStyles();
        // Setting custom styles array for PHPExcel
        $title['style'] = !empty($excelStyle['titleStyle']) 
                             ? $excelStyle['titleStyle'] : null;
        $extra['defaultStyle'] = !empty($excelStyle['defaultStyle'])
                                    ? $excelStyle['defaultStyle'] : null;
        $extra['defaultBorderStyle'] 
                = !empty($excelStyle['defaultBorderStyle'])
                 ? $excelStyle['defaultBorderStyle'] : null;

        $extra['workSheetTitle'] = $headerTitle;

        $extra['contentColumnAlignment'] 
                = $this->getReportColumnAlignmentData();
       if (!empty($content)) {
                Yii::$app->ExcelHandler->makeExcelReport(
                    $title, 
                    $content,
                    $extra,
                    $fullFilePath,
                    $headerTitle
         );
       
        Yii::$app->ExcelHandler->downloadExcelReport($fullFilePath);
        
        } else {
                Yii::error(
                    '$content is empty'.
                    'SaleController::downloadSaleReport()'
                );
                throw new NotFoundHttpException(
                    Yii::$app->params['excel']['excelReportError']
                );
        }
	}

    private function getSaleExcelReportStyles()
    {   
        return [
            'titleStyle' => [
                'font' => ['bold' => true], 
                'fill' => ['type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 
                'color' => ['rgb' => 'B2B2B2'] ],
                'alignment' => [
                    'horizontal' 
                        => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT
                ]
            ],
            'defaultStyle' => [
                'font' => [
                    'size' => 10,
                    'name' => 'Calibri'
                ],
                'alignment' => ['wrap' => true]
            ],
            'defaultBorderStyle' => [
                'borders' => [
                    'allborders' => [
                        'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ]
                ]
            ]
        ];
    }

    public function getReportColumnAlignmentData()
    {   
        return [
            'center' => [
            	'cells' => 'A,B,C,D,E,F,G,H,P,Q,R,S,T,X,Y,Z,AD,AE,AF,AG,AH,AI',
            	'style' =>  \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT
        	],
        'left' => [
            'cells' => 'I,J,K,L,M,N,O,U,V,W,AA,AB,AC,AJ,AG',
            'style' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
        ]
        ];
            
    }*/
}



















































?>