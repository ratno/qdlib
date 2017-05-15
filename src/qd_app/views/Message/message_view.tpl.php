<?php $this->RenderBegin() ?>
<div class="row-fluid">
    <div class="w-box">
        <div class="w-box-header">
            <?php _t($this->CustomTitle) ?>
        </div>
        <div class="w-box-content cnt_a">
            <?php $this->lblFromUserId->RenderWithName(); ?>
            <?php $this->lblToUserId->RenderWithName(); ?>
            <?php $this->lblSendTs->RenderWithName(); ?>
            <?php $this->lblReadTs->RenderWithName(); ?>
            <?php $this->lblSubject->RenderWithName(); ?>
            <?php $this->lblMessage->RenderWithName(); ?>
            <?php $this->lblParentId->RenderWithName(); ?>
        </div>
    </div>
</div>

<div class="row-fluid">
    <div id="save"><?php $this->btnSave->Render('CssClass=submit-save'); ?></div>
    <div id="cancel"><?php $this->btnCancel->Render('CssClass=submit-cancel'); ?></div>
</div>
<?php $this->DefaultWaitIcon->Render(); ?>
<?php $this->RenderEnd() ?>	