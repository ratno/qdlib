<?php
require(__BASEPATH__ . '/apps/models/base/WorkflowBase.class.php');

/**
 * @author Ratno Putro Sulistiyono, ratno@knoqdown.com
 * @company KNOQDOWN STUDIO
 *
 * @package PCU SIM FINANCE
 * @subpackage DataObjects
 *
 */
class Workflow extends WorkflowBase
{
    const next = 1;
    const back = 2;
    const na = 3;

    public static function encode($mixWorkflowHistory)
    {
        return json_encode($mixWorkflowHistory);
    }

    /**
     * @param string $mixWorkflowHistory
     * @param array $mixWorkflowHistory
     * @param string $strName
     * @return array
     * @throws Exception
     * @throws QCallerException
     * @throws QUndefinedPrimaryKeyException
     */
    public static function getStep($mixWorkflowHistory = null, $strName = "main")
    {
        $mixWorkflowHistory = Workflow::decode($mixWorkflowHistory);

        if (is_array($mixWorkflowHistory) && count($mixWorkflowHistory) > 0) {
            $mixWorkflowHistoryById = array();
            $all_step_id = array();
            foreach ($mixWorkflowHistory as $itemWorkflowHistory) {
                $all_step_id[$itemWorkflowHistory['step_id']] = $itemWorkflowHistory['step_id'];
                $mixWorkflowHistoryById[$itemWorkflowHistory['step_id']] = $itemWorkflowHistory;
            }
            $last_idx = count($mixWorkflowHistory) - 1;
            if ($mixWorkflowHistory[$last_idx]['status'] == Workflow::na) {
                return array($mixWorkflowHistory[$last_idx]);
            } else {
                if ($mixWorkflowHistory[$last_idx]['status'] == Workflow::next) {
                    $cond = QQ::Equal(QQN::WorkflowStep()->WorkflowStepAsWorkflowPrevStep->WorkflowStep->Id, $mixWorkflowHistory[$last_idx]["step_id"]);
                } else {
                    $cond = QQ::Equal(QQN::WorkflowStep()->ParentWorkflowStepAsWorkflowBackStep->WorkflowStep->Id, $mixWorkflowHistory[$last_idx]["step_id"]);
                }
                $objStepArray = WorkflowStep::QueryArray(QQ::AndCondition(
                    QQ::Equal(QQN::WorkflowStep()->Workflow->Name, $strName),
                    $cond
                ), QQ::Expand(QQN::WorkflowStep()->Workflow));

                $arrStepToReturn = array();
                if ($objStepArray) {
                    foreach ($objStepArray as $objStep) {
                        if ($objStep->OnlyOnce && array_key_exists($objStep->Id, $all_step_id)) {
                            continue; // skip this step because we only use it once
                        }

                        Workflow::setWfStep($arrWorkflowHistory, $objStep, $mixWorkflowHistoryById);
                        $arrStepToReturn[] = $arrWorkflowHistory;

                        if ($objStep->OnlyOnce) { // masih lanjut berarti only once tapi blum pernah dilewati, return ini
                            return array($arrWorkflowHistory);
                        }
                    }
                }
                return $arrStepToReturn;
            }
        } else {
            // step pertama
            $objStep = WorkflowStep::QuerySingle(
                QQ::AndCondition(
                    QQ::Equal(QQN::WorkflowStep()->Workflow->Name, $strName),
                    QQ::Equal(QQN::WorkflowStep()->StartStep, 1)
                ),
                QQ::Clause(
                    QQ::Expand(QQN::WorkflowStep()->Workflow)
                )
            );

            Workflow::setWfStep($arrWorkflowHistory, $objStep);
            Workflow::setWfOpen($arrWorkflowHistory);
            Workflow::setWfDone($arrWorkflowHistory, Workflow::next, "OK", "");
            return array($arrWorkflowHistory);
        }
    }

