<?php $this->RenderBegin() ?>
<div class="row-fluid">
    <div class="w-box">
        <div class="w-box-header">
            <?php _t($this->CustomTitle) ?>
        </div>
        <div class="w-box-content cnt_a">
            <?php $this->lblName->RenderWithName(); ?>
            <?php $this->lblUsername->RenderWithName(); ?>
            <?php $this->txtPassword->RenderWithName(); ?>
            <?php $this->txtNewPassword->RenderWithName(); ?>
            <?php $this->txtNewPasswordConfirm->RenderWithName(); ?>
        </div>
    </div>
</div>

<div class="row-fluid">
    <?php $this->btnSave->Render('CssClass=btn btn-primary btn-large'); ?>
    <?php $this->btnCancel->Render('CssClass=btn btn-primary btn-large'); ?>
</div>
<?php $this->DefaultWaitIcon->Render(); ?>
<?php $this->RenderEnd() ?>  