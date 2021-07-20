<?php

/**
 * User: os_vuongch
 * Date: 13 Dec 2012
 * Time: 3:49 PM
 */
class VtGuardChangePasswordForm extends BaseForm {

  public function setup() {
    $i18n = sfContext::getInstance()->getI18N();

    $this->setWidgets(array(
      'username' => new sfWidgetFormInputText(array(), array('style' => 'width: 200px')),
      'password' => new sfWidgetFormInputPassword(array(
        'type' => 'password',
              ), array('max_length' => 250)),
    ));

    $this->setValidators(array(
      'username' => new sfValidatorString(),
      'password' => new sfValidatorString(array(
        'trim' => false,
        'required' => true,
              ), array()),
    ));
    $this->setDefault('username', sfContext::getInstance()->getUser()->getGuardUser()->getUsername());
    $this->widgetSchema['username']->setAttribute('readonly', true);

    $this->widgetSchema['new_password'] = new sfWidgetFormInputPassword(array(
      'type' => 'password'), array('max_length' => 250));

    $this->validatorSchema['new_password'] = new sfValidatorRegex(array(
      'trim' => false,
      'pattern' => '/^.*(?=.{8,28})(?=.*\d)(?=.*\W+)(?![.\n])(?=.*[a-zA-Z]).*$/',
      'required' => true,
      'max_length' => 128,
    ), array('invalid' => $i18n->__('Mật khẩu phải từ 8-128 ký tự bao gồm chữ, số và ký tự đặc biệt')));

    $this->widgetSchema['repeat_password'] = clone $this->widgetSchema['new_password'];

    $this->validatorSchema['repeat_password'] = clone $this->validatorSchema['new_password'];

    $this->widgetSchema->moveField('repeat_password', 'after', 'new_password');

    $this->widgetSchema->setLabels(array(
      'username' => $i18n->__('Tên đăng nhập'),
      'password' => $i18n->__('Mật khẩu cũ'),
      'new_password' => $i18n->__('Mật khẩu mới'),
      'repeat_password' => $i18n->__('Nhập lại mật khẩu mới'),
    ));
    $this->validatorSchema->setPostValidator(new validatorChangePassUser());
    $this->mergePostValidator(new sfValidatorSchemaCompare('new_password', sfValidatorSchemaCompare::NOT_EQUAL, 'password', array(), array('invalid' => $i18n->__('Mật khẩu mới phải khác mật khẩu cũ.'))));
    $this->mergePostValidator(new sfValidatorSchemaCompare('repeat_password', sfValidatorSchemaCompare::EQUAL, 'new_password', array(), array('invalid' => $i18n->__('Phải nhập giống với mật khẩu mới.'))));
    $this->widgetSchema->setNameFormat('password[%s]');
    $this->mergePostValidator(new sfValidatorCallback(array(
      'callback' => array($this, 'checkValue')
    )));
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
