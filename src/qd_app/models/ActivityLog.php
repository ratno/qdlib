<?php

require(__BASEPATH__ . '/app/qd/models/base/ActivityLogBase.php');

/**
 * @author Ratno Putro Sulistiyono, ratno@knoqdown.com
 * @company KnoQDown Studio
 *
 * @package Sistem Informasi
 * @subpackage DataObjects
 *
 */
class ActivityLog extends ActivityLogBase
{

    public static function GetUniqueColumns()
    {
        $arrColumns = array();
        return $arrColumns;
    }

    public static function LoadArrayByObjectModel($strModelName, $intModelIdNumber, $mixActivityLogAction = null)
    {
        $arrConditions = array();
        $arrConditions[] = QQ::Equal(QQN::ActivityLog()->ObjectModel, $strModelName);
        $arrConditions[] = QQ::Equal(QQN::ActivityLog()->ObjectIdNumber, $intModelIdNumber);
        if (is_array($mixActivityLogAction)) {
            $arrConditions[] = QQ::In(QQN::ActivityLog()->Action, $mixActivityLogAction);
        } else {
            if (!is_null($mixActivityLogAction)) {
                $arrConditions[] = QQ::Equal(QQN::ActivityLog()->Action, $mixActivityLogAction);
            }
        }
        return ActivityLog::QueryArray(QQ::AndCondition($arrConditions), QQ::Clause(QQ::Expand(QQN::ActivityLog()->User), QQ::Expand(QQN::ActivityLog()->User->Role)));
    }

    public static function LoadArrayBySubjectModel($strModelName, $intModelIdNumber, $mixActivityLogAction = null)
    {
        $arrConditions = array();
        $arrConditions[] = QQ::Equal(QQN::ActivityLog()->SubjectModel, $strModelName);
        $arrConditions[] = QQ::Equal(QQN::ActivityLog()->SubjectIdNumber, $intModelIdNumber);
        if (is_array($mixActivityLogAction)) {
            $arrConditions[] = QQ::In(QQN::ActivityLog()->Action, $mixActivityLogAction);
        } else {
            if (!is_null($mixActivityLogAction)) {
                $arrConditions[] = QQ::Equal(QQN::ActivityLog()->Action, $mixActivityLogAction);
            }
        }
        return ActivityLog::QueryArray(QQ::AndCondition($arrConditions), QQ::Clause(QQ::Expand(QQN::ActivityLog()->User), QQ::Expand(QQN::ActivityLog()->User->Role)));
    }

    /**
     * Default "to string" handler
     * Allows pages to _p()/echo()/print() this object, and to define the default
     * way this object would be outputted.
     *
     * Can also be called directly via $objActivityLog->__toString().
     *
     * @return string a nicely formatted string representation of this object
     */
    public function __toString()
    {
        $strText[] = $this->User;
        $strText[] = $this->getAction();

        if ($this->AttachmentFile) {
            $strText[] = sprintf("[%s]", href(__OTHERS_URL__ . $this->AttachmentFile, $this->AttachmentName));
        }

//    return sprintf('[%s yang lalu] %s', $this->Ts->qFormat('DD/MM/YYYY hhhh:mm:ss'), implode(" ",$strText));
        return sprintf('[<a href="#" class="ttip_t" title="%s">%s yang lalu</a>] %s', $this->Ts->qFormat('DD/MM/YYYY hhhh:mm:ss'), time_ago($this->Ts->getTimestamp()), implode(" ", $strText));
    }

    protected function getAction()
    {
        switch ($this->Action) {
            case ActivityAction::Mention:
                $strText = sprintf(ActivityAction::ToString($this->Action), $this->ForUser);
                break;
            case ActivityAction::Add:
            case ActivityAction::Edit:
            case ActivityAction::Delete:
            case ActivityAction::ViewList:
            case ActivityAction::ViewDetail:
            case ActivityAction::Comment:
                $strText = sprintf(ActivityAction::ToString($this->Action), QConvertNotation::WordsFromCamelCase($this->SubjectModel));
                break;
            default:
                $strText = ActivityAction::ToString($this->Action);
                break;
        }
        return $strText;
    }

    public function printUnique()
    {
    }

