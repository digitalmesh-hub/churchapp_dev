<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;


class OfflineFileHandlerController extends Controller
{
	public function actionDeleteFile()
	{
		$this->deleteAgedFile();
	}
	
	protected function deleteAgedFile()
	{
		$dir =  Yii::getAlias('@service').'/'.Yii::$app->params['contacts']['contactPath'];
		// Open a directory, and read its contents
		if (is_dir($dir)){

		//Get a list of all of the file names in the folder.
		$files = glob($dir . '/*');
		 
		//Loop through the file list.
		foreach($files as $file){
		    //Make sure that this is a file and not a directory.
		    if(is_file($file)){
		        //Use the unlink function to delete the file.
		        //unlink($file);
		        $thatTime = strtotimeNew("-15 minutes");
		        $thisTime = filectime($file);
		        if($thisTime <= $thatTime) {
		        	unlink($file);
		        }
			}
		}
		}
	}
}