<?php

require(__BASEPATH__ . '/app/qd/formbases/UsersEditFormBase.class.php');

/**
 * @author Ratno Putro Sulistiyono, ratno@knoqdown.com
 * @company KnoQDown Studio
 *
 * @package My QD Apps
 * @subpackage Drafts
 */
class HomePreferences extends UsersEditFormBase
{

    // Override Form Event Handlers as Needed
//    protected function Form_Run() {}
//    protected function Form_Load() {}

    protected $lblName;
    protected $lblUsername;
    protected $txtNewPassword;
    protected $txtNewPasswordConfirm;
    protected $objUser;

    public static function exec()
    {
        try {
            return HomePreferences::Run('HomePreferences', __BASEPATH__ . '/app/qd/views/Home/home_preferences.tpl.php');
        } catch (Exception $e) {
            QForm::setFlashMessages("error|" . $e->getMessage());
            QApplication::Redirect(qd_url("Home", "cpanel"));
        }
    }

    protected function Form_Create()
    {
        $this->GlobalLayout = "backend";
        $this->objDefaultWaitIcon = new QWaitIcon($this);
        $this->objUser = QApplication::GetUser();
        $this->mctUsers = UsersMetaControl::Create($this, $this->objUser->Id);
        $this->lblId = $this->mctUsers->lblId_Create();
        $this->lblName = $this->mctUsers->lblName_Create();
        $this->lblUsername = $this->mctUsers->lblUsername_Create();
        $this->txtPassword = $this->mctUsers->txtPassword_Create();
        $this->txtNewPassword = $this->mctUsers->txtPassword_Create();
        $this->txtNewPasswordConfirm = $this->mctUsers->txtPassword_Create();
        $this->txtPassword->Name = "Password Lama";
        $this->txtPassword->Text = "";
        $this->txtNewPassword->Name = "Password Baru";
        $this->txtNewPassword->Text = "";
        $this->txtNewPasswordConfirm->Name = "Ketik Ulang Password Baru";
        $this->txtNewPasswordConfirm->Text = "";

        $this->txtPassword->TextMode = QTextMode::Password;
        $this->txtNewPassword->TextMode = QTextMode::Password;
        $this->txtNewPasswordConfirm->TextMode = QTextMode::Password;

        // Create Buttons and Actions on this Form
        $this->btnSave = new QButton($this);
        $this->btnSave->Text = QApplication::Translate('Save');
        $this->btnSave->AddAction(new QClickEvent(), new QAjaxAction('btnSave_Click'));
//    $this->btnSave->CausesValidation = true;

        $this->btnCancel = new QButton($this);
        $this->btnCancel->Text = QApplication::Translate('Cancel');
        $this->btnCancel->AddAction(new QClickEvent(), new QAjaxAction('btnCancel_Click'));

        $this->CustomTitle = "Ubah Password";
    }

    protected function btnSave_Click($strFormId, $strControlId, $strParameter)
    {
        $blnSave = false;
        if (sha1($this->txtPassword->Text . Users::GetSalt()) == $this->objUser->Password) {
            // password lama benar, berarti bisa update password
            if ($this->txtNewPassword->Text == $this->txtNewPasswordConfirm->Text) {
                $this->objUser->Password = trim($this->txtNewPassword->Text);
                $blnSave = true;
            } else {
                $this->txtNewPassword->Warning = "Password baru tidak match";
                $this->txtNewPasswordConfirm->Warning = "Password baru tidak match";
            }
        } else {
            $this->txtPassword->Warning = "Password Lama Anda Keliru!";
        }

        if ($blnSave) {
            $this->objUser->Save();
            QApplication::SetUser($this->objUser);
            $this->FlashMessages = "Password anda telah diupdate!";
        } else {
            $this->FlashMessages = "error|Password gagal diupdate!";
        }
    }

    protected function RedirectToListPage()
    {
        QApplication::Redirect(__WEB_URL__);
    }

}

?>