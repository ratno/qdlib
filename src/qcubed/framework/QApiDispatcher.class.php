<?php

/**
 * QApiDispatcher
 * Dispatch API request
 *
 * @author Ratno Putro Sulistiyono (ratno@knoqdown.com)
 */
class QApiDispatcher extends QBaseClass
{

    protected $intAuthLevel;
    protected $objToken;
    protected $objUser;
    protected $arrOutput;
    protected $strClassFolderName;
    protected $strClassTaskName;
    protected $strConsumerKey;
    protected $strUsername;
    protected $strPassword;
    protected $strAccessToken;
    protected $intTimestamp;
    protected $strSignature;
    protected $strSignatureMethod;

    public function __construct()
    {
        //get request
        $classfoldername_request = str_replace("-", "_", QApplication::PathInfo(0));
        $classtaskname_request = str_replace("-", "_", QApplication::PathInfo(1));
        $this->strClassFolderName = QConvertNotation::CamelCaseFromUnderscore($classfoldername_request);
        $this->strClassTaskName = QConvertNotation::CamelCaseFromUnderscore($classtaskname_request);

        $this->strConsumerKey = QR::Param("oauth_consumer_key");
        $this->strUsername = QR::Param("oauth_username");
        $this->strPassword = QR::Param("oauth_password");
        $this->strAccessToken = QR::Param("oauth_access_token");
        $this->intTimestamp = QR::Param("oauth_timestamp");
        $this->strSignature = QR::Param("oauth_signature");
        $this->strSignatureMethod = QR::Param("oauth_signature_method");

        // this should be add in the config
        $this->intAuthLevel = QAPI_LEVEL;
    }

    public function Execute()
    {
        // log incoming api & out
        if (strtolower($this->strClassFolderName) == "login") {
            if ($this->VerifyLogin()) {
                // success
                json($this->arrOutput);
            } else {
                // failed
                json($this->arrOutput);
            }
        } else {
            if ($this->Auth()) {
                if ($this->objUser) QApplication::SetApiUser($this->objUser);
                $class_request = QApplication::PathInfo(0);
                $task_request = QApplication::PathInfo(1);
                $class = str_replace(array("-", "_"), "", ucwords(strtolower($class_request)));
                $task = str_replace(array("-"), "_", strtolower($task_request));// memastikan rekues bisa dipisah dengan - atau _ tidak camelcase

                $api = Api::QuerySingle(QQ::AndCondition(QQ::Equal(QQN::Api()->ClassName, $class), QQ::Equal(QQN::Api()->ActionName, $task)));
                if ($api instanceof Api) {
                    $filename = __BASEPATH__ . "/apps/apis/" . $api->ClassName . "/" . $api->Filename;
                    if (file_exists($filename)) {
                        include($filename);
                        $strClassName = $api->ClassName . QString::ConvertToCamelCase($api->ActionName);
                        $objApi = new $strClassName();
                        if ($this->objToken) {
                            $objApi->CurrentUserToken = $this->objToken;
                        }
                        if ($this->objUser) {
                            $objApi->CurrentUser = $this->objUser;
                        }
                        $this->arrOutput = $objApi->GetJson();
                    } else {
                        $this->arrOutput = array("status" => "error", "msg" => QError::ToString(QError::api_not_found), "error_code" => QError::api_not_found);
                    }
                } else {
                    $filename = __BASEPATH__ . "/apps/apis/$class/" . strtolower("{$class}_{$task}") . ".php";
                    if (file_exists($filename) && (QAPI_LEVEL == 1 || QAPI_DEV)) {
                        include($filename);
                        $strClassName = $class . QString::ConvertToCamelCase($task);
                        $objApi = new $strClassName();
                        if ($this->objToken) {
                            $objApi->CurrentUserToken = $this->objToken;
                        }
                        if ($this->objUser) {
                            $objApi->CurrentUser = $this->objUser;
                        }
                        $this->arrOutput = $objApi->GetJson();
                    } else {
                        $this->arrOutput = array("status" => "error", "msg" => QError::ToString(QError::api_invalid), "error_code" => QError::api_invalid);
                    }
                }
                json($this->arrOutput);
            } else {
                json($this->arrOutput);
            }
        }
    }

