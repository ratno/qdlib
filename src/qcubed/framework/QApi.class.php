<?php

/**
 * Description of QApi
 *
 * @author Ratno Putro Sulistiyono (ratno@knoqdown.com)
 * @property User $CurrentUser current logged in user
 * @property UserToken $CurrentUserToken current logged in user token
 */
class QApi extends QBaseClass
{
    protected $paramQuery;
    protected $paramStartId;
    protected $paramEndId;
    protected $paramInId;
    protected $paramNumberOfData;
    protected $paramPageNumber;
    protected $paramKeyword;
    protected $arrConditions;
    protected $objConditions;
    protected $objClauses;
    protected $arrIncludeItemData;
    protected $arrExcludeItemData;
    protected $arrOutput;
    protected $arrParamDocumentations;
    protected $objCurrentUser;
    protected $objCurrentUserToken;

    public function __construct()
    {
        $this->paramQuery = QR::Param("q");
        $this->paramStartId = QR::Param("sid");
        $this->paramEndId = QR::Param("eid");
        $this->paramInId = QR::Param("inid");
        $this->paramNumberOfData = QR::Param("num");
        $this->paramPageNumber = QR::Param("page");
        $this->paramKeyword = QR::Param("keyword");

        $this->arrConditions = array(
            "primary" => array(),
            "unique" => array(),
            "ref" => array(),
            "string" => array(),
            "keyword" => array(),
            "boolean" => array(),
            "tgl" => array(),
            "others" => array()
        );

        $this->PrepConditions();
    }

    protected function PrepConditions()
    {
    }

    public function RespondDocumentation()
    {
    }

    public function ParamDocumentation()
    {
    }

    public function GetJson()
    {
        $this->Process();
        json($this->arrOutput);
    }

    protected function Process()
    {
    }

    public function Execute()
    {
    }

    public function __get($strName)
    {
        switch ($strName) {
            case 'CurrentUser':
                return $this->objCurrentUser;
            case 'CurrentUserToken':
                return $this->objCurrentUserToken;

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
            case 'CurrentUser':
                try {
                    return ($this->objCurrentUser = QType::Cast($mixValue, 'Users'));
                } catch (QInvalidCastException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case 'CurrentUserToken':
                try {
                    return ($this->objCurrentUserToken = QType::Cast($mixValue, 'UserToken'));
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

}
