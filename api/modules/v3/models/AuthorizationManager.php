<?php
namespace api\modules\v3\models;

use yii;
use yii\base\Model;
use common\models\basemodels\BaseModel;
use common\models\extendedmodels\ExtendedUserCredentials;
use common\models\extendedmodels\ExtendedLocalAuthenticationRegisteredUser;

class AuthorizationManager extends Model
{

    public static function validateUser($username, $password, $deviceKey, $deviceType, $deviceIdentifier, $appVersionNo, $activeAppVersion, $deviceIdentifierLA)
    {  
        $response = new \stdClass();
        $userToken = null;
        $OTPSent = false;
        try {
            $response->value = ExtendedUserCredentials::findByMobileNo($username);

            if ($response->value) {

                $isLocalAuthentication = false;

                if (($deviceIdentifierLA != '' && $deviceIdentifierLA != null)) {
                    $isLocalAuthentication = true;
                    $extendedModelLocalAuthRegisteredUser = new ExtendedLocalAuthenticationRegisteredUser();
                    $hasDeviceRegisteredForLocalAuthentication = $extendedModelLocalAuthRegisteredUser->hasDeviceOfUserRegisteredForLocalAuthentication($response->value['userid'], $deviceIdentifierLA);
                
                    if ($hasDeviceRegisteredForLocalAuthentication) {
                        $extendedModelLocalAuthRegisteredUser->logLastLoginWithLocalAuthentication($response->value['userid'], $deviceIdentifierLA);
                    }
                }

                if ($isLocalAuthentication && !$hasDeviceRegisteredForLocalAuthentication) {
                    $response->StatusCode = 604;
                    $response->ErrorMessage = "Device is not registered. Please register the device.";
                    $response->Status = false;
                    return $response;
                } else if (($password != '' && $password != null) && (empty($response->value['userpin']) || !Yii::$app->getSecurity()->validatePassword($password, $response->value['userpin']))) {
                    $response->StatusCode = 500;
                    $response->ErrorMessage = "Invalid username or password.";
                    $response->Status = false;
                    return $response;
                } else {
                    // success login
                    if ($response->value['ActiveInstitution']) {
                        self::updateUserLastLogin($response->value['userid'], date(yii::$app->params['dateFormat']['sqlDandTFormat']));
                        $token = ExtendedUserCredentials::addUserToken($response->value['userid'], $deviceKey);
                        $response->token = ($token) ? $token : "";
                        // token to be returned
                        if ($password != null && $password != "") {
                            if ($deviceKey != "" && $deviceKey != null) {
                                if ($deviceIdentifier) {
                                    self::deleteDeviceDetailsUsingDeviceIdentifier($deviceIdentifier);
                                }
                                self::addOrUpdateDeviceKey($deviceKey, $response->value['userid'], $response->value['usertype'], $response->value['institutionid'], $response->value['timezone'], $deviceType, $deviceIdentifier, $appVersionNo);
                            }
                        }
                        
                        $response->Status = true;
                        $response->ErrorMessage = "Successfully logged in";
                    } else {
                        $response->StatusCode = 601;
                        $response->ErrorMessage = "Sorry, we’re unable to take you in. The institution is not active. Please contact the manager.";
                        $response->Status = false;
                        return $response;
                    }
                }
                $response->RequiresOtp = false;
                if(yii::$app->params['disableOTP']) {
                    $response->ErrorMessage = "OTP service was temporarily disabled.";
                    $response->Status = false;
                    $response->StatusCode = 502;
                    return $response;
                } 
                if (($password == null || $password == "")) {
                    if ($response->value['initiallogin'] == 0) {
                        $countryCodeList = self::getUserMobileCountryCodes($response->value['userid']);
                        /* sending otp to member email ids */
                        $memberEmails = Yii::$app->db->createCommand("SELECT member.member_email 
                        FROM member 
                        LEFT JOIN usermember 
                        ON usermember.memberid=member.memberid 
                        WHERE usermember.userid=:userId"
                        )->bindValue(':userId', $response->value['userid'])
                        ->queryAll();
                        $validMemberEmails = [];
                        foreach ($memberEmails as $memberEmail) {
                            if (filter_var($memberEmail['member_email'], FILTER_VALIDATE_EMAIL) && !in_array($memberEmail['member_email'],$validMemberEmails)) {
                                array_push($validMemberEmails,$memberEmail['member_email']);
                            }
                        }
						$emailOTPSent = false;
                        if(count($validMemberEmails)>0)
                        {
                            $emailOTPSent = yii::$app->communicationManager->sendMailOTP($response->value['userid'],$validMemberEmails);
                            $response->RequiresOtp = true; 
                        }
                        if (!empty($countryCodeList)) {
                            $canSendOtp = false;
                            foreach ($countryCodeList as $key => $value) {
                                switch ($value['mobilecountrycode']) {
                                    case yii::$app->params['defaultCountry']['COUNTRY_CODE']:
                                        $response->RequiresOtp = true;
                                        $canSendOtp = true;
                                        break;
                                    case yii::$app->params['defaultCountry']['INDIA_COUNTRY_CODE']:
                                        $response->RequiresOtp = true;
                                        $canSendOtp = true;
                                        break;
                                }
                            }
                            if ($canSendOtp) {
                                // Sending OTP
                                $OTPSent = yii::$app->communicationManager->SendOTP($response->value['userid'], $username);
                            } else {
                                $OTPSent = true;
                                $response->otpInfoText = 'Bypass the OTP - already it is working fine.';
                            }
                        } else {
                            $OTPSent = true;
                        }
                        
                        if ($OTPSent || $emailOTPSent) {
                            // $response->ErrorMessage = "An OTP has been sent to your registered mobile number.";
                            $response->ErrorMessage = "An OTP has been sent to your registered mobile number and/or email id.";
                            $response->Status = true;
                            $response->StatusCode = 200;
                        } else {
                            $response->Status = false;
                            $response->StatusCode = 500;
                            $response->otpInfoText = 'Something went wrong. Please try again';
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
                    }
                }
            } else {
                $response->ErrorMessage = "Dear member you need to register your mobile number before using the application. Please contact manager.";
                $response->StatusCode = 500;
                $response->Status = false;
            }
        } catch (\Exception $e) {
            $response->ErrorMessage = "Sorry, we’re unable to take you in. Please try again.";
            $response->Status = false;
            $response->ErrorCode = 1;
            $response->StatusCode = 500;
            yii::error($e->getMessage());
        }
        
        return $response;
    }

    protected static function deleteDeviceDetailsUsingDeviceIdentifier($deviceIdentifier)
    {
        $sql = "DELETE FROM devicedetails WHERE deviceidentifier = :deviceIdentifier";
        $flag = false;
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            $flag = $db->createCommand($sql)
                ->bindValue(':deviceIdentifier', $deviceIdentifier)
                ->execute();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            return $flag;
        }
        return $flag;
    }

