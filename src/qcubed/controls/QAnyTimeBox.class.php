<?php

/**
 * @property string $DateFormat
 * @property string $DateTimeFormat
 * @property QDateTime $DateTime
 */
class QAnyTimeBox extends QAnyTimeBoxBase
{

    static private $mapQC2AT = array(
        'MMMM' => '%m',
        'MMM' => '%M',
        'MM' => '%m',
        'M' => '%n',
        'DDDD' => '%l',
        'DDD' => '%D',
        'DD' => '%d',
        'D' => '%j',
        'YYYY' => '%Y',
        'YY' => '%y',
        'hhhh' => '%H',
        'hh' => '%h',
        'mm' => '%i',
        'ss' => '%s'
    );
    static private $mapAT2QC = null;
    protected $DayAbbreviations = array('Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab');
    protected $DayNames = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
    protected $MonthNames = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
    protected $MonthAbbreviations = array('Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des');
    protected $LabelDayOfMonth = "Tanggal";
    protected $LabelHour = "Jam";
    protected $LabelMinute = "Menit";
    protected $LabelSecond = "Detik";
    protected $LabelYear = "Tahun";
    protected $LabelMonth = "Bulan";
    // map the Anytime datepicker format specs to QCodo QDateTime format specs.
    //qcodo	Anytime			php	Description
    //-------------------------------------------------
    //MMMM	  %F			  F	Month as full name (e.g., March)
    //MMM	  	%M	      M	Month as three-letters (e.g., Mar)
    //MM	    %m	      m	Month as an integer with leading zero (e.g., 03)
    //M				%n        n	Month as an integer (e.g., 3)
    //DDDD	 	%l  	    l	Day of week as full name (e.g., Wednesday)
    //DDD	    %D        D	Day of week as three-letters (e.g., Wed)
    //DD      %d	      d	Day as an integer with leading zero (e.g., 02)
    //D       %j        j	Day as an integer (e.g., 2)
    //YYYY    %Y		    Y	Year as a four-digit integer (e.g., 1977)
    //YY      %y        y	Year as a two-digit integer (e.g., 77)
    protected $strDateTimeFormat = "DD/MM/YYYY";
    protected $dttDateTime;

    static public function qcFrmt($atFrmt)
    {
        if (!QAnyTimeBox::$mapAT2QC) {
            QAnyTimeBox::$mapAT2QC = array_flip(QAnyTimeBox::$mapQC2AT);
        }
        return strtr($atFrmt, QAnyTimeBox::$mapAT2QC);
    }

    public function ParsePostData()
    {
        // Check to see if this Control's Value was passed in via the POST data
        if (array_key_exists($this->strControlId, $_POST)) {
            parent::ParsePostData();
            $this->dttDateTime = new QDateTime($this->strText, null, false, QAnyTimeBox::phpFrmt($this->DateTimeFormat));
        }
    }

    static public function phpFrmt($qcFrmt)
    {
        return str_replace('%', '', strtr($qcFrmt, QAnyTimeBox::$mapQC2AT));
    }

    public function __get($strName)
    {
        switch ($strName) {
            // MISC
            case 'DateTimeFormat':
            case 'DateFormat':
                return $this->strDateTimeFormat;
            case 'DateTime':
                return $this->dttDateTime;

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
        $this->blnModified = true;

        switch ($strName) {
            case 'DateTime':
                try {
                    if (is_null($mixValue) || $mixValue instanceof QDateTime) {
                        $this->dttDateTime = QType::Cast($mixValue, QType::DateTime);
//            $this->dttDateTime = QDateTime::Now();
                    } else {
                        $this->dttDateTime = new QDateTime($mixValue, null, false, QAnyTimeBox::phpFrmt($this->DateTimeFormat));
                    }
                    if (!$this->dttDateTime || !$this->strDateTimeFormat) {
                        parent::__set('Text', '');
                    } else {
                        parent::__set('Text', $this->dttDateTime->qFormat($this->strDateTimeFormat));
                    }
                    break;
                } catch (QInvalidCastException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }

//      case 'AtDateTimeFormat':
//        try {
//          parent::__set($strName, $mixValue);
//          $this->strDateTimeFormat = QAnyTimeBox::qcFrmt($this->AtDateTimeFormat);
//          // trigger an update to reformat the text with the new format
//          $this->DateTime = $this->dttDateTime;
//          break;
//        } catch (QInvalidCastException $objExc) {
//          $objExc->IncrementOffset();
//          throw $objExc;
//        }

            case 'DateTimeFormat':
            case 'DateFormat':
                try {
                    $this->strDateTimeFormat = QType::Cast($mixValue, QType::String);
                    parent::__set('AtDateTimeFormat', QAnyTimeBox::atFrmt($this->strDateTimeFormat));
                    // trigger an update to reformat the text with the new format
                    $this->DateTime = $this->dttDateTime;
                    break;
                } catch (QInvalidCastException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }

            case 'Text':
                parent::__set($strName, $mixValue);
                $this->dttDateTime = new QDateTime($this->strText);
                break;

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

    static public function atFrmt($qcFrmt)
    {
        return strtr($qcFrmt, QAnyTimeBox::$mapQC2AT);
    }

}