<?php
$token = QR::Param("token");
if ($token) {
    $check_result = UserToken::Check($token);
    if ($check_result['status'] == "ok") {
        // update token
        $objToken = $check_result['token'];
        $objToken->LogoutTypeId = LogoutType::Manually;
        $objToken->IsActive = false;
        $objToken->TsLogout = QDateTime::Now();
        $objToken->Save();
        // update user
        $objToken->Users->IsOnline = false;
        $objToken->Users->Save();
        QApplication::Logout();
        json(array("status" => "ok", "msg" => "Anda telah logout."));
    } else {
        json($check_result);
    }
} else {
    $objUser = Users::Load(QApplication::GetUser()->Id);
    if ($objUser instanceof Users) {
        $objUser->IsOnline = false;
        $objUser->Save();
    }
    QApplication::Logout();
    QApplication::Redirect(__WEB_URL__);
    die();
}