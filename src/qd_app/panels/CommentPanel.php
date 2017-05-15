<?php

class CommentPanel extends QPanel
{

    public $pnlListComment;
    public $pnlAttachment;
    public $pnlHistory;
    public $txtAddComment;
    public $intAttachmentNumber = 2;
    public $flcAttachmentArray = array();
    public $btnSubmit;
    public $intCounter = array();
    public $AutocompleteConditions = null;
    public $AdditionalActivityLogForComments = null;
    public $ctlTimer;
    protected $strTaskClassName;
    protected $strTaskModelName;
    protected $strTaskActionName;
    protected $intTaskId;
    protected $strTaskFileName;
    protected $intUserId;
    protected $arrParticipants;
    protected $currentcounter = 0;

    public function __construct($objParentObject, $strControlId = null)
    {
        // Call the Parent
        try {
            parent::__construct($objParentObject, $strControlId);
        } catch (QCallerException $objExc) {
            $objExc->IncrementOffset();
            throw $objExc;
        }

        $this->strTaskClassName = $objParentObject->ClassName;
        $this->strTaskModelName = $objParentObject->TaskClassName;
        $this->strTaskActionName = $objParentObject->TaskActionName;
        $this->intTaskId = ($objParentObject->TaskId) ? $objParentObject->TaskId : null;
        $this->strTaskFileName = $objParentObject->ClassFileName;
        $this->intUserId = $objParentObject->User->Id;

        $this->Template = dirname(__FILE__) . "/CommentPanel.tpl.php";

        // comment itu ada dua bagian,
        // pertama: bagian komentar-komentar
        // kedua: bagian menambahkan komentar
        $this->pnlListComment = new QPanel($this, "pnlListComment");
        $this->pnlListComment->AutoRenderChildren = true;
        $this->pnlListComment->Height = "400px";
        $this->pnlListComment->Overflow = QOverflow::Scroll;
        $this->pnlListComment->Padding = "5px 0px 10px 0px";

        $this->pnlAttachment = new QPanel($this, "pnlAttachment");
        $this->pnlAttachment->AutoRenderChildren = true;
        $this->pnlHistory = new QPanel($this, "pnlHistory");
        $this->pnlHistory->AutoRenderChildren = true;

        $this->LoadComment();

        $this->txtAddComment = new QTinyMCE($this);
        $this->txtAddComment->Rows = 10;
        $this->txtAddComment->Width = "100%";
        if (is_null($this->AutocompleteConditions)) {
            $this->AutocompleteConditions = QQ::All();
        }
        $users = Users::QueryArray($this->AutocompleteConditions);
        if ($users) {
            foreach ($users as $objUser) {
                if ($objUser->Id == $this->intUserId) continue;
                $this->txtAddComment->addAutoCompleteData($objUser->__toString(), $objUser->Id);
            }
        }

        for ($i = 0; $i < $this->intAttachmentNumber; $i++) {
            $this->flcAttachmentArray[$i] = new QFileControl($this);
        }

        $this->btnSubmit = new QButton($this);
        $this->btnSubmit->Text = "Kirim Komentar";
        $this->btnSubmit->AddAction(new QClickEvent(), new QServerControlAction($this, "btnSubmit_Click"));

        $this->ctlTimer = new QJsTimer($this, 10000);
        $this->ctlTimer->AddAction(new QTimerExpiredEvent(), new QAjaxControlAction($this, 'LoadComment'));
//        $this->ctlTimer->AddAction(new QTimerExpiredEvent(), new QJavaScriptAction("var elem = document.getElementById('pnlListComment');elem.scrollTop = elem.scrollHeight;"));
        $this->startTimer();
    }

