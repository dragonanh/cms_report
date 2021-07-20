<?php

/**
 * BasesfGuardFormSignin
 *
 * @package    sfDoctrineGuardPlugin
 * @subpackage form
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: BasesfGuardFormSignin.class.php 25546 2009-12-17 23:27:55Z Jonathan.Wage $
 */
class BasesfGuardFormAdminSignin extends BaseForm {

  /**
   * @see sfForm
   */
  public function setup() {
    $i18n = sfContext::getInstance()->getI18N();
    $this->setWidgets(array(
        'username' => new sfWidgetFormInputText(),
        'password' => new sfWidgetFormInputPassword(array('type' => 'password')),
        'remember' => new sfWidgetFormInputCheckbox(),
        'captcha' => new sfWidgetCaptchaGD(array('is_easy' => true)),
    ));

      $this->setValidators(array(
        'username' => new sfValidatorString(array(), array('required' => $i18n->__('Phải nhập tên đăng nhập'))),
        'password' => new sfValidatorString(array(), array('required' => $i18n->__('Phải nhập mật khẩu'))),
        'remember' => new sfValidatorBoolean(),
        'captcha' => new sfValidatorCaptchaGD(array(),
            array('invalid' => $i18n->__('Mã xác nhận không đúng.'),
                'required' => $i18n->__('Mã xác nhận không được để trống.'))),
    ));

    if (sfConfig::get('app_sf_guard_plugin_allow_login_with_email', true)) {
      $this->widgetSchema['username']->setLabel($i18n->__('Tên Đăng Nhập'));
      $this->widgetSchema['password']->setLabel($i18n->__('Mật Khẩu'));
      $this->widgetSchema['captcha']->setLabel($i18n->__('Mã Xác Nhận'));
    }

    $this->validatorSchema->setPostValidator(new sfCaptchaGuardValidatorUser(array(), array('invalid' => $i18n->__('Tên đăng nhập hoặc mật khẩu không chính xác!'))));

    $this->widgetSchema->setNameFormat('signin[%s]');
  }
  
}