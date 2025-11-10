<?php
namespace common\components;

use Yii;
use yii\base\Component;
use common\models\extendedmodels\ExtendedUserCredentials;
use common\models\basemodels\UserOtp;
use yii\helpers\Url;

class CommunicationManager extends component
{
	public function sendOtp($userId, $mobileNumber) 
	{  
		$OTP = 0;
        $result = false;
        $IsSMSSent = false;
        $response = "";
        $OTPMessage = "";
        $nodeErrorMessage = "";

        try{
        	$userCredentials = ExtendedUserCredentials::findOne($userId);
        	
        	if ($userCredentials) {
        		$fdate = new \DateTime($userCredentials->otpcreateddatetime);
				$fdate->modify("+48 hours");
        		if ($userCredentials->otpcreateddatetime == null || 
        			$fdate->format("Y-m-d H:i:s") < gmdate('Y-m-d H:i:s') || strlen((string)$userCredentials->otp) !=4) { 
        			    $OTP = str_pad (rand(0, pow(10, 4)-1), 4, '0', STR_PAD_LEFT);
                    } else {
                        $OTP = $userCredentials->otp;
                    }
                    if ($userCredentials->initiallogin) {
                       $OTPMessage = "To reset the PIN for Re-member app use OTP " . (string)$OTP . ". - RE-MEMBER";
                    } else {
                       $OTPMessage = "Greetings from Re-member! " . (string)$OTP . " is the OTP to verify your mobile no. Verify and enjoy using Re-member app. - RE-MEMBER";
                    }
               //disabled it for blocking sms
                $response = yii::$app->textMessageHandler->sendSMS($mobileNumber, $OTPMessage); 
                if ($response && $response === "success") {
						$IsSMSSent = true;
                }
                 // Update OTP to the corresponding USER ID
                if ($IsSMSSent) {
                	$userCredentials->otp = $OTP;
                	$userCredentials->otpcreateddatetime = gmdate('Y-m-d H:i:s');
                    $userCredentials->update(false);
                }
                $result = $IsSMSSent ? true : false;
        	}

        }catch(\Exception $e){
			yii::error($e->getMessage());
            $result = false;
        }
        return $result;

	}

	public function sendMailOTP($userId,$email) 
	{  
		$OTP = 0;
        $result = false;
        $IsMailSent = false;
        $response = "";
        $OTPMessage = "";
        try{
			$userCredentials = ExtendedUserCredentials::findOne($userId);
        	if ($userCredentials) {
        		$fdate = new \DateTime($userCredentials->otpcreateddatetime);
				$fdate->modify("+48 hours");
        		if ($userCredentials->otpcreateddatetime == null || 
        			$fdate->format("Y-m-d H:i:s") < gmdate('Y-m-d H:i:s') || strlen((string)$userCredentials->otp) !=4) { 
        			    $OTP = str_pad (rand(0, pow(10, 4)-1), 4, '0', STR_PAD_LEFT);
                    } else {
                        $OTP = $userCredentials->otp;
                    }
                    if ($userCredentials->initiallogin) {
                       $OTPMessage = "To reset the PIN for Re-member app use OTP " . (string)$OTP . ".";
                    } else {
                       $OTPMessage = "Greetings from Re-member! " . (string)$OTP . " is the OTP to verify your email id. Verify and enjoy using Re-member app.";
					}
					$mailContent['content'] = "<table align='center' cellpadding='0' cellspacing='0' border='0' width='100%'bgcolor='#f0f0f0'>
						<tr>
						<td style='padding: 30px 30px 20px 30px;'>
							<table cellpadding='0' cellspacing='0' border='0' width='100%' bgcolor='#ffffff' style='max-width: 650px; margin: auto;'>
							<tr>
								<td style='text-align: left; padding: 0px 50px;' valign='top'>
									<p style='font-size: 18px; margin: 0; line-height: 24px; font-family: 'Nunito Sans', Arial, Verdana, Helvetica, sans-serif; color: #666; text-align: left; padding-bottom: 3%;'>
									   Dear Member,
									</p>
									<p style='font-size: 18px; margin: 0; line-height: 24px; font-family: 'Nunito Sans', Arial, Verdana, Helvetica, sans-serif; color: #666; text-align: left; padding-bottom: 3%;'>
									$OTPMessage
									</p>
								</td>
							</tr>
							<tr>
								<td style='text-align: left; padding: 30px 50px 50px 50px' valign='top'>
									<p style='font-size: 18px; margin: 0; line-height: 24px; font-family: 'Nunito Sans', Arial, Verdana, Helvetica, sans-serif; color: #505050; text-align: left;'>
										Thanks,<br/>Re-member app Team
									</p>
								</td>
							</tr>
							</table>
						</td>
					</tr>
				</table>";
                $mailContent['template'] = "send-otp";

			   $IsMailSent = yii::$app->EmailHandler->sendEmail(
				're-member@digitalmesh.in',
				$email,
				[],
				'OTP - Re-member app',
				$mailContent,
				null,
				null
			);
                 // Update OTP to the corresponding USER ID
                if ($IsMailSent) {
                	$userCredentials->otp = $OTP;
                	$userCredentials->otpcreateddatetime = gmdate('Y-m-d H:i:s');
                    $userCredentials->update(false);
				}
				
					$result = $IsMailSent ? true : false;
        	}

        }catch(\Exception $e){
			yii::error($e->getMessage());
            $result = false;
        }
        return $result;

	}
	
