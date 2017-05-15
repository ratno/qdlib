<?php $this->RenderBegin() ?>
    <div class="row-fluid">
        <div class="w-box">
            <div class="w-box-header">
                <?php _t($this->CustomTitle) ?>
            </div>
            <div class="w-box-content cnt_a">
                <?php $this->pnlHome->Render(); ?>
            </div>
        </div>
    </div>
<?php $this->DefaultWaitIcon->Render(); ?>
<?php $this->RenderEnd() ?>