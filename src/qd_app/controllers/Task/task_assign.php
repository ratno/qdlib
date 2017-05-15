<?php

/**
 * @author Ratno Putro Sulistiyono, ratno@knoqdown.com
 * @company KnoQDown Studio
 *
 * @package Sistem Informasi
 * @subpackage Drafts
 */
class TaskAssign extends QForm
{

    // Override Form Event Handlers as Needed
//		protected function Form_Run() {}
//		protected function Form_Load() {}

    protected $btnReloadTask;
    protected $dtgTasks;
    protected $arrTaskRole = array();
    protected $btnSave;
    protected $objTasks;
    protected $pnlRole;
    protected $objRoles;
    protected $lstFilterClassName;
    protected $txtFilterClassName;
    protected $lstFilterActionName;
    protected $lstFilterLinkType;
    protected $lstFilterLinkVisibilityType;
    protected $lstFilterMenuGroup;
    protected $lstFilterRole;

    /**
     * @var QButton Filter
     */
    protected $btnFilter;

    /**
     * @var QButton ResetFilter
     */
    protected $btnResetFilter;

    protected $objMenuGroups;
    protected $txtTitle = array();
    protected $txtOrderNum = array();
    protected $lstMenuGroup = array();
    protected $chk = array();
protected $arrRoleChecked = array();

    public static function exec()
    {
        return TaskAssign::Run('TaskAssign');
    }

    public function btnFilter_Click()
    {
        $this->dtgTasks->PageNumber = 1;
        $arrConditionals = array();

        if ($this->txtFilterClassName->Text)
            $arrConditionals[] = QQ::Like(QQN::Task()->ClassName, $this->txtFilterClassName->Text);

        if ($this->lstFilterClassName->SelectedValue)
            $arrConditionals[] = QQ::Equal(QQN::Task()->ClassName, $this->lstFilterClassName->SelectedValue);

        if ($this->lstFilterActionName->SelectedValue)
            $arrConditionals[] = QQ::Equal(QQN::Task()->ActionName, $this->lstFilterActionName->SelectedValue);

        if ($this->lstFilterLinkType->SelectedValue)
            $arrConditionals[] = QQ::Equal(QQN::Task()->LinkTypeId, $this->lstFilterLinkType->SelectedValue);

        if ($this->lstFilterLinkVisibilityType->SelectedValue)
            $arrConditionals[] = QQ::Equal(QQN::Task()->LinkVisibilityId, $this->lstFilterLinkVisibilityType->SelectedValue);

        if ($this->lstFilterMenuGroup->SelectedValue)
            $arrConditionals[] = QQ::Equal(QQN::Task()->MenuGroupId, $this->lstFilterMenuGroup->SelectedValue);

        if ($this->lstFilterRole->SelectedValue)
            $arrConditionals[] = QQ::Equal(QQN::Task()->Role->RoleId, $this->lstFilterRole->SelectedValue);

        if ($arrConditionals) {
            $this->dtgTasks->AdditionalConditions = QQ::AndCondition($arrConditionals);
            $this->dtgTasks->Refresh();
        } else {
            $this->btnReset_Click();
        }
    }

    public function btnReset_Click()
    {
        $this->lstFilterClassName->SelectedValue = null;
        $this->txtFilterClassName->Text = "";
        $this->lstFilterActionName->SelectedValue = null;
        $this->lstFilterLinkType->SelectedValue = null;
        $this->lstFilterLinkVisibilityType->SelectedValue = null;
        $this->lstFilterMenuGroup->SelectedValue = null;
        $this->lstFilterRole->SelectedValue = null;

        $this->dtgTasks->AdditionalConditions = QQ::All();
        $this->dtgTasks->Refresh();
    }

    public function colTitle_Render($objTask)
    {
        $this->txtTitle[$objTask->Id] = new QTextBox($this->dtgTasks);
        $this->txtTitle[$objTask->Id]->Width = "260px";
        $this->txtTitle[$objTask->Id]->Text = ($objTask->Title) ? $objTask->Title : "";
        return $this->txtTitle[$objTask->Id]->Render(FALSE);
    }

