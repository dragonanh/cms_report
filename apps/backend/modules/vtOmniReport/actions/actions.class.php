<?php

require_once dirname(__FILE__).'/../lib/vtOmniReportGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/vtOmniReportGeneratorHelper.class.php';

/**
 * vtOmniReport actions.
 *
 * @package    cms_ctt
 * @subpackage vtOmniReport
 * @author     viettel
 * @version    SVN: $Id: actions.class.php_bak.bak 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class vtOmniReportActions extends autoVtOmniReportActions
{
  public function executeFilter(sfWebRequest $request)
  {
    $this->setPage(1);

    if ($request->hasParameter('_reset'))
    {
      $this->setFilters($this->configuration->getFilterDefaults());

      $this->redirect('@vt_ctt_transaction');
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
      $this->redirect('@vt_ctt_transaction');
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
    $sheet->setCellValue('A1', 'THỐNG KÊ GIAO DỊCH MUA SIM');
    $sheet->getStyle('A1')->getFont()->setBold(true);
    $sheet->mergeCells('A1:O1');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    $sheet->setCellValue('A2', $i18n->__('Từ ngày %from% Đến ngày %to%', ['%from%' => $from, '%to%' => $to]));
    $sheet->mergeCells('A2:O2');
    $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    $sheet->setCellValue('A3', $i18n->__('Dữ liệu trên My Viettel'));
    $sheet->mergeCells('A3:O3');
    $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    $sheet->setCellValue('A4', $i18n->__('STT'));
    $sheet->setCellValue('B4', $i18n->__('Số thuê bao login vào My Viettel'));
    $sheet->setCellValue('C4', $i18n->__('Số thuê bao đăng ký mua'));
    $sheet->setCellValue('D4', $i18n->__('Số thuê bao liên hệ'));
    $sheet->setCellValue('E4', $i18n->__('Số tiền mua sim'));
    $sheet->setCellValue('F4', $i18n->__('Số tiền mua gói cước'));
    $sheet->setCellValue('G4', $i18n->__('Số tiền mua dịch vụ khác'));
    $sheet->setCellValue('H4', $i18n->__('Phí giao hàng'));
    $sheet->setCellValue('I4', $i18n->__('Tổng tiền charge'));
    $sheet->setCellValue('J4', $i18n->__('Thời gian gửi đơn hàng'));
    $sheet->setCellValue('K4', $i18n->__('Trạng thái'));
    $sheet->setCellValue('L4', $i18n->__('Mã thanh toán (mã sinh ra từ my viettel)'));
    $sheet->setCellValue('M4', $i18n->__('Mã đơn hàng omni'));
    $sheet->setCellValue('N4', $i18n->__('Hình thức nhận hàng'));
    $sheet->setCellValue('O4', $i18n->__('Kênh đăng ký'));

    $sheet->getStyle('A3:O4')->applyFromArray(
      array(
        'font'  => array(
          'bold'  =>  true
        )
      )
    );

    for($i = 'A'; $i <= 'O'; $i++){
      $sheet->getStyle($i.'4')->getAlignment()->setWrapText(true);
      if($i == 'L')
        $sheet->getColumnDimension($i)->setWidth(20);
      else
        $sheet->getColumnDimension($i)->setWidth(15);
    }

    $results = $this->buildQuery()->execute();
    $startRow = 4;
    foreach ($results as $key => $result){
      $startRow++;
      $sheet->setCellValue("A$startRow", ++$key);
      $sheet->setCellValue("B$startRow", $result->getIsdn());
      $sheet->setCellValue("C$startRow", $result->getSimNumber());
      $sheet->setCellValue("D$startRow", $result->getContactPhone());
      $sheet->setCellValue("E$startRow", $result->getSimPrice());
      $sheet->setCellValue("F$startRow", $result->getMainPackagePrice());
      $sheet->setCellValue("G$startRow", $result->getOtherServicePrice());
      $sheet->setCellValue("H$startRow", $result->getTransportFee());
      $sheet->setCellValue("I$startRow", $result->getAmount());
      $sheet->setCellValue("J$startRow", $result->getUpdatedAt());
      $sheet->setCellValue("K$startRow", $result->getOmniErrorCodeName());
      $sheet->setCellValue("L$startRow", $result->getTranId());
      $sheet->setCellValue("M$startRow", $result->getOmniOrderCode());
      $sheet->setCellValue("N$startRow", $result->getReceiveTypeName());
      $sheet->setCellValue("O$startRow", $result->getSource());
    }

    $sheet->getStyle('A3:O'.$startRow)->applyFromArray(
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
    $fileName = 'omni_report_'.date('YmdHis').'.xlsx';
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
