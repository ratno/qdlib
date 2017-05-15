<?php
require(__BASEPATH__ . '/app/qd/models/base/UserTokenBase.php');

/**
 * @author Ratno Putro Sulistiyono, ratno@knoqdown.com
 * @company KNOQDOWN STUDIO (http://www.knoqdown.com)
 *
 * @package
 * @subpackage DataObjects
 *
 */
class UserToken extends UserTokenBase
{
    public static function GetUniqueColumns()
    {
        $arrColumns = array();
        $arrColumns['access_token'] = 'AccessToken';
        return $arrColumns;
    }

    public static function Create(Users $objUser, $intAppId)
    {
        $single = Setting::Get("single_user_mode");
        if (($single instanceof Setting) && strtolower($single) == "true") {
            // in active kan dulu yang lainnya
            $objTokens = UserToken::QueryArray(
                QQ::AndCondition(
                    QQ::Equal(QQN::UserToken()->UserId, $objUser->Id),
                    QQ::Equal(QQN::UserToken()->IsActive, TRUE)
                )
            );
            if ($objTokens) {
                foreach ($objTokens as $objOldToken) {
                    $objOldToken->IsActive = FALSE;
                    $objOldToken->LogoutTypeId = LogoutType::AutomaticallyLogout;
                    $objOldToken->TsLogout = QDateTime::Now(TRUE);
                    $objOldToken->Save();
                }
            }
        }
        // buat token baru
        $objToken = new UserToken();
        $objToken->AccessToken = (microtime(true) * 10000) . mt_rand(1000000000000, 9999999999999);
        // user application token as salt parameter
        $objToken->AccessTokenSecret = sha1($objUser->Username . $objToken->AccessToken . __TOKEN__);
        $objToken->AppId = $intAppId;
        $objToken->UserId = $objUser->Id;
        $objToken->IsActive = TRUE;
        $objToken->IpAddress = $_SERVER['REMOTE_ADDR'];
        $objToken->Info = $_SERVER['HTTP_USER_AGENT'];
        $objToken->TsCreate = QDateTime::Now(TRUE);
        $objToken->TsExpire = QDateTime::Now()->AddHours(QAPI_EXPIRED_HOURS);
        $objToken->Save();

        // update last token pada tabel user
        $objUser->AccessToken = $objToken->AccessToken;
        $objUser->AccessTokenSecret = $objToken->AccessTokenSecret;
        $objUser->LastLogin = QDateTime::Now();
        $objUser->IsOnline = true;
        $objUser->Save();

        return $objToken;
    }

    public static function Check($strAccessToken)
    {
        $objToken = UserToken::LoadByAccessToken($strAccessToken, QQ::Expand(QQN::UserToken()->User));
        if ($objToken) {
            if ($objToken->IsActive) {
                if ($objToken->TsExpire->IsLaterOrEqualTo(QDateTime::Now())) {
                    // setiap kali pengecekan, maka expire akan diupdate
                    $objToken->TsExpire = QDateTime::Now()->AddHours(QAPI_EXPIRED_HOURS);
                    $objToken->Save();
                    return array("status" => "ok", "msg" => "Valid Access Token", "token" => $objToken);
                } else {
                    $objToken->IsActive = FALSE;
                    $objToken->LogoutTypeId = LogoutType::AutomaticallyLogout;
                    $objToken->TsLogout = QDateTime::Now(TRUE);
                    $objToken->Save();
                    return array("status" => "error", "msg" => QError::ToString(QError::access_token_expired), "error_code" => QError::access_token_expired);
                }
            } else {
                return array("status" => "error", "msg" => QError::ToString(QError::access_token_expired), "error_code" => QError::access_token_expired);
            }
        } else {
            return array("status" => "error", "msg" => QError::ToString(QError::access_token_invalid), "error_code" => QError::access_token_invalid);
        }
    }

    /**
     * Default "to string" handler
     * Allows pages to _p()/echo()/print() this object, and to define the default
     * way this object would be outputted.
     *
     * Can also be called directly via $objUserToken->__toString().
     *
     * @return string a nicely formatted string representation of this object
     */
    public function __toString()
    {
//			return sprintf('UserToken Object  %s',  $this->intId);
        return sprintf('%s', $this->strAccessToken);
    }

    public function printUnique()
    {
        return sprintf('%s', $this->strAccessToken);
    }

    // Override or Create New Load/Count methods
    // (For obvious reasons, these methods are commented out...
    // but feel free to use these as a starting point)
    /*
            public static function LoadArrayBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return an array of UserToken objects
                return UserToken::QueryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::UserToken()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::UserToken()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }

            public static function LoadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a single UserToken object
                return UserToken::QuerySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::UserToken()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::UserToken()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }

            public static function CountBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a count of UserToken objects
                return UserToken::QueryCount(
                    QQ::AndCondition(
                        QQ::Equal(QQN::UserToken()->Param1, $strParam1),
                        QQ::Equal(QQN::UserToken()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }

            public static function LoadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
                // Performing the load manually (instead of using QCubed Query)

                // Get the Database Object for this Class
                $objDatabase = UserToken::GetDatabase();

                // Properly Escape All Input Parameters using Database->SqlVariable()
                $strParam1 = $objDatabase->SqlVariable($strParam1);
                $intParam2 = $objDatabase->SqlVariable($intParam2);

                // Setup the SQL Query
                $strQuery = sprintf('
                    SELECT
                        `user_token`.*
                    FROM
                        `user_token` AS `user_token`
                    WHERE
                        param_1 = %s AND
                        param_2 < %s',
                    $strParam1, $intParam2);

                // Perform the Query and Instantiate the Result
                $objDbResult = $objDatabase->Query($strQuery);
                return UserToken::InstantiateDbResult($objDbResult);
            }
    */


    // Override or Create New Properties and Variables
    // For performance reasons, these variables and __set and __get override methods
    // are commented out.  But if you wish to implement or override any
    // of the data base properties, please feel free to uncomment them.
    /*
            protected $strSomeNewProperty;

            public function __get($strName) {
                switch ($strName) {
                    case 'SomeNewProperty': return $this->strSomeNewProperty;

                    default:
                        try {
                            return parent::__get($strName);
                        } catch (QCallerException $objExc) {
                            $objExc->IncrementOffset();
                            throw $objExc;
                        }
                }
            }

            public function __set($strName, $mixValue) {
                switch ($strName) {
                    case 'SomeNewProperty':
                        try {
                            return ($this->strSomeNewProperty = QType::Cast($mixValue, QType::String));
                        } catch (QInvalidCastException $objExc) {
                            $objExc->IncrementOffset();
                            throw $objExc;
                        }

                    default:
                        try {
                            return (parent::__set($strName, $mixValue));
                        } catch (QCallerException $objExc) {
                            $objExc->IncrementOffset();
                            throw $objExc;
                        }
                }
            }
    */


    // Initialize each property with default values from database definition
    /*
            public function __construct()
            {
                $this->Initialize();
            }
    */

}

?>