<?php

/**
 * Description of QRequest
 *
 * @author ratno
 */
class QRequest
{
    const ALL = 1;
    const POST = 2;
    const GET = 3;
    const URL = 4;
    const SES = 5;
    const SESSION = 5;
    const JSON = 6;
    const TASK = "strTaskName";
    const ACTION = "strActionName";

    public $arrData;

    public function __construct($data = null)
    {
        switch ($data) {
            case QR::GET:
                $this->arrData = $_GET;
                break;
            case QR::POST:
                $this->arrData = $_POST;
                break;
            case QR::SES:
            case QR::SESSION:
                $this->arrData = $_SESSION;
                break;

            default:
                $this->arrData = array();
                break;
        }
    }

    public static function isPost()
    {
        return (strtolower(_env('REQUEST_METHOD')) == 'post');
    }

    public static function isGet()
    {
        return (strtolower(_env('REQUEST_METHOD')) == 'get');
    }

    public static function isDelete()
    {
        return (strtolower(_env('REQUEST_METHOD')) == 'delete');
    }

    public static function isXml()
    {
        return (array_key_exists('CONTENT_TYPE', $_SERVER) && strtolower($_SERVER['CONTENT_TYPE']) == "application/xml");
    }

    public static function User()
    {
        return QApplication::GetUser();
    }

    public static function getViewState($key)
    {
        $strViewStateKey = __TOKEN__ . $key . "ViewState";
        if (array_key_exists($strViewStateKey, $_SESSION)) {
            $arrViewState = $_SESSION[$strViewStateKey];
            unset($_SESSION[$strViewStateKey]);
            return $arrViewState;
        } else {
            return null;
        }
    }

    public static function setViewState($key, $value)
    {
        $strViewStateKey = __TOKEN__ . $key . "ViewState";
        $_SESSION[$strViewStateKey] = $value;
    }

    public static function clearViewState($key)
    {
        $strViewStateKey = __TOKEN__ . $key . "ViewState";
        QR::clearSession($strViewStateKey);
    }

    public static function clearSession($key)
    {
        if (array_key_exists($key, $_SESSION)) {
            unset($_SESSION[$key]);
        }
    }

    public static function checkViewState($key)
    {
        $strViewStateKey = __TOKEN__ . $key . "ViewState";
        return QR::checkSession($strViewStateKey);
    }

    public static function checkSession($key)
    {
        if (array_key_exists($key, $_SESSION)) {
            return true;
        } else {
            return false;
        }
    }

    public static function setTokenize($key, $value)
    {
        $strTokenizedKey = __TOKEN__ . $key;
        QR::setSession($strTokenizedKey, $value);
    }

    public static function clearTokenize($key)
    {
        $strTokenizeKey = __TOKEN__ . $key;
        QR::clearSession($strTokenizeKey);
    }

    public static function checkTokenize($key)
    {
        $strTokenizeKey = __TOKEN__ . $key;
        return QR::checkSession($strTokenizeKey);
    }

    public static function getEncrypted()
    {

    }

    public static function Param($key = null, $method = QRequest::ALL)
    {
        switch ($method) {
            case QRequest::ALL:
                if (is_null($key)) {
                    return array_merge(QR::getSession(), QR::getUrl(), QR::getGet(), QR::getPost(), QR::getJson());
                } else {
                    // seek, start from url => get => post => json => tokenize => session
                    if ($val = QR::getUrl($key)) {
                        return $val;
                    } elseif ($val = QR::getGet($key)) {
                        return $val;
                    } elseif ($val = QR::getPost($key)) {
                        return $val;
                    } elseif ($val = QR::getJson($key)) {
                        return $val;
                    } elseif ($val = QR::getTokenize($key)) {
                        return $val;
                    } elseif ($val = QR::getSession($key)) {
                        return $val;
                    } else {
                        return null;
                    }
                }
                break;
            case QRequest::POST:
                return QR::getPost($key);
                break;
            case QRequest::GET:
                return QR::getGet($key);
                break;
            case QRequest::URL:
                return QR::getUrl($key);
                break;
            case QRequest::SES:
            case QRequest::SESSION:
                return QR::getSession($key);
                break;
            case QRequest::JSON:
                return QR::getJson($key);
                break;
        }
    }

    public static function getSession($key = null, $subkey = null)
    {
        $objQRequest = new QR(QR::SESSION);
        foreach ($_SESSION as $seskey => $sescont) {
            if (preg_match('/^(qform)/', $seskey) || preg_match('/(ViewState)$/', $seskey)) {
                continue;
            } else {
                $objQRequest->arrData[$seskey] = $sescont;
            }
        }
        return $objQRequest->toReturn($key, $subkey);
    }

