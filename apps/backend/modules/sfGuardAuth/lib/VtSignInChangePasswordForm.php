<?php
/**
 * Created by JetBrains PhpStorm.
 * User: os_duynt10
 * Date: 1/5/13
 * Time: 1:43 PM
 * To change this template use File | Settings | File Templates.
 */
class VtSignInChangePasswordForm extends BasesfGuardFormSignin
{
  public function configure()
  {
    $i18n = sfContext::getInstance()->getI18N();

    parent::configure();
    unset($this['remember']);

    $this->widgetSchema['new_password'] = new sfWidgetFormInputPassword();
    $this->validatorSchema['new_password'] = new sfValidatorRegex(array('pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})/',
              'required' =>'true'));

    $this->widgetSchema['confirm_pass'] = new sfWidgetFormInputPassword();
    $this->validatorSchema['confirm_pass'] = clone $this->validatorSchema['new_password'];

    $this->widgetSchema->moveField('confirm_pass', 'after', 'new_password');
    $this->mergePostValidator(new sfValidatorSchemaCompare('new_password', sfValidatorSchemaCompare::EQUAL,
      'confirm_pass', array(), array('invalid' => 'Mật khẩu không khớp.')));

    $this->mergePostValidator(new sfValidatorSchemaCompare('new_password',
      sfValidatorSchemaCompare::NOT_EQUAL, 'password', array(), array('invalid' => $i18n->__('Mật khẩu mới không được trùng mật khẩu cũ!'))));

//    $this->mergePostValidator(new sfValidatorSchemaCompare('repeat_password',
//      sfValidatorSchemaCompare::EQUAL, 'new_password', array(), array('invalid' => $i18n->__('Please enter the same password as above.'))));

    $this->widgetSchema['username']->setAttribute('readonly', true);
//    $this->widgetSchema['username'] = new sfWidgetFormPlainText(array("value_data" => $this->getObject()->getUsername()));
//    $this->validatorSchema['username'] = new sfValidatorPass();

    $this->widgetSchema['captcha'] = new sfWidgetCaptchaGD();
    $this->validatorSchema['captcha'] = new sfValidatorCaptchaGD(array(),
      array('invalid' => $i18n->__('Mã xác nhận không đúng.'),
        'required' => $i18n->__('Mã xác nhận không được để trống.')));
    $this->widgetSchema['password']->setLabel($i18n->__("Mật khẩu cũ"));
    $this->widgetSchema['username']->setLabel($i18n->__("Tên đăng nhập"));
    $this->widgetSchema['captcha']->setLabel($i18n->__("Mã xác nhận"));
    $this->useFields(array('username', 'password', 'new_password', 'confirm_pass', 'captcha'));
    $this->mergePostValidator(new sfValidatorCallback(array(
      'callback' => array($this, 'checkValue')
    )));
  }

  public function setUserName($username)
  {
    $this->setDefault('username', $username);
  }

  public function checkValue($validator, $values)
  {
    $i18n = sfContext::getInstance()->getI18N();
    if(isset($values['new_password']) && $values['new_password']){
      if(VtHelper::checkWeakPass($values['new_password'])){
        $error = new sfValidatorError($validator, $i18n->__('Mật khẩu của bạn chưa cụm từ dễ đoán, vui lòng nhập mật khẩu mạnh hơn.'));
        throw new sfValidatorErrorSchema($validator, array('new_password' => $error));
      }
    }

    return $values;
  }
}