	public function generateOtp($mobileNumber) 
	{
	    $OTPSent = false;
	    $response = new \stdClass();
	    $response->Status = false;
	    try {
	        $userCredentials = ExtendedUserCredentials::findByMobileNumber($mobileNumber);
			if(yii::$app->params['disableOTP']) {
				$response->ErrorMessage = "OTP service was temporarily disabled.";
				$response->ErrorCode = 502;
				$response->Status = true; 
				return $response;
			}
            if ($userCredentials) {
					$countryCodeList = $this->getUserMobileCountryCodes($userCredentials->id);
					$indianNumber =(in_array(yii::$app->params['defaultCountry']['COUNTRY_CODE'], $countryCodeList) || in_array(yii::$app->params['defaultCountry']['INDIA_COUNTRY_CODE'], $countryCodeList)) ? true : false;
					/* sending otp to member email ids */
					$memberEmails = Yii::$app->db->createCommand("SELECT member.member_email 
					FROM member 
					LEFT JOIN usermember 
					ON usermember.memberid=member.memberid 
					WHERE usermember.userid=:userId"
					)->bindValue(':userId', $userCredentials->id)
					->queryAll();
					$validMemberEmails = [];
					foreach ($memberEmails as $memberEmail) {
						if (filter_var($memberEmail['member_email'], FILTER_VALIDATE_EMAIL) && !in_array($memberEmail['member_email'],$validMemberEmails)) {
							array_push($validMemberEmails,$memberEmail['member_email']);
						}
					}
	                if ($indianNumber || count($validMemberEmails)>0) {
						$OTPSent = false;
						$emailOTPSent = false;
						
						if(count($validMemberEmails)>0)
						{
							$emailOTPSent = $this->sendMailOTP($userCredentials->id,$validMemberEmails);
						}
						if($indianNumber)
						{
							$OTPSent = $this->sendOTP($userCredentials->id, $mobileNumber);
						}
	                    if ($OTPSent || $emailOTPSent) {
	                        $response->ErrorMessage = "An OTP has been sent to your registered mobile number and/or email id.";
	                        $response->ErrorCode = 200;
							$response->Status = true;  
	                    }
						if($emailOTPSent && $OTPSent) {
							$response->otpInfoText = 'We have sent the OTP to your email address and phone number';
						}
						elseif($emailOTPSent) {
							$response->otpInfoText = 'We have sent the OTP to your registered email address';
						}
						elseif($OTPSent) {
							$response->otpInfoText = 'We have sent the OTP to your registered phone number';
						}
						else {
							$response->otpInfoText = 'Something went wrong. Please try again';
						}
	                }  else {
	                    $response->ErrorMessage = "The mobile number doest not support OTP. Please set a passcode to continue.";
						$response->otpInfoText = 'Bypass the OTP - already it is working fine.';
	                    $response->ErrorCode = 502;
	                    $response->Status = true;
	                }  
	            }
	        
	    } catch (\Exception $e) {
	        yii::error($e->getMessage());
	    }
	    
	    return $response;
	}
	
