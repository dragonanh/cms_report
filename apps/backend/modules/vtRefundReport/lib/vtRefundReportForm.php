<?php
/**
 * Created by PhpStorm.
 * User: halt14
 * Date: 11/15/2016
 * Time: 2:28 PM
 */
class vtRefundReportForm extends sfForm
{
    public function configure()
    {
        parent::configure();

        $this->widgetSchema->setNameFormat('refund-form[%s]');
    }


}