    public function colOrderNum_Render($objTask)
    {
        $this->txtOrderNum[$objTask->Id] = new QTextBox($this->dtgTasks);
        $this->txtOrderNum[$objTask->Id]->Width = "30px";
        $this->txtOrderNum[$objTask->Id]->Text = ($objTask->OrderNum) ? $objTask->OrderNum : 0;
        return $this->txtOrderNum[$objTask->Id]->Render(FALSE);
    }

    public function colMenuGroup_Render($objTask)
    {
        // MenuGroupId
        $this->lstMenuGroup[$objTask->Id] = new QListBox($this->dtgTasks);
        $this->lstMenuGroup[$objTask->Id]->Width = "140px";
        $this->lstMenuGroup[$objTask->Id]->AddItem(QApplication::Translate("- Select One -"), null);
        if ($this->objMenuGroups) {
            foreach ($this->objMenuGroups as $objMenuGroup) {
                $objListItem = new QListItem($objMenuGroup->__toString(), $objMenuGroup->Id, ($objTask->MenuGroupId == $objMenuGroup->Id));
                $this->lstMenuGroup[$objTask->Id]->AddItem($objListItem);
            }
        }
        return $this->lstMenuGroup[$objTask->Id]->Render(FALSE);
    }

    public function colCheckBox_Render($tblColumnName, $objTask)
    {
        $this->objTasks[$objTask->Id] = $objTask;
        $this->chk[$tblColumnName][$objTask->Id] = new QCheckBox($this->dtgTasks);
        switch ($tblColumnName) {
            case 'LinkTypeId':
                if ($objTask->LinkTypeId == LinkType::_Public) {
                    $this->chk[$tblColumnName][$objTask->Id]->Checked = true;
                } else {
                    $this->chk[$tblColumnName][$objTask->Id]->Checked = false;
                }
                break;
            case 'LinkVisibilityId':
                if ($objTask->LinkVisibilityId == LinkVisibilityType::Show) {
                    $this->chk[$tblColumnName][$objTask->Id]->Checked = true;
                } else {
                    $this->chk[$tblColumnName][$objTask->Id]->Checked = false;
                }
                break;
            case 'IsGlobalMenu':
                $this->chk[$tblColumnName][$objTask->Id]->Checked = $objTask->IsGlobalMenu;
                break;
            case 'IsIndependent':
                $this->chk[$tblColumnName][$objTask->Id]->Checked = $objTask->IsIndependent;
                break;
        }
        return $this->chk[$tblColumnName][$objTask->Id]->Render(FALSE);
    }

        public function colRoleCheckBox_Render($intRoleId, $objTask)
    {
        $this->chk['RoleId'][$objTask->Id][$intRoleId] = new QCheckBox($this->dtgTasks);
        if (array_key_exists($objTask->Id, $this->arrTaskRole) && array_key_exists($intRoleId, $this->arrTaskRole[$objTask->Id])) {
            $this->chk['RoleId'][$objTask->Id][$intRoleId]->Checked = true;
        } else {
            $this->chk['RoleId'][$objTask->Id][$intRoleId]->Checked = false;
        }
        $this->arrRoleChecked[$objTask->Id][$intRoleId] = $this->chk['RoleId'][$objTask->Id][$intRoleId]->Checked;
        return $this->chk['RoleId'][$objTask->Id][$intRoleId]->Render(FALSE);
    } // menyimpan state role, di check ato ga

