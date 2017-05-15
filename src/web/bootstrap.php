<?php
//ini_set('session.cookie_httponly', 1);
//ini_set('session.use_only_cookies', 1);
//ini_set('session.cookie_secure', 1);

require_once(dirname(dirname(__FILE__)) . '/app/qd/config.php');
include_once(__BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/func.php');
require_once(__BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/qcubed.inc.php');

abstract class QApplication extends QApplicationBase
{
    /**
     * This is called by the PHP5 Autoloader.  This method overrides the
     * one in ApplicationBase.
     *
     * @return void
     */
    public static function Autoload($strClassName)
    {
        // First use the QCubed Autoloader
        if (!parent::Autoload($strClassName)) {
            // TODO: Run any custom autoloading functionality (if any) here...
            $filename = __BASEPATH__ . "/app/qd/models/$strClassName.class.php";
            if (file_exists($filename)) include_once($filename);
        }
    }

    ////////////////////////////
    // QApplication Customizations (e.g. EncodingType, etc.)
    ////////////////////////////
    // public static $EncodingType = 'ISO-8859-1';

    ////////////////////////////
    // Additional Static Methods
    ////////////////////////////
    // TODO: Define any other custom global WebApplication functions (if any) here...

    public static function Auth($username, $password, $encription = "sha1")
    {
        $objUser = QApplication::GetUser($username);
        if ($objUser) {
            if ($objUser->Password == $encription($password)) {
                // success
                if ($objUser->IsActive) {
                    if ($objUser->IsLoginAllowed) {
                        // go ahead
                        return $objUser;
                    } else {
                        return array(
                            "type" => "warning",
                            "msg" => QError::ToString(QError::login_disallow),
                            "error_code" => QError::login_disallow
                        );
                    }
                } else {
                    return array(
                        "type" => "warning",
                        "msg" => QError::ToString(QError::account_inactive),
                        "error_code" => QError::account_inactive
                    );
                }
            } else {
                return array(
                    "type" => "error",
                    "msg" => QError::ToString(QError::password_invalid),
                    "error_code" => QError::password_invalid
                );
            }
        } else {
            return array(
                "type" => "error",
                "msg" => QError::ToString(QError::username_invalid),
                "error_code" => QError::username_invalid
            );
        }
    }

    public static function GetUser($username = "")
    {
        if ($username != "") {
            $objUser = Users::LoadByUsername($username, QQ::Clause(QQ::Expand(QQN::Users()->Role)));
        } else {
            $objUser = @unserialize(QR::getTokenize("objUser"));
            if (!$objUser)
                $objUser = false;
        }
        return $objUser;
    }

    public static function SetUser(Users $objUser)
    {
        QR::setTokenize("objUser", serialize($objUser));
    }

    public static function GetApiUser()
    {
        $objUser = @unserialize(QR::getTokenize("objApiUser"));
        if (!$objUser)
            $objUser = false;
        return $objUser;
    }

    public static function SetApiUser(Users $objUser)
    {
        QR::setTokenize("objApiUser", serialize($objUser));
    }

    public static function DestroyApiSessions()
    {
        QR::clearTokenize("objApiUser");
    }

    public static function CheckTask($strClassName, $strActionName)
    {
        $request = $strClassName . "/" . QConvertNotation::UnderscoreFromCamelCase($strActionName);
        return check_privileges($request);
    }

    public static function GetTask($strClassName, $blnExclude = true, $strExclude = "list,new,import,export", $strOrder = "list,add,view,edit,delete,import,export")
    {
        $arrToReturn = array();
        $task_privs = get_privileges('task_privs', strtolower($strClassName));
        if (is_array($task_privs)) {
            $arrOrder = explode(",", $strOrder);
            //urutkan
            foreach ($arrOrder as $itemOrder) {
                if (array_key_exists($itemOrder, $task_privs)) {
                    $arrToReturn[$itemOrder] = $task_privs[$itemOrder];
                    unset($task_privs[$itemOrder]);
                }
            }
            //sisanya
            foreach ($task_privs as $key => $val) {
                $arrToReturn[$key] = $val;
            }
            unset($task_privs);
            if ($blnExclude) {
                $arrExclude = explode(",", $strExclude);
                foreach ($arrExclude as $itemExclude) {
                    if (array_key_exists($itemExclude, $arrToReturn)) unset ($arrToReturn[$itemExclude]);
                }
            }
            return $arrToReturn;
        } else {
            return false;
        }
    }

    public static function Logout()
    {
        unset($_SESSION);
        session_unset();
        session_destroy();
    }

    public static function Footer($blnReturn = false)
    {
        if ($blnReturn) {
            return __FOOTER__;
        } else {
            echo __FOOTER__;
        }
    }

    public static function GetFilename($classname, $taskname)
    {
        $filename = __BASEPATH__ . "/app/qd/controllers/{$classname}/" . QConvertNotation::UnderscoreFromCamelCase($classname) . "_" . QConvertNotation::UnderscoreFromCamelCase($taskname) . ".php";
        return $filename;
    }

    public static function GetFolder($classname)
    {
        $folder = __BASEPATH__ . "/app/qd/controllers/{$classname}";
        return $folder;
    }

    public static function GetUrlParam($intStart = 3)
    {
        $strPathInfo = rawurldecode(QApplication::$PathInfo);

        // Remove Starting '/'
        if (QString::FirstCharacter($strPathInfo) == '/')
            $strPathInfo = substr($strPathInfo, 1);

        // removing unnecessary items
        $strPathInfoArray = explode('/', $strPathInfo);
        for ($i = 0; $i < $intStart; $i++) {
            unset ($strPathInfoArray[$i]);
        }

        $arrToReturn = array();
        $i = 0;
        // reiterate
        foreach ($strPathInfoArray as $data) {
            $task_privs = explode(":", $data);
            if (count($task_privs) == 2) {
                $arrToReturn[$task_privs[0]] = $task_privs[1];
            } else {
                $arrToReturn[$i] = $data;
                $i++;
            }
        }

        return $arrToReturn;
    }

    public static function GetBreadcrumb($bc = array())
    {
//    if(QApplication::GetUser()){
//      $home = href(url(__MAIN_CLASS__,__MAIN_TASK__),"<div class='bc home_bc'></div>");
//    } else {
//      $home = href(url(__PUBLIC_CLASS__,__PUBLIC_TASK__),"<div class='bc home_bc'></div>");
//    }

        // old style
//    $home = href(__WEB_URL__,"<div class='bc home_bc'></div>");
        // qd style
        $home = "<li>" . href(__WEB_URL__, '<i class="icon-home"></i>') . "</li>";
        $separator = "<div class='bc separator_bc'></div>";
        $clear = "<div class='clear'></div>";

        $arrbc = array();
        $arrbc[] = $home;
        if (count($bc) > 0) {
            foreach ($bc as $item) {
                $arrtemp = explode("|", $item);
                if (count($arrtemp) > 1) {
                    // old style
//          $arrbc[] = "<div class='item_bc'>".href("#",$arrtemp[0],array('class'=>$arrtemp[1]))."</div>";
                    // qd style
//          $arrbc[] = "<li>".href("#",$arrtemp[0],array('class'=>$arrtemp[1]))."</li>";
                    $arrbc[] = "<li>" . $arrtemp[0] . "</li>";
                } else {
                    // old style
//          $arrbc[] = "<div class='item_bc'>$item</div>";
                    // qd style
                    $arrbc[] = "<li>$item</li>";
                }
            }
        }

        // old style
        //$strbc = implode($separator, $arrbc).$clear;

        // qd style
        $strbc = '<nav><div id="jCrumbs" class="breadCrumb module"><ul>';
        $strbc .= implode("", $arrbc);
        $strbc .= '</ul></div></nav>';

        return $strbc;
    }


    public static function GoReferer($else = __WEB_URL__, $is_from_login = false)
    {
        $ref = array_key_exists("HTTP_REFERER", $_SERVER) ? $_SERVER["HTTP_REFERER"] : "";
        if (substr($ref, 0, strlen(__WEB_URL__)) == __WEB_URL__) {
            // jika ref masih di dalam area web, maka referer dipergunakan
            // make sure redirect is not to home/login from home/login (infinite loop)
            $url_ref = complete_url($ref, TRUE);
            if (($url_ref == complete_url("Home/login", TRUE)
                    || strtolower($ref) == complete_url("Home/login", TRUE)
                    || strtolower($ref) == complete_url("index.php/Home/login", TRUE)) && $is_from_login
            ) {
                QApplication::Redirect(__WEB_URL__);
            } else {
                QApplication::Redirect($ref);
            }
        } else {
            if ($else)
                QApplication::Redirect($else);
            else
                QApplication::Redirect(__WEB_URL__);
        }
    }

    public static function InitLog()
    {
        unset ($_SESSION['log']);
        if (__QLOG__) {
            $_SESSION['log'] = array();
        }
    }

    public static function Log($file, $func, $log, $type = "sql")
    {
        if (!__QLOG__) return false;
        $_SESSION['log'][$type][] = array(
            "ts" => date('d/m/Y h:i:s'),
            "file" => $file,
            "func" => $func,
            "log" => $log,
            "trace" => array_reverse(debug_backtrace())
        );
    }

    public static function ViewLog()
    {
        if (!__QLOG__) return false;
        $strOut = "<h2>Logging:</h2>";
        foreach ($_SESSION['log'] as $type => $itemlogs) {
            $banyak = count($itemlogs);
            $strOut .= "<h3>$type($banyak)</h3>";
            $strOut .= "<div class='logblock'>";
            foreach ($itemlogs as $item) {
                $strOut .= "<div class='logitem'>";
                $strOut .= "<span class='logitem_ts'>ts: {$item['ts']}</span><br />";
                $strOut .= "<span class='logitem_file'>file: {$item['file']}</span><br />";
                $strOut .= "<span class='logitem_func'>func: {$item['func']}</span><br />";
                $strOut .= "<span class='logitem_logtitle'>log:</span><br />";
                $log = is_array($item['log']) ? print_r($item['log'], true) : $item['log'];
                $strOut .= "<div class='logitem_logdetail'><pre>{$log}</pre></div>";
                $strOut .= "<span class='logitem_tracetitle'>trace:</span><br />";
                $strOut .= "<div class='logitem_tracedetail'>";
                $strOut .= "<table class='logitem_table' cellpadding='2' cellspacing='0' width='100%'>";
                $nomor = 1;
                foreach ($item['trace'] as $itemtrace) {
                    $trace_func = $itemtrace['function'];
                    $trace_line = (array_key_exists("line", $itemtrace)) ? $itemtrace['line'] : "";
                    $trace_file = (array_key_exists("file", $itemtrace)) ? $itemtrace['file'] : "";
                    $strOut .= "<tr>";
                    $strOut .= "<td class='trace_num'>" . $nomor++ . "</td>";
                    $strOut .= "<td class='trace_func'>{$trace_func}</td>";
                    $strOut .= "<td class='trace_line'>{$trace_line}</td>";
                    $strOut .= "<td class='trace_file'>{$trace_file}</td>";
                    $strOut .= "</tr>";
                }
                $strOut .= "</table>"; // closing tracedetail
                $strOut .= "</div>"; // closing tracedetail
                $strOut .= "</div>"; // closing logitem
            }
            $strOut .= "</div>";
        }
        echo $strOut;
    }
}

// Register the autoloader
spl_autoload_register(array('QApplication', 'Autoload'));

///////////////////////
// Setup Error Handling
///////////////////////
/*
 * Set Error/Exception Handling to the default
 * QCubed HandleError and HandlException functions
 * (Only in non CLI mode)
 *
 * Feel free to change, if needed, to your own
 * custom error handling script(s).
 */
if (array_key_exists('SERVER_PROTOCOL', $_SERVER)) {
    set_error_handler('QcodoHandleError', error_reporting());
    set_exception_handler('QcodoHandleException');
}


////////////////////////////////////////////////
// Initialize the Application and DB Connections
////////////////////////////////////////////////
QApplication::Initialize();
QApplication::InitializeDatabaseConnections();

/////////////////////////////
// Start Session Handler (if required)
/////////////////////////////
session_start();

//////////////////////////////////////////////
// Setup Internationalization and Localization (if applicable)
// Note, this is where you would implement code to do Language Setting discovery, as well, for example:
// * Checking against $_GET['language_code']
// * checking against session (example provided below)
// * Checking the URL
// * etc.
// TODO: options to do this are left to the developer
//////////////////////////////////////////////
if (isset($_SESSION)) {
    if (array_key_exists('country_code', $_SESSION))
        QApplication::$CountryCode = $_SESSION['country_code'];
    if (array_key_exists('language_code', $_SESSION))
        QApplication::$LanguageCode = $_SESSION['language_code'];
}

// Initialize I18n if QApplication::$LanguageCode is set
if (QApplication::$LanguageCode)
    QI18n::Initialize();
else {
    QApplication::$CountryCode = 'id';
    QApplication::$LanguageCode = 'id';
    QI18n::Initialize();
}

// include related to models
QApplicationBase::$ClassFile['qqn'] = __BASEPATH__ . '/app/qd/models/base/QQN.class.php';
@include_once(__BASEPATH__ . '/app/qd/models/base/_class_paths.inc.php');
@include_once(__BASEPATH__ . '/app/qd/models/base/_type_class_paths.inc.php');

// include my enhancement
@include_once(__BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/ActivityAction.class.php');

// include excel reader 2
@include_once(__BASEPATH__ . "/vendor/autoload.php");