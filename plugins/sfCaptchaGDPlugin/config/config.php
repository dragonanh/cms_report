<?php
//include_once $_SERVER['DOCUMENT_ROOT'] . '/securimage/securimage.php';
if (sfConfig::get('app_sfCaptchaGDPlugin_routes_register', true) && in_array('sfCaptchaGD', sfConfig::get('sf_enabled_modules')))
{
  $this->dispatcher->connect('routing.load_configuration', array('sfCaptchaGDRouting', 'listenToRoutingLoadConfigurationEvent'));
}
