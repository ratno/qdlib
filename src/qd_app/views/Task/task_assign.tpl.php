<?php $this->RenderBegin() ?>
<div class="row-fluid">
    <div class="span12">
        <div class="w-box">
            <div class="w-box-header" id="filterbox_head">
                <div class="head-collapse ui-custom-icon ui-custom-icon-triangle-1-e"></div>
                <?php _t("Pencarian/Filter"); ?>
            </div>
            <div class="w-box-content" id="filterbox_content">
                <div style="overflow: auto; padding: 5px;">
                    <div class="row-fluid">
                        <div class="span6">
                            <?php $this->txtFilterClassName->RenderWithName(); ?>
                            <?php $this->lstFilterClassName->RenderWithName(); ?>
                            <?php $this->lstFilterActionName->RenderWithName(); ?>
                            <?php $this->lstFilterLinkType->RenderWithName(); ?>
                        </div>
                        <div class="span6">
                            <?php $this->lstFilterLinkVisibilityType->RenderWithName(); ?>
                            <?php $this->lstFilterMenuGroup->RenderWithName(); ?>
                            <?php $this->lstFilterRole->RenderWithName(); ?>
                        </div>
                    </div>
                    <div id="filterbox-button">
                        <?php $this->btnFilter->Render('CssClass=btn btn-primary') ?>
                        <?php $this->btnResetFilter->Render('CssClass=btn btn-primary') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row-fluid">
    <div class="w-box">
        <div class="w-box-header">
            <div class="head-collapse ui-custom-icon ui-custom-icon-triangle-1-s"></div>
            Role Legend:
        </div>
        <div class="w-box-content cnt_a">
            <?php $this->pnlRole->Render(); ?>
        </div>
    </div>
</div>

<div class="row-fluid">
    <?php $this->dtgTasks->Render(); ?>
</div>

<div class="row-fluid">
    <?php $this->btnSave->Render('CssClass=btn btn-success btn-large'); ?>
    <?php $this->btnReloadTask->Render('CssClass=btn btn-primary btn-large'); ?>
</div>

<?php $this->DefaultWaitIcon->Render(); ?>
<?php $this->RenderEnd() ?>
<script type="text/javascript">
    (function () {
        $('#filterbox_content').hide();
    }());
</script>