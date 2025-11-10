<?php
namespace common\components;

use Yii;
use yii\base\Component;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Exception\RfcComplianceException;

class EmailHandlerComponent extends Component {
   
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
    	$bcc = null
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
                        , ['logo'    => $logo
                        , 'name'    => empty($mailContent['name'])? "<br>" : $mailContent['name']
                        , 'content'    => empty($mailContent['content'])? "<br>" : $mailContent['content']
                        , 'month'    => empty($mailContent['month'])? "<br>" : $mailContent['month']
                        , 'year'    => empty($mailContent['year'])? "<br>" : $mailContent['year']
                        , 'openingdebit' => empty($mailContent['openingdebit'])? "<br>" : $mailContent['openingdebit']
                        , 'openingcredit' => empty($mailContent['openingcredit'])? "<br>" : $mailContent['openingcredit']
                        , 'grandtotaldebit' => empty($mailContent['grandtotaldebit'])? "<br>" : $mailContent['grandtotaldebit']
                        , 'grandtotalcredit' => empty($mailContent['grandtotalcredit'])? "<br>" : $mailContent['grandtotalcredit']
                        , 'balancedebit'  => empty($mailContent['balancedebit'])? "<br>" : $mailContent['balancedebit']
                        , 'balancecredit' => empty($mailContent['balancecredit'])? "<br>" : $mailContent['balancecredit']
                        , 'description' => empty($mailContent['description'])? "<br>" : $mailContent['description']
                        , 'debit' => empty($mailContent['debit'])? "<br>" : $mailContent['debit']
                        , 'credit' => empty($mailContent['credit'])? "<br>" : $mailContent['credit']
                        , 'transactiondate' => empty($mailContent['transactiondate'])? "<br>" : $mailContent['transactiondate']
                        , 'transdata' => empty($mailContent['transdata'])? "<br>" : $mailContent['transdata']
                        , 'institutionname' => empty($mailContent['institutionname'])? "<br>" : $mailContent['institutionname']
                        , 'notehead' => empty($mailContent['notehead'])? "<br>" : $mailContent['notehead']
                        , 'member' => isset($mailContent['member'])? $mailContent['member'] : "<br>" 
                        , 'children' => isset($mailContent['children'])? $mailContent['children']: "<br>"  
                        , 'guest' => isset($mailContent['guest'])? $mailContent['guest'] :  "<br>"
                        , 'total' => isset($mailContent['total'])? $mailContent['total'] : "<br>"
                        , 'generatedlink'=> empty($mailContent['generatedlink'])? "<br>" : $mailContent['generatedlink']
                        , 'srname' => empty($mailContent['srname'])? "<br>" : $mailContent['srname'],
                        'approvelDependantDetails' => empty($mailContent['approvelDependantDetails']) ? [] : $mailContent['approvelDependantDetails'],	
                        'approvelMemberDetails' => empty($mailContent['approvelMemberDetails']) ? []: $mailContent['approvelMemberDetails'],
                        'approvelDepentandList' => empty($mailContent['approvelDepentandList'])? [] : $mailContent['approvelDepentandList'],
                        'institutionLogo' => empty($mailContent['logo'])? "#" :$mailContent['logo'],
                        'toname' => empty($mailContent['toname'])?"<br>" :$mailContent['toname'],
                        'memberno' => empty($mailContent['memberno'])? "<br>" : $mailContent['memberno'],
                        'memberId' => empty($mailContent['memberId'])?"<br>" :$mailContent['memberId'],
                        'ratingImage' => empty($mailContent['ratingImage'])?"<br>" :$mailContent['ratingImage'],
                        'memberImage' => empty($mailContent['memberImage'])?"<br>" : $mailContent['memberImage'],
                        'memberTitle' => empty($mailContent['memberTitle'])?"<br>" : $mailContent['memberTitle'],
                        'updateLink' => empty($mailContent['updateLink'])?"<br>" : $mailContent['updateLink']
                 ]
                )
                ->setFrom($from)
                ->setTo($to)
                ->setSubject($subject);
                
                /* code for attachment    */
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
        catch( \Exception $ex ){
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
