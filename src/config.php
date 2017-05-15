<?php
/**
 * @author Ratno Putro Sulistiyono, ratno@knoqdown.com
 * @company QD
 *
 */

if(file_exists(config_path("qdlib.php"))) {
    $custom_config = include_once config_path("qdlib.php");
} else {
    $custom_config = include_once __DIR__ ."/config/qdlib.php";
}

if (php_sapi_name() == 'cli' or PHP_SAPI == 'cli') {
    $__SERVER_ADDR = gethostbyname(gethostname());
    $__SERVER_NAME = php_uname("n");
} else {
    $__SERVER_ADDR = $_SERVER['SERVER_ADDR'];
    $__SERVER_NAME = $_SERVER['SERVER_NAME'];
    subdomain('','');
}

define('__GCM_API_KEY__', $custom_config['gcm_api_key']);
define('__GCM_URL__', $custom_config['gcm_url']);
define('__GCM_SENDER_ID__', $custom_config['gcm_sender_id']); // use only on android mobile app

$hostname = ($__SERVER_NAME) ? $__SERVER_NAME : $_SERVER["HTTP_HOST"];

$subdir = str_replace("\\", "/", substr(dirname(dirname(__FILE__)), strlen($_SERVER["DOCUMENT_ROOT"])));

if(file_exists($_SERVER["DOCUMENT_ROOT"] . $subdir . "/web") && is_dir($_SERVER["DOCUMENT_ROOT"] . $subdir . "/web")) {
    $docroot = $_SERVER["DOCUMENT_ROOT"];
    define('WEB_AS_DOCROOT', false);
} else {
    $docroot = dirname($_SERVER["DOCUMENT_ROOT"]);
    define('WEB_AS_DOCROOT', true);
}

define('MY_ERROR_REPORTING', E_ERROR);
define('BYPASS_PRIVS', false);
define('ALLOW_REMOTE_ADMIN', true);
define('DB_CONNECTION_1', serialize(array(
    'adapter' => 'MySqli5',
    'server' => 'localhost',
    'port' => 3306,
    'database' => $custom_config['database'],
    'username' => $custom_config['username'],
    'password' => $custom_config['password'],
    'profiling' => false
)));

define('__URL_REWRITE__', 'none');
define('__MOD_REWRITE__', true);
define('__DOCROOT__', $docroot);
define('__VIRTUAL_DIRECTORY__', '');
define('__HOSTNAME__', "http://" . $hostname);
define('__GABUNG_JSCSS__', true);
define('QAPI_LEVEL', 4);
define('QAPI_TIMESTAMP_MINUTES', -30);
define('QAPI_EXPIRED_HOURS', 2);
define('QAPI_DEV', true);

// default page
define('__MAIN_CLASS__', 'Home');
define('__MAIN_TASK__', 'cpanel');
define('__LOGIN_TASK__', 'login');
define('__DEFAULT_TASK__', serialize(array('index', 'list')));

define('__PUBLIC_CLASS__', 'Home');
//define('__PUBLIC_TASK__', 'index');
define('__PUBLIC_TASK__', __LOGIN_TASK__);

// appsname
define('__APPSNAME__', $custom_config['apps_name']);
// footer
define('__FOOTER__', $custom_config['apps_footer']);

// enabling notification
define('__ENABLE_NOTIFICATION__', false);

// path
if (!defined('__SUBDIRECTORY__')) {
    define('__SUBDIRECTORY__', str_replace("\\", "/", substr(dirname(dirname(__FILE__)), strlen(__DOCROOT__))));
}

// url
define('__BASEURL__', __HOSTNAME__ . __VIRTUAL_DIRECTORY__ . __SUBDIRECTORY__);
if(WEB_AS_DOCROOT) {
    define('__WEB_URL__', __BASEURL__);
    define('__WEB_URL_WITHOUT_HOSTNAME__', __VIRTUAL_DIRECTORY__ . __SUBDIRECTORY__);
} else {
    define('__WEB_URL__', __BASEURL__ . "/web");
    define('__WEB_URL_WITHOUT_HOSTNAME__', __VIRTUAL_DIRECTORY__ . __SUBDIRECTORY__."/web");
}
define('__CSS_URL__', __WEB_URL__ . "/css");
define('__JS_URL__', __WEB_URL__ . "/js");
define('__IMAGES_URL__', __WEB_URL__ . "/images");
define('__OTHERS_URL__', __WEB_URL__ . "/others");

