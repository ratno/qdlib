<?php

/**
 * This file contains the QCheckBox class.
 *
 * @package Controls
 */

/**
 * This class will render an HTML Checkbox.
 *
 * @package Controls
 *
 * @property string $Text is used to display text that is displayed next to the checkbox.  The text is rendered as an html "Label For" the checkbox.
 * @property string $TextAlign specifies if "Text" should be displayed to the left or to the right of the checkbox.
 * @property boolean $Checked specifices whether or not hte checkbox is checked
 * @property boolean $HtmlEntities specifies whether the checkbox text will have to be run through htmlentities or not.
 */
class QCheckBox extends QControl
{

    ///////////////////////////
    // Private Member Variables
    ///////////////////////////
    // APPEARANCE
    /** @var string Text opposite to the checkbox */
    protected $strText = null;

    /** @var QTextAlign|string the alignment of the string */
    protected $strTextAlign = QTextAlign::Right;
    // BEHAVIOR
    /** @var bool Should the htmlentities function be run on the control's text (strText)? */
    protected $blnHtmlEntities = true;
    // MISC
    /** @var bool Determines whether the checkbox is checked? */
    protected $blnChecked = false;

    //////////
    // Methods
    //////////
    /**
     * Parses the Post Data submitted for the control and sets the values
     * according to the data submitted
     */
    public function ParsePostData()
    {
        if ($this->objForm->IsCheckableControlRendered($this->strControlId)) {
            if (array_key_exists($this->strControlId, $_POST)) {
                if ($_POST[$this->strControlId])
                    $this->blnChecked = true;
                else
                    $this->blnChecked = false;
            } else {
                $this->blnChecked = false;
            }
        }
    }

    /**
     * Returns the HTML code for the control which can be sent to the client
     * @return string THe HTML for the control
     */
    public function GetControlHtml()
    {
        if (!$this->blnEnabled)
            $strDisabled = 'disabled="disabled" ';
        else
            $strDisabled = "";

        if ($this->intTabIndex)
            $strTabIndex = sprintf('tabindex="%s" ', $this->intTabIndex);
        else
            $strTabIndex = "";

        if ($this->strToolTip)
            $strToolTip = sprintf('title="%s" ', $this->strToolTip);
        else
            $strToolTip = "";

        if ($this->strCssClass)
            $strCssClass = sprintf('class="%s" ', $this->strCssClass);
        else
            $strCssClass = "";

        if ($this->strAccessKey)
            $strAccessKey = sprintf('accesskey="%s" ', $this->strAccessKey);
        else
            $strAccessKey = "";

        if ($this->blnChecked)
            $strChecked = 'checked="checked" ';
        else
            $strChecked = "";

        $strStyle = $this->GetStyleAttributes();
        if (strlen($strStyle) > 0)
            $strStyle = sprintf('style="%s" ', $strStyle);

        $strCustomAttributes = $this->GetCustomAttributes();

        $strActions = $this->GetActionAttributes();

        if (strlen($this->strText)) {
            $this->blnIsBlockElement = true;
            if ($this->strTextAlign == QTextAlign::Left) {
                $strToReturn = sprintf('<table cellspacing="0" cellpadding="0" border="0"><tr><td %s%s%s%s%s><label for="%s">%s</label><input type="checkbox" id="%s" name="%s" %s%s%s%s /></td></tr></table>', $strCssClass, $strToolTip, $strStyle, $strCustomAttributes, $strDisabled, $this->strControlId, ($this->blnHtmlEntities) ? QApplication::HtmlEntities($this->strText) : $this->strText, $this->strControlId, $this->strControlId, $strDisabled, $strChecked, $strAccessKey, $strTabIndex
                );
            } else {
                $strToReturn = sprintf('<table cellspacing="0" cellpadding="0" border="0"><tr><td><input type="checkbox" id="%s" name="%s" %s%s%s%s /></td><td %s%s%s%s%s><label for="%s">%s</label></td></tr></table>', $this->strControlId, $this->strControlId, $strDisabled, $strChecked, $strAccessKey, $strTabIndex, $strCssClass, $strToolTip, $strStyle, $strCustomAttributes, $strDisabled, $this->strControlId, ($this->blnHtmlEntities) ? QApplication::HtmlEntities($this->strText) : $this->strText
                );
            }
        } else {
            $this->blnIsBlockElement = false;
            $strToReturn = sprintf('<input type="checkbox" id="%s" name="%s" %s%s%s%s%s%s%s%s />', $this->strControlId, $this->strControlId, $strCssClass, $strDisabled, $strChecked, $strAccessKey, $strToolTip, $strTabIndex, $strCustomAttributes, $strStyle);
        }

        return $strToReturn;
    }

    /**
     * Checks whether the post data submitted for the control is valid or not
     * Right now it tests whether or not the control was marked as required and then tests whether it
     * was checked or not
     * @return bool
     */
    public function Validate()
    {
        if ($this->blnRequired) {
            if (!$this->blnChecked) {
                if ($this->strName)
                    $this->strValidationError = QApplication::Translate($this->strName) . ' ' . QApplication::Translate('is required');
                else
                    $this->strValidationError = QApplication::Translate('Required');
                return false;
            }
        }

        $this->strValidationError = null;
        return true;
    }

    /////////////////////////
    // Public Properties: GET
    /////////////////////////
    /**
     * PHP __get magic method implementation
     * @param string $strName Name of the property
     *
     * @return mixed
     * @throws QCallerException
     */
    public function __get($strName)
    {
        switch ($strName) {
            // APPEARANCE
            case "Text":
                return $this->strText;
            case "TextAlign":
                return $this->strTextAlign;

            // BEHAVIOR
            case "HtmlEntities":
                return $this->blnHtmlEntities;

            // MISC
            case "Checked":
                return $this->blnChecked;
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
    /**
     * PHP __set magic method implementation
     * @param string $strName
     * @param string $mixValue
     *
     * @return mixed
     * @throws QInvalidCastException|QCallerException
     */
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
            case "TextAlign":
                try {
                    $this->strTextAlign = QType::Cast($mixValue, QType::String);
                    break;
                } catch (QInvalidCastException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }

            case "HtmlEntities":
                try {
                    $this->blnHtmlEntities = QType::Cast($mixValue, QType::Boolean);
                    break;
                } catch (QInvalidCastException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }

            // MISC
            case "Checked":
                try {
                    $this->blnChecked = QType::Cast($mixValue, QType::Boolean);
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

?>