    protected static function addOrUpdateDeviceKey($deviceKey, $userId, $usertype, $institutionid, $timeZone, $devicetype, $deviceIdentifier, $appVersionNo)
    {
        $params = [
            ':p_deviceid' => $deviceKey,
            ':p_userid' => $userId,
            ':p_usertype' => $usertype,
            ':p_institutionid' => $institutionid,
            ':p_registeredon' => BaseModel::convertToUserTimezone(gmdate('Y-m-d H:i:s'), $timeZone), // fix me for timezone
            ':p_devicetype' => $devicetype,
            ':deviceidentifier' => $deviceIdentifier,
            ':appversion' => $appVersionNo
        ];
        
        try {
            $flag = Yii::$app->db->createCommand("CALL setdevicedetails(
                        :p_deviceid,
                        :p_userid,
                        :p_usertype,
                        :p_institutionid,
                        :p_registeredon,
                        :p_devicetype,
                        :deviceidentifier,
                        :appversion)")
                ->bindValues($params)
                ->execute();
        } catch (\Exception $e) {
            yii::error($e->getMessage());
        }
        return true;
    }

    protected static function updateUserLastLogin($userId, $lastLogin)
    {
        $sql = "UPDATE usercredentials SET lastLogin = :lastLogin WHERE id =:userId";
        $db = Yii::$app->db;
        try {
            $db->createCommand($sql)
                ->bindValue(':lastLogin', $lastLogin)
                ->bindValue(':userId', $userId)
                ->execute();
        } catch (\Exception $e) {
            yii::error($e->getMessage());
        }
        return true;
    }

