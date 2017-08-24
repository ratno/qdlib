<?php

/**
 * This file contain QPage which is use to save information about page
 *
 * @author ratno
 */

/**
 * @package Framework
 *
 * @property string $ClassName
 * @property string $TaskClassName
 * @property string $TaskActionName
 * @property string $TaskClassNameBreadCrumb
 * @property string $TaskActionNameBreadCrumb
 * @property string $HtmlIncludeFilePath
 * @property string $CustomTitle
 * @property string $CustomSubTitle
 * @property string $CustomJs
 * @property string $CustomCss
 * @property string $GlobalLayout
 * @property array $ArrayCustom
 * @property array $PageCss
 * @property array $AdditionalCss
 * @property array $PageJs
 * @property array $AdditionalJs
 * @property string|array $FlashMessages
 * @property int $TaskId
 * @property string $ClassFileName
 * @property Users $User
 * @property string $UrlReferer
 * @property string $UrlRequest
 */
class QPage extends QBaseClass
{

    protected $strClassName;
    protected $strHtmlIncludeFilePath;
    protected $strPageTitle = "";
    protected $strPageSubTitle = "";
    protected $strPageCustomJs = "";
    protected $strPageCustomCss = "";
    protected $strGlobalLayout = "layout";
    protected $arrCustom = array();
    protected $objUser;
    protected $arrPageCss;
    protected $arrAdditionalCss;
    protected $arrPageJs;
    protected $arrAdditionalJs;
    protected $objActivityLog;
    protected $strTaskClassName;
    protected $strTaskActionName;
    protected $strTaskClassNameBreadCrumb;
    protected $strTaskActionNameBreadCrumb;
    protected $intTaskId;
    protected $strClassFileName;
    protected $strUrlReferer;
    protected $strUrlRequest;
    protected $arrPageMenu = array();
    protected $arrPageMenuExclude = array();

    public static function basic_auth()
    {
//    if(QApplication::GetUser() instanceof Users) {
//      // go ahead, karena udah login
//    } else {
        // ambil dari get/post
        $username = QR::Param("username");
        $password = QR::Param("password");

        // jika ga ada, mungkin basic auth
        if (is_empty($username)) $username = $_SERVER['PHP_AUTH_USER'];
        if (is_empty($password)) $password = $_SERVER['PHP_AUTH_PW'];

        if (is_empty($username) && is_empty($password)) {
            json(array("status" => "error", "msg" => QError::ToString(QError::username_password_required), "error_code" => QError::username_password_required));
        } else {
            $auth_result = QApplication::Auth($username, $password);
            if (is_array($auth_result)) {
                json(array("status" => "error", "msg" => $auth_result['msg'], "error_code" => $auth_result['error_code']));
            } else {
                return $auth_result;
            }
        }
//    }
    }

    /*
     * input menu itu beberapa possibility:
     * 1. classname aja
     * 2. classname dan action name
     * 3. classname, action name dan parameter
     * 4. task yg independent aja [blom]
     * 5. task yg non independent [blom]
     * 6. url
     */

    public static function setFlashMessages($strFlashMessages)
    {
        $arrFlashMessages = QR::getTokenize("FlashMessages");
        $arrFlashMessages[] = QType::Cast($strFlashMessages, QType::String);
        QR::setTokenize("FlashMessages", $arrFlashMessages);
    }

    public static function Run($strClassName, $strAlternateHtmlFile = null)
    {
        $trace = debug_backtrace();
        $tplFilename = str_replace(array('/app/qd/controllers/', '.php'), array('/app/qd/views/', '.tpl.php'), $trace[0]['file']);

        $objClass = new $strClassName();
        $objClass->SetInformation($strClassName);
        $objClass->Page_Create();
        $objClass->HtmlIncludeFilePath = ($strAlternateHtmlFile) ? $strAlternateHtmlFile : $tplFilename;
        $objClass->Render(); // this function will switch context between static and instance

        return $objClass;
    }

