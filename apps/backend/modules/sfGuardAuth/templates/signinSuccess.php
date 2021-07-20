<?php use_helper('I18N') ?>

<?php include_partial('tmcTwitterBootstrap/assets') ?>
<?php include_partial('header') ?>

<div id="login" class="container">
    <div class="hero-unit">
        <div class="pull-left login-left">
            <?php include_partial('logo') ?>
        </div>
        <div class="pull-left login-right" style="width: 55%;">
            <h2><?php echo __('QUẢN TRỊ HỆ THỐNG VIETTEL SHOP V2', null, 'tmcTwitterBootstrapPlugin') ?></span></h2>

            <?php include_partial('alerts') ?>
            <?php include_partial('signin_form', array('form' => $form, 'routes' => $sf_context->getRouting()->getRoutes(),'change_password' => $change_password)) ?>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('.captcha').click();
    })
</script>
<?php include_component('tmcTwitterBootstrap', 'footer') ?>