	public function generateOtpNew($mobileNumber,$type="initial-login", $canSendSms = true, $canSendEmail = false) 
	{
	    $response = new \stdClass();
	    $response->Status = false;
		$response->ErrorCode = 502;
	    try {
			if(yii::$app->params['disableOTP'] || (!$canSendSms && !$canSendEmail)) {
				$response->ErrorMessage = "OTP service was temporarily disabled. Please contact your administrator.";
				$response->Status = true; 
				return $response;
			} 
	        $userCredentials = ExtendedUserCredentials::findByMobileNumber($mobileNumber);
            if ($userCredentials) {
					$countryCodeList = $canSendSms ? $this->getUserMobileCountryCodes($userCredentials->id) : [];

					$defaultCountry = yii::$app->params['defaultCountry'] ?? [ 
						'INDIA' => 94,
						'COUNTRY_CODE' => +91,
						'INDIA_COUNTRY_CODE' => 91
					];

					$indianNumber =(in_array($defaultCountry['COUNTRY_CODE'], $countryCodeList) || in_array($defaultCountry['INDIA_COUNTRY_CODE'], $countryCodeList)) ? true : false;
					/* sending otp to member email ids */
					$memberEmails = [];
					if($canSendEmail) {

						$memberEmails = Yii::$app->db->createCommand("SELECT member.member_email 
						FROM member 
						LEFT JOIN usermember 
						ON usermember.memberid=member.memberid 
						WHERE usermember.userid=:userId"
						)->bindValue(':userId', $userCredentials->id)
						->queryAll();
					}
					$validMemberEmails = [];
					foreach ($memberEmails as $memberEmail) {
						if (filter_var($memberEmail['member_email'], FILTER_VALIDATE_EMAIL) && !in_array($memberEmail['member_email'],$validMemberEmails)) {
							array_push($validMemberEmails,$memberEmail['member_email']);
						}
					}
	                if ($indianNumber || count($validMemberEmails)>0) {
						$OTPSent = false;
						$emailOTPSent = false;
						$otp = mt_rand(1111,9999);
						
						if(count($validMemberEmails)>0)
						{
							$emailOTPSent = $this->sendMailOTPNew($validMemberEmails,$otp,$type);
						}
						if($indianNumber)
						{
							$OTPSent = $this->sendOtpNew($mobileNumber,$otp,$type);
						}
	                    if ($OTPSent || $emailOTPSent) {
	                        $response->ErrorMessage = "An OTP has been sent to your registered mobile number and/or email id.";
	                        $response->ErrorCode = 200;
							$response->Status = true;

							$model = UserOtp::find()
								->where([
									'mobile_number' => $mobileNumber, 
									'request_type' => $type 
								])->one();
							if ($model === null) {
								$model = new UserOtp();
								$model->created_at = date('Y-m-d H:i:s');
							}
							$model->mobile_number = $mobileNumber;
							$model->request_type = $type;
							$model->otp = $otp;
							$model->updated_at = date('Y-m-d H:i:s');

							if(!$model->save()) {
								$response->ErrorMessage = "Something went wrong. Please try again";
								$response->otpInfoText = 'Something went wrong. Please try again';
								$response->ErrorCode = 502;
								$response->Status = false;
							}
							
							if($emailOTPSent && $OTPSent) {
								$response->otpInfoText = 'We have sent the OTP to your email address and phone number';
							} elseif($emailOTPSent && !$OTPSent) {
								$response->otpInfoText = 'We have sent the OTP to your registered email address';
							} else {
								$response->otpInfoText = 'We have sent the OTP to your registered phone number';
							}  
	                    }
						else {
							$response->otpInfoText = 'Something went wrong. Please try again';
						}
	                }  else {
	                    $response->ErrorMessage = ($type == 'profile-edit') ? 'We are unable to send OTP to your mobile number. Please contact your administrator.' : "The mobile number doest not support OTP. Please set a passcode to continue.";
						$response->otpInfoText = 'Bypass the OTP - already it is working fine.';
	                    $response->ErrorCode = 502;
	                    $response->Status = true;
	                }  
	            }
	        
	    } catch (\Exception $e) {
	        yii::error($e->getMessage());
	    }
	    
	    return $response;
	}

