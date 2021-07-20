<?php

require_once dirname(__FILE__).'/../lib/vtHomePageGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/vtHomePageGeneratorHelper.class.php';

/**
 * HomePage actions.
 *
 * @package    mobiletv
 * @subpackage HomePage
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php_bak.bak 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class vtHomePageActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
//    if ($request->hasParameter('username')) {
//
//      $user = $this->getUser()->getGuardUser();
////      $this->redirect('@sf_guard_user_vtManageUserInfo_edit?id=' . $user->getId());
//        $this->redirect('@sf_guard_user_edit?id=' . $user->getId());
//    }
//    var_dump(VtHelper::decrypt('L5gE21fskvfXX2d8LIRmRib%2FhBMWJpBZVqyULv36%2BYI%3D','RVN0103045'));die;
    $this->header = sfConfig::get('app_tmcTwitterBootstrapPlugin_header', array());
    $this->setVar('menus', array_key_exists('menu', $this->header) ? $this->header['menu'] : array('Home' => 'homepage'), true);
    $this->setVar('routes', $this->getContext()->getRouting()->getRoutes(), true);
    $this->current_route = $this->getContext()->getRouting()->getCurrentRouteName();
  }
}