    public static function decode($mixWorkflowHistory)
    {
        $temp = $mixWorkflowHistory;
        if (!is_array($mixWorkflowHistory) && !is_null($mixWorkflowHistory)) {
            $mixWorkflowHistory = json_decode($temp, true);
        }

        for ($i = 0; $i < count($mixWorkflowHistory); $i++) {
            if (count($mixWorkflowHistory[$i]) > 0) {
                // do nothing
            } else {
                unset($mixWorkflowHistory[$i]);
            }
        }

        return $mixWorkflowHistory;
    }

    public static function setWfStep(&$wf, WorkflowStep $objStep, $mixWorkflowHistory = array())
    {
        $wf["step_id"] = $objStep->Id;
        $wf["step_name"] = $objStep->Name;
        $wf["workflow_id"] = $objStep->WorkflowId;
        $wf["workflow_name"] = $objStep->Workflow->Name;
        $wf["order_num"] = $objStep->OrderNum;

        $arrRoles = $objStep->GetRoleAsWorkflowStepArray();
        if ($arrRoles) {
            foreach ($arrRoles as $objRole) {
                $wf["roles"][] = $objRole->Id;
            }
        }

        if (array_key_exists($objStep->Id, $mixWorkflowHistory)) {
            $data_user = $mixWorkflowHistory[$objStep->Id]['user'];
        } else {
            if ($mixWorkflowHistory) {
                foreach ($mixWorkflowHistory as $step_id => $wfHistoryItem) {
                    if (in_array($wfHistoryItem['user']['role_id'], $wf['roles'])) {
                        $data_user = $wfHistoryItem['user'];
                    }
                }
            } else {
                $data_user = false;
            }
        }

        if ($data_user) {
            Workflow::setWfUser($wf, $data_user);
        } else {
            $count_user = Users::QueryCount(QQ::In(QQN::Users()->RoleId, $wf['roles']));
            if ($count_user == 1) {
                $objUser = Users::QuerySingle(QQ::In(QQN::Users()->RoleId, $wf['roles']), QQ::Expand(QQN::Users()->Role));
                Workflow::setWfUser($wf, $objUser);
            } else {
                Workflow::setWfUser($wf);
            }
        }

        $wf["ts_create"] = QDateTime::Now(true);
        $wf["status"] = Workflow::na;
    }

    public static function setWfUser(&$wf, $objUser = null)
    {
        if (is_array($wf)) {
            if ($objUser instanceof Users) {
                $wf["user"]["id"] = $objUser->Id;
                $wf["user"]["username"] = $objUser->Username;
                $wf["user"]["name"] = $objUser->Username;
                $wf["user"]["handphone"] = $objUser->Handphone;
                $wf["user"]["email"] = $objUser->Email;
                $wf["user"]["role_id"] = $objUser->RoleId;
                $wf["user"]["role_name"] = $objUser->Role->Name;
            } elseif (is_array($objUser) && count($objUser) > 0) {
                $wf["user"] = $objUser;
            } else {
                $wf["user"]["id"] = null;
                $wf["user"]["username"] = null;
                $wf["user"]["name"] = null;
                $wf["user"]["handphone"] = null;
                $wf["user"]["email"] = null;
                $wf["user"]["role_id"] = null;
                $wf["user"]["role_name"] = null;
            }
        }
    }

    public static function setWfOpen(&$wf, QDateTime $dttOpen = null)
    {
        if (array_key_exists("ts_open", $wf) && is_array($wf["ts_open"])) {
            // no update ts open
        } else {
            if ($dttOpen) {
                $wf["ts_open"] = $dttOpen;
            } else {
                $wf["ts_open"] = QDateTime::Now(true);
            }
        }
    }

    public static function setWfDone(&$wf, $status, $hasil, $keterangan = "", QDateTime $dttDone = null)
    {
        if ($dttDone) {
            $wf["ts_done"] = $dttDone;
        } else {
            $wf["ts_done"] = QDateTime::Now(true);
        }
        $wf["status"] = $status;
        $wf["hasil"] = $hasil;
        $wf["keterangan"] = $keterangan;
    }