    public function LoadComment()
    {
        $strClassName = $this->strTaskModelName;

        // activity
        $this->pnlHistory->Text = "";
        $acts = $strClassName::LoadActivities($this->intTaskId);
        $this->intCounter['history'] = 0;
        foreach ($acts as $objActivityLog) {
            $this->intCounter['history']++;
            $this->pnlHistory->Text .= $objActivityLog->__toString() . "<br />";
            // get participants
            $this->arrParticipants[$objActivityLog->UserId] = $objActivityLog->UserId;
            if ($objActivityLog->ForUserId)
                $this->arrParticipants[$objActivityLog->ForUserId] = $objActivityLog->ForUserId;
        }

        // attachment
        $objAttachments = $strClassName::LoadAttachments($this->intTaskId);
        $arrCommentAttachments = array();
        $this->intCounter['attachment'] = 0;
        if ($objAttachments) {
            $this->pnlAttachment->Text = "<ul class='attach_list'>";
            foreach ($objAttachments as $objAttachment) {
                $this->intCounter['attachment']++;
                $pnlAttachmentText = "<li class='attach_item'>";
                $pnlAttachmentText .= href(__OTHERS_URL__ . $objAttachment->AttachmentFile, $objAttachment->AttachmentName);
                $pnlAttachmentText .= "</li>";
                $this->pnlAttachment->Text .= $pnlAttachmentText;
                $arrCommentAttachments[$objAttachment->ParentId][] = $pnlAttachmentText;
            }
            $this->pnlAttachment->Text .= "</ul>";
        }

        // comment
        $arrComments = array();
        $intIndex = 0;
        $objComments = $strClassName::LoadComments($this->intTaskId, $this->AdditionalActivityLogForComments);
        $this->intCounter['comment'] = 0;
        if ($objComments) {
            $strComments = "";
            $intUserId = "";
            $blnSameAsBefore = false;
            $blnDataPertama = true;
            foreach ($objComments as $objComment) {
                $this->intCounter['comment']++;
                if ($strComments == $objComment->CommentContent and $intUserId == $objComment->UserId) {
                    $blnSameAsBefore = true; // jika sama maka data for akan digabungkan jadi satu
                    if ($blnDataPertama) {
                        $intIndex -= 1; // karena sama dengan sebelumnya maka kita kurangi indexnya
                    }
                } else {
                    if ($blnSameAsBefore) {
                        $intIndex++;
                    }
                    $strComments = $objComment->CommentContent;
                    $intUserId = $objComment->UserId;
                    $blnSameAsBefore = false;
                    $blnDataPertama = true;
                }

                // initialize var
                $blnForMe = false;
                $blnFromMe = false;
                $strAttn = "";
                $strFor = "";

                // proses comment attention
                if ($objComment->ForUser) {
                    $strAttn = "Attn";
                    $strFor = $objComment->ForUser;
                    if ($objComment->ForUserId == $this->intUserId)
                        $blnForMe = true;
                }

                // proses apakah comment berasal dari user aktif?
                if ($objComment->UserId == $this->intUserId)
                    $blnFromMe = true;

                $arrComments[$intIndex]['attn'] = $strAttn;
                if ($blnSameAsBefore) {
                    $blnDataPertama = false;
                    if (is_array($arrComments[$intIndex]['for'])) {
                        $arrComments[$intIndex]['for'][] = $strFor;
                    } else {
                        $strForBefore = $arrComments[$intIndex]['for'];
                        $arrComments[$intIndex]['for'] = array($strForBefore, $strFor);
                    }
                    $arrComments[$intIndex]['bln_for_me'] = $blnForMe || $arrComments[$intIndex]['bln_for_me'];
                    $arrComments[$intIndex]['bln_from_me'] = $blnFromMe || $arrComments[$intIndex]['bln_from_me'];
                } else {
                    $arrComments[$intIndex]['for'] = $strFor;
                    $arrComments[$intIndex]['bln_for_me'] = $blnForMe;
                    $arrComments[$intIndex]['bln_from_me'] = $blnFromMe;
                }

                if (strpos($objComment->CommentContent, "MsoNormal")) {
                    $comment_content = "- Maaf, komentar tidak dapat ditampilkan karena data bermasalah -";
                } else {
                    $comment_content = nl2br($objComment->CommentContent);
                }
                $arrComments[$intIndex]['comment'] = $comment_content;
                $arrComments[$intIndex]['from'] = $objComment->User->__toString();
//                $arrComments[$intIndex]['from_img'] = $objComment->User->ProfilePictureFile;
                $arrComments[$intIndex]['ts'] = $objComment->Ts->qFormat("DDDD, DD-MMMM-YYYY hhhh:mm:ss");

                if (array_key_exists($objComment->Id, $arrCommentAttachments)) {
                    $arrComments[$intIndex]['attachments'] = $arrCommentAttachments[$objComment->Id];
                } else {
                    $arrComments[$intIndex]['attachments'] = array();
                }

                if (!$blnSameAsBefore) {
                    $intIndex++;
                }
            }

            // loop through arrComments and set output
            $text = "";
            for ($i = 0; $i < count($arrComments); $i++) {
                $text .= $this->CommentRender($arrComments[$i]);
            }

            if ($this->currentcounter != $this->intCounter['comment']) {
                $this->pnlListComment->Text = "";
                $this->currentcounter = $this->intCounter['comment'];
                $this->pnlListComment->Text .= $text;
                QApplication::ExecuteJavaScript("var elem = document.getElementById('pnlListComment');elem.scrollTop = elem.scrollHeight;");
            }
        }
    }

