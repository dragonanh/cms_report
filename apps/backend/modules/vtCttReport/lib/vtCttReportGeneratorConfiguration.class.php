<?php

/**
 * vtCttReport module configuration.
 *
 * @package    cms_ctt
 * @subpackage vtCttReport
 * @author     viettel
 * @version    SVN: $Id: configuration.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class vtCttReportGeneratorConfiguration extends BaseVtCttReportGeneratorConfiguration
{
  public function getFilterDefaults()
  {
    return array("process_time" => ["from" => date("Y-m-d 00:00:00"), "to" => date("Y-m-d 23:59:59")]);
  }
}