<?php

/**
 * Description of ActivityAction
 *
 * @author ratno
 */
abstract class ActivityAction
{

    const Add = 1;
    const Edit = 2;
    const Delete = 3;
    const ViewList = 4;
    const ViewDetail = 5;
    const Login = 6;
    const Logout = 7;
    const Comment = 8;
    const Attachment = 9;
    const Accept = 10;
    const Import = 11;
    const Export = 12;
    const Mention = 13;

    public static $Add = ActivityAction::Add;
    public static $Edit = ActivityAction::Edit;
    public static $Delete = ActivityAction::Delete;
    public static $ViewList = ActivityAction::ViewList;
    public static $ViewDetail = ActivityAction::ViewDetail;
    public static $Login = ActivityAction::Login;
    public static $Logout = ActivityAction::Logout;
    public static $Comment = ActivityAction::Comment;
    public static $Attachment = ActivityAction::Attachment;
    public static $Accept = ActivityAction::Accept;
    public static $Import = ActivityAction::Import;
    public static $Export = ActivityAction::Export;
    public static $Mention = ActivityAction::Mention;

    public static function ToString($intActivityActionId)
    {
        switch ($intActivityActionId) {
            case ActivityAction::Add:
                return "menambahkan %s";
            case ActivityAction::Edit:
                return "mengubah %s";
            case ActivityAction::Delete:
                return "menghapus %s";
            case ActivityAction::ViewList:
                return "melihat daftar %s";
            case ActivityAction::ViewDetail:
                return "melihat detail %s";
            case ActivityAction::Login:
                return "login";
            case ActivityAction::Logout:
                return "logout";
            case ActivityAction::Comment:
                return "memberikan komentar pada %s";
            case ActivityAction::Attachment:
                return "menambahkan attachment file";
            case ActivityAction::Accept:
                return "menerima";
            case ActivityAction::Import:
                return "mengimpor";
            case ActivityAction::Export:
                return "mengekspor";
            case ActivityAction::Mention:
                return "menyebut %s dalam komentarnya";
            default:
                throw new QCallerException(sprintf('Invalid intActivityActionId: %s', $intActivityActionId));
        }
    }

    public static function ToIcon($intActivityActionId)
    {
        switch ($intActivityActionId) {
            case ActivityAction::Add:
                return Icon::ToImage(Icon::add);
            case ActivityAction::Edit:
                return Icon::ToImage(Icon::edit);
            case ActivityAction::Delete:
                return Icon::ToImage(Icon::delete);
            case ActivityAction::ViewDetail:
                return Icon::ToImage(Icon::view);
            case ActivityAction::Comment:
                return Icon::ToImage(Icon::comment);
            case ActivityAction::Attachment:
                return Icon::ToImage(Icon::attach);
            default:
                throw new QCallerException(sprintf('Invalid intActivityActionId: %s', $intActivityActionId));
        }
    }
}