    protected static function getUserMobileCountryCodes($userId)
    {
        try {
            return Yii::$app->db->createCommand("CALL getuser_mobilecountrycode(:userId)")
                ->bindvalue(':userId', $userId)
                ->queryAll();
        } catch (\Exception $e) {
            yii::error($e->getMessage());
        }
        return false;
    }

    public static function validateOtp($mobilenumber, $otp)
    {
        $response = new \stdClass();
        try {
            $userCredentials = ExtendedUserCredentials::findByMobileNumber($mobilenumber);
            if (! empty($userCredentials)) {
                $userOTP = (string) $userCredentials->otp;
                if ($userOTP && strlen($userOTP) == 4 && $userCredentials->otpcreateddatetime) {
                    $fdate = new \DateTime($userCredentials->otpcreateddatetime);
                    $fdate->modify("+48 hours");
                    
                    if ($fdate->format("Y-m-d H:i:s") > gmdate('Y-m-d H:i:s')) {
                        if ($userOTP == $otp) {
                            $response->ErrorMessage = "OTP verification was successful";
                            $response->ErrorCode = 200;
                            $response->Status = true;
                            $userCredentials->otp = null;
                            $userCredentials->otpcreateddatetime = null;
                            $userCredentials->update(false);
                        } else {
                            $response->ErrorMessage = "The given OTP is invalid. Please try again.";
                            $response->ErrorCode = 501;
                            $response->Status = false;
                        }
                    } else {
                        $response->ErrorMessage = "The given OTP is invalid. Please try again.";
                        $response->ErrorCode = 501;
                        $response->Status = false;
                    }
                } else {
                    $response->ErrorMessage = "The given OTP is invalid. Please try again.";
                    $response->ErrorCode = 501;
                    $response->Status = false;
                }
            } else {
                $response->ErrorMessage = "Invalid user";
                $response->ErrorCode = 501;
                $response->Status = false;
            }
        } catch (\Exception $e) {
            $response->ErrorMessage = "An error occurred while processing the request";
            $response->ErrorCode = 500;
            $response->Status = false;
            yii::error($e->getMessage());
        }
        return $response;
    }

    public static function resetPasscode($token, $passcode, $isFirstLogin)
    {
        $response = new \stdClass();
        try {
            $userCredentials = ExtendedUserCredentials::findIdentityByAccessToken($token);
            if ($userCredentials) {
                $userCredentials->scenario = ExtendedUserCredentials::SCENARIO_RESET_PASSCODE;
                $userCredentials->userpin = Yii::$app->getSecurity()->generatePasswordHash($passcode);
                $userCredentials->initiallogin = "1";
                if ($userCredentials->update()) {
                    $response->Status = true;
                    $response->statusCode = 200;
                    $response->ErrorMessage = "Pin changed successfully";
                } elseif (empty($userCredentials->getErrors())) {
                    $response->Status = true;
                    $response->statusCode = 200;
                    $response->ErrorMessage = "Pin changed successfully";
                } else {
                    $response->Status = false;
                    $response->statusCode = 500;
                    $response->ErrorMessage = "An error occurred while processing the request";
                }
            } else {
                $response->ErrorMessage = "Session has expired. Please login again.";
                $response->statusCode = 498;
                $response->Status = false;
            }
        } catch (\Exception $e) {
            yii::error($e->getMessage());
        }
        return $response;
    }
}
  