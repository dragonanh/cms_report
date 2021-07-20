<?php use_helper('I18N') ?>

<?php if (count($errors=$form->getGlobalErrors()) > 0): ?>
<div class="control-group error">
    <ul class="error_list">
    <?php foreach ($errors as $error): ?>
        <li><?php echo __($error, null, 'tmcTwitterBootstrapPlugin') ?></li>
    <?php endforeach; ?>
    </ul>
</div>
<?php endif ?>

<form class="form-horizontal" action="<?php echo url_for('@sf_guard_signin') ?>" method="post" autocomplete="off">
    <?php echo $form->renderHiddenFields() ?>
    <div class="control-group<?php echo $form['username']->hasError() ? ' error' : ''?>">
        <?php echo $form['username']->renderLabel(null, array('class' => $form['username']->hasError() ? 'strong' : '')) ?>
        <?php if ($form['username']->hasError()): ?>
            <span class="help-block"><?php echo $form['username']->renderError() ?></span>
	    <br />
	    <br />
        <?php endif ?>
            
        <?php echo $form['username']->render(array('class' => 'input-xlarge')) ?>
    </div>
    <div class="control-group<?php echo $form['password']->hasError() ? ' error' : ''?>">
        <?php echo $form['password']->renderLabel(null, array('class' => $form['password']->hasError() ? 'strong' : '')) ?>
        <?php if ($form['password']->hasError()): ?>
            <span class="help-block"><?php echo $form['password']->renderError() ?></span>
	    <br />
        <?php endif ?>
            
        <?php echo $form['password']->render(array('class' => 'input-xlarge')) ?>
    </div>
  <?php if ($change_password == 1): ?>
  <div class="control-group<?php echo $form['new_password']->hasError() ? ' error' : ''?>">
    <?php echo $form['new_password']->renderLabel(__('Mật khẩu mới *', null, 'sf_guard'), array('class' => $form['new_password']->hasError() ? 'strong' : '')) ?>
    <?php echo $form['new_password']->render(array('class' => 'input-xlarge')) ?>
    <?php if ($form['new_password']->hasError()): ?>
    <span class="help-block"><?php echo $form['new_password']->renderError() ?></span>
    <?php endif ?>
  </div>
  <div class="control-group<?php echo $form['confirm_pass']->hasError() ? ' error' : ''?>">
    <?php echo $form['confirm_pass']->renderLabel(__('Nhập lại mật khẩu mới *', null, 'sf_guard'), array('class' => $form['confirm_pass']->hasError() ? 'strong' : '')) ?>
    <?php echo $form['confirm_pass']->render(array('class' => 'input-xlarge')) ?>
    <?php if ($form['confirm_pass']->hasError()): ?>
    <span class="help-block"><?php echo $form['confirm_pass']->renderError() ?></span>
    <?php endif ?>
  </div>
  <?php endif ?>
  <div class="control-group<?php echo $form['captcha']->hasError() ? ' error' : ''?>">
        <?php echo $form['captcha']->renderLabel(null, array('class' => $form['captcha']->hasError() ? 'strong' : '')) ?>
        <?php if ($form['captcha']->hasError()): ?>
            <span class="help-block"><?php echo $form['captcha']->renderError() ?></span>
	    <br />
        <?php endif ?>
            
        <?php echo $form['captcha']->render(array('class' => 'input-xlarge')) ?>
    </div>
  
    <?php if (isset($form['remember'])): ?>
        <label class="checkbox">
            <?php echo $form['remember']->render() ?>
            <?php echo $form['remember']->renderLabelName() ?>
        </label>
    <?php endif; ?>
    <input type="hidden" value="<?php echo $change_password ?>" name="change_password" />
<!--    <input class="btn" type="submit" value="--><?php //echo __('Đăng Nhập', null, 'tmcTwitterBootstrapPlugin') ?><!--" />-->
  <input class="btn" type="submit" value="<?php echo __(($change_password == 1) ? 'Đăng Nhập' : 'Đăng Nhập', null, 'tmcTwitterBootstrapPlugin') ?>" />
  <?php if ($change_password == 1) : ?>
    <input class="btn" type="button" value="<?php echo __('Hủy' , null, 'tmcTwitterBootstrapPlugin') ?>" href="<?php echo url_for('sf_guard_signin')?>" onclick="window.location ='<?php echo url_for('sf_guard_signin')?>'; "/>
  <?php endif; ?>
</form>

<?php if (sfConfig::get('app_sfGuardPlugin_signin_links', false)): ?>
    <div id="links">
        <?php if (!include_slot('signin_links')): ?>
            <ul>
                <?php if (isset($routes['sf_guard_forgot_password'])): ?>
                    <li><a href="<?php echo url_for('@sf_guard_forgot_password') ?>"><?php echo __('Quên Mật Khẩu?', null, 'tmcTwitterBootstrapPlugin') ?></a></li>
                <?php endif; ?>
                <?php if (isset($routes['sf_guard_register'])): ?>
                    <li><a href="<?php echo url_for('@sf_guard_register') ?>"><?php echo __('Want to register?', null, 'tmcTwitterBootstrapPlugin') ?></a></li>
                <?php endif; ?>
            </ul>
        <?php endif ?>
        <div class="clear"></div>
    </div>
    <?php
 endif ?>