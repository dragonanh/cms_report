<?php

/**
 * vtManageBackendUser module helper.
 *
 * @package    imuzik
 * @subpackage vtManageBackendUser
 * @author     Your name here
 * @version    SVN: $Id: helper.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class vtManageBackendUserGeneratorHelper extends BasevtManageBackendUserGeneratorHelper
{
   public function linkToSaveAndExit($object, $params)
  {
    return '<input type="submit" class= "btn btn-primary" value="'.__($params['label'], array(), 'sf_admin').'" name="_save_and_exit" />';
  }
}