    protected function CommentRender($item_comment)
    {
        $blnAttn = false;
        if ($item_comment['bln_for_me']) {
            $strAdditionalClass = " forme";
        }
        if ($item_comment['bln_from_me']) {
            $strAdditionalClass = " fromme";
        }

        if ($item_comment['attn'] != "" and $item_comment['for'] != "") {
            $blnAttn = true;
        }

        $strToOut = "<div class='comment_item'>";
        $strToOut .= "<div class='comment_from'>" . $item_comment['from'] . "</div>";
        $strToOut .= "<div class='comment_ts'>[" . $item_comment['ts'] . "]</div>";
        $strToOut .= "<div class='comment_content$strAdditionalClass'>";
        if ($blnAttn) {
            $strToOut .= "<div class='comment_attn'>";
            if (is_array($item_comment['for'])) {
                $strToOut .= $item_comment['attn'] . ": ";
                $strToOut .= "<ul class='disposisi'>";
                foreach ($item_comment['for'] as $item_for_user) {
                    if ($item_for_user->Id == $this->intUserId) {
                        $strToOut .= "<li style='background-color:green; color: white; font-weight: bolder;'>" . $item_for_user . "</li>";
                    } else {
                        $strToOut .= "<li>" . $item_for_user . "</li>";
                    }
                }
                $strToOut .= "</ul>";
            } else {
                $strToOut .= $item_comment['attn'] . ": " . $item_comment['for'];
            }
            $strToOut .= "</div>";
        }
        $strToOut .= $item_comment['comment'];
        if (is_array($item_comment['attachments']) && count($item_comment['attachments']) > 0) {
            $strToOut .= "<ul>" . implode("", $item_comment['attachments']) . "</ul>";
        }
        $strToOut .= "</div>";
        $strToOut .= "</div>";
        return $strToOut;
    }

    public function startTimer()
    {
        $this->ctlTimer->Start();
    }

