<?php

require_once dirname(__FILE__).'/../lib/MerchantOrderReportGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/MerchantOrderReportGeneratorHelper.class.php';

/**
 * MerchantOrderReport actions.
 *
 * @package    cms_ctt
 * @subpackage MerchantOrderReport
 * @author     viettel
 * @version    SVN: $Id: actions.class.php_bak.bak 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class MerchantOrderReportActions extends autoMerchantOrderReportActions
{
  public function executeFilter(sfWebRequest $request)
  {
    $this->setPage(1);

    if ($request->hasParameter('_reset'))
    {
      $this->setFilters($this->configuration->getFilterDefaults());

      $this->redirect('@merchant_order');
    }

    $this->filters = $this->configuration->getFilterForm($this->getFilters());
    //Chuyennv2 trim du lieu
    $filterValues = $request->getParameter($this->filters->getName());
    foreach ($filterValues as $key => $value)
    {
      if (isset($filterValues[$key]['text']))
      {
        $filterValues[$key]['text'] = trim($filterValues[$key]['text']);
      }
    }

    $this->filters->bind($filterValues);
    if ($this->filters->isValid())
    {
      $this->setFilters($this->filters->getValues());
      if ($request->hasParameter('export')) {
        $this->exportExcel();
      }
      $this->redirect('@merchant_order');
    }
    $this->sidebar_status = $this->configuration->getListSidebarStatus();
    $this->pager = $this->getPager();
    $this->sort = $this->getSort();

    $this->setTemplate('index');
  }

  public function exportExcel()
  {
    $fileDesName = date('YmdHis'). rand(100,999) . "_merchant_order.xlsx";
    $filePath = sfConfig::get('sf_log_dir') . '/' . $fileDesName;
    $header = array(
      'transaction_id','sub_id','merchant_code' ,'myvt_account','order_time','status','payment_status','order_code' ,'customer_name' ,
      'customer_phone','base_price'  ,'price','product_name','quantity','product_price' ,'category','discount','discount_price'
    );
    $writer = new spoutHelper($filePath);
    $writer->writeHeaderRow($header);

    $results = $this->buildQuery()->fetchArray();

    foreach ($results as $key => $result) {
      $row = [
        $result['transaction_id'],$result['sub_id'],$result['merchant_code'],$result['myvt_account'],$result['order_time'],$result['status'],
        $result['payment_status'],$result['order_code'],$result['customer_name'],$result['customer_phone'],$result['base_price'],$result['price'],
        $result['product_name'],$result['quantity'],$result['product_price'],$result['category'],$result['discount'],$result['discount_price']
      ];
      $writer->writeRow($row);
    }
    $writer->close();

    header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Pragma: public");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header(sprintf('Content-Disposition: attachment; filename="%s"', $fileDesName));
    ob_end_clean();
    ob_start();
    readfile($filePath);
    $size = ob_get_length();
    header("Content-Length: $size");
    ob_end_flush();
    unlink($filePath);
    return;
  }
}
