<?php

/**
 * This file contains the QDateTimeTextBox class.
 *
 * @package Controls
 */

/**
 * @package Controls
 *
 * @property QDateTime $Maximum
 * @property QDateTime $Minimum
 * @property string $DateTimeFormat
 * @property QDateTime $DateTime
 * @property string $LabelForInvalid
 */
class QDateTimeTextBox extends QTextBox
{

    ///////////////////////////
    // Private Member Variables
    ///////////////////////////
    // MISC
    protected $dttMinimum = null;
    protected $dttMaximum = null;
    protected $strDateTimeFormat = "DD/MM/YYYY";
    protected $dttDateTime = null;
    protected $strDateTimeInputFormat = "DD/MM/YYYY";
    protected $strLabelForInvalid = 'Contoh, "17/8/1945 10:00" or "17/8/1945"';
    protected $calLinkedControl;

    //////////
    // Methods
    //////////

    public function ParsePostData()
    {
        // Check to see if this Control's Value was passed in via the POST data
        if (array_key_exists($this->strControlId, $_POST)) {
            parent::ParsePostData();
            $this->dttDateTime = QDateTimeTextBox::ParseForDateTimeValue($this->strText, $this->DateTimeFormat);
        }
    }

//  public static function ParseForDateTimeValue($strText) {
//    // Trim and Clean
//    $strText = strtolower(trim($strText));
//    while (strpos($strText, '  ') !== false)
//      $strText = str_replace('  ', ' ', $strText);
//    $strText = str_replace('.', '', $strText);
//    $strText = str_replace('@', ' ', $strText);
//
//    // Are we ATTEMPTING to parse a Time value?
//    if ((strpos($strText, ':') === false) &&
//            (strpos($strText, 'am') === false) &&
//            (strpos($strText, 'pm') === false)) {
//      // There is NO TIME VALUE
//      $dttToReturn = new QDateTime($strText);
//      if ($dttToReturn->IsDateNull())
//        return null;
//      else
//        return $dttToReturn;
//    }
//
//    // Add ':00' if it doesn't exist AND if 'am' or 'pm' exists
//    if ((strpos($strText, 'pm') !== false) &&
//            (strpos($strText, ':') === false)) {
//      $strText = str_replace(' pm', ':00 pm', $strText, $intCount);
//      if (!$intCount)
//        $strText = str_replace('pm', ':00 pm', $strText, $intCount);
//    } else if ((strpos($strText, 'am') !== false) &&
//            (strpos($strText, ':') === false)) {
//      $strText = str_replace(' am', ':00 am', $strText, $intCount);
//      if (!$intCount)
//        $strText = str_replace('am', ':00 am', $strText, $intCount);
//    }
//
//    $dttToReturn = new QDateTime($strText);
//    if ($dttToReturn->IsDateNull())
//      return null;
//    else
//      return $dttToReturn;
//  }

    public static function ParseForDateTimeValue($strText, $strInputFormat)
    {
        // parse date time format ini hanya berlaku untuk ku :D, ratno@knoqdown.com, 26 April 2012
        // convert ke format php
        // kemungkinan:
        // - DD/MM/YYYY hhhh:mm:ss
        // - DD/MM/YYYY hhhh:mm
        // - DD/MM/YYYY
        // - hhhh:mm:ss
        // - hhhh:mm
        switch ($strInputFormat) {
            case "DD/MM/YYYY hhhh:mm:ss":
                $intFormatType = QDateTime::DateAndTimeType;
                $strFormat = 'd/m/Y h:i:s';
                break;
            case "DD/MM/YYYY hhhh:mm":
                $intFormatType = QDateTime::DateAndTimeType;
                $strFormat = 'd/m/Y h:i';
                break;
            case "DD/MM/YYYY":
                $intFormatType = QDateTime::DateOnlyType;
                $strFormat = 'd/m/Y';
                break;
            case "hhhh:mm:ss":
                $intFormatType = QDateTime::TimeOnlyType;
                $strFormat = 'h:i:s';
                break;
            case "hhhh:mm":
                $intFormatType = QDateTime::TimeOnlyType;
                $strFormat = 'h:i';
                break;
        }

        $dttToReturn = new QDateTime($strText, null, $intFormatType, $strFormat);
        return $dttToReturn;
    }

