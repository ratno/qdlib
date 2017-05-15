<?php

/**
 * contains the QDataGrid class
 *
 * @package Controls
 * @filesource
 */

/**
 * QDataGrid can help generate tables automatically with pagination. It can also be used to
 * render data directly from database by using a 'DataSource'. The code-generated search pages you get for
 * every table in your database are all QDataGrids
 *
 * @package Controls
 */
class QDataGrid extends QDataGridBase
{

    /** @var int $intCellSpacing Set the space between the cells */
    protected $intCellSpacing = 0;

    /** @type int $intCellPadding Set the space between the cell wall and the cell content */
    protected $intCellPadding = 0;

    ///////////////////////////
    // DataGrid Preferences
    ///////////////////////////
    // Feel free to specify global display preferences/defaults for all QDataGrid controls
    protected $blnShowFooter = true;

    // Override any of these methods/variables below to alter the way the DataGrid gets rendered
//	protected function GetPaginatorRowHtml() {}
//  protected function GetHeaderRowHtml() {}

    /**
     * QDataGrid::__construct()
     *
     * @param mixed $objParentObject The Datagrid's parent
     * @param string $strControlId Control ID
     *
     * @throws QCallerException
     * @return \QDataGrid
     */
    public function __construct($objParentObject, $strControlId = null)
    {
        try {
            parent::__construct($objParentObject, $strControlId);
        } catch (QCallerException $objExc) {
            $objExc->IncrementOffset();
            throw $objExc;
        }

        // For example... let's default the CssClass to datagrid
        $this->strCssClass = 'datagrid';
    }

//  protected function GetFooterRowHtml() {
//		return sprintf('<tr><td colspan="%s" style="text-align: center">Some Footer Can Go Here</td></tr>', count($this->objColumnArray));
//	}
//  protected function GetFooterRowHtml() {
//    if ($this->objPaginatorAlternate)
//      return sprintf('<tr><td class="footer_caption" colspan="%s">%s</td></tr>', count($this->objColumnArray), $this->GetPaginatorRowHtml($this->objPaginatorAlternate));
//  }
//  protected function GetDataGridRowHtml($objObject) {}

    public function GetControlHtml()
    {
        $this->DataBind();
        $strToReturn = '<div class="w-box">';
//			// Paginator Row (if applicable)
        if ($this->objPaginator) {
            $strToReturn .= '<div class="w-box-header datagrid-box">';
            $strToReturn .= $this->GetPaginatorRowHtml($this->objPaginator, $this->Title);
            $strToReturn .= '</div>';
        }

        $strToReturn .= '<div class="w-box-content">';

        // Table Tag
        $strStyle = $this->GetStyleAttributes();
        if ($strStyle)
            $strStyle = sprintf('style="%s" ', $strStyle);
        $strToReturn .= sprintf("<table id=\"%s\" %s%s>\r\n", $this->strControlId, $this->GetAttributes(), $strStyle);
        // Header Row (if applicable)
        if ($this->blnShowHeader) {
            $strToReturn .= "<thead>\r\n" . $this->GetHeaderRowHtml();

            // Filter Row (if applicable)
            if ($this->blnShowFilter)
                $strToReturn .= $this->GetFilterRowHtml();

            $strToReturn .= "</thead>\r\n";
        }

        // DataGrid Rows
        $strToReturn .= "<tbody>\r\n";
        $this->intCurrentRowIndex = 0;
        if ($this->objDataSource)
            foreach ($this->objDataSource as $objObject)
                $strToReturn .= $this->GetDataGridRowHtml($objObject);
        // Cleanup all the extra rows from the previous rendering
        for ($i = $this->intCurrentRowIndex; $i < $this->intRowCount; ++$i) {
            $strTrId = sprintf("%srow%s", $this->strControlId, $i);
            $this->RemoveChildControl($strTrId, true);
        }
        $this->intRowCount = $this->intCurrentRowIndex;

        $strToReturn .= "</tbody>\r\n";

        // Footer Row (if applicable)
        if ($this->blnShowFooter)
            $strToReturn .= "<tfoot>\r\n" . $this->GetFooterRowHtml() . "</tfoot>\r\n";

        // Finish Up
        $strToReturn .= '</table>';

        $strToReturn .= '</div>';
        if ($this->objPaginatorAlternate) {
            $strToReturn .= '<div class="w-box-footer">';
            $strToReturn .= $this->GetPaginatorRowHtml($this->objPaginatorAlternate);
            $strToReturn .= '</div>';
        }
        $strToReturn .= '</div>';
        $this->objDataSource = null;
        return $strToReturn;
    }

    protected function GetPaginatorRowHtml($objPaginator, $strTitle = null)
    {
        $strToReturn = '<div>';
        if (is_null($strTitle)) {
            if ($this->TotalItemCount > 0) {
                $intStart = (($this->PageNumber - 1) * $this->ItemsPerPage) + 1;
                $intEnd = $intStart + count($this->objDataSource) - 1;
                $strToReturn .= sprintf($this->strLabelForPaginated, $this->strNounPlural, $intStart, $intEnd, $this->TotalItemCount);
            } else {
                $intCount = count($this->objDataSource);
                if ($intCount == 0)
                    $strToReturn .= sprintf($this->strLabelForNoneFound, $this->strNounPlural);
                else if ($intCount == 1)
                    $strToReturn .= sprintf($this->strLabelForOneFound, $this->strNoun);
                else
                    $strToReturn .= sprintf($this->strLabelForMultipleFound, $intCount, $this->strNounPlural);
            }
        } else {
//        $strToReturn .= '<div class="head-collapse ui-custom-icon ui-custom-icon-triangle-1-s"></div>';
            $strToReturn .= "<span class='datagrid_title'>" . $strTitle . "</span>";
        }
        $strToReturn .= '<div class="pull-right">';
        $strToReturn .= $objPaginator->Render(false);
        $strToReturn .= '</div>';
        $strToReturn .= '</div>';
        return $strToReturn;
    }

}

?>