    /**
     * Set Informasi ini akan selalu diakses, baik dari create atau recreate
     * @param type $strClassName
     */
    public function SetInformation($strClassName)
    {
        $this->ClassName = $strClassName;
        if ($user = QApplication::GetUser()) {
            $this->User = $user;
        }
    }

    /**
     * Page Create ini hanya akan dipanggil saat penciptaan pertama kali (create)
     */
    public function Page_Create()
    {
        // jika ada user maka activity log bisa dilakukan
        if ($this->User instanceof Users) {
            $this->objActivityLog = new ActivityLog();
            $this->objActivityLog->UserId = $this->User->Id;
        }
        $this->strUrlReferer = array_key_exists("HTTP_REFERER", $_SERVER) ? complete_url($_SERVER["HTTP_REFERER"]) : "";
        $this->strUrlRequest = array_key_exists("REQUEST_URI", $_SERVER) ? complete_url($_SERVER["REQUEST_URI"]) : "";

        // initialize flash messages
//    $this->initflash(); 
        // per 4 Juni 2012, pemanggilan initflash() disini ditiadakan,
        // karena ia menyebabkan gagal transfer notifikasi antar halaman
        // jika dikemudian hari terdapat masalah dengan notifikasi maka kita cari solusinya
    }

    public function addMenu($strClassName, $strActionName = null, $arrParameter = null)
    {
        if (preg_match("/^(<a)/", $strClassName)) {
            $a = str_get_html($strClassName)->find('a', 0);
            $this->arrPageMenu[$a->href] = array('item' => $strClassName, 'type' => 'link', 'link' => $strClassName, 'class' => "", 'action' => "");
        } else {
            $objTaskArray = array();
            $_arrTask = get_privileges("task_privs", strtolower($strClassName));
            if (is_empty($strActionName)) {
                $objTaskArray = $_arrTask;
            } else {
                $objTaskArray[] = $_arrTask[$strActionName];
            }

            $intId = null;
            $blnForce = false;
            if (is_array($arrParameter) && array_key_exists("id", $arrParameter)) {
                $intId = $arrParameter['id'];
                $blnForce = true;
                unset($arrParameter['id']);
            } else {
                $intId = QR::getUrl(1);
                if (!$intId) {
                    $intId = QR::getUrl('id');
                }
            }

            if ($objTaskArray) {
                foreach ($objTaskArray as $objTaskItem) {
                    $objTask = unserialize($objTaskItem);
                    if ($objTask->ActionName == "api") continue;
                    if ($objTask->IsIndependent) {
                        if ($blnForce) {
                            $url = qd_url($objTask->TableName, $objTask->ActionName . "/" . $intId, $arrParameter);
                        } else {
                            $url = qd_url($objTask->TableName, $objTask->ActionName, $arrParameter);
                        }
                    } else {
                        if ($intId) {
                            $url = qd_url($objTask->TableName, $objTask->ActionName . "/" . $intId, $arrParameter);
                        } else {
                            $url = "";
                        }
                    }
                    if (!is_empty($url)) {
                        $title = ($objTask->ActionName == 'list') ? QApplication::Translate(ucwords($objTask->ActionName)) . " " . $objTask->Title : $objTask->Title;
                        $link = href($url, $title);
                        $class = ($objTask && $objTask->ModelName) ? $objTask->ModelName : "";
                        $action = ($objTask && $objTask->ActionName) ? $objTask->ActionName : "";
                        $this->arrPageMenu[$url] = array('item' => $objTask, 'type' => 'task', 'title' => $title, 'link' => $link, 'class' => $class, 'action' => $action);
                    }
                }
            }
        }
    }

    public function excludeMenu($action, $class = null)
    {
        $class = ($class) ? $class : $this->TaskClassName;
        if (is_array($action) && count($action) > 0) {
            foreach ($action as $item_action)
                $this->arrPageMenuExclude[] = strtolower("$class/$item_action");
        } else {
            $this->arrPageMenuExclude[] = strtolower("$class/$action");
        }
    }

