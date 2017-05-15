<?php
require(__BASEPATH__ . '/app/qd/models/base/SettingBase.php');

/**
 * @author Ratno Putro Sulistiyono, ratno@knoqdown.com
 * @company KnoQDown Studio
 *
 * @package Sistem Informasi
 * @subpackage DataObjects
 *
 */
class Setting extends SettingBase
{
    public static function GetUniqueColumns()
    {
        $arrColumns = array();
        $arrColumns['name'] = 'Name';

        return $arrColumns;
    }

    public static function generate($strSettingName, $initval = 1, $initopt = null)
    {
        $obj = Setting::Get($strSettingName, true);

        $return = null;
        if ($obj instanceof Setting) {
            $return = $obj->Val;
        } else {
            $obj = new Setting();
            $obj->Name = $strSettingName;
            $obj->Val = $initval;
            $obj->Opt = $initopt;
            $obj->Save();
            $return = $obj->Val;
        }

        // increment
        $obj->Val = $obj->Val + 1;
        $obj->Save();

        return $return;
    }

    public static function Get($strSettingName, $blnObj = false)
    {
        $obj = Setting::LoadByName($strSettingName);
        if ($obj instanceof Setting) {
            if ($blnObj) {
                return $obj;
            } else {
                return $obj->Val;
            }
        } else {
            return null;
        }
    }

    /**
     * Default "to string" handler
     * Allows pages to _p()/echo()/print() this object, and to define the default
     * way this object would be outputted.
     *
     * Can also be called directly via $objSetting->__toString().
     *
     * @return string a nicely formatted string representation of this object
     */
    public function __toString()
    {
//			return sprintf('Setting Object  %s',  $this->intId);
        return sprintf('%s', $this->strName);
    }

    public function printUnique()
    {
        return sprintf('%s', $this->strName);
    }

    // Override or Create New Load/Count methods
    // (For obvious reasons, these methods are commented out...
    // but feel free to use these as a starting point)
    /*
        public static function LoadArrayBySample($strParam1, $intParam2, $objOptionalClauses = null) {
          // This will return an array of Setting objects
          return Setting::QueryArray(
            QQ::AndCondition(
              QQ::Equal(QQN::Setting()->Param1, $strParam1),
              QQ::GreaterThan(QQN::Setting()->Param2, $intParam2)
            ),
            $objOptionalClauses
          );
        }

        public static function LoadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
          // This will return a single Setting object
          return Setting::QuerySingle(
            QQ::AndCondition(
              QQ::Equal(QQN::Setting()->Param1, $strParam1),
              QQ::GreaterThan(QQN::Setting()->Param2, $intParam2)
            ),
            $objOptionalClauses
          );
        }

        public static function CountBySample($strParam1, $intParam2, $objOptionalClauses = null) {
          // This will return a count of Setting objects
          return Setting::QueryCount(
            QQ::AndCondition(
              QQ::Equal(QQN::Setting()->Param1, $strParam1),
              QQ::Equal(QQN::Setting()->Param2, $intParam2)
            ),
            $objOptionalClauses
          );
        }

        public static function LoadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
          // Performing the load manually (instead of using QCubed Query)

          // Get the Database Object for this Class
          $objDatabase = Setting::GetDatabase();

          // Properly Escape All Input Parameters using Database->SqlVariable()
          $strParam1 = $objDatabase->SqlVariable($strParam1);
          $intParam2 = $objDatabase->SqlVariable($intParam2);

          // Setup the SQL Query
          $strQuery = sprintf('
            SELECT
              `setting`.*
            FROM
              `setting` AS `setting`
            WHERE
              param_1 = %s AND
              param_2 < %s',
            $strParam1, $intParam2);

          // Perform the Query and Instantiate the Result
          $objDbResult = $objDatabase->Query($strQuery);
          return Setting::InstantiateDbResult($objDbResult);
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