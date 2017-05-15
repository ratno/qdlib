<?php
require(__BASEPATH__ . '/app/qd/meta_controls/base/PrivateMessageMetaControlBase.class.php');

/**
 * /**
 * @author Ratno Putro Sulistiyono, ratno@knoqdown.com
 * @company KnoQDown Studio
 *
 * @package Sistem Informasi
 * @subpackage MetaControls
 */
class PrivateMessageMetaControl extends PrivateMessageMetaControlBase
{
    // Initialize fields with default values from database definition
    /**
     * Create and setup QListBox lstFromUser
     * @param string $strControlId optional ControlId to use
     * @return QListBox
     */
    public function lstFromUser_Create($intOnlyFromUserId = null, $strControlId = null)
    {
        $this->lstFromUser = new QListBox($this->objParentObject, $strControlId);
        $this->lstFromUser->Name = QApplication::Translate('From Users');
        $this->lstFromUser->AddItem(QApplication::Translate('- Select One -'), null);
        if (is_null($intOnlyFromUserId)) {
            $objFromUserArray = Users::LoadAll();
            if ($objFromUserArray) foreach ($objFromUserArray as $objFromUser) {
                $objListItem = new QListItem($objFromUser->__toString(), $objFromUser->Id);
                if (($this->objPrivateMessage->FromUser) && ($this->objPrivateMessage->FromUser->Id == $objFromUser->Id))
                    $objListItem->Selected = true;
                $this->lstFromUser->AddItem($objListItem);
            }
        } else {
            $objFromUser = Users::Load($intOnlyFromUserId);
            $objListItem = new QListItem($objFromUser->__toString(), $objFromUser->Id);
            $this->lstFromUser->AddItem($objListItem);
        }
        return $this->lstFromUser;
    }

    /**
     * Create and setup QListBox lstToUser
     * @param string $strControlId optional ControlId to use
     * @return QListBox
     */
    public function lstToUser_Create($intSkipId = null, $strControlId = null)
    {
        $this->lstToUser = new QListBox($this->objParentObject, $strControlId);
        $this->lstToUser->Name = QApplication::Translate('To Users');
        $this->lstToUser->AddItem(QApplication::Translate('- Select One -'), null);
        $objToUserArray = Users::LoadAll();
        if ($objToUserArray) foreach ($objToUserArray as $objToUser) {
            if (!is_null($intSkipId) && $objToUser->Id == $intSkipId) continue;
            $objListItem = new QListItem($objToUser->__toString(), $objToUser->Id);
            if (($this->objPrivateMessage->ToUser) && ($this->objPrivateMessage->ToUser->Id == $objToUser->Id))
                $objListItem->Selected = true;
            $this->lstToUser->AddItem($objListItem);
        }
        return $this->lstToUser;
    }
    /*
            public function __construct($objParentObject, PrivateMessage $objPrivateMessage) {
                parent::__construct($objParentObject,$objPrivateMessage);
                if ( !$this->blnEditMode ){
                    $this->objPrivateMessage->Initialize();
                }
            }
    */

}

?>