	public function generateOtpAndSendEmail(string $email, string $requestType)
	{
		$response = new \stdClass();
	    $response->Status = false;
		$response->ErrorCode = 502;
		try {
			if(yii::$app->params['disableOTP']) {
				$response->ErrorMessage = "OTP service was temporarily disabled. Please contact your administrator.";
				$response->Status = true; 
				return $response;
			}
			$otp = mt_rand(1111,9999);
			$emailOTPSent = $this->sendMailOTPNew([$email],$otp,$requestType);
			if ($emailOTPSent) {

				$response->Status = true;
				$response->ErrorCode = 200;
				$response->otpInfoText = 'We have sent the OTP to your registered email address';
				$response->ErrorMessage = "An OTP has been sent to your registered mobile number and/or email id.";

				$model = UserOtp::find()
					->where([
						'email' => $email, 
						'request_type' => $requestType 
					])->one();
				if ($model === null) {
					$model = new UserOtp();
					$model->created_at = date('Y-m-d H:i:s');
				}
				$model->email = $email;
				$model->request_type = $requestType;
				$model->otp = $otp;
				$model->updated_at = date('Y-m-d H:i:s');

				if(!$model->save()) {
					$response->ErrorMessage = "Something went wrong. Please try again";
					$response->otpInfoText = 'Something went wrong. Please try again';
					$response->ErrorCode = 502;
					$response->Status = false;
				} 
			}
			else {
				$response->otpInfoText = 'Something went wrong. Please try again';
			}
		} catch (\Exception $e) {
			yii::error($e->getMessage());
		}

		return $response;
	}

	public function sendOtpNew($mobileNumber, $otp, $requestType) 
	{  
        $result = false;
        $IsSMSSent = false;
        $response = "";
        $OTPMessage = "";

        try{
			if($requestType == 'profile-edit') {
				$OTPMessage = "Please use the OTP " . (string)$otp . " to verify your mobile number. Do not share this verification code with anyone - RE-MEMBER";
			} else if ($requestType == 'forgot-password') {
				$OTPMessage = "To reset the PIN for Re-member app use OTP " . (string)$otp . ". - RE-MEMBER";
			} else {
				$OTPMessage = "Greetings from Re-member! " . (string)$otp . " is the OTP to verify your mobile no. Verify and enjoy using Re-member app. - RE-MEMBER";
			}
			//disabled it for blocking sms
			$response = yii::$app->textMessageHandler->sendSMS($mobileNumber, $OTPMessage); 
			if ($response && $response === "success") {
				$IsSMSSent = true;
			}
            $result = $IsSMSSent ? true : false;

        }catch(\Exception $e){
			yii::error($e->getMessage());
            $result = false;
        }
        return $result;

	}

