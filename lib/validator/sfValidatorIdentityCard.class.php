<?php
/**
 * Created by JetBrains PhpStorm.
 * User: tuanbm
 * Date: 9/15/12
 * Time: 1:07 PM
 * To change this template use File | Settings | File Templates.
 */
class sfValidatorIdentityCard extends sfValidatorNumber
{
  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);
    $this->addOption('payType');
  }

  protected function doClean($value)
  {
    //if (!ctype_digit($value))
    //if (!preg_match('/^[a-zA-Z0-9]+$/', $value))
    if (!ctype_alnum($value))
    {
      throw new sfValidatorError($this, 'invalid', array('value' => $value));
    }

//    $clean = floatval($value);tuanbm return

    $length = function_exists('mb_strlen') ? mb_strlen($value, $this->getCharset()) : strlen($value);

    if ($this->hasOption('max') && $length > $this->getOption('max'))
    {
      throw new sfValidatorError($this, 'max', array('value' => $value, 'max' => $this->getOption('max')));
    }
    if ($this->hasOption('min') && $length < $this->getOption('min'))
    {
      throw new sfValidatorError($this, 'min', array('value' => $value, 'min' => $this->getOption('min')));
    }

    if(sfConfig::get('app_enable_check_identity')) {
      $payType = VtSimOrderRegisterTypeEnum::convertToPayType($this->getOption('payType'));
      $result = VtSimOrder::checkIndentityCard($value, $payType);
      $logger = VtHelper::getLogger4Php("vtsim");
      $logger->info("Validate:" . var_export($result, true));
      $errorCode = $result["errorCode"];
      if ($errorCode != "0") {
        //$message = $result["message"];
        throw new sfValidatorError($this, 'invalid2', array('value' => $value));
      }
    }
    return $value;
  }
}