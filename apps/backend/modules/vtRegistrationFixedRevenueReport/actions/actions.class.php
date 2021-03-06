<?php

require_once dirname(__FILE__) . '/../lib/vtRegistrationFixedRevenueReportGeneratorConfiguration.class.php';
require_once dirname(__FILE__) . '/../lib/vtRegistrationFixedRevenueReportGeneratorHelper.class.php';

/**
 * vtRegistrationFixedRevenueReport actions.
 *
 * @package    cms_ctt
 * @subpackage vtRegistrationFixedRevenueReport
 * @author     viettel
 * @version    SVN: $Id: actions.class.php_bak.bak 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class vtRegistrationFixedRevenueReportActions extends autoVtRegistrationFixedRevenueReportActions
{
    public function executeFilter(sfWebRequest $request)
    {
        $this->setPage(1);

        if ($request->hasParameter('_reset')) {
            $this->setFilters($this->configuration->getFilterDefaults());

            $this->redirect('@vt_ctt_transaction_vtRegistrationFixedRevenueReport');
        }

        $this->filters = $this->configuration->getFilterForm($this->getFilters());
        //Chuyennv2 trim du lieu
        $filterValues = $request->getParameter($this->filters->getName());
        foreach ($filterValues as $key => $value) {
            if (isset($filterValues[$key]['text'])) {
                $filterValues[$key]['text'] = trim($filterValues[$key]['text']);
            }
        }

        $this->filters->bind($filterValues);
        if ($this->filters->isValid()) {
            $this->setFilters($this->filters->getValues());
            if ($request->hasParameter('export')) {
                $this->exportExcel();
            }
            $this->redirect('@vt_ctt_transaction_vtRegistrationFixedRevenueReport');
        }
        $this->sidebar_status = $this->configuration->getListSidebarStatus();
        $this->pager = $this->getPager();
        $this->sort = $this->getSort();

        $this->setTemplate('index');
    }

    public function exportExcel()
    {
        $filterValues = $this->getFilters();
        $from = !empty($filterValues['process_time']['from']) ? date('d-m-Y', strtotime($filterValues['process_time']['from'])) : '';
        $to = !empty($filterValues['process_time']['to']) ? date('d-m-Y', strtotime($filterValues['process_time']['to'])) : '';
        $i18n = $this->getContext()->getI18N();
        $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'B??o c??o doanh thu ????ng k?? d???ch v??? c??? ?????nh');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->mergeCells('A1:K1');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', $i18n->__('T??? ng??y %from% ?????n ng??y %to%', ['%from%' => $from, '%to%' => $to]));
        $sheet->mergeCells('A2:K2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

//        $sheet->setCellValue('A3', $i18n->__('D??? li???u tr??n My Viettel'));
//        $sheet->mergeCells('A3:G3');
//        $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A3', $i18n->__('STT'));
        $sheet->setCellValue('B3', $i18n->__('S??T ????ng k??/acc ????ng k?? d???ch v???'));
        $sheet->setCellValue('C3', $i18n->__('Lo???i d???ch v???'));
        $sheet->setCellValue('D3', $i18n->__('G??i c?????c'));
        $sheet->setCellValue('E3', $i18n->__('M?? c?????c ????ng tr?????c'));
        $sheet->setCellValue('F3', $i18n->__('M?? ????n h??ng'));
        $sheet->setCellValue('G3', $i18n->__('Th???i gian ????ng k??'));
        $sheet->setCellValue('H3', $i18n->__('M?? thanh to??n (M?? sinh ra t??? MyVT)'));
        $sheet->setCellValue('I3', $i18n->__('M?? giao d???ch (M?? sinh ra t??? C???ng thanh to??n)'));
        $sheet->setCellValue('J3', $i18n->__('Ch??nh s??ch ????ng c?????c tr?????c'));
        $sheet->setCellValue('K3', $i18n->__('T???ng ti???n'));

        $sheet->getStyle('A2:K3')->applyFromArray(
            array(
                'font' => array(
                    'bold' => true
                )
            )
        );

        for ($i = 'A'; $i <= 'O'; $i++) {
            $sheet->getStyle($i . '3')->getAlignment()->setWrapText(true);
            if ($i == 'L')
                $sheet->getColumnDimension($i)->setWidth(20);
            else
                $sheet->getColumnDimension($i)->setWidth(15);
        }

        $results = $this->buildQuery()->execute();
        $startRow = 3;
        foreach ($results as $key => $result) {
            $startRow++;
            $sheet->setCellValue("A$startRow", ++$key);
            $sheet->setCellValue("B$startRow", $result->getIsdn());
            $sheet->setCellValue("C$startRow", $result->getServiceType());
            $sheet->setCellValue("D$startRow", $result->getCttPackageName());
            $sheet->setCellValue("E$startRow", $result->getPrepaidCode());
            $sheet->setCellValue("F$startRow", $result->getOmniProcessId());
            $sheet->setCellValue("G$startRow", $result->getCreatedAt());
            $sheet->setCellValue("H$startRow", $result->getTranId());
            $sheet->setCellValue("I$startRow", $result->getCttId());
            $sheet->setCellValue("J$startRow", $result->getPolicy());
            $sheet->setCellValue("K$startRow", $result->getTotalFee());
        }

        $sheet->getStyle('A2:K' . $startRow)->applyFromArray(
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
        $fileName = 'registration_fixed_revenue_report_' . date('YmdHis') . '.xlsx';
        $dirPath = sfConfig::get('sf_web_dir') . '/export';
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0007, true);
        }
        $filePath = $dirPath . '/' . $fileName;
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