    public function getMenu()
    {
        // get global menu
        $objTasks = get_privileges("privs_global");
        $intId = QR::getUrl(1);
        if ($objTasks) {
            foreach ($objTasks as $objTaskItem) {
                $objTask = unserialize($objTaskItem);
                if ($objTask->IsIndependent) {
                    $url = qd_url($objTask->TableName, $objTask->ActionName);
                } else {
                    if ($intId) {
                        $url = qd_url($objTask->TableName, $objTask->ActionName . "/" . $intId);
                    } else {
                        $url = "";
                    }
                }
                if (!is_empty($url)) {
                    $title = ($objTask->ActionName == 'list') ? QApplication::Translate(ucwords($objTask->ActionName)) . " " . $objTask->Title : $objTask->Title;
                    $link = href($url, $title);
                    $class = ($objTask && $objTask->ModelName) ? $objTask->ModelName : "";
                    $action = ($objTask && $objTask->ActionName) ? $objTask->ActionName : "";
                    $this->arrPageMenu[$url] = array('item' => $objTask, 'type' => 'task', 'title' => $title, 'link' => $link, 'class' => $class, 'action' => $action);
                }
            }
        }

        if (is_array($this->arrPageMenu) && count($this->arrPageMenu) > 0) {
            $arrtemp = array();
            foreach ($this->arrPageMenu as $menu) {
                if (in_array(strtolower($menu['class'] . "/" . $menu['action']), $this->arrPageMenuExclude)) continue;
//        $current = (strtolower($menu['class'])==strtolower($this->TaskClassName) && strtolower($menu['action'])==strtolower($this->TaskActionName))?" class='current'":"";
                $current = (strtolower($menu['class']) == strtolower($this->TaskClassName) && strtolower($menu['action']) == strtolower($this->TaskActionName)) ? " class='active'" : "";
                switch ($menu['action']) {
                    default:
                        $_action = $menu['action'];
                        break;
                }
                if (property_exists("Icon", $_action)) {
                    $a = str_get_html($menu['link'])->find('a', 0);
                    $li = "<li{$current}>" . href($a->href, img(Icon::ToImage(Icon::${$_action}), array("title" => Icon::ToTitle(Icon::${$_action}), "height" => "24")) . "<span class='scroll_menu_title'>" . $menu['title'] . "</span>") . "</li>";
                } else {
                    $li = "<li{$current}>" . $menu['link'] . "</li>";
                }
                if (strtolower($menu['class']) == strtolower($this->TaskClassName)) {
                    $arrtemp[$menu['class']][$menu['action']] = $li;
                } else {
                    $arrtemp[] = $li;
                }
            }
            // urutkan
            $arrToReturn = array();
            if (array_key_exists($this->TaskClassName, $arrtemp) && is_array($arrtemp[$this->TaskClassName])) {
                // pertama ambil array dari this class
                $arrActionOrder = array("list", "new", "view", "edit", "delete", "import", "export");
                foreach ($arrActionOrder as $strActionItemOrder) {
                    if (array_key_exists($strActionItemOrder, $arrtemp[$this->TaskClassName])) {
                        $arrToReturn[] = $arrtemp[$this->TaskClassName][$strActionItemOrder];
                        unset($arrtemp[$this->TaskClassName][$strActionItemOrder]);
                    }
                }
                // klo ada action yg ga ada di action order maka proses aja tanpa berurutan
                foreach ($arrtemp[$this->TaskClassName] as $li_key => $li_item) {
                    $arrToReturn[] = $li_item;
                    unset($arrtemp[$this->TaskClassName][$li_key]);
                }
                // klo udah kosongkan array class tsb
                unset($arrtemp[$this->TaskClassName]);
            }
            // lalu ambil yg lainnya
            foreach ($arrtemp as $li_item) {
                $arrToReturn[] = $li_item;
            }
            // lalu implode
            $strToReturn = implode("", $arrToReturn);
            return $strToReturn;
        } else {
            return "";
        }
    }

