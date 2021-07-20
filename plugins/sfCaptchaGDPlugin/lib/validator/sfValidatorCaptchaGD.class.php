<?php

/**
 * PHP Captcha Form Validator
 *
 * @package    sfPHPCaptchaPlugin
 * @subpackage form
 * @author     Sven Wappler <info@wapplersystems.de>
 */
class sfValidatorCaptchaGD extends sfValidatorBase {


  protected function configure($options = array(), $messages = array()) {
    parent::configure($options, $messages);
  }

  protected function doClean($value) {

    $value = (string) $value;

    $img = new Securimage();

    $valid = $img->check($value);

    if($valid == false){
      throw new sfValidatorError($this, 'invalid', array('value' => $value));
    }else{
      return true;
    }

  }


}

