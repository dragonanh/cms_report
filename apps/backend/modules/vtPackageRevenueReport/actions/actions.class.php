<?php

require_once dirname(__FILE__).'/../lib/vtPackageRevenueReportGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/vtPackageRevenueReportGeneratorHelper.class.php';

/**
 * vtPackageRevenueReport actions.
 *
 * @package    cms_ctt
 * @subpackage vtPackageRevenueReport
 * @author     viettel
 * @version    SVN: $Id: actions.class.php_bak.bak 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class vtPackageRevenueReportActions extends autoVtPackageRevenueReportActions
{
  public function executeFilter(sfWebRequest $request)
  {
    $this->setPage(1);

    if ($request->hasParameter('_reset'))
    {
      $this->setFilters($this->configuration->getFilterDefaults());

      $this->redirect('@vt_ctt_transaction_vtPackageRevenueReport');
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
      $this->redirect('@vt_ctt_transaction_vtPackageRevenueReport');
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
    $sheet->setCellValue('A1', $i18n->__('BÁO CÁO DOANH THU ĐĂNG KÝ DỊCH VỤ DATA, VAS'));
    $sheet->getStyle('A1')->getFont()->setBold(true);

    $sheet->setCellValue('A2', $i18n->__('Từ ngày %from% Đến ngày %to%', ['%from%' => $from, '%to%' => $to]));
    $sheet->setCellValue('A4', $i18n->__('STT'));
    $sheet->setCellValue('B4', $i18n->__('Số thuê bao đăng ký dịch vụ'));
    $sheet->setCellValue('C4', $i18n->__('Mã trừ cước'));
    $sheet->setCellValue('D4', $i18n->__('Loại dịch vụ'));
    $sheet->setCellValue('E4', $i18n->__('Thời gian đăng ký trên My Viettel'));
    $sheet->setCellValue('F4', $i18n->__('Thời gian charge cước trên cổng thanh toán'));
    $sheet->setCellValue('G4', $i18n->__('Kênh bán dịch vụ'));
    $sheet->setCellValue('H4', $i18n->__('Mã giao dịch (mã sinh ra từ cổng thanh toán)'));
    $sheet->setCellValue('I4', $i18n->__('Mã thanh toán (mã sinh ra từ My Viettel)'));
    $sheet->setCellValue('J4', $i18n->__('Dữ liệu giao dịch trên My Viettel '));
    $sheet->setCellValue('J5', $i18n->__('Mênh giá'));
    $sheet->setCellValue('K5', $i18n->__('Chiết khấu'));
    $sheet->setCellValue('L5', $i18n->__('Phí dịch vụ'));
    $sheet->setCellValue('M5', $i18n->__('Thuế VAT (10%)'));
    $sheet->setCellValue('N5', $i18n->__('Tổng tiền thu'));
    $sheet->setCellValue('O5', $i18n->__('Trạng thái đăng ký'));


    $sheet->getStyle('A4:O5')->applyFromArray(
      array(
        'font'  => array(
          'bold'  =>  true
        )
      )
    );

    $sheet->mergeCells('A1:O1');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $sheet->mergeCells('A2:O2');
    $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $sheet->mergeCells('J4:O4');
    $sheet->getStyle('J4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    for ($i='A'; $i <= 'I'; $i++){
      $sheet->mergeCells(sprintf('%s4:%s5', $i, $i));
      $sheet->getStyle($i.'4')->getAlignment()->setWrapText(true);
      if(in_array($i, ['E','I','H']))
        $sheet->getColumnDimension($i)->setWidth(20);
      elseif($i != 'A')
        $sheet->getColumnDimension($i)->setWidth(15);
    }
    for ($i='J'; $i <= 'O'; $i++){
      $sheet->getStyle($i.'4')->getAlignment()->setWrapText(true);
      if(in_array($i, ['O']))
        $sheet->getColumnDimension($i)->setWidth(20);
      else
        $sheet->getColumnDimension($i)->setWidth(12);
    }

    $results = $this->buildQuery()->fetchArray();
    $startRow = 5;
    foreach ($results as $key => $result){
      $startRow++;
      $statusName = $result['omni_error_code'] == 0 ? $i18n->__('success') : $i18n->__('fail');
      $sheet->setCellValue("A$startRow", ++$key);
      $sheet->setCellValue("B$startRow", $result['isdn']);
      $sheet->setCellValue("C$startRow", $result['ctt_package']);
      $sheet->setCellValue("D$startRow", $result['service_pay']);
      $sheet->setCellValue("E$startRow", $result['updated_at']);
      $sheet->setCellValue("G$startRow", $result['source']);
      $sheet->setCellValue("H$startRow", $result['ctt_id']);
      $sheet->setCellValue("I$startRow", $result['tran_id']);
      $sheet->setCellValue("J$startRow", $result['amount']);
      $sheet->setCellValue("N$startRow", $result['amount']);
      $sheet->setCellValue("O$startRow", $statusName);
    }

    $sheet->getStyle('A4:O'.$startRow)->applyFromArray(
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
    $fileName = 'package_revenue_report_'.date('YmdHis').'.xlsx';
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