    public function flash()
    {
        if (is_array($this->FlashMessages) && count($this->FlashMessages) > 0) {
            $tmp = array();
            foreach ($this->FlashMessages as $message) {
                $message_array = explode("|", $message);
                if (count($message_array) == 2) {
                    $tmp[$message_array[0]][] = $message_array[1];
                } else {
                    $tmp['info'][] = $message;
                }
            }

            $out = "<div id='messages'>";
            foreach ($tmp as $key => $items) {
                $cssclass = ($key == "alert") ? "alert" : "alert alert-$key";
                $out .= "<div id='messages_$key' class='$cssclass alert-block'>";
                $out .= '<button type="button" class="close" data-dismiss="alert">&times;</button>';
                $out .= '<h4>' . ucwords($key) . ':</h4>';
                $out .= "<ul>";
                foreach ($items as $item) {
                    $out .= "<li>" . $item . "</li>";
                }
                $out .= "</ul>";
                $out .= "</div>";
            }
            $out .= "</div>";
        } else {
            $out = "";
        }
        // reinitialize flash messages
        $this->initflash();
        return $out;
    }

    public function initflash()
    {
        QR::setTokenize("FlashMessages", array());
    }

    public function css($arrDefault = array())
    {
        if ($this->arrPageCss) {
            return $this->getCss($this->arrPageCss);
        } else {
            $strToReturn = $this->getCss($arrDefault);
            if ($this->arrAdditionalCss) {
                $strToReturn .= $this->getCss($this->arrAdditionalCss);
            }
            return $strToReturn;
        }
    }

    protected function getCss($arrCss, $media = "all")
    {
        $strToReturn = "";
        if (__GABUNG_JSCSS__) {
            include_once(__WEB_PATH__ . "/cssmin.php");
            $gabung_filename = md5(__HOSTNAME__ . __FILE__ . json_encode($arrCss));
            if (!file_exists(__CSS_PATH__ . "/gabung/{$gabung_filename}.min.css")) {
                foreach ($arrCss as $css) {
                    if (substr($css, strrpos($css, ".")) == ".css") {
                        $cssfile = __CSS_PATH__ . "/" . $css;
                    } else {
                        $cssfile = __CSS_PATH__ . "/" . $css . ".css";
                    }
                    $blnMinify = preg_match("/(\.min|\.min.css)$/", $cssfile) ? false : true;
                    if (file_exists($cssfile)) {
                        $csscontent = file_get_contents($cssfile);
                        $csscontent = str_replace(array("__CSS_URL__", "__WEB_URL__", "__IMAGES_URL__"), array(__CSS_URL__, __WEB_URL__, __IMAGES_URL__), $csscontent);
                        $strToReturn .= "/* $css */\n";
                        if ($blnMinify) {
                            $strToReturn .= CssMin::minify($csscontent) . "\n";
                        } else {
                            $strToReturn .= $csscontent . "\n";
                        }
                    }
                }

                $f = fopen(__CSS_PATH__ . "/gabung/{$gabung_filename}.min.css", "w+");
                fwrite($f, $strToReturn);
                fclose($f);
            }
            $strToReturn = '<link href="' . __CSS_URL__ . "/gabung/{$gabung_filename}.min.css" . '" rel="stylesheet" type="text/css" media="' . $media . '" />' . "\n";
        } else {
            foreach ($arrCss as $css) {
                // pilihan untuk opsi media
                if (is_array($css) && array_key_exists("css", $css)) {
                    $strToReturn .= $this->getCssLink($css['css'], $css['media']);
                } else {
                    $strToReturn .= $this->getCssLink($css);
                }
            }
        }
        return $strToReturn;
    }

