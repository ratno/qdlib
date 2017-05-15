<?php

/**
 * @author Ratno Putro Sulistiyono, ratno@knoqdown.com
 * @company KnoQDown Studio
 *
 * @package Sistem Informasi
 * @subpackage ImportForm
 */
class ImportFormBase extends QForm
{
    protected $flcImpor;
    protected $txtSheetNum;
    protected $txtRowNumOfFieldTitle;
    protected $txtRowNumOfFirstTuple;
    protected $txtRowNumOfLastTuple;
    protected $txtFieldsInDatabase;
    protected $btnImport;
    protected $pnlHasil;
    protected $btnSaveData;
    protected $arrRowsData = array();
    /**
     * @var QButton Cancel
     */
    protected $btnCancel;

    protected function Form_Create()
    {
        $this->flcImpor = new QFileControl($this);
        $this->flcImpor->Name = "File XLS";

        $this->txtSheetNum = new QIntegerTextBox($this);
        $this->txtSheetNum->Name = QApplication::Translate("Sheet Number");
        $this->txtSheetNum->Text = 1;

        $this->txtRowNumOfFieldTitle = new QIntegerTextBox($this);
        $this->txtRowNumOfFieldTitle->Name = QApplication::Translate("Row Number of Field Title");
        $this->txtRowNumOfFieldTitle->Text = 1;

        $this->txtRowNumOfFirstTuple = new QIntegerTextBox($this);
        $this->txtRowNumOfFirstTuple->Name = QApplication::Translate("Row Number of First Tuple");
        $this->txtRowNumOfFirstTuple->Text = 2;

        $this->txtRowNumOfLastTuple = new QIntegerTextBox($this);
        $this->txtRowNumOfLastTuple->Name = QApplication::Translate("Row Number of First Tuple");

        $this->txtFieldsInDatabase = new QTextBox($this);
        $this->txtFieldsInDatabase->Name = QApplication::Translate("Fields in database (Separate with comma)");
        $this->txtFieldsInDatabase->TextMode = QTextMode::MultiLine;
        $this->txtFieldsInDatabase->Rows = 10;

        $this->pnlHasil = new QPanel($this);
        $this->pnlHasil->Visible = FALSE;

        $this->btnImport = new QButton($this);
        $this->btnImport->Text = QApplication::Translate("Import");
        $this->btnImport->AddAction(new QClickEvent(), new QServerAction("btnSave_Click"));

        $this->btnSaveData = new QButton($this);
        $this->btnSaveData->Text = QApplication::Translate("Save Data");
        $this->btnSaveData->Visible = false;
        $this->btnSaveData->AddAction(new QClickEvent(), new QServerAction("btnSaveData_Click"));

        $this->btnCancel = new QButton($this);
        $this->btnCancel->Text = QApplication::Translate('Cancel');
        $this->btnCancel->AddAction(new QClickEvent(), new QAjaxAction('btnCancel_Click'));
    }

    protected function btnSave_Click($strFormId, $strControlId, $strParameter)
    {
        $this->flcImpor->Visible = false;
        $this->txtSheetNum->Visible = false;
        $this->txtRowNumOfFieldTitle->Visible = false;
        $this->txtRowNumOfFirstTuple->Visible = false;
        $this->txtRowNumOfLastTuple->Visible = false;
        $this->txtFieldsInDatabase->Visible = false;
        $this->btnImport->Visible = false;

        $this->pnlHasil->Text = "<h3>Hasil Import File " . $this->flcImpor->FileName . "</h3>";
        $this->pnlHasil->Text .= $this->import_xls($this->flcImpor->File);
        $this->pnlHasil->Visible = true;
        $this->btnSaveData->Visible = true;
    }

    protected function import_xls($file)
    {
        // load xls file
        $xls = new Spreadsheet_Excel_Reader($file);

        // params
        $sheet_num = $this->txtSheetNum->Text - 1;// sheet num dimulai dari 0
        $header_row = $this->txtRowNumOfFieldTitle->Text;
        $first_row = $this->txtRowNumOfFirstTuple->Text;
        $last_row = ($this->txtRowNumOfLastTuple->Text) ? ($this->txtRowNumOfLastTuple->Text) : count($xls->rowInfo[$sheet_num]);

        // ambil cells nya
        $cells = $xls->sheets[$sheet_num]['cells'];
        if ($cells) { // jika ditemukan cells maka lanjutkan
            if ($this->txtFieldsInDatabase->Text) {
                $columns = explode(",", $this->txtFieldsInDatabase->Text);
                $colstemp = array();
                foreach ($columns as $column) {
                    $colstemp[] = trim($column);
                }
                $columns = $colstemp;
            } else {
                $columns = $cells[$header_row];
            }

            $col_num = count($cells[$header_row]);

            $inserts = array();

            // judul
            $tbl_header = "";
            $tbl_header .= "<tr>";
            $i = 0;
            foreach ($cells[$header_row] as $col) {
                $tbl_header .= "<th>";
                $tbl_header .= $col;
                $tbl_header .= "<br />";
                $tbl_header .= "{" . $columns[$i++] . "}";
                $tbl_header .= "</th>";
            }
            $tbl_header .= "</tr>";

            // content
            $tbl_content = "";
            for ($i = $first_row; $i <= $last_row; $i++) {
                $tbl_content .= "<tr>";
                $row = array();
                for ($c = 1; $c <= $col_num; $c++) {
                    $tbl_content .= "<td>" . $cells[$i][$c] . "</td>";

                    $row[$columns[$c - 1]] = $cells[$i][$c];
                }
                $tbl_content .= "</tr>";
                $this->arrRowsData[] = $row;
            }

            $tbl = "<table class='datagrid' border='1' cellpadding='5' cellspacing='0'>";
            $tbl .= "<thead>";
            $tbl .= $tbl_header;
            $tbl .= "</thead>";
            $tbl .= "<tbody>";
            $tbl .= $tbl_content;
            $tbl .= "</tbody>";
            $tbl .= "</table>";

            return $tbl;
        } else {
            return "No Data Imported";
        }
    }

    protected function btnSaveData_Click($strFormId, $strControlId, $strParameter)
    {

    }

    protected function btnCancel_Click($strFormId, $strControlId, $strParameter)
    {
        $this->RedirectToListPage();
    }
}