    public function btnSave_Click()
    {
        if ($this->objTasks) {
            foreach ($this->objTasks as $objTask) {
                $intLinkTypeId = ($this->chk['LinkTypeId'][$objTask->Id]->Checked) ? LinkType::_Public : LinkType::_Protected;
                $intLinkVisibilityId = ($this->chk['LinkVisibilityId'][$objTask->Id]->Checked) ? LinkVisibilityType::Show : LinkVisibilityType::Hidden;
                $intMenuGroupId = $this->lstMenuGroup[$objTask->Id]->SelectedValue;
                $intOrderNum = $this->txtOrderNum[$objTask->Id]->Text;
                $txtTitle = $this->txtTitle[$objTask->Id]->Text;
                $blnIsGlobalMenu = $this->chk['IsGlobalMenu'][$objTask->Id]->Checked;
                $blnIsIndependent = $this->chk['IsIndependent'][$objTask->Id]->Checked;

                // check dengan value
                $blnNotChanged = true;
                $blnNotChanged = ($blnNotChanged && $objTask->Title == $txtTitle) ? true : false;
                $blnNotChanged = ($blnNotChanged && $objTask->LinkTypeId == $intLinkTypeId) ? true : false;
                $blnNotChanged = ($blnNotChanged && $objTask->LinkVisibilityId == $intLinkVisibilityId) ? true : false;
                $blnNotChanged = ($blnNotChanged && $objTask->MenuGroupId == $intMenuGroupId) ? true : false;
                $blnNotChanged = ($blnNotChanged && $objTask->OrderNum == $intOrderNum) ? true : false;
                $blnNotChanged = ($blnNotChanged && $objTask->IsGlobalMenu == $blnIsGlobalMenu) ? true : false;
                $blnNotChanged = ($blnNotChanged && $objTask->IsIndependent == $blnIsIndependent) ? true : false;
                if (!$blnNotChanged) {
                    $objTask->Title = $txtTitle;
                    $objTask->LinkTypeId = $intLinkTypeId;
                    $objTask->LinkVisibilityId = $intLinkVisibilityId;
                    $objTask->MenuGroupId = $intMenuGroupId;
                    $objTask->OrderNum = $intOrderNum;
                    $objTask->IsGlobalMenu = $blnIsGlobalMenu;
                    $objTask->IsIndependent = $blnIsIndependent;
                    $objTask->Save();
                }

                foreach ($this->objRoles as $objRole) {
                    if ($this->chk['RoleId'][$objTask->Id][$objRole->Id]->Checked == $this->arrRoleChecked[$objTask->Id][$objRole->Id]) {
                        // no changes
                    } else {
                        if ($this->chk['RoleId'][$objTask->Id][$objRole->Id]->Checked) {
                            $objTask->AssociateRole($objRole);
                        } else {
                            $objTask->UnassociateRole($objRole);
                        }
                        $this->arrRoleChecked[$objTask->Id][$objRole->Id] = $this->chk['RoleId'][$objTask->Id][$objRole->Id]->Checked; // update state
                    }
                }
            }
            $this->FlashMessages = "Task Assignment Updated.";
        }
    }