    protected function getCssLink($css, $media = "all")
    {
        if (substr($css, strrpos($css, ".")) == ".css") {
            if (trim($css))
                $strToReturn = '<link href="' . $this->getFile($css, 'css') . '" rel="stylesheet" type="text/css" media="' . $media . '" />' . "\n";
        } else {
            if (trim($css))
                $strToReturn = '<link href="' . __WEB_URL__ . '/css.php?f=' . $css . '" rel="stylesheet" type="text/css" media="' . $media . '" />' . "\n";
        }

        return $strToReturn;
    }

    protected function getFile($filename, $type)
    {
        $data = array(
            'css' => array('path' => __CSS_PATH__, 'url' => __CSS_URL__),
            'js' => array('path' => __JS_PATH__, 'url' => __JS_URL__)
        );
        if (is_file($data[$type]['path'] . "/" . $filename)) {
            return $data[$type]['url'] . '/' . $filename;
        } else {
            return __WEB_URL__ . "/" . $filename;
        }
    }

    public function js($arrDefault = array())
    {
        if ($this->arrPageJs) {
            return $this->getJs($this->arrPageJs);
        } else {
            $strToReturn = $this->getJs($arrDefault);
            if ($this->arrAdditionalJs) {
                $strToReturn .= $this->getJs($this->arrAdditionalJs);
            }
            return $strToReturn;
        }
    }

    protected function getJs($arrJs)
    {
        $strToReturn = "";
        if (__GABUNG_JSCSS__) {
            include_once(__WEB_PATH__ . "/jsmin.php");
            $gabung_filename = md5(__HOSTNAME__ . __FILE__ . json_encode($arrJs));
            if (!file_exists(__JS_PATH__ . "/gabung/{$gabung_filename}.min.js")) {
                foreach ($arrJs as $js) {
                    if (substr($js, strrpos($js, ".")) == ".js") {
                        if (trim($js)) {
                            $jsfile = __JS_PATH__ . "/" . $js;
                            $blnMinify = preg_match("/(\.min|\.min.js)$/", $jsfile) ? false : true;
                            if (file_exists($jsfile)) {
                                $jscontent = file_get_contents($jsfile);
                                $strToReturn .= "/* $js */\n";
                                if ($blnMinify) {
                                    $strToReturn .= JSMin::minify($jscontent) . "\n";
                                } else {
                                    $strToReturn .= $jscontent . "\n";
                                }
                            }
                        }
                    } else {
                        if (trim($js)) {
                            $strToReturn .= JSMin::minify($js) . "\n";
                        }
                    }
                }
                $f = fopen(__JS_PATH__ . "/gabung/{$gabung_filename}.min.js", "w+");
                fwrite($f, $strToReturn);
                fclose($f);
            }
            $strToReturn = '<script type="text/javascript" src="' . __JS_URL__ . "/gabung/{$gabung_filename}.min.js" . '"></script>' . "\n";
        } else {
            foreach ($arrJs as $js) {
                if (substr($js, strrpos($js, ".")) == ".js") {
                    if (trim($js))
                        $strToReturn .= '<script type="text/javascript" src="' . $this->getFile($js, 'js') . '"></script>' . "\n";
                } else {
                    if (trim($js))
                        $strToReturn .= '<script type="text/javascript">' . $js . '</script>' . "\n";
                }
            }
        }
        return $strToReturn;
    }

