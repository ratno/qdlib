<?php

class HomeIndex extends QForm
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
            return HomeIndex::Run('HomeIndex', __BASEPATH__ . '/apps/views/Home/home_index.tpl.php');
        } catch (Exception $objExc) {
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            exec_debug($objExc);
        }
    }

    protected function Form_Create()
    {
        $this->objDefaultWaitIcon = new QWaitIcon($this);
        $this->CustomTitle = "Halaman Utama / Dashboard";
        $this->pnlHome = new QPanel($this);
        $strPesan = "<h1>Selamat Datang Public</h1>";
        $this->pnlHome->Text = $strPesan;
        $this->arrCustom['show_top'] = true;
    }
}