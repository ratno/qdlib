<?php if ($_CONTROL->pnlHistory->Visible) : ?>
    <div class="row-fluid">
        <div class="w-box">
            <div class="w-box-header" id="pnl_history_header">
                <div class="head-collapse ui-custom-icon ui-custom-icon-triangle-1-e"></div>
                History:
                <div class="pull-right"><span
                            class="label label-success"><?php echo $_CONTROL->intCounter['history'] ?></span></div>
            </div>
            <div class="w-box-content cnt_a" id="pnl_history_container">
                <?php $_CONTROL->pnlHistory->Render(); ?>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php if ($_CONTROL->pnlAttachment->Visible) : ?>
    <div class="row-fluid">
        <div class="w-box">
            <div class="w-box-header" id="pnl_attachment_header">
                <div class="head-collapse ui-custom-icon ui-custom-icon-triangle-1-e"></div>
                Attachment:
                <div class="pull-right"><span
                            class="label label-success"><?php echo $_CONTROL->intCounter['attachment'] ?></span></div>
            </div>
            <div class="w-box-content cnt_a" id="pnl_attachment_container">
                <?php $_CONTROL->pnlAttachment->Render(); ?>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php if ($_CONTROL->pnlListComment->Visible) : ?>
    <div class="row-fluid">
        <div class="w-box">
            <div class="w-box-header" id="pnl_komentar_header">
                <div class="head-collapse ui-custom-icon ui-custom-icon-triangle-1-s"></div>
                Komentar:
                <div class="pull-right"><span
                            class="label label-success"><?php echo $_CONTROL->intCounter['comment'] ?></span></div>
            </div>

            <div class="w-box-content cnt_a" id="pnl_komentar_container">
                <div class="row-fluid">
                    <div class="span8">
                        <?php $_CONTROL->pnlListComment->Render(); ?>
                    </div>
                    <div class="span4">
                        <div>
                            <?php $_CONTROL->txtAddComment->Render(); ?>
                        </div>
                        <div>
                            <?php for ($i = 0; $i < $_CONTROL->intAttachmentNumber; $i++) : ?>
                                <div class="komentar_attach_item">
                                    <?php $_CONTROL->flcAttachmentArray[$i]->Render(); ?>
                                </div>
                            <?php endfor; ?>
                        </div>
                        <?php $_CONTROL->btnSubmit->Render("CssClass=btn btn-primary"); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<script type="text/javascript">
    $('span.token').on('click', function (e) {
        document.location.href = web_url + "/user/view/" + $(this).attr('title').split(":")[1];
    });
    $('#pnl_history_container').hide();
    $('#pnl_attachment_container').hide();
</script>
