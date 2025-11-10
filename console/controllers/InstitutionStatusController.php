<?php

namespace console\controllers;

use common\models\basemodels\Institution;
use Yii;
use yii\console\Controller;

class InstitutionStatusController extends Controller
{
	/**
	 * To deactivate expired demo institutions
	 */
	public function actionDeactivateExpiredInstitutions()
	{
		$demoInstitutions = Institution::find()
			->where(['demo' => 1])
			->andWhere(['<=', 'demo_expiry', date('Y-m-d')])
			->andWhere(['active'=> 1])
			->all();
		foreach ($demoInstitutions as $demoInstitution) {
			$institutionId   = $demoInstitution->id;
			if (!empty($demoInstitution)) {
				$demoInstitution->active = 0;
				$demoInstitution->modifieddate = date('Y-m-d H:i:s');
				if ($demoInstitution->updateAttributes(['active', 'modifieddate'])) {
					Yii::info('Demo institution with id: ' . $institutionId . ' deactivated successfully');
				} else {
					Yii::info('Error in deactivating demo institution with id: ' . $institutionId);
				}
			}
		}
	}
}