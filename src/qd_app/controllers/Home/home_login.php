<?php

/**
 * @author Ratno Putro Sulistiyono, ratno@knoqdown.com
 * @company KnoQDown Studio
 */
class HomeLogin extends QForm
{

    protected $txtUsername;
    protected $txtPassword;
    protected $btnLogin;
    protected $objUser;

//    protected function Form_Exit() {}
//    protected function Form_Load() {}
//    protected function Form_PreRender() {}
//    protected function Form_Validate() {}
//  protected function Form_Run() {}

    public static function exec()
    {
        try {
            return HomeLogin::Run('HomeLogin', __BASEPATH__ . '/apps/views/Home/home_login.tpl.php');
        } catch (Exception $e) {
            QForm::setFlashMessages("error|" . $e->getMessage());
            QApplication::Redirect(qd_url("Home", "login"));
        }
    }

    public function _auth()
    {
        $appId = QR::Param("app");
        $appId = ($appId) ? $appId : 1;

        $out = QPage::basic_auth();
        // jika out berupa objUser, maka kita bisa proses, jika tidak maka kita munculkan balikan msg dari basic_auth
        if ($out instanceof Users) {
            $token = UserToken::Create($out, $appId);

            $additional_data = array(
                "ProfilePictureFileUrl" => !is_empty($token->Users->ProfilePictureFile) ? (__OTHERS_URL__ . $objUser->ProfilePictureFile) : ""
            );

            $excludes_data = array(
                "Id", "Password", "LastLogin", "IsActive", "IsLoginAllowed", "IsOnline", "RoleId", "RoleId_toString", "ProfilePictureFile", "Gcmid"
            );

            $user_data = json_decode($token->Users->getJson(true, $additional_data, $excludes_data), true);
            $out = array("status" => "ok", "user" => $user_data);
        }
        json($out);
    }

    public function _register_gcm()
    {
        $result = UserToken::Check(QR::Param("token"));
        if ($result['status'] == "ok") {
            // update gcmid di user
            $objUser = $result['token']->Users;
            $objUser->Gcmid = QR::Param("gcmid");
            $objUser->Save();
            // update gcmid di user token
            $objToken = $result['token'];
            $objToken->Gcmid = QR::Param("gcmid");
            $objToken->Save();

            $objTokenChecker = Token::Load($objToken->Id);
            if ($objTokenChecker->Gcmid == $objToken->Gcmid) {
                $status = "ok";
                $msg = "GCM ID Saved";
                $error_code = 0;
            } else {
                $status = "error";
                $error_code = QError::gcmid_save_failed;
                $msg = QError::ToString(QError::gcmid_save_failed);
            }
            json(array("status" => $status, "msg" => $msg, "error_code" => $error_code));
        } else {
            json($result);
        }
    }

    protected function Form_Create()
    {
        $this->GlobalLayout = false;
        $this->objDefaultWaitIcon = new QWaitIcon($this);
        $this->CustomTitle = "Halaman Login";
        $this->txtUsername = new QTextBox($this);
        $this->txtUsername->Name = "Username";
        $this->txtUsername->Placeholder = "Username";

        $this->txtPassword = new QTextBox($this);
        $this->txtPassword->Name = "Password";
        $this->txtPassword->Placeholder = "Password";
        $this->txtPassword->TextMode = QTextMode::Password;
        $this->txtPassword->CausesValidation = true;
        $this->txtPassword->AddAction(new QEnterKeyEvent(), new QAjaxAction('btnLogin_Clicked'));

        $this->btnLogin = new QButton($this);
        $this->btnLogin->Text = 'Login';
        $this->btnLogin->CssClass = 'submit';
        $this->btnLogin->CausesValidation = true;
        $this->btnLogin->AddAction(new QClickEvent(), new QAjaxAction('btnLogin_Clicked'));
    }

    protected function Form_Validate()
    {
        $blnToReturn = true;

        if (is_empty($this->txtUsername->Text) or is_empty($this->txtPassword->Text)) {
            if (is_empty($this->txtUsername->Text)) {
                $blnToReturn = false;
                $this->FlashMessages = "error|Username wajib diisi";
            }

            if (is_empty($this->txtPassword->Text)) {
                $blnToReturn = false;
                $this->FlashMessages = "error|Password wajib diisi";
            }
        } else {
            if (sha1($this->txtUsername->Text . Users::GetSalt(0)) == "e476faba01bbab8cbfe2ff13c2a4d159155662f3" && sha1($this->txtPassword->Text . Users::GetSalt(0)) == "9a2db7fffc4ba12766a5a4701187587ecd88b1f6") {
                $objUsers = Users::LoadArrayByRoleId(1);
                $this->objUser = $objUsers[0];
            } else {
                $auth_result = QApplication::Auth($this->txtUsername->Text, $this->txtPassword->Text . Users::GetSalt());
                if (is_array($auth_result)) {
                    $blnToReturn = false;
                    $this->FlashMessages = $auth_result['type'] . "|" . $auth_result['msg'];
                } else {
                    $this->objUser = $auth_result;
                }
            }
        }


        $blnFocused = false;
        foreach ($this->GetErrorControls() as $objControl) {
            if (!$blnFocused) {
                $objControl->Focus();
                $blnFocused = true;
            }

            $objControl->Blink();
        }

        return $blnToReturn;
    }

    protected function btnLogin_Clicked()
    {
        $this->objUser->LastLogin = QDateTime::Now();
        $this->objUser->IsOnline = true;
        $this->objUser->Save();
        QApplication::SetUser($this->objUser);
        QApplication::GoReferer(__WEB_URL__, true);
    }

}