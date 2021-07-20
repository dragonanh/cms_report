<?php

require_once dirname(__FILE__) . '/../lib/vtPaymentDebtGeneratorConfiguration.class.php';
require_once dirname(__FILE__) . '/../lib/vtPaymentDebtGeneratorHelper.class.php';

/**
 * vtRefundReport actions.
 *
 * @package    cms_ctt
 * @subpackage vtRefundReport
 * @author     viettel
 * @version    SVN: $Id: actions.class.php_bak.bak 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class vtPaymentDebtActions extends autoVtPaymentDebtActions
{
    public function executeIndex(sfWebRequest $request)
    {
        parent::executeIndex($request);
    }

    public function executeFilter(sfWebRequest $request)
    {
        $this->setPage(1);

        if ($request->hasParameter('_reset')) {
            $this->setFilters($this->configuration->getFilterDefaults());

            $this->redirect('@vt_payment_debt');
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
            if($request->hasParameter('export')){
                $this->exportExcel();
            }
            $this->redirect('@vt_payment_debt');
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
        $sheet->setCellValue('A1', $i18n->__('BÁO CÁO THANH TOÁN CƯỚC'));
        $sheet->getStyle('A1')->getFont()->setBold(true);

        $sheet->setCellValue('A2', $i18n->__('Từ ngày %from% Đến ngày %to%', ['%from%' => $from, '%to%' => $to]));
        $sheet->setCellValue('A4', $i18n->__('STT'));
        $sheet->setCellValue('B4', $i18n->__('BÁO CÁO THANH TOÁN CƯỚC'));
        $sheet->setCellValue('B5', $i18n->__('Thuê bao'));
        $sheet->setCellValue('C5', $i18n->__('Tên hàm'));
        $sheet->setCellValue('D5', $i18n->__('Loại giao dịch'));
        $sheet->setCellValue('E5', $i18n->__('Giá tiền'));
        $sheet->setCellValue('F5', $i18n->__('Trạng thái'));
        $sheet->setCellValue('G5', $i18n->__('Mã nhân viên tư vấn'));
        $sheet->setCellValue('H5', $i18n->__('utm_source'));
        $sheet->setCellValue('I5', $i18n->__('aff_sid'));
        $sheet->setCellValue('J5', $i18n->__('utm_medium'));


        $sheet->getStyle('A4:J5')->applyFromArray(
            array(
                'font'  => array(
                    'bold'  =>  true
                )
            )
        );

        $sheet->mergeCells('A1:J1');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells('A2:J2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells('A4:A5');
        $sheet->mergeCells('B4:J4');
        $sheet->getStyle('B4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getColumnDimension('A4')->setWidth(15);

        for ($i='B'; $i <= 'J'; $i++){
            $sheet->getStyle($i.'5')->getAlignment()->setWrapText(true);
            if(in_array($i, ['E','G','H','I','J']))
                $sheet->getColumnDimension($i)->setWidth(20);
            else
                $sheet->getColumnDimension($i)->setWidth(15);
        }

        $results = $this->buildQuery()->execute();
        $startRow = 5;
        foreach ($results as $key => $result){
            $startRow++;
            $orderType = '';
            if($result->getOrderType() == '37'){
                $orderType = 'Hàm gạch nợ';
            }elseif($result->getOrderType() == '24'){
                $orderType = 'Hàm topup';
            }
            if($result->getServiceType() == '1'){
                $serviceType = 'topup';
            }elseif($result->getServiceType() == '2'){
                $serviceType = 'Thanh toán cước di động trả sau';
            }else{
                $serviceType = 'Thanh toán cước di dộng cố định';
            }
            $status = '';
            if($result->getStatus() === '0'){
                $status = 'Thất bại';
            }elseif($result->getStatus() == '1'){
                $status = 'Thành công';
            }
            $sheet->setCellValue("A$startRow", ++$key);
            $sheet->setCellValue("B$startRow", $result->getMsisdn());
            $sheet->setCellValue("C$startRow", $orderType);
            $sheet->setCellValue("D$startRow", $serviceType);
            $sheet->setCellValue("E$startRow", $result->getPrice());
            $sheet->setCellValue("F$startRow", $status);
            $sheet->setCellValue("G$startRow", $result->getStaffCode());
            $sheet->setCellValue("H$startRow", $result->getUtmSource());
            $sheet->setCellValue("I$startRow", $result->getAffSid());
            $sheet->setCellValue("J$startRow", $result->getUtmMedium());
        }

        $sheet->getStyle('A4:J'.$startRow)->applyFromArray(
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
        $fileName = 'baocaothanhtoancuoc'.date('YmdHis').'.xlsx';
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
