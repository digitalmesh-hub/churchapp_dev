<?php
namespace common\components;

use Exception;
use Yii;
use yii\base\Component;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Exception\RfcComplianceException;

class EmailHandler extends Component {
	
	public $fromId;
	public $toId;
	
	/**
	 * Static function to send email
	 * @param String $from
	 * @param Array $to
	 * @param String $subject
	 * @param String content of email as $mailContent
	 * @param Array of filename as $attachment
	 */
	
	public function sendEmail(
		$from = "", 
		$to = array(), 
		$cc = array(),
		$subject = '', 
		$mailContent = array(),
		$attachment = array(), 
		$logo = null,
    	$bcc = array()
	){ 
		try {
			// if no recipient found then return with Zero.
			if ( empty( $to ) ) {
				// default admin email id
				//$to =  Yii::$app->params['email']['to'];
				return 0;
				Yii::$app->end();
			}

			$emailAttachments = array();
			if( $attachment ){
				foreach ( $attachment as $attach ) {
					// here base path from @backend/email/attachments/ { $filename.extension }
					
					$attachPath = Yii::getAlias('@backend'.Yii::$app->params['email']['attachmentPath'].$attach);
					if( file_exists ( $attachPath ) ){
						array_push($emailAttachments, $attachPath);
					}			
				}
			}

			/* if ( !empty($from) ){
				// check multiple mail id
				$from = is_string($from) ? trim($from) : $from;
			}else{
				$from = Yii::$app->params['clientEmail'];
			} */

            if ( !empty($from) && is_array($from)){
                // check multiple mail id
                $from = [Yii::$app->params['clientEmail'] => array_values($from)[0]];
            } else {
                $from = Yii::$app->params['clientEmail'];
            }

			if(!empty($mailContent)) {
				/* Compose mail using Yii2 Swift Mailer */
				//$mailContent['template'] = 'emailmessage'
				$emailObj = Yii::$app->mailer->compose(['html'=>$mailContent['template'] ]
						, ['logo'	=> $logo
						, 'name'	=> empty($mailContent['name'])? "<br>" : $mailContent['name']
						, 'content'	=> empty($mailContent['content'])? "<br>" : $mailContent['content']
				]
				)
				->setFrom($from)
				->setTo($to)
				->setSubject($subject);
				/* code for attachment	*/
				if($emailAttachments){
					foreach ( $emailAttachments as $file ){
						$emailObj->attach($file);
					}
				}

				if (!empty($cc)) {
					$emailObj->setCc($cc);
				}
               
                if (!empty($bcc)) {
                	
                	$emailObj->setBcc($bcc);
                }

				/* end */
				$result = $emailObj->send();
				
			}

		}
		catch( Exception $ex ){
			Yii::error($ex->getMessage());
			//Yii::$app->getSession()->setFlash('danger', Yii::$app->params['email']['emailFailed']);
			return 0;
		}
		catch ( TransportExceptionInterface $ex){
			/* Catch TransportExceptionInterface timeout error */
			Yii::error($ex->getMessage());
			//Yii::$app->getSession()->setFlash('danger', Yii::$app->params['email']['emailFailedNetwork']);
			return 0;
		}
		catch( RfcComplianceException $ex){
            yii::error("Email error occured");
            Yii::error($ex->getMessage());
            return 0;
        }
		if( $result ){
			return 1;
			Yii::$app->end();
		}
		return 0;
		Yii::$app->end();
	}
}
