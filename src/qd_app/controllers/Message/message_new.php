<?php
require(__BASEPATH__ . '/app/qd/formbases/PrivateMessageEditFormBase.php');

/**
 * @author Ratno Putro Sulistiyono, ratno@knoqdown.com
 * @company KnoQDown Studio
 *
 * @package Sistem Informasi
 * @subpackage NewForm
 */
class MessageNew extends PrivateMessageEditFormBase
{
    // Override Form Event Handlers as Needed
//		protected function Form_Run() {}

//		protected function Form_Load() {}

    public static function exec()
    {
        return MessageNew::Run('MessageNew', __BASEPATH__ . '/app/qd/views/Message/message_new.tpl.php');
    }

    public function btnSave_Click($strFormId, $strControlId, $strParameter)
    {
        // Delegate "Save" processing to the PrivateMessageMetaControl
        $this->mctPrivateMessage->SavePrivateMessage();

        // Record Activity
        $this->objActivityLog->SubjectIdNumber = $this->mctPrivateMessage->PrivateMessage->Id;
        $this->objActivityLog->ObjectIdNumber = $this->mctPrivateMessage->PrivateMessage->Id;
        $this->SaveActivityLog();

        // redirecting
        $this->RedirectToListPage();
    }

    protected function RedirectToListPage()
    {
        QApplication::Redirect($_SESSION['BackUrl']);
    }

    protected function Form_Create()
    {
        // general parameter
        $this->TaskClassName = 'Message';
        $this->TaskActionName = 'New';
        $this->CustomTitle = "Buat Pesan Pribadi";
        $this->GlobalLayout = "backend";
        $this->objDefaultWaitIcon = new QWaitIcon($this);
        // Use the CreateFromPathInfo shortcut (this can also be done manually using the PrivateMessageMetaControl constructor)
        // MAKE SURE we specify "$this" as the MetaControl's (and thus all subsequent controls') parent
        $this->mctPrivateMessage = PrivateMessageMetaControl::CreateFromPathInfo($this);
        $this->lstToUser = $this->mctPrivateMessage->lstToUser_Create($this->User->Id);
        $this->lstFromUser = $this->mctPrivateMessage->lstFromUser_Create($this->User->Id);
        // call parent form create
        parent::Form_Create();
        if (array_key_exists('BalasPesan', $_SESSION)) {
            $intPesanId = $_SESSION['BalasPesan']['id'];
            $strSubject = $_SESSION['BalasPesan']['subject'];
            $intFromUserId = $_SESSION['BalasPesan']['user_id'];
            $this->txtSubject->Text = "Balas: " . $strSubject;
            $this->lstParent->SelectedValue = $intPesanId;
            $this->lstToUser->SelectedValue = $intFromUserId;
        }
        $this->lstFromUser->SelectedValue = $this->User->Id;
        $this->lstFromUser->Visible = false;
        $this->calSendTs->Visible = false;
        $this->calReadTs->Visible = false;
        $this->lstParent->Visible = false;

        // Record Activity
        $this->objActivityLog->Controller = __CLASS__;
        $this->objActivityLog->Action = ActivityAction::Add;
        $this->objActivityLog->SubjectModel = "PrivateMessage";
        $this->objActivityLog->ObjectModel = "PrivateMessage";
        $this->objActivityLog->FilenameLogger = substr(__FILE__, strlen(__BASEPATH__));
    }
}

?>