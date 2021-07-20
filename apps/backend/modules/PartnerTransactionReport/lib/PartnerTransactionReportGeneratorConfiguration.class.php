<?php

/**
 * PartnerTransactionReport module configuration.
 *
 * @package    cms_ctt
 * @subpackage PartnerTransactionReport
 * @author     viettel
 * @version    SVN: $Id: configuration.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class PartnerTransactionReportGeneratorConfiguration extends BasePartnerTransactionReportGeneratorConfiguration
{
    public function getFilterDefaults()
    {
        return array("process_time" => ["from" => date("Y-m-d 00:00:00"), "to" => date("Y-m-d 23:59:59")]);
    }
}