    public function __get($strName)
    {
        switch ($strName) {
            case "HtmlIncludeFilePath":
                return $this->strHtmlIncludeFilePath;
            case "CustomTitle":
                return $this->strPageTitle;
            case "CustomSubTitle":
//        if (is_null($this->strPageSubTitle) or empty($this->strPageSubTitle) or $this->strPageSubTitle == "") {
//          return $this->strPageTitle;
//        } else {
                return $this->strPageSubTitle;
//        }
            case "CustomJs":
                return $this->strPageCustomJs;
            case "CustomCss":
                return $this->strPageCustomCss;
            case "GlobalLayout":
                return $this->strGlobalLayout;
            case "ArrayCustom":
                return $this->arrCustom;
            case "ClassName":
                return $this->strClassName;
            case "User":
                return $this->objUser;
            case "FlashMessages":
                return QR::getTokenize("FlashMessages");
            case "TaskClassName":
                return $this->strTaskClassName;
            case "TaskActionName":
                return $this->strTaskActionName;
            case "TaskClassNameBreadCrumb":
                return $this->strTaskClassNameBreadCrumb;
            case "TaskActionNameBreadCrumb":
                return $this->strTaskActionNameBreadCrumb;
            case "TaskId":
                return $this->intTaskId;
            case "ClassFileName":
                return $this->strClassFileName;
            case "UrlReferer":
                return $this->strUrlReferer;
            case "UrlRequest":
                return $this->strUrlRequest;

            default:
                try {
                    return parent::__get($strName);
                } catch (QCallerException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
        }
    }

    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            case "ClassName":
                try {
                    return ($this->strClassName = QType::Cast($mixValue, QType::String));
                } catch (QCallerException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "TaskClassName":
                try {
                    return ($this->strTaskClassName = QType::Cast($mixValue, QType::String));
                } catch (QCallerException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "TaskActionName":
                try {
                    return ($this->strTaskActionName = QType::Cast($mixValue, QType::String));
                } catch (QCallerException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "TaskClassNameBreadCrumb":
                try {
                    return ($this->strTaskClassNameBreadCrumb = QType::Cast($mixValue, QType::String));
                } catch (QCallerException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "TaskActionNameBreadCrumb":
                try {
                    return ($this->strTaskActionNameBreadCrumb = QType::Cast($mixValue, QType::String));
                } catch (QCallerException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "HtmlIncludeFilePath":
                // Passed-in value is null -- use the "default" path name of file".tpl.php"
                if (!$mixValue) {
                    $strFolder = explode("_", QConvertNotation::UnderscoreFromCamelCase($this->ClassName));
                    unset($strFolder[(count($strFolder) - 1)]);
                    $strFolder = QConvertNotation::CamelCaseFromUnderscore(implode("_", $strFolder));
                    $strPath = __BASEPATH__ . "/app/qd/views/{$strFolder}/" . QConvertNotation::UnderscoreFromCamelCase($this->ClassName) . ".tpl.php";
                } // Use passed-in value
                else
                    $strPath = realpath($mixValue);

                // Verify File Exists, and if not, throw exception
                if (is_file($strPath)) {
                    $this->strHtmlIncludeFilePath = $strPath;
                    return $strPath;
                } else
                    throw new QCallerException('Accompanying HTML Include File does not exist: "' . $mixValue . '"');
                break;


            case "User":
                try {
                    return ($this->objUser = QType::Cast($mixValue, 'Users'));
                } catch (QCallerException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "CustomTitle":
                try {
                    return ($this->strPageTitle = QType::Cast($mixValue, QType::String));
                } catch (QCallerException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "CustomSubTitle":
                try {
                    return ($this->strPageSubTitle = QType::Cast($mixValue, QType::String));
                } catch (QCallerException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "CustomJs":
                try {
                    return ($this->strPageCustomJs = QType::Cast($mixValue, QType::String));
                } catch (QCallerException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "CustomCss":
                try {
                    return ($this->strPageCustomCss = QType::Cast($mixValue, QType::String));
                } catch (QCallerException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "GlobalLayout":
                try {
                    return ($this->strGlobalLayout = QType::Cast($mixValue, QType::String));
                } catch (QCallerException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "ArrayCustom":
                try {
                    return ($this->arrCustom = QType::Cast($mixValue, QType::ArrayType));
                } catch (QCallerException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "PageCss":
                try {
                    return ($this->arrPageCss = QType::Cast($mixValue, QType::ArrayType));
                } catch (QCallerException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "AdditionalCss":
                try {
                    return ($this->arrAdditionalCss = QType::Cast($mixValue, QType::ArrayType));
                } catch (QCallerException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "PageJs":
                try {
                    return ($this->arrPageJs = QType::Cast($mixValue, QType::ArrayType));
                } catch (QCallerException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "AdditionalJs":
                try {
                    return ($this->arrAdditionalJs = QType::Cast($mixValue, QType::ArrayType));
                } catch (QCallerException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "FlashMessages":
                try {
                    $arrFlashMessages = QR::getTokenize("FlashMessages");
                    $arrFlashMessages[] = QType::Cast($mixValue, QType::String);
                    QR::setTokenize("FlashMessages", $arrFlashMessages);
                    return true;
                } catch (QCallerException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "TaskId":
                try {
                    return ($this->intTaskId = QType::Cast($mixValue, QType::Integer));
                } catch (QCallerException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "ClassFileName":
                try {
                    return ($this->strClassFileName = QType::Cast($mixValue, QType::String));
                } catch (QCallerException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "UrlReferer":
                try {
                    return ($this->strUrlReferer = QType::Cast($mixValue, QType::String));
                } catch (QCallerException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "UrlRequest":
                try {
                    return ($this->strUrlRequest = QType::Cast($mixValue, QType::String));
                } catch (QCallerException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }

            default:
                try {
                    return parent::__set($strName, $mixValue);
                } catch (QCallerException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
        }
    }

    protected function SaveActivityLog()
    {
        $this->objActivityLog->Ts = QDatetime::Now();
        $this->objActivityLog->Save();
    }

    protected function Diff($json1, $json2)
    {
        $arrJson1 = json_decode($json1, true);
        $arrJson2 = json_decode($json2, true);
        $arrDiff = array();
        if (is_array($arrJson1)) {
            foreach ($arrJson1 as $key => $val) {
                if ((is_array($val) && array_key_exists("date", $val)) || (is_array($arrJson2[$key]) && array_key_exists("date", $arrJson2[$key]))) {
                    if ($val['date'] != $arrJson2[$key]['date']) {
                        $arrDiff[$key]['old'] = $val['date'];
                        $arrDiff[$key]['new'] = $arrJson2[$key]['date'];
                    }
                } else {
                    if ($val != $arrJson2[$key]) {
                        $arrDiff[$key]['old'] = $val;
                        $arrDiff[$key]['new'] = $arrJson2[$key];
                    }
                }
            }
        } else {
            foreach ($arrJson2 as $key => $val) {
                if (is_array($val) && array_key_exists("date", $val)) {
                    $arrDiff[$key]['old'] = null;
                    $arrDiff[$key]['new'] = $arrJson2[$key]['date'];
                } else {
                    $arrDiff[$key]['old'] = null;
                    $arrDiff[$key]['new'] = $arrJson2[$key];
                }
            }
        }
        return $arrDiff;
    }

    protected function SetReadNotification($intUserId, $strModel, $intModelId)
    {
        // ambil notifikasi terkait
        $objs = Notifications::QueryArray(
            QQ::AndCondition(
                QQ::Equal(QQN::Notifications()->UserId, $intUserId),
                QQ::IsNull(QQN::Notifications()->ReviewTs),
                QQ::Equal(QQN::Notifications()->Log->SubjectModel, $strModel),
                QQ::Equal(QQN::Notifications()->Log->SubjectIdNumber, $intModelId)
            ), QQ::Expand(QQN::Notifications()->Log)
        );

        // flag read notif
        if ($objs) {
            foreach ($objs as $objNotification) {
                $objNotification->ReviewTs = QDateTime::Now();
                $objNotification->Save();
            }
        }
    }

    protected function Render()
    {
        require_once($this->HtmlIncludeFilePath);
    }

}