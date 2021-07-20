<?php

require_once dirname(__FILE__).'/../lib/FlashSaleLogGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/FlashSaleLogGeneratorHelper.class.php';

/**
 * FlashSaleLog actions.
 *
 * @package    cms_ctt
 * @subpackage FlashSaleLog
 * @author     viettel
 * @version    SVN: $Id: actions.class.php_bak.bak 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class FlashSaleLogActions extends autoFlashSaleLogActions
{
  public function executeFilter(sfWebRequest $request)
  {
    $this->setPage(1);

    if ($request->hasParameter('_reset'))
    {
      $this->setFilters($this->configuration->getFilterDefaults());

      $this->redirect('@flash_sale_log');
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
      $this->redirect('@flash_sale_log');
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
    $sheet->setCellValue('A1', $i18n->__('Quản lý giao dịch chương trình Flash sale'));
    $sheet->getStyle('A1')->getFont()->setBold(true);

    $sheet->setCellValue('A2', $i18n->__('Từ ngày %from% Đến ngày %to%', ['%from%' => $from, '%to%' => $to]));
    $sheet->setCellValue('A4', $i18n->__('STT'));
    $sheet->setCellValue('B4', $i18n->__('Hệ thống My Viettel'));
    $sheet->setCellValue('B5', $i18n->__('Số thuê bao'));
    $sheet->setCellValue('C5', $i18n->__('Mã gói cước'));
    $sheet->setCellValue('D5', $i18n->__('Kênh đăng ký gói cước'));
    $sheet->setCellValue('E5', $i18n->__('Serial thẻ cào'));
    $sheet->setCellValue('F5', $i18n->__('Trạng thái quét tặng thẻ'));
    $sheet->setCellValue('G5', $i18n->__('Ngày đăng ký gói'));


    $sheet->getStyle('A4:G5')->applyFromArray(
      array(
        'font'  => array(
          'bold'  =>  true
        )
      )
    );

    $sheet->mergeCells('A1:G1');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $sheet->mergeCells('A2:G2');
    $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $sheet->mergeCells('A4:A5');
    $sheet->mergeCells('B4:G4');
    $sheet->getStyle('B4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $sheet->getColumnDimension('A4')->setWidth(15);

    for ($i='B'; $i <= 'G'; $i++){
      $sheet->getStyle($i.'5')->getAlignment()->setWrapText(true);
      if(in_array($i, ['E','G']))
        $sheet->getColumnDimension($i)->setWidth(20);
      else
        $sheet->getColumnDimension($i)->setWidth(15);
    }

    $results = $this->buildQuery()->execute();
    $startRow = 5;
    foreach ($results as $key => $result){
      $startRow++;
      $sheet->setCellValue("A$startRow", ++$key);
      $sheet->setCellValue("B$startRow", $result->getMsisdn());
      $sheet->setCellValue("C$startRow", $result->getPackCode());
      $sheet->setCellValue("D$startRow", $result->getAppCode());
      $sheet->setCellValueExplicit("E$startRow", $result->getSerial(),'f');
      $sheet->setCellValue("F$startRow", $result->getProcessedName());
      $sheet->setCellValue("G$startRow", $result->getCreatedAt());
    }

    $sheet->getStyle('A4:G'.$startRow)->applyFromArray(
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
    $fileName = 'report_flash_sale'.date('YmdHis').'.xlsx';
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
