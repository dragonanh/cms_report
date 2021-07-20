<?php

/**
 * PHP Captcha Form Validator
 *
 * @package    sfPHPCaptchaPlugin
 * @subpackage form
 * @author     Sven Wappler <info@wapplersystems.de>
 */
class sfCaptchaGDValidator extends sfValidatorBase {


  protected function configure($options = array(), $messages = array()) {
    $this->addOption('namespace');
    parent::configure($options, $messages);
  }

  protected function doClean($value) {

    $value = (string) $value;

    $img = new Securimage();
    if($this->hasOption('namespace'))
      $img->namespace = $this->getOption('namespace');
    $valid = $img->check($value);
    if($valid == false){
      throw new sfValidatorError($this, 'invalid', array('value' => $value));
    }else{
      return true;
    }

  }


}

