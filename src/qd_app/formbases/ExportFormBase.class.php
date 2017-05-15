<?php

/**
 * @author Ratno Putro Sulistiyono, ratno@knoqdown.com
 * @company KnoQDown Studio
 *
 * @package Sistem Informasi
 * @subpackage ExportForm
 */
class ExportFormBase extends QForm
{
    protected $arrIds;

    protected $pnlExport;
    protected $btnExport;
    protected $btnCancel;

    protected $arrCols;
    protected $arrSelectedCols;

    protected $dtgExport;
    protected $chkColumn;

    protected $intIdxRowInit = 1;
    protected $intIdxColInit = 0;
    protected $intIdxRow;
    protected $intIdxCol;

    protected $arrMetaData;
    protected $objPHPExcel;

    public static function exec()
    {
        return UsersExport::Run('UsersExport', __BASEPATH__ . '/apps/views/Users/users_export.tpl.php');
    }

    public function chk_Render($strColumn)
    {
        $this->chkColumn[$strColumn] = new QCheckBox($this->dtgExport);
        $this->chkColumn[$strColumn]->ActionParameter = $strColumn;
        $this->chkColumn[$strColumn]->Checked = true;
        return $this->chkColumn[$strColumn]->Render(false);
    }

    protected function Form_Create()
    {
        $this->arrIds = QR::getTokenize($this->TaskClassName . "ExportIds");

        $this->pnlExport = new QPanel($this);
        $this->pnlExport->HtmlEntities = false;

        $this->btnExport = new QButton($this);
        $this->btnExport->Text = "Export";
        $this->btnExport->AddAction(new QClickEvent(), new QServerAction("btnExport_Click"));

        $this->btnCancel = new QButton($this);
        $this->btnCancel->Text = QApplication::Translate('Back');
        $this->btnCancel->AddAction(new QClickEvent(), new QServerAction('btnCancel_Click'));

        if (is_array($this->arrIds) && $this->arrIds) {
            $this->pnlExport->Text = "<h3>Jumlah data yang akan diexport: " . count($this->arrIds) . " record.</h3>";
            $this->pnlExport->Text .= "<h3>Pilihlah kolom-kolom yang akan diexport:</h3>";
        } else {
            $this->FlashMessages = "error|Tidak ada data yang akan di export";
            $this->pnlExport->Text = "";
            $this->btnExport->Visible = false;
        }

        $this->prepareColumns();

        $this->dtgExport = new QDataGrid($this);
        $this->dtgExport->AddColumn(new QDataGridColumn("Exp", '<?= $_FORM->chk_Render($_ITEM["column_name"]) ?>', array('HtmlEntities' => false, 'Width' => 25)));
        $this->dtgExport->AddColumn(new QDataGridColumn("Kolom", '<?= $_ITEM["property_name"] ?>'));
        $this->dtgExport->DataSource = $this->arrCols;
    }

    protected function prepareColumns()
    {
    }

    protected function btnExport_Click($strFormId, $strControlId, $strParameter)
    {
        $this->intIdxCol = $this->intIdxColInit;
        $this->intIdxRow = $this->intIdxRowInit;

        foreach ($this->chkColumn as $chk) {
            if ($chk->Checked) $this->arrSelectedCols[$chk->ActionParameter] = $this->arrCols[$chk->ActionParameter];
        }

        $this->prepareMetaData();

        $this->objPHPExcel = new PHPExcel();
        $this->objPHPExcel->getProperties()->setCreator($this->arrMetaData['creator'])
            ->setLastModifiedBy($this->arrMetaData['creator'])
            ->setTitle($this->arrMetaData['title'])
            ->setSubject($this->arrMetaData['title'])
            ->setDescription($this->arrMetaData['title']);

        $this->objPHPExcel->createSheet(0);
        $this->objPHPExcel->setActiveSheetIndex(0);
        $this->objPHPExcel->getActiveSheet()->setTitle($this->arrMetaData['title']);

        // render columns
        foreach ($this->arrSelectedCols as $col_item) {
            $judul_kolom = strtoupper(trim(str_replace("id", "", strtolower(QConvertNotation::WordsFromCamelCase($col_item["property_name"])))));
            $this->objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($this->intIdxCol, $this->intIdxRow, $judul_kolom);
            $this->intIdxCol++;
        }
        $this->intIdxRow++;

        $this->createHeader();
        $this->createContent();
        $this->createFooter();

        for ($i = 0; $i < count($this->arrSelectedCols); $i++) {
            $this->objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);
        }

        // set the active sheet when this excel file open
        $this->objPHPExcel->setActiveSheetIndex(0);

        $filename = date("d.m.Y_h.i.s") . "_" . rand(1000, 9999) . "_" . $this->arrMetaData['filename'];

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    protected function prepareMetaData()
    {
        $this->arrMetaData['creator'] = "Ratno Putro Sulistiyono, ratno@knoqdown.com";
        $this->arrMetaData['title'] = "Export";
        $this->arrMetaData['filename'] = "Export";
    }

    protected function createHeader()
    {
    }

    protected function createContent()
    {
    }

    protected function createFooter()
    {
    }

    protected function btnCancel_Click($strFormId, $strControlId, $strParameter)
    {
        $this->RedirectToListPage();
    }

    protected function RedirectToListPage()
    {
        QApplication::Redirect(qd_url($this->TaskClassName, "list"));
    }
}

?>