    public static function setSession($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function toReturn($key, $subkey = null)
    {
        if (is_null($key)) {
            if (count($this->arrData) > 0) {
                return $this->arrData;
            } else {
                return array();
            }
        } elseif (is_array($this->arrData) && array_key_exists($key, $this->arrData)) {
            if (is_null($subkey)) {
                if (is_array($this->arrData[$key])) {
                    return $this->arrData[$key];
                } else {
                    return rawurldecode(trim($this->arrData[$key]));
                }
            } elseif (array_key_exists($subkey, $this->arrData[$key])) {
                if (is_array($this->arrData[$key][$subkey])) {
                    return $this->arrData[$key][$subkey];
                } else {
                    return rawurldecode(trim($this->arrData[$key][$subkey]));
                }
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public static function getUrl($key = null)
    {
        $objQRequest = new QR();

        // Processing URL params
        $strPathInfo = rawurldecode(QApplication::$PathInfo);

        // Remove Starting '/'
        if (QString::FirstCharacter($strPathInfo) == '/')
            $strPathInfo = substr($strPathInfo, 1);

        $strPathInfoArray = explode('/', $strPathInfo);
        // set task
        if (array_key_exists(0, $strPathInfoArray))
            $objQRequest->arrData['strTaskName'] = $strPathInfoArray[0];
        // set action
        if (array_key_exists(1, $strPathInfoArray))
            $objQRequest->arrData['strActionName'] = $strPathInfoArray[1];
        // unset task and action
        unset($strPathInfoArray[0], $strPathInfoArray[1]);

        $i = 1;
        // reiterate
        foreach ($strPathInfoArray as $data) {
            $arrTemp = explode(":", $data);
            if (count($arrTemp) == 2) {
                $objQRequest->arrData[$arrTemp[0]] = $arrTemp[1];
            } else {
                if ($data) {
                    $objQRequest->arrData[$i] = $data;
                    $i++;
                }
            }
        }

        return $objQRequest->toReturn($key);
    }

    public static function getGet($key = null)
    {
        $objQRequest = new QR(QR::GET);
        return $objQRequest->toReturn($key);
    }

    public static function getPost($key = null)
    {
        $objQRequest = new QR(QR::POST);
        return $objQRequest->toReturn($key);
    }

    public static function getJson($key = null)
    {
        $objQRequest = new QR();
        if (QRequest::isAjax() || QRequest::isPut() || QRequest::isJson()) {
            $objQRequest->arrData = json_decode(file_get_contents("php://input"), true);
        } elseif ($data = file_get_contents("php://input")) {
            if (is_array($arrData = json_decode($data, true))) {
                // masuk sini artinya data yang dikirim adalah json karena berhasil dibaca dan di decode
                $objQRequest->arrData = $arrData;
            } elseif (is_array($arrData = json_decode(html_entity_decode($data), true))) {
                //  jika data di encode, maka di decode disini
                $objQRequest->arrData = $arrData;
            } elseif (is_array($arrData = json_decode(substr(html_entity_decode($data), 1), true))) {
                //  jika data di encode dan tidak punya key untuk datanya, maka di decode disini dan dipotong karakter pertamanya yaitu =
                $objQRequest->arrData = $arrData;
            } elseif (is_array($arrData = json_decode(rawurldecode($data), true))) {
                //  jika data di encode pake urlencode, maka di decode disini
                $objQRequest->arrData = $arrData;
            } elseif (is_array($arrData = json_decode(substr(rawurldecode($data), 1), true))) {
                //  jika data di encode pake urlencode dan tidak punya key untuk datanya,
                //  maka di decode disini dan dipotong karakter pertamanya yaitu =
                $objQRequest->arrData = $arrData;
            }
        }

        return $objQRequest->toReturn($key);
    }

    public static function isAjax()
    {
        return _env('HTTP_X_REQUESTED_WITH') === "XMLHttpRequest";
    }

    public static function isPut()
    {
        return (strtolower(_env('REQUEST_METHOD')) == 'put');
    }

    public static function isJson()
    {
        return (array_key_exists('CONTENT_TYPE', $_SERVER) && strtolower($_SERVER['CONTENT_TYPE']) == "application/json");
    }

    public static function getTokenize($key, $subkey = null)
    {
        $strTokenizedKey = __TOKEN__ . $key;
        return QR::getSession($strTokenizedKey, $subkey);
    }
}

class QR extends QRequest
{
}