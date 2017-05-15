<?php $this->RenderBegin() ?>
    <div class="row-fluid">
        <?php $this->dtgPrivateMessages->Render(); ?>
    </div>

    <div class="row-fluid">
        <?php echo href(qd_url('Message', 'new'), 'Buat Pesan', array('class' => 'submit-new')) ?>
    </div>
<?php $this->DefaultWaitIcon->Render(); ?>
<?php $this->RenderEnd() ?>