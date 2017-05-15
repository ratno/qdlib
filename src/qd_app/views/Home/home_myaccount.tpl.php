<?php $this->RenderBegin() ?>
<div class="row-fluid">
    <div class="w-box">
        <div class="w-box-header">
            <?php _t($this->CustomTitle) ?>
        </div>
        <div class="w-box-content cnt_a">
            <?php $this->txtName->RenderWithName(); ?>
            <?php $this->txtTempatLahir->RenderWithName(); ?>
            <?php $this->calTglLahir->RenderWithName(); ?>
            <?php $this->lstGender->RenderWithName(); ?>
            <?php $this->txtAlamat->RenderWithName(); ?>
            <?php $this->txtHandphone->RenderWithName(); ?>
            <?php $this->txtEmail->RenderWithName(); ?>
            <?php $this->txtProfilePictureFile->RenderWithName(); ?>
        </div>
    </div>
</div>
<div class="row-fluid">
    <div id="save"><?php $this->btnSave->Render('CssClass=btn btn-primary btn-large'); ?></div>
</div>
<?php $this->DefaultWaitIcon->Render(); ?>
<?php $this->RenderEnd() ?>	