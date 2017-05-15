<?php

/**
 * Description of Icon
 *
 * @author ratno
 */
abstract class Icon
{

    const view = 1;
    const edit = 2;
    const delete = 3;
    const accept = 4;
    const add = 5;
    const tick = 6;
    const cross = 7;
    const attach = 8;
    const mailattach = 9;
    const docattach = 10;
    const download = 11;
    const assign = 12;
    const comment = 13;
    const export = 14;
    const import = 15;
    const listdata = 16;
    const child = 17;

    public static $view = Icon::view;
    public static $edit = Icon::edit;
    public static $delete = Icon::delete;
    public static $accept = Icon::accept;
    public static $add = Icon::add;
    public static $new = Icon::add;
    public static $tick = Icon::tick;
    public static $cross = Icon::cross;
    public static $attach = Icon::attach;
    public static $mailattach = Icon::mailattach;
    public static $docattach = Icon::docattach;
    public static $download = Icon::download;
    public static $assign = Icon::assign;
    public static $comment = Icon::comment;
    public static $export = Icon::export;
    public static $import = Icon::import;
    public static $listdata = Icon::listdata;
    public static $list = Icon::listdata;
    public static $child = Icon::child;

    public static function has($strIconName)
    {
        if (property_exists(Icon::class, $strIconName)) {
            return Icon::$$strIconName;
        } else {
            return false;
        }
    }

    public static function ToImage($intIconId)
    {
        switch ($intIconId) {
            case Icon::view:
                return '/icons/glyphicons_236_zoom_in.png';
            case icon::edit:
                return '/icons/glyphicons_150_edit.png';
            case icon::delete:
                return '/icons/glyphicons_192_circle_remove.png';
            case icon::accept:
                return '/accept.png';
            case icon::add:
                return '/icons/glyphicons_190_circle_plus.png';
            case icon::tick:
                return '/tick.png';
            case icon::cross:
                return '/cross.png';
            case icon::attach:
                return '/icons/glyphicons_062_paperclip.png';
            case icon::mailattach:
                return '/email_attach.png';
            case icon::docattach:
                return '/page_attach.png';
            case icon::download:
                return '/attach.png';
            case icon::assign:
                return '/icons/glyphicons_151_new_window.png';
            case icon::comment:
                return '/comments.png';
            case icon::import:
                return '/icons/glyphicons_218_circle_arrow_top.png';
            case icon::export:
                return '/icons/glyphicons_219_circle_arrow_down.png';
            case icon::listdata:
                return '/icons/glyphicons_114_list.png';
            case icon::child:
                return 'category_16.png';

            default:
                return null;
//        throw new QCallerException(sprintf('Invalid intIconId: %s', $intIconId));
                break;
        }
    }

    public static function ToTitle($intIconId)
    {
        switch ($intIconId) {
            case Icon::view:
                return 'Lihat Detail';
            case icon::edit:
                return 'Ubah';
            case icon::delete:
                return 'Hapus';
            case icon::attach:
                return 'Tambah Attachment';
            case icon::download:
                return 'Download';
            case icon::assign:
                return 'Assign';
            case icon::comment:
                return 'Komentar';
            case icon::add:
                return 'Tambah';
            case icon::import:
                return 'Import';
            case icon::export:
                return 'Export';
            case icon::listdata:
                return 'Daftar';
            case icon::child:
                return 'Tambah Anak/Sub';

            default:
                return null;
//        throw new QCallerException(sprintf('Invalid intIconId: %s', $intIconId));
                break;
        }
    }
}