    public function Validate()
    {
        if (parent::Validate()) {
            if ($this->strText != "") {
                $dttTest = QDateTimeTextBox::ParseForDateTimeValue($this->strText, $this->DateTimeFormat);

                if (!$dttTest) {
                    $this->strValidationError = $this->strLabelForInvalid;
                    return false;
                }

                if (!is_null($this->dttMinimum)) {
                    if ($this->dttMinimum == QDateTime::Now) {
                        $dttToCompare = new QDateTime(QDateTime::Now);
                        $strError = 'in the past';
                    } else {
                        $dttToCompare = $this->dttMinimum;
                        $strError = 'before ' . $this->dttMinimum->__toString();
                    }

                    if ($dttTest->IsEarlierThan($dttToCompare)) {
                        $this->strValidationError = 'Date cannot be ' . $strError;
                        return false;
                    }
                }

                if (!is_null($this->dttMaximum)) {
                    if ($this->dttMaximum == QDateTime::Now) {
                        $dttToCompare = new QDateTime(QDateTime::Now);
                        $strError = 'in the future';
                    } else {
                        $dttToCompare = $this->dttMaximum;
                        $strError = 'after ' . $this->dttMaximum->__toString();
                    }

                    if ($dttTest->IsLaterThan($dttToCompare)) {
                        $this->strValidationError = 'Date cannot be ' . $strError;
                        return false;
                    }
                }
            }
        } else
            return false;

        $this->strValidationError = '';
        return true;
    }

    /////////////////////////
    // Public Properties: GET
    /////////////////////////
    public function __get($strName)
    {
        switch ($strName) {
            // MISC
            case "Maximum":
                return $this->dttMaximum;
            case "Minimum":
                return $this->dttMinimum;
            case 'DateTimeFormat':
                return $this->strDateTimeFormat;
            case 'DateTime':
                return $this->dttDateTime;
            case 'LabelForInvalid':
                return $this->strLabelForInvalid;

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
            // MISC
            case 'Maximum':
                try {
                    if ($mixValue == QDateTime::Now)
                        $this->dttMaximum = QDateTime::Now;
                    else
                        $this->dttMaximum = QType::Cast($mixValue, QType::DateTime);
                    break;
                } catch (QInvalidCastException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }

            case 'Minimum':
                try {
                    if ($mixValue == QDateTime::Now)
                        $this->dttMinimum = QDateTime::Now;
                    else
                        $this->dttMinimum = QType::Cast($mixValue, QType::DateTime);
                    break;
                } catch (QInvalidCastException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }

            case 'DateTimeFormat':
                try {
                    $this->strDateTimeFormat = QType::Cast($mixValue, QType::String);
                    // trigger an update to reformat the text with the new format
                    $this->DateTime = $this->dttDateTime;
                    return $this->strDateTimeFormat;
                } catch (QInvalidCastException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }

            case 'DateTime':
                try {
                    $this->dttDateTime = QType::Cast($mixValue, QType::DateTime);
                    if (!$this->dttDateTime || !$this->strDateTimeFormat) {
                        parent::__set('Text', '');
                    } else {
                        parent::__set('Text', $this->dttDateTime->qFormat($this->strDateTimeFormat));
                    }
                    return $this->dttDateTime;
                } catch (QInvalidCastException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }

            case 'Text':
                $this->dttDateTime = QDateTimeTextBox::ParseForDateTimeValue($this->strText, $this->DateTimeFormat);
                return parent::__set('Text', $mixValue);

            case 'LabelForInvalid':
                try {
                    return ($this->strLabelForInvalid = QType::Cast($mixValue, QType::String));
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