    public function VerifyLogin()
    {
        if ($this->intAuthLevel == 1) {
            $this->arrOutput = array("status" => "ok");
            return true;
        } else {
            // lets check availability of username, password and consumer key
            if (is_empty($this->strUsername)) {
                $this->arrOutput = array("status" => "error", "msg" => QError::ToString(QError::username_required), "error_code" => QError::username_required);
                return false;
            }
            if (is_empty($this->strPassword)) {
                $this->arrOutput = array("status" => "error", "msg" => QError::ToString(QError::password_required), "error_code" => QError::password_required);
                return false;
            }
            if (($this->intAuthLevel > 2) && is_empty($this->strConsumerKey)) {
                $this->arrOutput = array("status" => "error", "msg" => QError::ToString(QError::consumer_key_required), "error_code" => QError::consumer_key_required);
                return false;
            }

            // at this point, all data needed is ready
            $auth_result = QApplication::Auth($this->strUsername, $this->strPassword . Users::GetSalt());
            if (is_array($auth_result)) {
                $this->arrOutput = array("status" => "error", "msg" => $auth_result['msg'], "error_code" => $auth_result['error_code']);
                return false;
            } else {
                $excludes_data = array(
                    "Id", "Password", "LastLogin", "IsActive", "IsLoginAllowed", "IsOnline", "RoleId", "RoleId_toString", "ProfilePictureFile", "Gcmid"
                );
                if ($this->intAuthLevel > 2) {
                    $objApp = ApplicationRegistry::LoadByConsumerKey($this->strConsumerKey);
                    if ($objApp instanceof ApplicationRegistry) {
                        if ($this->intAuthLevel > 3) {
                            if ($this->strSignatureMethod) {
                                if ($this->strSignature) {
                                    if ($this->intTimestamp) {
                                        $dtt = QDateTime::Now();
                                        $dtt->AddMinutes(QAPI_TIMESTAMP_MINUTES);
                                        if ($this->intTimestamp > $dtt->getTimestamp()) {
                                            $hash_data[] = $this->strUsername;
                                            $hash_data[] = $this->strPassword;
                                            $hash_data[] = $this->strConsumerKey;
                                            $hash_data[] = $this->intTimestamp;
                                            $hash_data[] = $objApp->ConsumerSecret;

                                            $key_data[] = $objApp->ConsumerSecret;
                                            $strHashData = implode("", $hash_data);
                                            $strKeyData = implode("", $key_data);
                                            $signature = hash_hmac($this->strSignatureMethod, $strHashData, $strKeyData);
                                            if ($this->strSignature == $signature) {
                                                // ok
                                            } else {
                                                $this->arrOutput = array("status" => "error", "msg" => QError::ToString(QError::signature_invalid), "error_code" => QError::signature_invalid);
                                                return false;
                                            }
                                        } else {
                                            $this->arrOutput = array("status" => "error", "msg" => QError::ToString(QError::timestamp_invalid), "error_code" => QError::timestamp_invalid);
                                            return false;
                                        }
                                    } else {
                                        $this->arrOutput = array("status" => "error", "msg" => QError::ToString(QError::timestamp_required), "error_code" => QError::timestamp_required);
                                        return false;
                                    }
                                } else {
                                    $this->arrOutput = array("status" => "error", "msg" => QError::ToString(QError::signature_required), "error_code" => QError::signature_required);
                                    return false;
                                }
                            } else {
                                $this->arrOutput = array("status" => "error", "msg" => QError::ToString(QError::signature_method_required), "error_code" => QError::signature_method_required);
                                return false;
                            }
                        }
                        // untuk level 3 maka langsung kesini, untuk diatas 3 akan diproses dahulu persyaratannya
                        $token = UserToken::Create($auth_result, $objApp->Id);
                        $additional_data = array(
                            "ProfilePictureFileUrl" => !is_empty($token->User->ProfilePictureFile) ? (__OTHERS_URL__ . $token->User->ProfilePictureFile) : ""
                        );
                        $user_data = json_decode($token->User->getJson(true, $additional_data, $excludes_data), true);
                        $this->arrOutput = array("status" => "ok", "user" => $user_data);
                        return true;
                    } else {
                        $this->arrOutput = array("status" => "error", "msg" => QError::ToString(QError::consumer_key_invalid), "error_code" => QError::consumer_key_invalid);
                        return false;
                    }
                } else {
                    $additional_data = array(
                        "ProfilePictureFileUrl" => !is_empty($auth_result->ProfilePictureFile) ? (__OTHERS_URL__ . $auth_result->ProfilePictureFile) : ""
                    );
                    $excludes_data[] = "AccessToken";
                    $excludes_data[] = "AccessTokenSecret";
                    $user_data = json_decode($auth_result->getJson(true, $additional_data, $excludes_data), true);
                    $this->arrOutput = array("status" => "ok", "user" => $user_data);
                    return true;
                }
            }
        }
    }