    public function btnReloadTask_Click()
    {
        $arrDefault = array(
            "list" => array("link_type" => LinkType::_Protected, "link_visibility" => LinkVisibilityType::Show, "is_independent" => TRUE),
            "new" => array("link_type" => LinkType::_Protected, "link_visibility" => LinkVisibilityType::Hidden, "is_independent" => TRUE),
            "edit" => array("link_type" => LinkType::_Protected, "link_visibility" => LinkVisibilityType::Hidden, "is_independent" => FALSE),
            "view" => array("link_type" => LinkType::_Protected, "link_visibility" => LinkVisibilityType::Hidden, "is_independent" => FALSE),
            "delete" => array("link_type" => LinkType::_Protected, "link_visibility" => LinkVisibilityType::Hidden, "is_independent" => FALSE),
            "export" => array("link_type" => LinkType::_Protected, "link_visibility" => LinkVisibilityType::Hidden, "is_independent" => TRUE),
            "import" => array("link_type" => LinkType::_Protected, "link_visibility" => LinkVisibilityType::Hidden, "is_independent" => TRUE),
            "api" => array("link_type" => LinkType::_Protected, "link_visibility" => LinkVisibilityType::Hidden, "is_independent" => TRUE),
            "listedit" => array("link_type" => LinkType::_Protected, "link_visibility" => LinkVisibilityType::Hidden, "is_independent" => TRUE)
        );

        $arrAdminExcludeClassVisibility = array("activitylog", "privatemessage", "notification", "task", "role", "menugroup", "privatemessagecontent", "setting", "usertoken", "applicationregistry");
        $arrAdminExcludeRoleAction = array("import", "export", "listedit");

        $objRoles = Role::LoadAll();
        // browse controller directory
        $controller_dir = __BASEPATH__ . "/app/qd/controllers";
        $d = dir($controller_dir);
        while (false !== ($class_folder_name = $d->read())) {
            if ($class_folder_name != '.' && $class_folder_name != '..' && !preg_match('/^\./', $class_folder_name)) {
                $class_folder_dir = $controller_dir . "/" . $class_folder_name;
                $s = dir($class_folder_dir);
                while (false !== ($class_file_name = $s->read())) {
                    if ($class_file_name != '.' && $class_file_name != '..' && !preg_match("/^\./", $class_file_name)) {
                        $token = substr($class_file_name, 0, strrpos($class_file_name, "."));
                        $task = Task::LoadByToken($token);
                        if (!$task) {
                            $task = new Task();
                            $task->Token = $token;
                            $task->ClassName = $class_folder_name;
                            $task->ActionName = substr($token, strlen(QConvertNotation::UnderscoreFromCamelCase($class_folder_name)) + 1);
                            $task->Filename = $class_file_name;
                            if ($task->ActionName == 'list' || $task->ActionName == 'listedit') {
                                $title_prefix = "";
                            } else {
                                $title_prefix = ucwords(str_replace("_", " ", $task->ActionName));
                            }
                            if (strtolower($task->ClassName) == "home") {
                                $title_suffix = "";
                                $blnHome = true;
                            } else {
                                $title_suffix = QConvertNotation::WordsFromCamelCase($task->ClassName);
                                $blnHome = false;
                            }
                            $title = trim(QApplication::Translate($title_prefix) . ' ' . QApplication::Translate($title_suffix));
                            $task->Title = $title;
                            $task->Link = $task->ClassName . "/" . $task->ActionName;
                            if ($blnHome) {
                                if (in_array($task->ActionName, array("index", "login", "logout"))) {
                                    $task->LinkTypeId = LinkType::_Public;
                                }
                            } else {
                                $task->LinkTypeId = (array_key_exists($task->ActionName, $arrDefault) && $arrDefault[$task->ActionName]['link_type']) ? $arrDefault[$task->ActionName]['link_type'] : LinkType::_Protected;
                            }
                            if (in_array(strtolower($task->ClassName), $arrAdminExcludeClassVisibility)) {
                                $task->LinkVisibilityId = LinkVisibilityType::Hidden;
                            } else {
                                $task->LinkVisibilityId = (array_key_exists($task->ActionName, $arrDefault) && $arrDefault[$task->ActionName]['link_visibility']) ? $arrDefault[$task->ActionName]['link_visibility'] : LinkVisibilityType::Hidden;
                            }
                            $task->MenuGroupId = 1;
                            $task->OrderNum = 0;
                            $task->IsGlobalMenu = 0;
                            $task->IsIndependent = (array_key_exists($task->ActionName, $arrDefault) && $arrDefault[$task->ActionName]['is_independent']) ? $arrDefault[$task->ActionName]['is_independent'] : 0;

                            $task->Save(); //group root belum kepake

                            foreach ($objRoles as $objRole) {
                                if ($blnHome) {
                                    $task->AssociateRole($objRole);
                                } else {
                                    if ($objRole->Id == 1) {
                                        if (in_array(strtolower($task->ActionName), $arrAdminExcludeRoleAction)) {
                                            // skip
                                        } else {
                                            $task->AssociateRole($objRole);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $s->close();
            }
        }
        $d->close();

        $this->dtgTasks->Refresh();
    }

    protected function Form_PreRender()
    {
        $_SESSION['dtgTasksViewState'] = array(
            "PageNumber" => $this->dtgTasks->PageNumber,
            "SortDirection" => $this->dtgTasks->SortDirection,
            "SortColumnIndex" => $this->dtgTasks->SortColumnIndex,
            "AdditionalConditions" => serialize($this->dtgTasks->AdditionalConditions),
            "AdditionalClauses" => serialize($this->dtgTasks->AdditionalClauses)
        );
    }

    protected function Form_Create()
    {
        $this->TaskClassName = 'Task';
        $this->TaskActionName = 'Assign';
        $this->CustomTitle = "Assign Task";
        $this->GlobalLayout = "backend";
        $this->addMenu($this->TaskClassName);
        $this->objDefaultWaitIcon = new QWaitIcon($this);
        $this->excludeMenu('export');

        $this->btnReloadTask = new QButton($this);
        $this->btnReloadTask->Text = "Reload New Task";
        $this->btnReloadTask->AddAction(new QClickEvent(), new QAjaxAction('btnReloadTask_Click'));

        $this->btnSave = new QButton($this);
        $this->btnSave->Text = "Simpan Perubahan";
        $this->btnSave->AddAction(new QClickEvent(), new QAjaxAction('btnSave_Click'));

        $this->lstFilterClassName = new QListBox($this);
        $this->lstFilterClassName->Name = "Class Name";
        $this->lstFilterClassName->AddItem(QApplication::Translate("- Select One -"), null);

        $strClassNames = Task::LoadAllClassNames();
        if ($strClassNames) {
            foreach ($strClassNames as $objTaskOfClassName) {
                $objListItem = new QListItem($objTaskOfClassName->ClassName, $objTaskOfClassName->ClassName);
                $this->lstFilterClassName->AddItem($objListItem);
            }
        }

        $this->txtFilterClassName = new QTextBox($this);
        $this->txtFilterClassName->Name = "Class Name (like)";

        $this->lstFilterActionName = new QListBox($this);
        $this->lstFilterActionName->Name = "Action Name";
        $this->lstFilterActionName->AddItem(QApplication::Translate("- Select One -"), null);

        $strActionNames = Task::LoadAllActionNames();
        if ($strActionNames) {
            foreach ($strActionNames as $objTaskOfActionName) {
                $objListItem = new QListItem($objTaskOfActionName->ActionName, $objTaskOfActionName->ActionName);
                $this->lstFilterActionName->AddItem($objListItem);
            }
        }

        $this->lstFilterLinkType = new QListBox($this);
        $this->lstFilterLinkType->Name = "Link Type";
        $this->lstFilterLinkType->AddItem(QApplication::Translate("- Select One -"), null);

        foreach (LinkType::$NameArray as $key => $value) {
            $this->lstFilterLinkType->AddItem($value, $key);
        }

        $this->lstFilterLinkVisibilityType = new QListBox($this);
        $this->lstFilterLinkVisibilityType->Name = "Link Visibility Type";
        $this->lstFilterLinkVisibilityType->AddItem(QApplication::Translate("- Select One -"), null);

        foreach (LinkVisibilityType::$NameArray as $key => $value) {
            $this->lstFilterLinkVisibilityType->AddItem($value, $key);
        }

        $this->lstFilterMenuGroup = new QListBox($this);
        $this->lstFilterMenuGroup->Name = "Menu Group";
        $this->lstFilterMenuGroup->AddItem(QApplication::Translate("- Select One -"), null);

        $this->objMenuGroups = MenuGroup::LoadAll();
        if ($this->objMenuGroups) {
            foreach ($this->objMenuGroups as $objMenuGroup) {
                $objListItem = new QListItem($objMenuGroup->__toString(), $objMenuGroup->Id);
                $this->lstFilterMenuGroup->AddItem($objListItem);
            }
        }


        $this->lstFilterRole = new QListBox($this);
        $this->lstFilterRole->Name = "Role";
        $this->lstFilterRole->AddItem(QApplication::Translate("- Select One -"), null);


        $this->btnFilter = new QButton($this);
        $this->btnFilter->Text = "Filter";
        $this->btnFilter->AddAction(new QClickEvent(), new QAjaxAction('btnFilter_Click'));
        $this->btnResetFilter = new QButton($this);
        $this->btnResetFilter->Text = "Reset";
        $this->btnResetFilter->AddAction(new QClickEvent(), new QAjaxAction('btnReset_Click'));

        $this->dtgTasks = new TaskDataGrid($this);
        $this->dtgTasks->ShowFilter = FALSE;
        $this->dtgTasks->CssClass = 'datagrid';
        $this->dtgTasks->AlternateRowStyle->CssClass = 'alternate';
        $this->dtgTasks->Paginator = new QPaginator($this->dtgTasks);
        $this->dtgTasks->ItemsPerPage = 50;

        $col = $this->colActionRender();

        $this->pnlRole = new QPanel($this);

        $this->objRoles = Role::LoadAll(QQ::Clause(QQ::OrderBy(QQN::Role()->Id), QQ::ExpandAsArray(QQN::Role()->Task)));
        if ($this->objRoles) {
            foreach ($this->objRoles as $objRole) {
                $objListItem = new QListItem($objRole->__toString(), $objRole->Id);
                $this->lstFilterRole->AddItem($objListItem);

                $this->pnlRole->Text .= "<b>R" . $objRole->Id . "</b> : " . $objRole->__toString() . "<br />";
                $this->dtgTasks->AddColumn(new QDataGridColumn("R" . $objRole->Id, '<?= $_FORM->colRoleCheckBox_Render(' . $objRole->Id . ',$_ITEM) ?>', 'HtmlEntities=false'));
                foreach ($objRole->_TaskArray as $objTask) {
                    $this->arrTaskRole[$objTask->Id][$objRole->Id] = $objTask->Id;
                }
            }
        }


        $this->dtgTasks->MetaAddColumn('Token');
        $this->dtgTasks->AddColumn(new QDataGridColumn("Title", '<?= $_FORM->colTitle_Render($_ITEM) ?>', 'HtmlEntities=false'));
        $this->dtgTasks->AddColumn(new QDataGridColumn("Public", '<?= $_FORM->colCheckBox_Render("LinkTypeId",$_ITEM) ?>', 'HtmlEntities=false'));
        $this->dtgTasks->AddColumn(new QDataGridColumn("Show Link", '<?= $_FORM->colCheckBox_Render("LinkVisibilityId",$_ITEM) ?>', 'HtmlEntities=false'));
        $this->dtgTasks->AddColumn(new QDataGridColumn("Global", '<?= $_FORM->colCheckBox_Render("IsGlobalMenu",$_ITEM) ?>', 'HtmlEntities=false'));
        $this->dtgTasks->AddColumn(new QDataGridColumn("Independent", '<?= $_FORM->colCheckBox_Render("IsIndependent",$_ITEM) ?>', 'HtmlEntities=false'));
        $this->dtgTasks->AddColumn(new QDataGridColumn("Menu Group", '<?= $_FORM->colMenuGroup_Render($_ITEM) ?>', 'HtmlEntities=false'));
        $this->dtgTasks->AddColumn(new QDataGridColumn("Order Num", '<?= $_FORM->colOrderNum_Render($_ITEM) ?>', 'HtmlEntities=false'));

        $this->dtgTasks->FormAdditionalClauses = QQ::Clause(QQ::OrderBy(QQN::Task()->MenuGroupId), QQ::OrderBy(QQN::Task()->OrderNum));

        if (isset($_SESSION['dtgTasksViewState']) && $viewState = $_SESSION['dtgTasksViewState']) {
            unset($_SESSION['dtgTasksViewState']);
            $this->dtgTasks->SortColumnIndex = $viewState['SortColumnIndex'];
            $this->dtgTasks->SortDirection = $viewState['SortDirection'];
            $this->dtgTasks->PageNumber = $viewState['PageNumber'];
            $this->dtgTasks->AdditionalConditions = unserialize($viewState['AdditionalConditions']);
            $this->dtgTasks->AdditionalClauses = unserialize($viewState['AdditionalClauses']);
        }
    }

    public function colActionRender()
    {
        $strColumnTitle = "Aksi";
        $arrParameter = QApplication::GetTask("Task");
        if ($arrParameter) {
            $arrParameter = array("delete" => "Delete");
        }
        $strHtml = "";
        if ($arrParameter && is_array($arrParameter)) {
            $strHtml = '<div class="datagrid_actions">';
            foreach ($arrParameter as $key => $value) {
                $strPageUrl = qd_url("Task", $key, '<?= rawurlencode($_ITEM->Id); ?>');
                if (Icon::$$key)
                    $strHtml .= href($strPageUrl, img(Icon::ToImage(Icon::$$key), array('width' => 20, 'title' => Icon::ToTitle(Icon::$$key))));
            }
            $strHtml .= "</div>";
            $col = new QDataGridColumn($strColumnTitle, $strHtml, 'HtmlEntities=False');
            $this->dtgTasks->AddColumn($col);
            return $col;
        }
    }
}