<?php

namespace backend\controllers;

use yii;
use backend\controllers\BaseController;
use common\models\basemodels\Qurbana;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use common\models\searchmodels\ExtendedQurbanaSearch;
use common\models\basemodels\QurbanaType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use yii\data\ActiveDataProvider;


class QurbanaController extends BaseController
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => [
                    'list-qurbana',
                    'export-qurbana-list'
                ],
                'rules' => [
                    [
                        'actions' => [
                            'list-qurbana',
                            'export-qurbana-list'
                        ],
                        'allow' => true,
                        'roles' => ['0c26fee6-3df8-4cd6-83d9-45a556a75b64']
                    ]
                ],
            ]
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

    /**
     * List the Qurbana requests.
     *
     * This method retrieves and lists all the Qurbana requests.
     *
     * @return void
     */
    public function actionListQurbana()
    {
        $dataProvider = array();
        $searchModel = new ExtendedQurbanaSearch();
        $searchModel->qurbana_date = date('Y-m-d');
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $qurbanaTypes = ArrayHelper::map(QurbanaType::find()->all(), 'id', 'type');
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'qurbanaTypes' => $qurbanaTypes
        ]);
    }

    /**
     * Export all qurbana requests
     *
     * @return void
     */
    public function actionExportQurbanaList()
    {
        ini_set('memory_limit', '-1');
        set_time_limit(300);
        $searchModel = new ExtendedQurbanaSearch();
        $searchModel->qurbana_date = date('Y-m-d');
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $qurbanas = $dataProvider->getModels();
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Name')
            ->setCellValue('B1', 'Contact')
            ->setCellValue('C1', 'Qurbana Type')
            ->setCellValue('D1', 'Name to be remembered')
            ->setCellValue('E1', 'Qurbana Date');

        $row = 2;
        $lastColumn = 'E';

        foreach ($qurbanas as $qurbana) {

            $qurbanaType = $qurbana->qurbanatype ? $qurbana->qurbanatype->type : 'N/A';
            $memberData = $qurbana->member;

            $memberName = $qurbana->member->FullNameWithTitle;

            $sheet->setCellValue('A' . $row, $memberName)
                ->setCellValue('B' . $row, $memberData->member_mobile1 ?? 'NA')
                ->setCellValue('C' . $row, $qurbanaType)
                ->setCellValue('D' . $row, $qurbana->name ?? 'Self')
                ->setCellValue('E' . $row, (!empty($qurbana->qurbana_date) ? date_format(date_create($qurbana->qurbana_date), Yii::$app->params['dateFormat']['formDateFormat']) : ''));
            $row++;
        }

        foreach (range('A', $lastColumn) as $columnID) {
            $sheet->getColumnDimension($columnID)->setWidth(25);
        }

        foreach (range(1, ($row - 1)) as $rowId) {
            foreach (range('A', $lastColumn) as $columnID) {
                $sheet->getStyle($columnID . $rowId)
                    ->getBorders()->getOutline()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)
                    ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF808080'));
            }
        }

        $fileName = 'qurbana_list.xlsx';
        $writer = new Xlsx($spreadsheet);
        $filePath = sys_get_temp_dir() . '/' . $fileName;
        ob_end_clean();
        $writer->save($filePath);
        return Yii::$app->response->sendFile($filePath, $fileName, ['inline' => true]);
    }
}
