<?php

/**
 * This file contains the QWaitIcon class.
 *
 * @package Controls
 */

/**
 * @package Controls
 *
 * @property string $Text
 * @property string $TagName
 * @property string $Padding
 * @property string $HorizontalAlign
 * @property string $VerticalAlign
 */
class QWaitIcon extends QControl
{

    ///////////////////////////
    // Private Member Variables
    ///////////////////////////
    // APPEARANCE
    protected $strText = null;
    protected $strPadding = null;
    protected $strTagName = 'span';
    protected $blnDisplay = false;
    // LAYOUT
    protected $strHorizontalAlign = QHorizontalAlign::NotSet;
    protected $strVerticalAlign = QVerticalAlign::NotSet;

    public function __construct($objParentObject, $strControlId = null)
    {
        parent::__construct($objParentObject, $strControlId);

        $this->strText = '<div class="waiticon_block">';
        $this->strText .= '<div class="waiticon_transparent">';
        $this->strText .= '</div>';
        $this->strText .= '<div class="waiticon_centered">';
        $this->strText .= sprintf('<img id="waiticon_image" src="%s/waiticon.gif" alt="Please Wait..."/>', __IMAGES_URL__);
        $this->strText .= '</div>';
        $this->strText .= '</div>';
    }

    public function ParsePostData()
    {

    }

    //////////
    // Methods
    //////////

    public function Validate()
    {
        return true;
    }

    public function GetControlHtml()
    {
        $strStyle = $this->GetStyleAttributes();

        if ($strStyle)
            $strStyle = sprintf('style="%s"', $strStyle);

        $strToReturn = sprintf('<%s id="%s" %s%s>%s</%s>', $this->strTagName, $this->strControlId, $this->GetAttributes(true, false), $strStyle, $this->strText, $this->strTagName);

        return $strToReturn;
    }

    public function GetStyleAttributes()
    {
        $strStyle = parent::GetStyleAttributes();

        if ($this->strPadding)
            $strStyle .= sprintf('padding:%s;', $this->strPadding);

        if (($this->strHorizontalAlign) && ($this->strHorizontalAlign != QHorizontalAlign::NotSet))
            $strStyle .= sprintf('text-align:%s;', $this->strHorizontalAlign);

        if (($this->strVerticalAlign) && ($this->strVerticalAlign != QVerticalAlign::NotSet))
            $strStyle .= sprintf('vertical-align:%s;', $this->strVerticalAlign);

        return $strStyle;
    }

    /////////////////////////
    // Public Properties: GET
    /////////////////////////

    public function __get($strName)
    {
        switch ($strName) {
            // APPEARANCE
            case "Text":
                return $this->strText;
            case "TagName":
                return $this->strTagName;
            case "Padding":
                return $this->strPadding;

            // LAYOUT
            case "HorizontalAlign":
                return $this->strHorizontalAlign;
            case "VerticalAlign":
                return $this->strVerticalAlign;

            default:
                try {
                    return parent::__get($strName);
                } catch (QCallerException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
        }
    }

    /////////////////////////
    // Public Properties: SET
    /////////////////////////
    public function __set($strName, $mixValue)
    {
        $this->blnModified = true;

        switch ($strName) {
            // APPEARANCE
            case "Text":
                try {
                    $this->strText = QType::Cast($mixValue, QType::String);
                    break;
                } catch (QInvalidCastException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }

            case "TagName":
                try {
                    $this->strTagName = QType::Cast($mixValue, QType::String);
                    break;
                } catch (QInvalidCastException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }

            case "Padding":
                try {
                    $this->strPadding = QType::Cast($mixValue, QType::String);
                    break;
                } catch (QInvalidCastException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }

            case "HorizontalAlign":
                try {
                    $this->strHorizontalAlign = QType::Cast($mixValue, QType::String);
                    break;
                } catch (QInvalidCastException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }

            case "VerticalAlign":
                try {
                    $this->strVerticalAlign = QType::Cast($mixValue, QType::String);
                    break;
                } catch (QInvalidCastException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }

            default:
                try {
                    parent::__set($strName, $mixValue);
                } catch (QCallerException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
        }
    }

}

?>