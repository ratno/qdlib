<?php
require(__BASEPATH__ . '/apps/formbases/PrivateMessageViewFormBase.class.php');

/**
 * @author Ratno Putro Sulistiyono, ratno@knoqdown.com
 * @company KnoQDown Studio
 *
 * @package Sistem Informasi
 * @subpackage ViewDetail
 */
class MessageView extends PrivateMessageViewFormBase
{
    // Override Form Event Handlers as Needed
//		protected function Form_Run() {}

//		protected function Form_Load() {}

    public static function exec()
    {
        return MessageView::Run('MessageView', __BASEPATH__ . '/apps/views/Message/message_view.tpl.php');
    }

    protected function Form_Create()
    {
        // general parameter
        $this->TaskClassName = 'Message';
        $this->TaskActionName = 'View';
        $this->CustomTitle = "Detail Data " . ucwords(str_replace('_', ' ', 'private_message'));
        $this->GlobalLayout = "backend";
        $this->objDefaultWaitIcon = new QWaitIcon($this);
        $this->mctPrivateMessage = PrivateMessageMetaControl::CreateFromPathInfo($this);
        // call parent form create
        parent::Form_Create();

        $this->lblReadTs->Visible = false;
        $this->lblParentId->Visible = false;

        $strBackUrl = $_SESSION['BackUrl'];
        $inout = substr($strBackUrl, strrpos($strBackUrl, "/"));
        $this->btnSave->Text = "Balas";
        if (strtolower($inout) == '/inbox') {
            $this->btnSave->Visible = true;
        } else {
            $this->btnSave->Visible = false;
        }
        $this->btnCancel->Text = "Kembali";

        // Record Activity
        $this->objActivityLog->Controller = __CLASS__;
        $this->objActivityLog->Action = ActivityAction::ViewDetail;
        $this->objActivityLog->SubjectModel = "PrivateMessage";
        $this->objActivityLog->SubjectIdNumber = $this->mctPrivateMessage->PrivateMessage->Id;
        $this->objActivityLog->ObjectModel = "PrivateMessage";
        $this->objActivityLog->ObjectIdNumber = $this->mctPrivateMessage->PrivateMessage->Id;
        $this->objActivityLog->FilenameLogger = substr(__FILE__, strlen(__BASEPATH__));
        $this->SaveActivityLog();
    }

    protected function btnSave_Click($strFormId, $strControlId, $strParameter)
    {
        $_SESSION['BalasPesan'] = array(
            'id' => $this->mctPrivateMessage->PrivateMessage->Id,
            'subject' => $this->mctPrivateMessage->PrivateMessage->Subject,
            'user_id' => $this->mctPrivateMessage->PrivateMessage->FromUserId
        );
        QApplication::Redirect(qd_url($this->TaskClassName, 'new'));
    }

    protected function RedirectToListPage()
    {
        QApplication::Redirect($_SESSION['BackUrl']);
    }
}

?>