<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Alex Kubyshkin <glint@techinfo.net.ru>
 */
class sfCaptchaGDRouting
{
  /**
   * Listens to the routing.load_configuration event.
   *
   * @param sfEvent An sfEvent instance
   */
  static public function listenToRoutingLoadConfigurationEvent(sfEvent $event)
  {
    $r = $event->getSubject();

    // preprend our routes
//    $r->prependRoute('sf_captchagd', '/captcha', array('module' => 'sfCaptchaGD', 'action' => 'show_image'));
    $r->prependRoute('sf_captchagd', new sfRequestRoute('/captcha', array('module' => 'sfCaptchaGD', 'action' => 'showImage')));
    $r->prependRoute('sf_captchagd_easy', new sfRequestRoute('/captcha1', array('module' => 'sfCaptchaGD', 'action' => 'showImageEasy')));
    $r->prependRoute('sf_captchagd_4g', new sfRequestRoute('/4g/captcha', array('module' => 'sfCaptchaGD', 'action' => 'showImageEasy')));
  }
}