    public function ToStringForNotification($strPrefix = "", $strSuffix = "", $strMiddlex = "")
    {
        if (!is_empty($strPrefix))
            $strText[] = $strPrefix;
        $strText[] = $this->User;

        if (!is_empty($strMiddlex)) {
            $strText[] = $strMiddlex;
        } else {
            $strText[] = $this->getAction();
        }

        if (!is_empty($strSuffix))
            $strText[] = $strSuffix;

//    return sprintf('[%s] %s', $this->Ts->qFormat('DD/MM/YYYY hhhh:mm:ss'), implode(" ", $strText));
        return sprintf('[<a href="#" class="ttip_t" title="%s">%s yang lalu</a>] %s', $this->Ts->qFormat('DD/MM/YYYY hhhh:mm:ss'), time_ago($this->Ts->getTimestamp()), implode(" ", $strText));
    }

    // Override or Create New Load/Count methods
    // (For obvious reasons, these methods are commented out...
    // but feel free to use these as a starting point)
    /*
      public static function LoadArrayBySample($strParam1, $intParam2, $objOptionalClauses = null) {
      // This will return an array of ActivityLog objects
      return ActivityLog::QueryArray(
      QQ::AndCondition(
      QQ::Equal(QQN::ActivityLog()->Param1, $strParam1),
      QQ::GreaterThan(QQN::ActivityLog()->Param2, $intParam2)
      ),
      $objOptionalClauses
      );
      }

      public static function LoadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
      // This will return a single ActivityLog object
      return ActivityLog::QuerySingle(
      QQ::AndCondition(
      QQ::Equal(QQN::ActivityLog()->Param1, $strParam1),
      QQ::GreaterThan(QQN::ActivityLog()->Param2, $intParam2)
      ),
      $objOptionalClauses
      );
      }

      public static function CountBySample($strParam1, $intParam2, $objOptionalClauses = null) {
      // This will return a count of ActivityLog objects
      return ActivityLog::QueryCount(
      QQ::AndCondition(
      QQ::Equal(QQN::ActivityLog()->Param1, $strParam1),
      QQ::Equal(QQN::ActivityLog()->Param2, $intParam2)
      ),
      $objOptionalClauses
      );
      }

      public static function LoadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
      // Performing the load manually (instead of using QCubed Query)

      // Get the Database Object for this Class
      $objDatabase = ActivityLog::GetDatabase();

      // Properly Escape All Input Parameters using Database->SqlVariable()
      $strParam1 = $objDatabase->SqlVariable($strParam1);
      $intParam2 = $objDatabase->SqlVariable($intParam2);

      // Setup the SQL Query
      $strQuery = sprintf('
      SELECT
      `activity_log`.*
      FROM
      `activity_log` AS `activity_log`
      WHERE
      param_1 = %s AND
      param_2 < %s',
      $strParam1, $intParam2);

      // Perform the Query and Instantiate the Result
      $objDbResult = $objDatabase->Query($strQuery);
      return ActivityLog::InstantiateDbResult($objDbResult);
      }
     */


    // Override or Create New Properties and Variables
    // For performance reasons, these variables and __set and __get override methods
    // are commented out.  But if you wish to implement or override any
    // of the data generated properties, please feel free to uncomment them.
    /*
      protected $strSomeNewProperty;

      public function __get($strName) {
      switch ($strName) {
      case 'SomeNewProperty': return $this->strSomeNewProperty;

      default:
      try {
      return parent::__get($strName);
      } catch (QCallerException $objExc) {
      $objExc->IncrementOffset();
      throw $objExc;
      }
      }
      }

      public function __set($strName, $mixValue) {
      switch ($strName) {
      case 'SomeNewProperty':
      try {
      return ($this->strSomeNewProperty = QType::Cast($mixValue, QType::String));
      } catch (QInvalidCastException $objExc) {
      $objExc->IncrementOffset();
      throw $objExc;
      }

      default:
      try {
      return (parent::__set($strName, $mixValue));
      } catch (QCallerException $objExc) {
      $objExc->IncrementOffset();
      throw $objExc;
      }
      }
      }
     */


    // Initialize each property with default values from database definition
    /*
      public function __construct()
      {
      $this->Initialize();
      }
     */
}

?>