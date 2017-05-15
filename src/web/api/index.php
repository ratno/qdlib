<?php
include "../bootstrap.php";

ini_set('display_errors', false);
ini_set('log_errors', FALSE);
ini_set('error_reporting', E_ALL);

function QApiHandleException(Exception $__exc_objException)
{
    if (class_exists('QApplicationBase'))
        QApplicationBase::$ErrorFlag = true;

    global $__exc_strType;
    if (isset($__exc_strType))
        return;

    $__exc_objReflection = new ReflectionObject($__exc_objException);

    $__exc_strType = "Exception";
    $__exc_strMessage = $__exc_objException->getMessage();
    $__exc_strObjectType = $__exc_objReflection->getName();

    if ($__exc_objException instanceof QDatabaseExceptionBase) {
        $__exc_objErrorAttribute = new QErrorAttribute("Database Error Number", $__exc_objException->ErrorNumber, false);
        $__exc_objErrorAttributeArray[0] = $__exc_objErrorAttribute;

        if ($__exc_objException->Query) {
            $__exc_objErrorAttribute = new QErrorAttribute("Query", $__exc_objException->Query, true);
            $__exc_objErrorAttributeArray[1] = $__exc_objErrorAttribute;
        }
    }

    if ($__exc_objException instanceof QDataBindException) {
        if ($__exc_objException->Query) {
            $__exc_objErrorAttribute = new QErrorAttribute("Query", $__exc_objException->Query, true);
            $__exc_objErrorAttributeArray[1] = $__exc_objErrorAttribute;
        }
    }

    $__exc_strFilename = $__exc_objException->getFile();
    $__exc_intLineNumber = $__exc_objException->getLine();
    $__exc_strStackTrace = trim($__exc_objException->getTraceAsString());

    json(array(
        "status" => "error",
        "msg" => $__exc_strMessage,
        "type" => $__exc_strType,
        "objecttype" => $__exc_strObjectType,
        "filename" => str_replace(__BASEPATH__, "", $__exc_strFilename . ":" . $__exc_intLineNumber),
        "stacktrace" => str_replace(__BASEPATH__, "", $__exc_strStackTrace)
    ));
}

function get_error_code($__exc_errno)
{
    switch ($__exc_errno) {
        case E_ERROR:
            $__exc_strObjectType = "E_ERROR";
            break;
        case E_WARNING:
            $__exc_strObjectType = "E_WARNING";
            break;
        case E_PARSE:
            $__exc_strObjectType = "E_PARSE";
            break;
        case E_NOTICE:
            $__exc_strObjectType = "E_NOTICE";
            break;
        case E_STRICT:
            $__exc_strObjectType = "E_STRICT";
            break;
        case E_CORE_ERROR:
            $__exc_strObjectType = "E_CORE_ERROR";
            break;
        case E_CORE_WARNING:
            $__exc_strObjectType = "E_CORE_WARNING";
            break;
        case E_COMPILE_ERROR:
            $__exc_strObjectType = "E_COMPILE_ERROR";
            break;
        case E_COMPILE_WARNING:
            $__exc_strObjectType = "E_COMPILE_WARNING";
            break;
        case E_USER_ERROR:
            $__exc_strObjectType = "E_USER_ERROR";
            break;
        case E_USER_WARNING:
            $__exc_strObjectType = "E_USER_WARNING";
            break;
        case E_USER_NOTICE:
            $__exc_strObjectType = "E_USER_NOTICE";
            break;
        case E_DEPRECATED:
            $__exc_strObjectType = 'E_DEPRECATED';
            break;
        case E_USER_DEPRECATED:
            $__exc_strObjectType = 'E_USER_DEPRECATED';
            break;
        case E_RECOVERABLE_ERROR:
            $__exc_strObjectType = 'E_RECOVERABLE_ERROR';
            break;
        default:
            $__exc_strObjectType = "Unknown";
            break;
    }
    return $__exc_strObjectType;
}

function QApiHandleError($__exc_errno, $__exc_errstr, $__exc_errfile, $__exc_errline, $__exc_errcontext)
{
    // If a command is called with "@", then we should return
    if (error_reporting() == 0)
        return;

    if (class_exists('QApplicationBase'))
        QApplicationBase::$ErrorFlag = true;

    global $__exc_strType;
    if (isset($__exc_strType))
        return;

    $__exc_strType = "Error";
    $__exc_strMessage = $__exc_errstr;

    $__exc_strObjectType = get_error_code($__exc_errno);

    $__exc_strFilename = $__exc_errfile;
    $__exc_intLineNumber = $__exc_errline;
    $__exc_strStackTrace = "";
    $__exc_objBacktrace = debug_backtrace();
    for ($__exc_intIndex = 0; $__exc_intIndex < count($__exc_objBacktrace); $__exc_intIndex++) {
        $__exc_objItem = $__exc_objBacktrace[$__exc_intIndex];

        $__exc_strKeyFile = (array_key_exists("file", $__exc_objItem)) ? $__exc_objItem["file"] : "";
        $__exc_strKeyLine = (array_key_exists("line", $__exc_objItem)) ? $__exc_objItem["line"] : "";
        $__exc_strKeyClass = (array_key_exists("class", $__exc_objItem)) ? $__exc_objItem["class"] : "";
        $__exc_strKeyType = (array_key_exists("type", $__exc_objItem)) ? $__exc_objItem["type"] : "";
        $__exc_strKeyFunction = (array_key_exists("function", $__exc_objItem)) ? $__exc_objItem["function"] : "";

        $__exc_strStackTrace .= sprintf("#%s %s(%s): %s%s%s()\n",
            $__exc_intIndex,
            $__exc_strKeyFile,
            $__exc_strKeyLine,
            $__exc_strKeyClass,
            $__exc_strKeyType,
            $__exc_strKeyFunction);
    }

    json(array(
        "status" => "error",
        "msg" => $__exc_strMessage,
        "type" => $__exc_strType,
        "objecttype" => $__exc_strObjectType,
        "filename" => str_replace(__BASEPATH__, "", $__exc_strFilename . ":" . $__exc_intLineNumber),
        "stacktrace" => str_replace(__BASEPATH__, "", $__exc_strStackTrace)
    ));
}

function shutdown()
{
    if ($error = error_get_last()) {
        $__exc_strObjectType = get_error_code($error['type']);
        json(array(
            "status" => "error",
            "msg" => $error['message'],
            "type" => $__exc_strObjectType,
            "objecttype" => $__exc_strObjectType,
            "filename" => str_replace(__BASEPATH__, "", $error['file'] . ":" . $error['line'])
        ));
    }
}

set_error_handler('QApiHandleError', E_ALL);
set_exception_handler('QApiHandleException');
register_shutdown_function('shutdown');

$dispatch = new QApiDispatcher();
$dispatch->Execute();