<?php

require_once dirname(__FILE__).'/../lib/vtOmniRevenueReportGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/vtOmniRevenueReportGeneratorHelper.class.php';

/**
 * vtOmniRevenueReport actions.
 *
 * @package    cms_ctt
 * @subpackage vtOmniRevenueReport
 * @author     viettel
 * @version    SVN: $Id: actions.class.php_bak.bak 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class vtOmniRevenueReportActions extends autoVtOmniRevenueReportActions
{
  public function executeFilter(sfWebRequest $request)
  {
    $this->setPage(1);

    if ($request->hasParameter('_reset'))
    {
      $this->setFilters($this->configuration->getFilterDefaults());

      $this->redirect('@vt_ctt_transaction_vtOmniRevenueReport');
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
      if($request->hasParameter('export')){
        $this->exportExcel();
      }
      $this->redirect('@vt_ctt_transaction_vtOmniRevenueReport');
    }
    $this->sidebar_status = $this->configuration->getListSidebarStatus();
    $this->pager = $this->getPager();
    $this->sort = $this->getSort();

    $this->setTemplate('index');
  }

  public function exportExcel(){
    $filterValues = $this->getFilters();
    $from = !empty($filterValues['process_time']['from']) ? date('d-m-Y',strtotime($filterValues['process_time']['from'])) : '';
    $to = !empty($filterValues['process_time']['to']) ? date('d-m-Y',strtotime($filterValues['process_time']['to'])) : '';
    $i18n = $this->getContext()->getI18N();
    $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'BIỂU MẪU ĐỐI SOÁT DOANH THU TOPUP');
    $sheet->getStyle('A1')->getFont()->setBold(true);

    $sheet->setCellValue('A2', $i18n->__('Từ ngày %from% Đến ngày %to%', ['%from%' => $from, '%to%' => $to]));
    $sheet->setCellValue('A4', $i18n->__('STT'));
    $sheet->setCellValue('B4', $i18n->__('Số thuê bao đăng nhập vào My Viettel'));
    $sheet->setCellValue('C4', $i18n->__('Số thuê bao đặt mua'));
    $sheet->setCellValue('D4', $i18n->__('Mã gói cước đi kèm'));
    $sheet->setCellValue('E4', $i18n->__('Mã dịch vụ đi kèm'));
    $sheet->setCellValue('F4', $i18n->__('Kênh bán dịch vụ'));
    $sheet->setCellValue('G4', $i18n->__('Thời gian gửi đơn hàng trên MyViettel'));
    $sheet->setCellValue('H4', $i18n->__('Thời gian charge cước bên cổng thanh toán'));
    $sheet->setCellValue('I4', $i18n->__('Mã giao dịch (Mã sinh ra từ cổng thanh toán)'));
    $sheet->setCellValue('J4', $i18n->__('Mã thanh toán (mã sinh ra từ My Viettel)'));
    $sheet->setCellValue('K4', $i18n->__('Mã đơn hàng omni'));
    $sheet->setCellValue('L4', $i18n->__('Dữ liệu giao dịch trên My Viettel '));
    $sheet->setCellValue('L5', $i18n->__('Tiền sim'));
    $sheet->setCellValue('M5', $i18n->__('Tiền gói cước'));
    $sheet->setCellValue('N5', $i18n->__('Tiền VAS'));
    $sheet->setCellValue('O5', $i18n->__('Phí vận chuyển'));
    $sheet->setCellValue('P5', $i18n->__('Số tiền phải thu'));
    $sheet->setCellValue('Q5', $i18n->__('Trạng thái'));

    $sheet->getStyle('A4:Q5')->applyFromArray(
      array(
        'font'  => array(
          'bold'  =>  true
        )
      )
    );

    $sheet->mergeCells('A1:Q1');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $sheet->mergeCells('A2:Q2');
    $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    for ($i='A'; $i <= 'K'; $i++){
      $sheet->mergeCells(sprintf('%s4:%s5', $i, $i));
      $sheet->getStyle($i.'4')->getAlignment()->setWrapText(true);
      if(in_array($i, ['E','G','H','I','J','K']))
        $sheet->getColumnDimension($i)->setWidth(20);
      elseif($i != 'A')
        $sheet->getColumnDimension($i)->setWidth(15);
    }

    $sheet->mergeCells('L4:Q4');
    $sheet->getStyle('L4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    for($i = 'L'; $i <= 'Q'; $i++){
      $sheet->getStyle($i.'5')->getAlignment()->setWrapText(true);
      $sheet->getColumnDimension($i)->setWidth(15);
    }

    $results = $this->buildQuery()->fetchArray();
    $startRow = 5;
    foreach ($results as $key => $result){
      $startRow++;
      $content = json_decode($result['content']);
      $listVas = $content->productInfo->vasInfos;
      $listVasCode = [];
      $vasPrice = 0;
      if(!empty($listVas)) {
        foreach ($listVas as $vas) {
          $listVasCode[] = $vas->vasCode;
          $vasPrice += $vas->price;
        }
      }

      $transportFee = 0;
      foreach ($content->feeRecords as $feeRecord){
        if($feeRecord->feeCode == 'TRANSFER_FEE'){
          $transportFee += $feeRecord->feeAmount;
          break;
        }
      }

      if($result['order_type'] == OrderTypeEnum::PREPAID) {
        $simPrice = $content->isdnPledgeInfo->price;
        $mainPackagePrice = $content->productInfo->price;
      }else{
        $simPrice = $content->isdnPledgeInfo->posPrice;
        $mainPackagePrice = $vasPrice = 0;
      }

      $status = $result['omni_error_code'] == 0 ? $i18n->__('success') : $i18n->__('fail');

      $sheet->setCellValue("A$startRow", ++$key);
      $sheet->setCellValue("B$startRow", $result['isdn']);
      $sheet->setCellValue("C$startRow", $content->isdn);
      $sheet->setCellValue("D$startRow", $content->productInfo->bundleCode);
      $sheet->setCellValue("E$startRow", implode(', ',$listVasCode));
      $sheet->setCellValue("F$startRow", $result['source']);
      $sheet->setCellValue("G$startRow", $result['updated_at']);
//      $sheet->setCellValue("H$startRow", $result['updated_at']);
      $sheet->setCellValue("I$startRow", $result['ctt_id']);
      $sheet->setCellValue("J$startRow", $result['tran_id']);
      $sheet->setCellValue("K$startRow", $result['omni_order_code']);
      $sheet->setCellValue("L$startRow", $simPrice);
      $sheet->setCellValue("M$startRow", $mainPackagePrice);
      $sheet->setCellValue("N$startRow", $vasPrice);
      $sheet->setCellValue("O$startRow", $transportFee);
      $sheet->setCellValue("P$startRow", $result['amount']);
      $sheet->setCellValue("Q$startRow", $status);
    }

    $sheet->getStyle('A4:P'.$startRow)->applyFromArray(
      array(
        'borders' => array(
          'allBorders' => array(
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            'color' => array('argb' => '000000'),
          ),
        ),
      )
    );

    $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $fileName = 'omni_revenue_report_'.date('YmdHis').'.xlsx';
    $filePath = sfConfig::get('sf_web_dir').'/export/'.$fileName;
    $writer->save($filePath);

    header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Pragma: public");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    ob_end_clean();
    ob_start();
    readfile($filePath);
    $size = ob_get_length();
    header("Content-Length: $size");
    ob_end_flush();
    unlink($filePath);
    die;
  }
}
