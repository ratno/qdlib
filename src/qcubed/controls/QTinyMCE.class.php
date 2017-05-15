<?php

/**
 * @property string $ScriptUrl
 * @property string $Theme
 * @property Array $AutoComplete
 * @property Array $Plugins
 */
class QTinyMCE extends QTextBoxBase
{

    protected $strScriptUrl = 'js_url+"/tiny_mce/tiny_mce.js"';
    protected $strTheme = QTinyMCETheme::Simple;
    protected $arrAutoComplete;
    protected $arrPlugins;
    protected $strTextMode = QTextMode::MultiLine;

    public function addAutoCompleteData($strName, $intId, $strDescription = null)
    {
        $data = array();
        $data['key'] = strtolower($strName);
        $data['id'] = $intId;
        if (!is_empty($strDescription)) {
            $data['description'] = $strDescription;
        }
        $this->arrAutoComplete[] = $data;
    }

    public function GetEndScript()
    {
        return $this->GetControlJavaScript() . '; ' . parent::GetEndScript();
    }

    public function GetControlJavaScript()
    {
        if ($this->TextMode == QTextMode::MultiLine) {
            $opsi = array();
            $opsi[] = $this->setJsProperty("script_url", $this->ScriptUrl, false);
            $opsi[] = $this->setJsProperty('theme', $this->Theme);

            if (is_array($this->AutoComplete) && count($this->AutoComplete) > 0) {
                $this->addPlugin(QTinyMCEPlugin::AutoComplete);
                $ac = array("options" => $this->AutoComplete);
                $opsi[] = $this->setJsProperty('ac_opt', $ac);
            }

            if (is_array($this->Plugins) && count($this->Plugins) > 0) {
                $opsi[] = $this->setJsProperty('plugins', implode(",", $this->Plugins));
            }

            return sprintf('jQuery("#%s").%s({%s})', $this->ControlId, "tinymce", implode(",", $opsi));
        }
    }

    protected function setJsProperty($strKey, $mixValue, $blnConvert = true)
    {
        if ($blnConvert) {
            return $strKey . ': ' . JavaScriptHelper::toJsObject($mixValue);
        } else {
            return $strKey . ': ' . $mixValue;
        }
    }

    public function addPlugin($strPluginName)
    {
        $this->arrPlugins[] = $strPluginName;
    }

    public function __get($strName)
    {
        switch ($strName) {
            case 'ScriptUrl':
                return $this->strScriptUrl;
            case 'Theme':
                return $this->strTheme;
            case 'AutoComplete':
                return $this->arrAutoComplete;
            case 'Plugins':
                return $this->arrPlugins;
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
            case 'ScriptUrl':
                try {
                    $this->strScriptUrl = QType::Cast($mixValue, QType::String);
                    break;
                } catch (QInvalidCastException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case 'Theme':
                try {
                    $this->strTheme = QType::Cast($mixValue, QType::String);
                    break;
                } catch (QInvalidCastException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case 'AutoComplete':
                try {
                    $this->arrAutoComplete = QType::Cast($mixValue, QType::ArrayType);
                    break;
                } catch (QInvalidCastException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case 'Plugins':
                try {
                    $this->arrPlugins = QType::Cast($mixValue, QType::ArrayType);
                    break;
                } catch (QInvalidCastException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }


            default:
                try {
                    parent::__set($strName, $mixValue);
                    break;
                } catch (QCallerException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
        }
    }
}