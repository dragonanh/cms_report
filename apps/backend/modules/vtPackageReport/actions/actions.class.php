<?php

require_once dirname(__FILE__).'/../lib/vtPackageReportGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/vtPackageReportGeneratorHelper.class.php';

/**
 * vtPackageReport actions.
 *
 * @package    cms_ctt
 * @subpackage vtPackageReport
 * @author     viettel
 * @version    SVN: $Id: actions.class.php_bak.bak 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class vtPackageReportActions extends autoVtPackageReportActions
{
  public function executeFilter(sfWebRequest $request)
  {
    $this->setPage(1);

    if ($request->hasParameter('_reset'))
    {
      $this->setFilters($this->configuration->getFilterDefaults());

      $this->redirect('@vt_ctt_transaction_vtPackageReport');
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
      $this->redirect('@vt_ctt_transaction_vtPackageReport');
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
    $sheet->setCellValue('A1', $i18n->__('THỐNG KÊ KH GIAO DỊCH MUA DỊCH VỤ DATA, VAS'));
    $sheet->getStyle('A1')->getFont()->setBold(true);

    $sheet->setCellValue('A2', $i18n->__('Từ ngày %from% Đến ngày %to%', ['%from%' => $from, '%to%' => $to]));
    $sheet->setCellValue('A4', $i18n->__('STT'));
    $sheet->setCellValue('B4', $i18n->__('Hệ thống My Viettel'));
    $sheet->setCellValue('B5', $i18n->__('Số thuê bao đăng ký dịch vụ'));
    $sheet->setCellValue('C5', $i18n->__('Mã dịch vụ'));
    $sheet->setCellValue('D5', $i18n->__('Loại dịch vụ'));
    $sheet->setCellValue('E5', $i18n->__('Kênh'));
    $sheet->setCellValue('F5', $i18n->__('Tổng tiền'));
    $sheet->setCellValue('G5', $i18n->__('Thời gian đăng ký'));
    $sheet->setCellValue('H5', $i18n->__('Trạng thái đăng ký'));
    $sheet->setCellValue('I5', $i18n->__('Mã giao dịch trên My Viettel'));


    $sheet->getStyle('A4:I5')->applyFromArray(
      array(
        'font'  => array(
          'bold'  =>  true
        )
      )
    );

    $sheet->mergeCells('A1:I1');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $sheet->mergeCells('A2:I2');
    $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $sheet->mergeCells('A4:A5');
    $sheet->mergeCells('B4:I4');
    $sheet->getStyle('B4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $sheet->getColumnDimension('A4')->setWidth(15);

    for ($i='B'; $i <= 'I'; $i++){
      $sheet->getStyle($i.'5')->getAlignment()->setWrapText(true);
      if(in_array($i, ['I','H']))
        $sheet->getColumnDimension($i)->setWidth(20);
      else
        $sheet->getColumnDimension($i)->setWidth(15);
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
      $sheet->setCellValue("E$startRow", $result['source']);
      $sheet->setCellValue("F$startRow", $result['amount']);
      $sheet->setCellValue("G$startRow", $result['updated_at']);
      $sheet->setCellValue("H$startRow", $statusName);
      $sheet->setCellValue("I$startRow", $result['tran_id']);
    }

    $sheet->getStyle('A4:I'.$startRow)->applyFromArray(
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
    $fileName = 'package_report_'.date('YmdHis').'.xlsx';
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
