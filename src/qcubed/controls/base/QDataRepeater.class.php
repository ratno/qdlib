<?php

/**
 * This file contains the QDataRepeater class.
 *
 * @package Controls
 */

/**
 * @package Controls
 *
 * @property string $Template
 * @property-read integer $CurrentItemIndex
 * @property string $TagName
 *
 */
class QDataRepeater extends QPaginatedControl
{

    ///////////////////////////
    // Private Member Variables
    ///////////////////////////
    // APPEARANCE
    protected $strTemplate = null;
    protected $intCurrentItemIndex = null;
    protected $strTagName = 'div';
    protected $strLabelForNoneFound;
    protected $strLabelForOneFound;
    protected $strLabelForMultipleFound;
    protected $strLabelForPaginated;

    //////////
    // Methods
    //////////

    public function __construct($objParentObject, $strControlId = null)
    {
        parent::__construct($objParentObject, $strControlId);

        // Labels
        $this->strLabelForNoneFound = QApplication::Translate('<b>Results:</b> No %s found.');
        $this->strLabelForOneFound = QApplication::Translate('<b>Results:</b> 1 %s found.');
        $this->strLabelForMultipleFound = QApplication::Translate('<b>Results:</b> %s %s found.');
        $this->strLabelForPaginated = QApplication::Translate('<b>Results:</b>&nbsp;Viewing&nbsp;%s&nbsp;%s-%s&nbsp;of&nbsp;%s.');
    }

    public function ParsePostData()
    {

    }

    public function GetControlHtml()
    {
        $this->DataBind();

        // Setup Style
        $strStyle = $this->GetStyleAttributes();
        if ($strStyle)
            $strStyle = sprintf('style="%s"', $strStyle);

        // Iterate through everything
        $this->intCurrentItemIndex = 0;
        $strEvalledItems = '';
        $strToReturn = '';

        // Paginator Row (if applicable)
        if ($this->objPaginator) {
            $strToReturn .= '<div style="margin-bottom:20px; height:20px;">';
            $strToReturn .= "<caption>\r\n" . $this->GetPaginatorRowHtml($this->objPaginator) . "</caption>\r\n";
            $strToReturn .= "</div>";
        }


        if (($this->strTemplate) && ($this->objDataSource)) {
            global $_FORM;
            global $_CONTROL;
            global $_ITEM;
            $_FORM = $this->objForm;
            $objCurrentControl = $_CONTROL;
            $_CONTROL = $this;

            foreach ($this->objDataSource as $objObject) {
                $_ITEM = $objObject;
                $strEvalledItems .= $this->objForm->EvaluateTemplate($this->strTemplate);
                $this->intCurrentItemIndex++;
            }

            $strToReturn .= sprintf('<%s id="%s" %s%s>%s</%s>', $this->strTagName, $this->strControlId, $this->GetAttributes(), $strStyle, $strEvalledItems, $this->strTagName);

            $_CONTROL = $objCurrentControl;
        }

        $this->objDataSource = null;
        return $strToReturn;
    }

    protected function GetPaginatorRowHtml($objPaginator)
    {
        $strToReturn = "  <span class=\"right\" style='float:right;'>";
        $strToReturn .= $objPaginator->Render(false);
        $strToReturn .= "</span>\r\n  <span class=\"left\" style=\"float:left;font-size:10px;font-weight: normal;font-family: Arial, Geneva, Tahoma, Verdana, sans-serif;\">";
        if ($this->TotalItemCount > 0) {
            $intStart = (($this->PageNumber - 1) * $this->ItemsPerPage) + 1;
            $intEnd = $intStart + count($this->DataSource) - 1;
            $strToReturn .= sprintf($this->strLabelForPaginated, $this->strNounPlural, $intStart, $intEnd, $this->TotalItemCount);
        } else {
            $intCount = count($this->objDataSource);
            if ($intCount == 0)
                $strToReturn .= sprintf($this->strLabelForNoneFound, $this->strNounPlural);
            else if ($intCount == 1)
                $strToReturn .= sprintf($this->strLabelForOneFound, $this->strNoun);
            else
                $strToReturn .= sprintf($this->strLabelForMultipleFound, $intCount, $this->strNounPlural);
        }

        $strToReturn .= "</span>\r\n";

        return $strToReturn;
    }

    /////////////////////////
    // Public Properties: GET
    /////////////////////////

    public function __get($strName)
    {
        switch ($strName) {
            // APPEARANCE
            case "Template":
                return $this->strTemplate;
            case "CurrentItemIndex":
                return $this->intCurrentItemIndex;
            case "TagName":
                return $this->strTagName;

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
            case "Template":
                try {
                    if (file_exists($mixValue))
                        $this->strTemplate = QType::Cast($mixValue, QType::String);
                    else
                        throw new QCallerException('Template file does not exist: ' . $mixValue);
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