	public function sendMailOTPNew($email, $otp, $requestType) 
	{ 
        $result = false;
        $OTPMessage = "";
        try{
			if($requestType == 'profile-edit') {
				$OTPMessage = "Please use the OTP " . (string)$otp . " to verify your mobile number. Do not share this verification code with anyone.";
			} else if ($requestType == 'forgot-password') {
				$OTPMessage = "To reset the PIN for Re-member app use OTP " . (string)$otp . ".";
			} else {
				$OTPMessage = "Greetings from Re-member! " . (string)$otp . " is the OTP to verify your email id. Verify and enjoy using Re-member app.";
			}
			$mailContent['content'] = "<table align='center' cellpadding='0' cellspacing='0' border='0' width='100%'bgcolor='#f0f0f0'>
				<tr>
					<td style='padding: 30px 30px 20px 30px;'>
						<table cellpadding='0' cellspacing='0' border='0' width='100%' bgcolor='#ffffff' style='max-width: 650px; margin: auto;'>
						<tr>
							<td style='text-align: left; padding: 0px 50px;' valign='top'>
								<p style='font-size: 18px; margin: 0; line-height: 24px; font-family: 'Nunito Sans', Arial, Verdana, Helvetica, sans-serif; color: #666; text-align: left; padding-bottom: 3%;'>
									Dear Member,
								</p>
								<p style='font-size: 18px; margin: 0; line-height: 24px; font-family: 'Nunito Sans', Arial, Verdana, Helvetica, sans-serif; color: #666; text-align: left; padding-bottom: 3%;'>
								$OTPMessage
								</p>
							</td>
						</tr>
						<tr>
							<td style='text-align: left; padding: 30px 50px 50px 50px' valign='top'>
								<p style='font-size: 18px; margin: 0; line-height: 24px; font-family: 'Nunito Sans', Arial, Verdana, Helvetica, sans-serif; color: #505050; text-align: left;'>
									Thanks,<br/>Re-member app Team
								</p>
							</td>
						</tr>
						</table>
					</td>
				</tr>
			</table>";
			$mailContent['template'] = "send-otp";

			$IsMailSent = yii::$app->EmailHandler->sendEmail(
				're-member@digitalmesh.in',
				$email,
				[],
				'OTP - Re-member app',
				$mailContent,
				null,
				null
			);
			$result = $IsMailSent ? true : false;

        }catch(\Exception $e){
			yii::error($e->getMessage());
            $result = false;
        }
        return $result;

	}

	protected function getUserMobileCountryCodes($userId)
	{
	    try {
	        return Yii::$app->db->createCommand(
	            "CALL getuser_mobilecountrycode(:userId)"
	            )
	            ->bindvalue(':userId', $userId)
	            ->queryOne();
	    } catch (\Exception $e) {
	        yii::error($e->getMessage());
	    }
	    return false;
	}

	/**
	 * Send email to the member for new member registration
	 * @param string $postDetails
	 */
	public function sendEmailForNewMemberRegistration(string $name, array $to, array $cc = [], array $bcc = [])
	{
        $result = false;

        try{

			$link = Url::base(true) . '/member/new-registered-members';

			$requestMessage = "$name has submitted the member request. Please click on the below link to see the details. <a href='$link' target='_blank'>$link</a>";
			
			$mailContent['content'] = "<table align='center' cellpadding='0' cellspacing='0' border='0' width='100%'bgcolor='#f0f0f0'>
				<tr>
					<td style='padding: 30px 30px 20px 30px;'>
						<table cellpadding='0' cellspacing='0' border='0' width='100%' bgcolor='#ffffff' style='max-width: 650px; margin: auto;'>
						<tr>
							<td style='text-align: left; padding: 0px 50px;' valign='top'>
								<p style='font-size: 18px; margin: 0; line-height: 24px; font-family: 'Nunito Sans', Arial, Verdana, Helvetica, sans-serif; color: #666; text-align: left; padding-bottom: 3%;'>
									Dear admin,
								</p>
								<br/>
								<p style='font-size: 18px; margin: 0; line-height: 24px; font-family: 'Nunito Sans', Arial, Verdana, Helvetica, sans-serif; color: #666; text-align: left; padding-bottom: 3%;'>
								$requestMessage
								</p>
							</td>
						</tr>
						<tr>
							<td style='text-align: left; padding: 30px 50px 50px 50px' valign='top'>
								<p style='font-size: 18px; margin: 0; line-height: 24px; font-family: 'Nunito Sans', Arial, Verdana, Helvetica, sans-serif; color: #505050; text-align: left;'>
									Thanks,<br/>Re-member app Team
								</p>
							</td>
						</tr>
						</table>
					</td>
				</tr>
			</table>";
			$mailContent['template'] = "send-otp";

			$IsMailSent = yii::$app->EmailHandler->sendEmail(
				're-member@digitalmesh.in',
				$to,
				$cc,
				'New Member Request',
				$mailContent,
				null,
				null,
				$bcc
			);
			$result = $IsMailSent ? true : false;

        }catch(\Exception $e){
			yii::error($e->getMessage());
            $result = false;
        }
        return $result;
	}

}
