<?php

class HomeCpanel extends QForm
{
    protected $pnlHome;

//    protected function Form_Exit() {}
//    protected function Form_Load() {}
//    protected function Form_PreRender() {}
//    protected function Form_Validate() {}
//    protected function Form_Run() {}

    public static function exec()
    {
        try {
            return HomeCpanel::Run('HomeCpanel', __BASEPATH__ . '/apps/views/Home/home_cpanel.tpl.php');
        } catch (Exception $objExc) {
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            exec_debug($objExc);
        }
    }

    public function _notif_count()
    {
        $cnt = Notification::QueryCount(
            QQ::AndCondition(
                QQ::Equal(QQN::Notification()->UserId, $this->User->Id),
                QQ::IsNull(QQN::Notification()->ReviewTs)
            )
        );
        json(array("status" => "ok", "total" => $cnt));
    }

    public function _notif()
    {
        $pnlNotification = "";
        $objNotifications = Notification::QueryArray(
            QQ::AndCondition(
                QQ::Equal(QQN::Notification()->UserId, $this->User->Id),
                QQ::IsNull(QQN::Notification()->ReviewTs)
            ),
            QQ::Clause(
                QQ::Expand(QQN::Notification()->Log),
                QQ::OrderBy(QQN::Notification()->NotifyTs, false)
            )
        );
        if ($objNotifications) {
            $pnlNotification .= '<table class="table table-condensed table-striped table-bordered table-hover" data-rowlink="a" id="myNotifTable">';
            $pnlNotification .= "<thead>";
            $pnlNotification .= "<tr>";
//      $pnlNotification .= "<th>Waktu</th>";
            $pnlNotification .= "<th>Notifikasi</th>";
            $pnlNotification .= "</tr>";
            $pnlNotification .= "</thead>";
            $pnlNotification .= "<tbody>";
            foreach ($objNotifications as $objNotification) {
                $strPrefix = "";
                $strSuffix = "";
                $class = $objNotification->Log->SubjectModel;

                $idNumber = $objNotification->Log->SubjectIdNumber;
                $strId = ($idNumber) ? " #" . $idNumber : "";
//        $url = __WEB_URL__.$objNotification->Log->UrlLogger;
                $url = qd_url($objNotification->Log->SubjectModel, "view/" . $objNotification->Log->SubjectIdNumber);
                $strSuffix = sprintf("[%s]", href($url, QConvertNotation::WordsFromCamelCase($objNotification->Log->Controller) . $strId));
                if ($objNotification->Contents == "mention") {
                    $strPrefix = "<span class='notify_mention'>[PENTING]</span>";
                }

                if ($objNotification->Log) {
                    $pnlNotification .= "<tr>";
                    $pnlNotification .= "<td>";
                    if ($objNotification->Log->SubjectModel == "PrivateMessage")
                        $pnlNotification .= $objNotification->Log->ToStringForNotification($strPrefix, $strSuffix, "mengirimkan");
                    else
                        $pnlNotification .= str_replace("menyebut " . $this->User, "menyebut anda", $objNotification->Log->ToStringForNotification($strPrefix, $strSuffix));
                    $pnlNotification .= "</td>";
                    $pnlNotification .= "</tr>";
                } else {
                    $pnlNotification .= "<tr>";
                    $pnlNotification .= "<td>";
                    $pnlNotification .= "<span style='color:red;'>[ACHTUNG! SYSTEM PANIC] Notification ID " . $objNotification->Id . " for user " . $objNotification->User . " doesn't have activity log</span>";
                    $pnlNotification .= "</td>";
                    $pnlNotification .= "</tr>";
                }
            }
            $pnlNotification .= "</tbody>";
            $pnlNotification .= "</table>";
        } else {
            $pnlNotification = "Tidak Ada Notifikasi Baru";
        }

        json(array("status" => "ok", "data" => $pnlNotification, "total" => count($objNotifications)));
    }

    protected function Form_Create()
    {
        $this->CustomTitle = "Halaman Dashboard";
        $this->GlobalLayout = 'backend';
        $this->objDefaultWaitIcon = new QWaitIcon($this);
        $this->pnlHome = new QPanel($this);
        $strPesan = "<h1>" . $this->User->Role->Name . "</h1>";
        $strPesan .= "<table class='table table-bordered'>";
        $strPesan .= "<tr>";
        $strPesan .= "<th>Nama</th>";
        $strPesan .= "<td>" . $this->User->Name . "</td>";
        $strPesan .= "</tr>";
        $strPesan .= "<tr>";
        $strPesan .= "<th>Username</th>";
        $strPesan .= "<td>" . $this->User->Username . "</td>";
        $strPesan .= "</tr>";
        $strPesan .= "<tr>";
        $strPesan .= "<th>Last Login</th>";
        $strPesan .= "<td>" . $this->User->LastLogin->qFormat("DDDD, DD MMMM YYYY hhhh:mm") . "</td>";
        $strPesan .= "</tr>";
        $strPesan .= "</table>";
        $this->pnlHome->Text = $strPesan;
    }
}