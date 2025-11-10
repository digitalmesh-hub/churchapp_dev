<?php
namespace common\components;

use yii;
use yii\base\Component;
use yii\imagine\Image;
use Exception;
use Imagine\Filter\Basic\Thumbnail;

class FileuploadComponent extends component
{
	public $thumbnailWidth  = 200;
	public $thumbnailHeight = 200;
/**
 * 
 * @param unknown $uploadFilename => upload file name
 * @param unknown $targetPath => file to store
 * @param unknown $tempName => tmp file
 * @param string $thumbnailPath => thumbnail path
 * @param string $imageType => type EX: member,event 
 * @param string $isUpdate
 * @param string $existingPath
 * @return multitype:string
 */
	public function uploader($uploadFilename, $targetPath, $tempName, $thumbnailPath = false, $imageType = false, $isUpdate = false, $existingPath = false,$name = false)
	{
		$responseData = [];
		$responseData['orginal'] = '';
		$responseData['thumbnail'] = '';
		if ($uploadFilename && $targetPath && $tempName ) {
			$filename = explode('.', $uploadFilename);
			$extension = end($filename);
			if($name) {
				$name = $name.'.'.$extension;
			} else {
				$name = rand(10,100).time().'.'.$extension;
			}
			$path = \Yii::getAlias('@service').'/'.$targetPath;
			if(!file_exists($path)){
				mkdir($path,0777,true);
			}
			
			$location =  $path.'/'.$name;

			if ($this->processFile($tempName, $location)) {
				$responseData['orginal'] =  '/'.$targetPath.'/'.$name;
				if ($isUpdate) {
					$oldOriginalPath = str_replace("thumbnailImage", "institutionImage", $existingPath);
					$this->removeFile($oldOriginalPath);
				}	
			}
			if ($thumbnailPath){
				if ($imageType) {
					if (isset(Yii::$app->params['image'][$imageType]['thumbnail'])) {
						if (isset(Yii::$app->params['image'][$imageType]['thumbnail']['width'])){
							$this->thumbnailWidth = Yii::$app->params['image'][$imageType]['thumbnail']['width'];
						}
						if (isset(Yii::$app->params['image'][$imageType]['thumbnail']['height'])) {
							$this->thumbnailHeight = Yii::$app->params['image'][$imageType]['thumbnail']['height'];
						}
					}
				}
				$thumbPath = \Yii::getAlias('@service').'/'.$thumbnailPath;
				if(!file_exists($thumbPath)){
					mkdir($thumbPath,0777,true);	
				} 
				$thumbLocation =  $thumbPath.'/'.$name;

				if ($this->processThumbnailImage($location, $thumbLocation)) {
					$responseData['thumbnail'] = '/'.$thumbnailPath.'/'.$name;
					if ($isUpdate) {
                 		$this->removeFile($existingPath);
					}	
				} else {
					$responseData['thumbnail'] = "";
				}
			}
		}
		return $responseData;
	}
	
	protected function processFile($fileName, $location)
	{	
		try { 
			if( move_uploaded_file($fileName, $location)) {
				return true;
			} else {
				return true;
			}
		} catch (Exception $e) {
			yii::error($e->getMessage());
			return true;
		}	
	}
	protected function processThumbnailImage($fileName, $location)
	{	
		try {
			if (file_exists($fileName)) {	
				Image::thumbnail($fileName, $this->thumbnailWidth, $this->thumbnailHeight)
				->save($location, ['quality' => 90]);
			}
		} catch (Exception $e) {
			yii::error($e->getMessage());
		}
		return true;
	}
	protected function removeFile($oldImage) 
	{
		if ($oldImage) {
			try {
			$remove = \Yii::getAlias('@service').'/'.$oldImage;
            	if (file_exists($remove)) {
                unlink($remove);
            	}	
			} catch (Exception $e) {
				yii::error($e->getMessage());
			}
        }
	}

	public function uploadContactsFile($data, $fileName)
	{
		try{
			if(!empty($data)){
				if (!file_exists($fileName)) {
					touch($fileName);
            		chmod($fileName, 0777);	
				}
				$outfile = fopen($fileName, 'w');
				$outfile.fwrite($outfile, $data);
 				fclose($outfile);
 				return true;
			}
		} catch(Exception $e){
			return false;
		}
	}
}