// path
define('__BASEPATH__', __DOCROOT__ . __SUBDIRECTORY__);
define('__WEB_PATH__', __BASEPATH__ . "/web");
define('__CSS_PATH__', __WEB_PATH__ . "/css");
define('__JS_PATH__', __WEB_PATH__ . "/js");
define('__IMAGES_PATH__', __WEB_PATH__ . "/images");
define('__OTHERS_PATH__', __WEB_PATH__ . "/others");

// special case, for which used enormously in system as backbone
define('__QDFJQUERY_BASE__', 'jquery/jquery.min.js');
define('__QDFJQUERY_EFFECTS__', 'jquery/jquery-ui.custom.min.js');
define('__QDFJQUERY_CSS__', 'jquery-ui-themes/ui-lightness/jquery-ui.custom.css');
define('__QDF_JS_CORE__', 'qcubed.js');
// set empty for all jquery because we already loaded it using QDFJQUERY_xxx
define('__JQUERY_BASE__', '');
define('__JQUERY_EFFECTS__', '');
define('__JQUERY_CSS__', '');
define('__QCUBED_JS_CORE__', '');

define('__CACHE__', __BASEPATH__ . '/lib/qcubed/tmp/cache');

//define('__FORM_STATE_HANDLER__', 'QSessionFormStateHandler');
define('__FORM_STATE_HANDLER__', 'QFileFormStateHandler');
// If using the QDbBackedSessionHandler, define the DB index where the table to store the formstates is present
define('__DB_BACKED_FORM_STATE_HANDLER_DB_INDEX__', 1);
// If using QDbBackedSessionHandler, specify the table name which would hold the formstates (must meet the requirements laid out above)
define('__DB_BACKED_FORM_STATE_HANDLER_TABLE_NAME__', 'qc_formstate');
// If using the QFileFormStateHandler, specify the path where QCubed will save the session state files (has to be writeable!)
define('__FILE_FORM_STATE_HANDLER_PATH__', __BASEPATH__ . '/lib/qcubed/tmp'); //must be 777

error_reporting(MY_ERROR_REPORTING);

define('__QLOG__', false);
define('__PERFORMANCE_MEASURE__', false);

define('ERROR_PAGE_PATH', __WEB_PATH__ . '/error/page.php');
//define('ERROR_LOG_PATH', __BASEPATH__ . '/error/log');
//define('ERROR_LOG_FLAG', true);
//define('ERROR_FRIENDLY_PAGE_PATH', __WEB_PATH__ . '/error/friendly.php');
//define('ERROR_FRIENDLY_AJAX_MESSAGE', 'Oops!  An error has occurred.\r\n\r\nThe error was logged, and we will take a look into this right away.');
/** The value for QApplication::$EncodingType constant */
define('__QAPPLICATION_ENCODING_TYPE__', 'UTF-8');
// override timezone
date_default_timezone_set('Asia/Jakarta');
define('__MAXYEAR__', date('Y') + 2);
define('__MINYEAR__', date('Y') - 2);

// set rand for user
define('__TOKEN__', $custom_config['token']);

define('MAX_DB_CONNECTION_INDEX', 9);
define("CACHE_PROVIDER_CLASS", null);
define('CACHE_PROVIDER_OPTIONS', serialize(
  array(
    array('host' => '127.0.0.1', 'port' => 11211,),
      //array('host' => '10.0.2.2', 'port' => 11211, ), // adds a second server
  )
)
);
define("DB_BACKED_SESSION_HANDLER_DB_INDEX", 0);
define("DB_BACKED_SESSION_HANDLER_TABLE_NAME", "qc_session");