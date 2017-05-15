<?php

require(__BASEPATH__ . '/app/qd/formbases/UsersEditFormBase.php');

/**
 * @author Ratno Putro Sulistiyono, ratno@knoqdown.com
 * @company KnoQDown Studio
 *
 * @package SI Alumni ITB
 * @subpackage Drafts
 */
class HomeMyaccount extends UsersEditFormBase
{

    protected $mctUsers;
    // Controls for User's Data Fields
    protected $lblId;
    protected $txtName;
    protected $txtTempatLahir;
    protected $calTglLahir;
    protected $lstGender;
    protected $txtAlamat;
    protected $txtHandphone;
    protected $txtEmail;
    protected $txtProfilePictureFile;
    // Other ListBoxes (if applicable) via Unique ReverseReferences and ManyToMany References
    // Other Controls
    /**
     * @var QButton Save
     */
    protected $btnSave;

    /**
     * @var QButton Cancel
     */
    protected $btnCancel;

    // Override Form Event Handlers as Needed
//		protected function Form_Run() {}
//		protected function Form_Load() {}

    public static function exec()
    {
        try {
            return HomeMyaccount::Run('HomeMyaccount');
        } catch (Exception $e) {
            QForm::setFlashMessages("error|" . $e->getMessage());
            QApplication::Redirect(qd_url("Home", "cpanel"));
        }
    }

    protected function Form_Create()
    {
        $this->CustomTitle = "Ubah Data Pribadi";
        $this->GlobalLayout = "backend";
        $this->objDefaultWaitIcon = new QWaitIcon($this);

        $this->mctUsers = UsersMetaControl::Create($this, $this->User->Id);

        // Call MetaControl's methods to create qcontrols based on User's data fields
        $this->txtName = $this->mctUsers->txtName_Create();
        $this->txtTempatLahir = $this->mctUsers->txtTempatLahir_Create();
        $this->calTglLahir = $this->mctUsers->calTglLahir_Create();
        $this->lstGender = $this->mctUsers->lstGender_Create();
        $this->txtAlamat = $this->mctUsers->txtAlamat_Create();
        $this->txtHandphone = $this->mctUsers->txtHandphone_Create();
        $this->txtEmail = $this->mctUsers->txtEmail_Create();
        $this->txtProfilePictureFile = $this->mctUsers->txtProfilePictureFile_Create();

        // Create Buttons and Actions on this Form
        $this->btnSave = new QButton($this);
        $this->btnSave->Text = QApplication::Translate('Save');
        $this->btnSave->AddAction(new QClickEvent(), new QServerAction('btnSave_Click'));
        $this->btnSave->CausesValidation = true;

        $this->btnCancel = new QButton($this);
        $this->btnCancel->Text = QApplication::Translate('Cancel');
        $this->btnCancel->AddAction(new QClickEvent(), new QAjaxAction('btnCancel_Click'));
    }

    // Button Event Handlers

    protected function Form_Validate()
    {
        // By default, we report that Custom Validations passed
        $blnToReturn = true;

        // Custom Validation Rules
        // TODO: Be sure to set $blnToReturn to false if any custom validation fails!


        $blnFocused = false;
        foreach ($this->GetErrorControls() as $objControl) {
            // Set Focus to the top-most invalid control
            if (!$blnFocused) {
                $objControl->Focus();
                $blnFocused = true;
            }

            // Blink on ALL invalid controls
            $objControl->Blink();
        }

        return $blnToReturn;
    }

    protected function btnSave_Click($strFormId, $strControlId, $strParameter)
    {
        // Delegate "Save" processing to the UsersMetaControl
        $this->mctUsers->SaveUsers();
        $this->User = $this->mctUsers->Users;
        QApplication::SetUser($this->User);
        $this->FlashMessages = "Perubahan data " . $this->mctUsers->Users->Name . " telah disimpan!";
    }

}

?>