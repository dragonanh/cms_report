<?php

require_once dirname(__FILE__).'/../lib/vtMnpReportGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/vtMnpReportGeneratorHelper.class.php';

/**
 * vtMnpReport actions.
 *
 * @package    cms_ctt
 * @subpackage vtMnpReport
 * @author     viettel
 * @version    SVN: $Id: actions.class.php_bak.bak 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class vtMnpReportActions extends autoVtMnpReportActions
{
    public function executeFilter(sfWebRequest $request)
    {

        $this->setPage(1);

        if ($request->hasParameter('_reset'))
        {
            $this->setFilters($this->configuration->getFilterDefaults());

            $this->redirect('@vt_ctt_transaction_vtMnpReport');
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
            $this->redirect('@vt_ctt_transaction_vtMnpReport');
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
        $sheet->setCellValue('A1', $i18n->__('TH???NG K?? GIAO D???CH MNP'));
        $sheet->getStyle('A1')->getFont()->setBold(true);

        $sheet->setCellValue('A2', $i18n->__('T??? ng??y %from% ?????n ng??y %to%', ['%from%' => $from, '%to%' => $to]));
        $sheet->setCellValue('A4', $i18n->__('STT'));
        $sheet->setCellValue('B4', $i18n->__('H??? th???ng My Viettel'));
        $sheet->setCellValue('B5', $i18n->__('S??? thu?? bao ????ng nh???p v??o My Viettel'));
        $sheet->setCellValue('C5', $i18n->__('S??? thu?? bao g???ch n???'));
        $sheet->setCellValue('D5', $i18n->__('S??? ti???n charge'));
        $sheet->setCellValue('E5', $i18n->__('Th???i gian charge'));
        $sheet->setCellValue('F5', $i18n->__('K??nh'));
        $sheet->setCellValue('G5', $i18n->__('Tr???ng th??i g???ch n???/n???p ti???n'));
        $sheet->setCellValue('H5', $i18n->__('M?? thanh to??n (m?? sinh ra t??? My Viettel)'));
        $sheet->setCellValue('I5', $i18n->__('M?? giao d???ch (M?? sinh ra t??? c???ng thanh to??n)'));


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
            if(in_array($i, ['E','G','H','I']))
                $sheet->getColumnDimension($i)->setWidth(20);
            else
                $sheet->getColumnDimension($i)->setWidth(15);
        }

        $results = $this->buildQuery()->execute();
        $startRow = 5;
        foreach ($results as $key => $result){
            $startRow++;
            $sheet->setCellValue("A$startRow", ++$key);
            $sheet->setCellValue("B$startRow", $result->getIsdnLogin());
            $sheet->setCellValue("C$startRow", $result->getIsdn());
            $sheet->setCellValue("D$startRow", $result->getAmount());
            $sheet->setCellValue("E$startRow", $result->getCreatedAt());
            $sheet->setCellValue("F$startRow", $result->getSource());
            $sheet->setCellValue("G$startRow", $result->getOmniErrorCodeName());
            $sheet->setCellValue("H$startRow", $result->getTransactionId());
            $sheet->setCellValue("I$startRow", $result->getCttId());
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
        $fileName = 'mnp_report_'.date('YmdHis').'.xlsx';
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
