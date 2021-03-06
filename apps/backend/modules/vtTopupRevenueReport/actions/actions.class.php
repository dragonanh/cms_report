<?php

require_once dirname(__FILE__).'/../lib/vtTopupRevenueReportGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/vtTopupRevenueReportGeneratorHelper.class.php';

/**
 * vtTopupRevenueReport actions.
 *
 * @package    cms_ctt
 * @subpackage vtTopupRevenueReport
 * @author     viettel
 * @version    SVN: $Id: actions.class.php_bak.bak 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class vtTopupRevenueReportActions extends autoVtTopupRevenueReportActions
{
  public function executeFilter(sfWebRequest $request)
  {
    $this->setPage(1);

    if ($request->hasParameter('_reset'))
    {
      $this->setFilters($this->configuration->getFilterDefaults());

      $this->redirect('@vt_vpg_transaction_vtTopupRevenueReport');
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
      $this->redirect('@vt_vpg_transaction_vtTopupRevenueReport');
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
    $sheet->setCellValue('A1', 'BI???U M???U ?????I SO??T DOANH THU TOPUP');
    $sheet->getStyle('A1')->getFont()->setBold(true);

    $sheet->setCellValue('A2', $i18n->__('T??? ng??y %from% ?????n ng??y %to%', ['%from%' => $from, '%to%' => $to]));
    $sheet->setCellValue('A4', $i18n->__('STT'));
    $sheet->setCellValue('B4', $i18n->__('S??? thu?? bao ????ng nh???p v??o My Viettel'));
    $sheet->setCellValue('C4', $i18n->__('S??? thu?? bao/ t??i kho???n (Viettelpay/NH) th???c hi???n thanh to??n'));
    $sheet->setCellValue('D4', $i18n->__('S??? thu?? bao ???????c n???p ti???n'));
    $sheet->setCellValue('E4', $i18n->__('Tr???ng th??i'));
    $sheet->setCellValue('F4', $i18n->__('Th???i gian g???i ????n h??ng tr??n My Viettel'));
    $sheet->setCellValue('G4', $i18n->__('Th???i gian charge c?????c tr??n c???ng thanh to??n'));
    $sheet->setCellValue('H4', $i18n->__('M?? giao d???ch (M?? sinh ra t??? c???ng thanh to??n)'));
    $sheet->setCellValue('I4', $i18n->__('M?? thanh to??n (m?? sinh ra t??? My Viettel)'));
    $sheet->setCellValue('J4', $i18n->__('My Viettel'));
    $sheet->setCellValue('J5', $i18n->__('M???nh gi??'));
    $sheet->setCellValue('K5', $i18n->__('Chi???t kh???u'));
    $sheet->setCellValue('L5', $i18n->__('Ph?? d???ch v???'));
    $sheet->setCellValue('M5', $i18n->__('S??? ti???n'));
    $sheet->setCellValue('N5', $i18n->__('Tr???ng th??i'));

    $sheet->getStyle('A4:N5')->applyFromArray(
      array(
        'font'  => array(
          'bold'  =>  true
        )
      )
    );

    $sheet->mergeCells('A1:N1');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $sheet->mergeCells('A2:N2');
    $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    for ($i='A'; $i <= 'I'; $i++){
      $sheet->mergeCells(sprintf('%s4:%s5', $i, $i));
      $sheet->getStyle($i.'4')->getAlignment()->setWrapText(true);
      if(in_array($i, ['G','H','I']))
        $sheet->getColumnDimension($i)->setWidth(20);
      elseif($i != 'A')
        $sheet->getColumnDimension($i)->setWidth(15);
    }

    $sheet->mergeCells('J4:N4');
    $sheet->getStyle('J4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    for($i = 'J'; $i <= 'N'; $i++){
      $sheet->getStyle($i.'5')->getAlignment()->setWrapText(true);
      if($i == 'N')
        $sheet->getColumnDimension($i)->setWidth(20);
      else
        $sheet->getColumnDimension($i)->setWidth(15);
    }

    $results = $this->buildQuery()->fetchArray();
    $startRow = 5;
    foreach ($results as $key => $result){
      $startRow++;
      $statusName = VtVpgStatusEnum::getStatusName($result['status']);
      $sheet->setCellValue("A$startRow", ++$key);
      $sheet->setCellValue("B$startRow", $result['isdn_login']);
      $sheet->setCellValue("C$startRow", $result['calling']);
      $sheet->setCellValue("D$startRow", $result['isdn']);
      $sheet->setCellValue("E$startRow", $i18n->__('success'));
      $sheet->setCellValue("F$startRow", $result['order_time']);
      $sheet->setCellValue("G$startRow", $result['created_at']);
      $sheet->setCellValue("H$startRow", $result['ctt_id']);
      $sheet->setCellValue("I$startRow", $result['tran_id']);
      $sheet->setCellValue("J$startRow", $result['amount']);
      $sheet->setCellValue("M$startRow", $result['amount']);
      $sheet->setCellValue("N$startRow", $statusName);
    }

    $sheet->getStyle('A4:N'.$startRow)->applyFromArray(
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
    $fileName = 'topup_report_'.date('YmdHis').'.xlsx';
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