    public function btnSubmit_Click($strFormId, $strControlId, $strParameter)
    {
        if (!is_empty($this->txtAddComment->Text)) {
            $strComment = $this->txtAddComment->Text;
            $html = str_get_html($strComment);
            $tokens = $html->find('span.token');
            $arrUserId = array();
            foreach ($tokens as $simple_dom_html) {
                if (!is_empty(strip_tags($simple_dom_html->innertext))) {
                    $intUserId = str_replace("id:", "", $simple_dom_html->attr['title']);
                    if (!is_empty($intUserId))
                        $arrUserId[] = $intUserId;
                }
            }
            $html->clear();

            $strComment = linkify($strComment);

            // log untuk aktivitas komentar
            $log = new ActivityLog();
            $log->CommentContent = $strComment;
            $log->Controller = $this->strTaskClassName;
            $log->Action = ActivityAction::Comment;
            $log->SubjectModel = $this->strTaskModelName;
            $log->SubjectIdNumber = $this->intTaskId;
            $log->FilenameLogger = substr($this->strTaskFileName, strlen(__BASEPATH__));
            $log->UrlLogger = str_replace(__WEB_URL_WITHOUT_HOSTNAME__, "", $_SERVER['REQUEST_URI']);
            $log->Ts = QDateTime::Now();
            $log->UserId = $this->intUserId;
            $log->Save();

            $strModelName = $log->SubjectModel;
            $objModel = $strModelName::Load($log->SubjectIdNumber);
            $objModel->TsUpdate = $log->Ts;
            $objModel->UpdaterId = $log->UserId;
            $objModel->Save();

            $intMainLogId = $log->Id;

            // notif ada komentar
            if ($log && $this->arrParticipants) {
                foreach ($this->arrParticipants as $intParticipantId) {
                    if ($intParticipantId) {
                        if ($this->intUserId == $intParticipantId)
                            continue;
                        $notif = new Notification();
                        $notif->LogId = $log->Id;
                        $notif->NotifyTs = QDateTime::Now();
                        $notif->UserId = $intParticipantId;
                        $notif->Save();
                    }
                }
            }

            unset($log);

            if ($arrUserId) {
                foreach ($arrUserId as $userid) {
                    $log = new ActivityLog();
                    $log->CommentContent = $strComment;
                    $log->Controller = $this->strTaskClassName;
                    $log->Action = ActivityAction::Mention;
                    $log->SubjectModel = $this->strTaskModelName;
                    $log->SubjectIdNumber = $this->intTaskId;
                    $log->FilenameLogger = substr($this->strTaskFileName, strlen(__BASEPATH__));
                    $log->UrlLogger = str_replace(__WEB_URL_WITHOUT_HOSTNAME__, "", $_SERVER['REQUEST_URI']);
                    $log->Ts = QDateTime::Now();
                    $log->UserId = $this->intUserId;
                    $log->ForUserId = $userid;
                    $log->ParentId = $intMainLogId;
                    $log->Save();
                    if ($log) {
                        $notif = new Notification();
                        $notif->LogId = $log->Id;
                        $notif->NotifyTs = QDateTime::Now();
                        $notif->UserId = $userid;
                        $notif->Contents = "mention";
                        $notif->Save();
                    }
                }
            }
        }

        unset($log);

        $intFileCounter = 0;
        for ($i = 0; $i <= $this->intAttachmentNumber; $i++) {
            if ($this->flcAttachmentArray[$i]->FileName) {
                $intFileCounter++;
                $uploaded_file = "/files/" . date('d.m.Y.H.i.s_') . $this->flcAttachmentArray[$i]->FileName;
                move_uploaded_file($this->flcAttachmentArray[$i]->File, __OTHERS_PATH__ . $uploaded_file);
                $log = new ActivityLog();
                $log->AttachmentName = $this->flcAttachmentArray[$i]->FileName;
                $log->AttachmentFile = $uploaded_file;
                $log->Controller = $this->strTaskClassName;
                $log->Action = ActivityAction::Attachment;
                $log->SubjectModel = $this->strTaskModelName;
                $log->SubjectIdNumber = $this->intTaskId;
                $log->FilenameLogger = substr($this->strTaskFileName, strlen(__BASEPATH__));
                $log->UrlLogger = str_replace(__WEB_URL_WITHOUT_HOSTNAME__, "", $_SERVER['REQUEST_URI']);
                $log->Ts = QDateTime::Now();
                $log->UserId = $this->intUserId;
                $log->ParentId = $intMainLogId;
                $log->Save();
                // notif ada file upload
                if ($log && $this->arrParticipants) {
                    foreach ($this->arrParticipants as $intParticipantId) {
                        if ($intParticipantId) {
                            if ($this->intUserId == $intParticipantId)
                                continue;
                            $notif = new Notification();
                            $notif->LogId = $log->Id;
                            $notif->NotifyTs = QDateTime::Now();
                            $notif->UserId = $intParticipantId;
                            $notif->Save();
                        }
                    }
                }
            }
        }

        $this->txtAddComment->Text = "";
        $this->LoadComment();
        $this->startTimer();
    }

}