    public function Auth()
    {
        /*
         * There is some probability of auth method:
         * 1. publicly open, whithout auth
         * 2. send username and password, for every request (which is bad)
         * 3. send only token, without signature, which is not bad
         * 4. send token, timestamp, signature and method, which good
         * 5. same with point 4, and add nonce, which is best so far for oauth
         */

        if ($this->intAuthLevel == 1) {
            return true;
        } else {
            if ($this->intAuthLevel == 2) {
                // lets check availability of username and password
                if (is_empty($this->strUsername)) {
                    $this->arrOutput = array("status" => "error", "msg" => QError::ToString(QError::username_required), "error_code" => QError::username_required);
                    return false;
                }
                if (is_empty($this->strPassword)) {
                    $this->arrOutput = array("status" => "error", "msg" => QError::ToString(QError::password_required), "error_code" => QError::password_required);
                    return false;
                }

                $auth_result = QApplication::Auth($this->strUsername, $this->strPassword . Users::GetSalt());
                if (is_array($auth_result)) {
                    $this->arrOutput = array("status" => "error", "msg" => $auth_result['msg'], "error_code" => $auth_result['error_code']);
                    return false;
                } else {
                    $this->objUser = $auth_result;
                    return true;
                }
            } else {
                if ($this->strAccessToken) {
                    $check_token = UserToken::Check($this->strAccessToken);
                    if ($check_token['status'] == "ok") {
                        $this->objToken = $check_token['token'];
                        $this->objUser = $this->objToken->User;
                        if ($this->intAuthLevel == 3) {
                            return true;
                        } else {
                            if ($this->strSignatureMethod) {
                                if ($this->strSignature) {
                                    if ($this->intTimestamp) {
                                        $dtt = QDateTime::Now();
                                        $dtt->AddMinutes(QAPI_TIMESTAMP_MINUTES);
                                        if ($this->intTimestamp > $dtt->getTimestamp()) {
                                            $hash_data[] = $this->strAccessToken;
                                            $hash_data[] = $this->intTimestamp;
                                            $hash_data[] = $this->objToken->AccessTokenSecret;
                                            $hash_data[] = $this->objToken->App->ConsumerKey;

                                            $key_data[] = $this->objToken->AccessTokenSecret;
                                            $key_data[] = $this->objToken->App->ConsumerSecret;
                                            $strHashData = implode("", $hash_data);
                                            $strKeyData = implode("", $key_data);
                                            $signature = hash_hmac($this->strSignatureMethod, $strHashData, $strKeyData);
                                            if ($this->strSignature == $signature) {
                                                return true;
                                            } else {
                                                $this->arrOutput = array("status" => "error", "msg" => QError::ToString(QError::signature_invalid), "error_code" => QError::signature_invalid);
                                                return false;
                                            }
                                        } else {
                                            $this->arrOutput = array("status" => "error", "msg" => QError::ToString(QError::timestamp_invalid), "error_code" => QError::timestamp_invalid);
                                            return false;
                                        }
                                    } else {
                                        $this->arrOutput = array("status" => "error", "msg" => QError::ToString(QError::timestamp_required), "error_code" => QError::timestamp_required);
                                        return false;
                                    }
                                } else {
                                    $this->arrOutput = array("status" => "error", "msg" => QError::ToString(QError::signature_required), "error_code" => QError::signature_required);
                                    return false;
                                }
                            } else {
                                $this->arrOutput = array("status" => "error", "msg" => QError::ToString(QError::signature_method_required), "error_code" => QError::signature_method_required);
                                return false;
                            }
                        }
                    } else {
                        $this->arrOutput = $check_token;
                        return false;
                    }
                } else {
                    $this->arrOutput = array("status" => "error", "msg" => QError::ToString(QError::access_token_required), "error_code" => QError::access_token_required);
                    return false;
                }
            }
        }
    }
}
