<?php

/**
 * Description of QError
 *
 * @author ratno
 */
class QError
{
    const unknown_error = 1000;
    const username_invalid = 1001;
    const password_invalid = 1002;
    const account_inactive = 1003;
    const login_disallow = 1004;
    const signature_invalid = 1005;
    const consumer_key_invalid = 1006;
    const access_token_invalid = 1007;
    const access_token_expired = 1008;
    const username_required = 1009;
    const password_required = 1010;
    const consumer_key_required = 1011;
    const signature_method_required = 1012;
    const signature_required = 1013;
    const timestamp_required = 1014;
    const username_password_required = 1015;
    const username_password_consumer_key_required = 1016;
    const unprivilege = 1017;
    const access_token_required = 1018;
    const gcmid_save_failed = 1019;
    const data_not_found = 1020;
    const data_deletion_failed = 1021;
    const data_save_failed = 1022;
    const timestamp_invalid = 1023;
    const api_invalid = 1024;
    const no_data = 1025;
    const api_not_found = 1026;

    public static function ToString($intErrorId)
    {
        $out = "";
        switch ($intErrorId) {
            case QError::unknown_error:
                $out = "Unknown Error";
                break;
            case QError::username_invalid:
                $out = "Username tidak ditemukan";
                break;
            case QError::password_invalid:
                $out = "Password salah";
                break;
            case QError::account_inactive:
                $out = "Akun Anda Tidak Aktif";
                break;
            case QError::login_disallow:
                $out = "Anda Tidak Diperbolehkan Login";
                break;
            case QError::signature_invalid:
                $out = "Invalid Signature";
                break;
            case QError::consumer_key_invalid:
                $out = "Invalid Consumer Key";
                break;
            case QError::access_token_invalid:
                $out = "Invalid Access Token";
                break;
            case QError::access_token_expired:
                $out = "Access Token Expired";
                break;
            case QError::username_required:
                $out = "Username harus disertakan";
                break;
            case QError::password_required:
                $out = "Password harus disertakan";
                break;
            case QError::consumer_key_required:
                $out = "Consumer Key harus disertakan";
                break;
            case QError::signature_method_required:
                $out = "Signature Method harus disertakan";
                break;
            case QError::signature_required:
                $out = "Signature harus disertakan";
                break;
            case QError::timestamp_required:
                $out = "Timestamp harus disertakan";
                break;
            case QError::username_password_required:
                $out = "Username dan Password harus disertakan";
                break;
            case QError::username_password_consumer_key_required:
                $out = "Username, Password, dan Consumer Key harus disertakan";
                break;
            case QError::unprivilege:
                $out = "Anda tidak memiliki privilege";
                break;
            case QError::access_token_required:
                $out = "Token harus disertakan";
                break;
            case QError::gcmid_save_failed:
                $out = "GCMID gagal disimpan";
                break;
            case QError::data_not_found:
                $out = "Data tidak ditemukan atau tidak ada data";
                break;
            case QError::data_deletion_failed:
                $out = "Data gagal dihapus";
                break;
            case QError::data_save_failed:
                $out = "Data gagal disimpan";
                break;
            case QError::timestamp_invalid:
                $out = "Invalid Timestamp";
                break;
            case QError::api_invalid:
                $out = "Invalid API";
                break;
            case QError::no_data:
                $out = "No Data";
                break;
            case QError::api_not_found:
                $out = "API is registered but not found";
                break;
        }

        return $out . " (error code: " . $intErrorId . ")";
    }
}