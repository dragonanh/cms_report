<?php if ($sf_user->hasFlash('success')): ?>
    <div class="alert alert-success fade in">
        <button class="close" data-dismiss="alert" type="button">×</button>
        <?php echo __($sf_user->getFlash('success'), array(), 'messages') ?>
        <?php if($sf_user->hasFlash('fileImportFail')):?>
            <a href="<?php echo url_for('vt_refund_report_download_fail_file', array('file_name' => $sf_user->getFlash('fileImportFail')))?>">tải file</a>
            <?php $sf_user->setAttribute('fileImportFail', 'true', 'symfony/user/sfUser/flash/remove') ?>
        <?php endif?>
    </div>
    <?php $sf_user->setAttribute('success', 'true', 'symfony/user/sfUser/flash/remove') ?>
<?php endif; ?>

<?php if ($sf_user->hasFlash('notice')): ?>
    <div class="alert alert-warning fade in">
        <button class="close" data-dismiss="alert" type="button">×</button>
        <?php echo __($sf_user->getFlash('notice'), array(), 'messages') ?>
    </div>
    <?php $sf_user->setAttribute('notice', 'true', 'symfony/user/sfUser/flash/remove') ?>
<?php endif; ?>

<?php if ($sf_user->hasFlash('error')): ?>
    <div class="alert alert-error fade in">
        <button class="close" data-dismiss="alert" type="button">×</button>
        <?php echo __($sf_user->getFlash('error'), array(), 'messages') ?>
    </div>
    <?php $sf_user->setAttribute('error', 'true', 'symfony/user/sfUser/flash/remove') ?>
<?php endif; ?>

<?php if ($sf_user->hasFlash('info')): ?>
    <div class="alert alert-info fade in">
        <button class="close" data-dismiss="alert" type="button">×</button>
        <?php echo __($sf_user->getFlash('info'), array(), 'messages') ?>
    </div>
    <?php $sf_user->setAttribute('info', 'true', 'symfony/user/sfUser/flash/remove') ?>
<?php endif; ?>