    public static function compose($data, $mixWorkflowHistory = null, Users $objUser = null)
    {
        $mixWorkflowHistory = Workflow::decode($mixWorkflowHistory);

        if (count($mixWorkflowHistory)) {
            $last_idx = count($mixWorkflowHistory) - 1;
            if ($mixWorkflowHistory[$last_idx]['step_id'] == $data['step_id']) {
                $current_idx = $last_idx;
            } else {
                $current_idx = $last_idx + 1;
            }
        } else {
            $current_idx = 0;
        }


        if ($objUser instanceof Users) {
            Workflow::setWfUser($data, $objUser);
        }

        $mixWorkflowHistory[$current_idx] = $data;
        return $mixWorkflowHistory;
    }

    public static function setWfCreate(&$wf, QDateTime $dttCreate)
    {
        $wf["ts_create"] = $dttCreate;
    }

    public static function getWfByStepId($wfHistory, $intStepId)
    {
        $wfHistory = Workflow::decode($wfHistory);
        foreach ($wfHistory as $wf) {
            if ($wf['step_id'] == $intStepId) {
                return $wf;
            }
        }
        return null;
    }

    public static function getLastStepId(array $arrWfHistory)
    {
        $arrStep = Workflow::getLastStep($arrWfHistory);
        if (is_array($arrStep) && array_key_exists("step_id", $arrStep)) {
            return $arrStep["step_id"];
        } else {
            return null;
        }
    }

    public static function getLastStep($mixWorkflowHistory)
    {
        $mixWorkflowHistory = Workflow::decode($mixWorkflowHistory);

        if (is_array($mixWorkflowHistory) && count($mixWorkflowHistory) > 0) {
            return $mixWorkflowHistory[count($mixWorkflowHistory) - 1];
        } else {
            return null;
        }
    }

    public static function getLastUserId(array $arrWfHistory)
    {
        $arrStep = Workflow::getLastStep($arrWfHistory);
        if (is_array($arrStep) && array_key_exists("user", $arrStep) && array_key_exists("id", $arrStep["user"])) {
            return $arrStep["user"]["id"];
        } else {
            return null;
        }
    }

    /**
     * Default "to string" handler
     * Allows pages to _p()/echo()/print() this object, and to define the default
     * way this object would be outputted.
     *
     * Can also be called directly via $objWorkflow->__toString().
     *
     * @return string a nicely formatted string representation of this object
     */
    public function __toString()
    {
//			return sprintf('Workflow Object  %s',  $this->intId);
        return sprintf('%s', $this->strName);
    }

    // Override or Create New Load/Count methods
    // (For obvious reasons, these methods are commented out...
    // but feel free to use these as a starting point)
    /*
            public static function LoadArrayBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return an array of Workflow objects
                return Workflow::QueryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::Workflow()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::Workflow()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }

            public static function LoadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a single Workflow object
                return Workflow::QuerySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::Workflow()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::Workflow()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }

            public static function CountBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a count of Workflow objects
                return Workflow::QueryCount(
                    QQ::AndCondition(
                        QQ::Equal(QQN::Workflow()->Param1, $strParam1),
                        QQ::Equal(QQN::Workflow()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }

            public static function LoadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
                // Performing the load manually (instead of using QCubed Query)

                // Get the Database Object for this Class
                $objDatabase = Workflow::GetDatabase();

                // Properly Escape All Input Parameters using Database->SqlVariable()
                $strParam1 = $objDatabase->SqlVariable($strParam1);
                $intParam2 = $objDatabase->SqlVariable($intParam2);

                // Setup the SQL Query
                $strQuery = sprintf('
                    SELECT
                        `workflow`.*
                    FROM
                        `workflow` AS `workflow`
                    WHERE
                        param_1 = %s AND
                        param_2 < %s',
                    $strParam1, $intParam2);

                // Perform the Query and Instantiate the Result
                $objDbResult = $objDatabase->Query($strQuery);
                return Workflow::InstantiateDbResult($objDbResult);
            }
    */


    // Override or Create New Properties and Variables
    // For performance reasons, these variables and __set and __get override methods
    // are commented out.  But if you wish to implement or override any
    // of the data base properties, please feel free to uncomment them.
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