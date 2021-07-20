<?php

/**
 * sfGuardUserAdminForm for admin generators
 *
 * @package    sfDoctrineGuardPlugin
 * @subpackage form
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfGuardUserAdminForm.class.php 23536 2009-11-02 21:41:21Z Kris.Wallsmith $
 */
class sfGuardUserAdminForm extends BasesfGuardUserAdminForm
{
  /**
   * @see sfForm
   */
  public function configure()
  {

    parent::configure();
    $i18n = sfContext::getInstance()->getI18N();

    if (!$this->isNew()) {
      unset($this['username']);
      $this->widgetSchema['username_show'] = new sfWidgetFormPlainText(array("value_data" => $this->getObject()->getUsername()));
      $this->validatorSchema['username_show'] = new sfValidatorPass();
    }else{
      $this->validatorSchema['username'] = new sfValidatorRegex([
        'pattern' => VtHelper::USERNAME_PATTERN,
        'max_length' => 128,
        'required' => true,
        'min_length' => 5
      ], [
        'invalid' => $i18n->__('Tên đăng nhập không được chứa ký tự đặc biệt'),
        'max_length' => $i18n->__('Trường quá dài(Tối đa %max_length% ký tự)'),
        'min_length' => $i18n->__('Trường quá ngắn(Tối thiểu %min_length% ký tự)'),
      ]);
    }

    $request = sfContext::getInstance()->getRequest();
    $formValues = $request->getParameter($this->getName());
    $this->widgetSchema['password'] = new sfWidgetFormInputPassword();
    $this->validatorSchema['password'] = new sfValidatorRegex(
      array(
        'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})/',
        'required' => $formValues['is_vsa_account'] ? false : $this->isNew(),
        'trim' => true
      ),
      array(
        'required' => $i18n->__('Không được để trống!'),
        'invalid' => $i18n->__('Mật khẩu tối thiểu 8 ký tự, bao gồm: chữ hoa, chữ thường, số và ký tự đặc biệt.')
      ));

    $this->widgetSchema['password_again'] = new sfWidgetFormInputPassword();
    $this->validatorSchema['password_again'] = clone $this->validatorSchema['password'];
    if(sfContext::getInstance()->getUser()->checkPermission('admin')){
      $query = Doctrine_Core::getTable('sfGuardPermission')->createQuery();
    }else{
      $this->widgetSchema->offsetUnset('is_super_admin');
      $query = Doctrine_Core::getTable('sfGuardPermission')->createQuery()->andWhereIn('id',['2','3','6']);
    }

    $this->widgetSchema['permissions_list'] = new sfWidgetFormDoctrineChoice(array(
      'multiple' => true,
      'expanded' => true,
      'model' => 'sfGuardPermission',
      'order_by' => array('name', 'asc'),
        'query' => $query
    ));
    $this->validatorSchema['permissions_list'] = new sfValidatorDoctrineChoice(array(
      'multiple' => true, 'model' => 'sfGuardPermission', 'required' => false,
      "query" => $query
    ));
    $this->widgetSchema->setLabels(array(
      'email_address' => $i18n->__('Địa chỉ Email'),
      'username' => $i18n->__('Tên đăng nhập'),
      'password' => $i18n->__('Mật khẩu'),
      'is_active' => $i18n->__('Kích hoạt'),
      'password_again' => $i18n->__('Nhập lại mật khẩu'),
      'is_super_admin' => $i18n->__('Siêu quản trị'),
      'permissions_list' => $i18n->__('Danh sách quyền'),
    ));

    $this->mergePostValidator(new sfValidatorCallback(array(
      'callback' => array($this, 'checkValue')
    )));
  }

  public function processValues($values)
  {
    $values = parent::processValues($values); // TODO: Change the autogenerated stub
    if(!sfContext::getInstance()->getUser()->checkPermission('admin')){
      $values["is_super_admin"] = 0;
    }

    return $values;
  }

  public function checkValue($validator, $values)
  {
    $i18n = sfContext::getInstance()->getI18N();
    if(isset($values['password']) && $values['password']){
      if(VtHelper::checkWeakPass($values['password'])){
        $error = new sfValidatorError($validator, $i18n->__('Mật khẩu của bạn chưa cụm từ dễ đoán, vui lòng nhập mật khẩu mạnh hơn.'));
        throw new sfValidatorErrorSchema($validator, array('password' => $error));
      }
    }

    return $values;
  }

}