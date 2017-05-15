<?php

require(__BASEPATH__ . '/app/qd/formbases/PrivateMessageListFormBase.class.php');

/**
 * @author Ratno Putro Sulistiyono, ratno@knoqdown.com
 * @company KnoQDown Studio
 *
 * @package Sistem Informasi
 * @subpackage List
 */
class MessageOutbox extends PrivateMessageListFormBase
{
    // Override Form Event Handlers as Needed
//		protected function Form_Run() {}

//		protected function Form_Load() {}

    public static function exec()
    {
        return MessageOutbox::Run('MessageOutbox', __BASEPATH__ . '/app/qd/views/Message/message_list.tpl.php');
    }

    public function colActionRender()
    {
        $strColumnTitle = "Aksi";
        $arrParameter = array('view' => "View");
        $strHtml = "";
        if ($arrParameter && is_array($arrParameter)) {
            $strHtml = '<div class="datagrid_actions">';
            foreach ($arrParameter as $key => $value) {
                $strPageUrl = qd_url($this->TaskClassName, $key, '<?= rawurlencode($_ITEM->Id); ?>');
                $strHtml .= href($strPageUrl, img(Icon::ToImage(Icon::$$key), array('width' => 20, 'title' => Icon::ToTitle(Icon::$$key))));
            }
            $strHtml .= "</div>";
            $col = new QDataGridColumn($strColumnTitle, $strHtml, 'HtmlEntities=False');
            $this->dtgPrivateMessages->AddColumn($col);
            return $col;
        }
    }

    public function colFile_Render($obj, $var)
    {
        if ($obj->$var) {
            return href(__OTHERS_URL__ . "/" . $obj->$var, img(Icon::ToImage(Icon::download)), array("title" => Icon::ToTitle(Icon::download)));
        }
    }

    protected function Form_Create()
    {
        // general parameter
        $this->TaskClassName = 'Message';
        $this->TaskActionName = 'Outbox';
        $this->CustomTitle = "Daftar Pesan Keluar";
        $this->GlobalLayout = "backend";
        $this->objDefaultWaitIcon = new QWaitIcon($this);
        $this->arrColumns = array(
            'ToUserId', // reference
            'SendTs',
            'Subject',
        );

        // Instantiate the Meta Control Filter
        $this->mctPrivateMessage = PrivateMessageMetaControl::Create($this);
        // Instantiate the Meta DataGrid
        $this->dtgPrivateMessages = new PrivateMessageDataGrid($this);
        $this->dtgPrivateMessages->Title = $this->CustomTitle;
        // call parent form create
        parent::Form_Create();
        $this->dtgPrivateMessages->AdditionalConditions = QQ::Equal(QQN::PrivateMessage()->FromUserId, $this->User->Id);
        if (array_key_exists('BalasPesan', $_SESSION)) unset ($_SESSION['BalasPesan']);
        $_SESSION['BackUrl'] = qd_url($this->TaskClassName, $this->TaskActionName);
    }
}

?>