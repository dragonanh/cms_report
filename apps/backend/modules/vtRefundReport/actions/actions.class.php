<?php

require_once dirname(__FILE__) . '/../lib/vtRefundReportGeneratorConfiguration.class.php';
require_once dirname(__FILE__) . '/../lib/vtRefundReportGeneratorHelper.class.php';

/**
 * vtRefundReport actions.
 *
 * @package    cms_ctt
 * @subpackage vtRefundReport
 * @author     viettel
 * @version    SVN: $Id: actions.class.php_bak.bak 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class vtRefundReportActions extends autoVtRefundReportActions
{
    public function executeIndex(sfWebRequest $request)
    {
        parent::executeIndex($request);
        $this->importForm = new vtRefundReportImportForm();
        $this->formId = new vtRefundReportForm();
    }

    public function executeFilter(sfWebRequest $request)
    {
//        parent::executeFilter($request);
        $this->importForm = new vtRefundReportImportForm();
        $this->formId = new vtRefundReportForm();

        $this->setPage(1);

        if ($request->hasParameter('_reset')) {
            $this->setFilters($this->configuration->getFilterDefaults());

            $this->redirect('@vt_ctt_transaction_vtRefundReport');
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
            $this->redirect('@vt_ctt_transaction_vtRefundReport');
        }
        $this->sidebar_status = $this->configuration->getListSidebarStatus();
        $this->pager = $this->getPager();
        $this->sort = $this->getSort();
        $this->setTemplate('index');
    }

    public function executeImportExcel(sfWebRequest $request)
    {
        parent::executeIndex($request);
        $this->importForm = new vtRefundReportImportForm();

        if ($request->hasParameter('_import')) {
            $files = $request->getFiles($this->importForm->getName());
            $arrMime = array(
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            );
            $maxSizeImport = 2;
            if (($files['file']['size'] / (1024 * 1024)) > $maxSizeImport) {
                $this->getUser()->setFlash('error', 'File import kh??ng qu?? ' . $maxSizeImport . 'Mb.');
                $this->redirect('@vt_ctt_transaction_vtRefundReport');
            }
            if (empty($files['file']) || !$files['file']['name']) {
                $this->getUser()->setFlash('error', 'Ch???n file tr?????c khi th???c hi???n import.');
                $this->redirect('@vt_ctt_transaction_vtRefundReport');
            }
            if (!in_array($files['file']['type'], $arrMime)) {
                $this->getUser()->setFlash('error', '?????nh d???ng file giao d???ch ho??n ti???n kh??ng h???p l???.');
                $this->redirect('@vt_ctt_transaction_vtRefundReport');
            }

            $arrFileUpload = array(
                'image/jpeg',
                'image/png',
                'application/pdf',
            );
            $maxSizeUpload = 5;

            if (!empty($files['image']['type']) && !in_array($files['image']['type'], $arrFileUpload)) {
                $this->getUser()->setFlash('error', '?????nh d???ng file ph?? duy???t kh??ng h???p l???. Vui l??ng ch???n file c?? ?????nh d???ng .pdf, .jpeg, .jpg, .png');
                $this->redirect('@vt_ctt_transaction_vtRefundReport');
            }
            if (!empty($files['image']['size']) && $files['image']['size'] / (1024 * 1024) > $maxSizeUpload) {
                $this->getUser()->setFlash('error', 'File upload kh??ng qu?? ' . $maxSizeUpload . ' MB');
                $this->redirect('@vt_ctt_transaction_vtRefundReport');
            }

            $this->importForm->bind(($request->getParameter($this->importForm->getName())), $files);
            if ($this->importForm->isValid()) {
                $logfile = 'import_channel.log';
                $this->processImport($files, $logfile);
                $this->redirect('@vt_ctt_transaction_vtRefundReport');
//            } else {
//                $this->redirect('@vt_ctt_transaction_vtRefundReport');
            }
        }
        $this->setTemplate('index');
    }

    public function processImport($files, $logfile)
    {
        ini_set('precision', '15');
        $logger = VtHelper::getLogger4Php("all");
        $listError = array();
        $validId = array();
        $validData = array();
        $name = array();
        $limit_upload = sfConfig::get("app_limit_channel_upload", '1000');
        try {
            $errorExport = null;

            // Read your Excel workbook
            $inputFileType = PHPExcel_IOFactory::identify($files['file']['tmp_name']);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($files['file']['tmp_name']);
            // Get worksheet dimensions
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            if ($highestRow <= ($limit_upload + 1)) {//+1 vi dong dau tien cua file la tieu de
                $success = 0;
                VtHelper::writeLogValue('Process Import Channel|Begin Import Channel...');
                $countRC = 0;
                for ($row = 2; $row <= $highestRow; $row++) {
                    $transationId = trim($sheet->getCell("A" . $row)->getValue());
                    if(preg_match('/^[\d]+$/', $transationId))
                      $transationId = (string)floatval($transationId);
                    $originalRequestId = (int)floatval(trim($sheet->getCell("B" . $row)->getValue()));
                    $refund_type = trim($sheet->getCell("C" . $row)->getValue());
                    $trans_amount = trim($sheet->getCell("D" . $row)->getValue());
                    $trans_content = trim($sheet->getCell("E" . $row)->getValue());

                    if($transationId != "") {
                      $name[] = array(
                        'transaction_id' => $transationId,
                        'originalRequestId' => $originalRequestId,
                        'refund_type' => $refund_type,
                        'trans_amount' => $trans_amount,
                        'trans_content' => $trans_content,
                      );
                    }
                }
                $paymentGateway = new PaymentGatewayWS();
                foreach ($name as $key => $value) {
                    if (empty($value['transaction_id'])) {
                        $errorExport[] = array($value['transaction_id'], 'Truy???n thi???u m?? giao d???ch');
                        continue;
                    }
                    if (empty($value['originalRequestId'])) {
                        $errorExport[] = array($value['transaction_id'], 'Truy???n thi???u m?? giao d???ch thanh to??n ph??a ViettelPay');
                        continue;
                    }
                    if (!in_array($value['refund_type'], ['1', '0'])) {
                        $errorExport[] = array($value['transaction_id'], 'H??nh th???c ho??n ti???n kh??ng h???p l??? ');
                        continue;
                    }
                    if ($value['refund_type'] == '1') {
                        if (empty($value['trans_amount'])) {
                            $errorExport[] = array($value['transaction_id'], 'Truy???n thi???u s??? ti???n');
                            continue;
                        }
                        if (!is_numeric($value['trans_amount'])) {
                            $errorExport[] = array($value['transaction_id'], 'S??? ti???n kh??ng h???p l???');
                            continue;
                        }
                    }
                    if (empty($value['trans_content'])) {
                        $errorExport[] = array($value['transaction_id'], 'Truy???n thi???u l?? do ho??n ti???n');
                        continue;
                    }
                    $validId[] = $value['transaction_id'];
                    $validData[$value['transaction_id']] = $value;
                }

                $logger->info(sprintf("processImportCTT | after validate | %s", json_encode($validData)));

                $totalRecord = count($name);
                $totalError = $totalRecord;

                if (!empty($validId) && !empty($validData)) {
                    $dataInvalid = $this->getRecord($validId);
                    $logger->info(sprintf("processImportCTT | list record in db | %s", json_encode($dataInvalid)));
                    if (!empty($dataInvalid)) {
                        $upLoadDir = sfConfig::get('sf_web_dir') . '/upload/report/';
                        $upLoadFile = null;
                        if(!empty($files['image']['name']))
                          $upLoadFile = $upLoadDir . '_' . date("Ymdhis") . '_' . basename($files['image']['name']);
                        foreach ($dataInvalid as $key => $query) {
                            $val = $validData[$query['tran_id']];
                            $logger->info(sprintf("processImportCTT | after validate | %s", $query['tran_id']));
                            unset($validData[$query['tran_id']]);
                            if (!empty($query) && $query['refund_status'] != 1 && $query['refund_status'] != 3) {
                                if (in_array($query['channel'], VtCttChannelEnum::listChannelVnpay())) {
                                    if ($val['refund_type'] == 1) {
                                        $trans_amount = $val['trans_amount'];
                                    } else {
                                        $trans_amount = $query['amount'];
                                    }
                                    $this->processRefundVnPay($query, $trans_amount, $val, $upLoadFile, $errorExport, $countRC);
                                }else{
                                    $checkPayment = $paymentGateway->checkTransaction($query['tran_id']);
                                    if ($checkPayment && $checkPayment->error_code == "00" && $checkPayment->payment_status == "1") {
                                        if ($val['refund_type'] == 1) {
                                            $trans_amount = $val['trans_amount'];
                                            if ($val['trans_amount'] >= $query['amount']) {
                                                $errorExport[] = array($val['transaction_id'], 'Vui l??ng nh???p s??? ti???n nh??? h??n s??? ti???n thanh to??n giao d???ch');
                                                continue;
                                            }
                                        } else {
                                            $trans_amount = $query['amount'];
                                        }

                                        if ($trans_amount <= $query['amount']) {
                                            $refundPayment = $paymentGateway->refundMoney($query['tran_id'], $val['originalRequestId'], $val['refund_type'], $trans_amount, $val['trans_content']);

                                            if ($refundPayment["errorCode"] == 0) {
                                                $this->updateParam($query['id'], true, 3);
                                                $this->insertRefundLog($query['tran_id'], $trans_amount, $val['refund_type'], $val['trans_content'], $upLoadFile, 1, $refundPayment["message"]);
                                                $countRC++;
                                            } else {
                                                $errorExport[] = array($val['transaction_id'], $refundPayment["message"]);
                                                $this->updateParam($query['id'], true, 4);
                                                $this->insertRefundLog($query['tran_id'], $trans_amount, $val['refund_type'], $val['trans_content'], null, 0, $refundPayment["message"]);
                                            }
                                        } else {
                                            $errorExport[] = array($val['transaction_id'], 'S??? ti???n ho??n kh??ng ???????c l???n h??n s??? ti???n thanh to??n giao d???ch');
                                        }


                                    } else {
                                        $errorExport[] = array($val['transaction_id'], 'Giao d???ch kh??ng t???n t???i ho???c ch??a b??? tr??? ti???n');
                                    }
                                }

                            } else {
                                $errorExport[] = array($val['transaction_id'], 'Giao d???ch ???? ho??n ti???n ho???c kh??ng t???n t???i');
                            }


                        }

                        $success = $countRC;
                        //upload file neu co ban ghi thanh cong
                        if ($success > 0 && !empty($files['image']['tmp_name'])) {
                            move_uploaded_file($files['image']['tmp_name'], $upLoadFile);
                        }
                        $totalError = $totalRecord - $success;
                    }

                    if (count($validData)) {
                        foreach ($validData as $key => $item) {
                            $errorExport[] = array($key, 'Giao d???ch kh??ng t???n t???i');
                        }
                    }
                }

                if ($errorExport) {
                    $this->exportExcelFail($errorExport);
                }
                VtHelper::writeLogValue('Process Import Channel|Validated Channel OK.');

                VtHelper::logActions(1, sprintf('Ho??n ti???n th??nh c??ng: %d giao d???ch', $success), $logfile);

                $this->getUser()->setFlash('success', 'G???i ho??n ti???n th??nh c??ng: ' . $success . ' giao d???ch. G???i ho??n ti???n th???t b???i ' . $totalError . ' giao d???ch');
            } else {
                $this->getUser()->setFlash('error', '\'G???i ho??n ti???n kh??ng th??nh c??ng. S??? l?????ng kh??ng ???????c v?????t qu?? ' . $limit_upload . ' giao d???ch');
            }
        } catch
        (Exception $e) {
            VtHelper::writeLogValue('Error loading file "' . pathinfo($files['attach_file']['tmp_name'], PATHINFO_BASENAME) . '": ' . $e->getMessage());
            $this->getUser()->setFlash('error', 'C?? l???i trong qu?? tr??nh upload file. Vui l??ng ki???m tra l???i!');
        }
    }

    public function processRefundVnPay($query, $trans_amount, $val, $upLoadFile, &$errorExport, &$countRC){
        if ($trans_amount <= $query['amount']) {
            $paymentGateway = new PartnerWS("APPVT003");
            $refundPayment = $paymentGateway->refundVNPAYMoney($query['tran_id'], $trans_amount * 100, $val['trans_content'], $val['refund_type'], $query['created_at'], $this->getUser()->getUsername());
            if ($refundPayment["vnp_ResponseCode"] == 0) {
                $this->updateParam($query['id'], true, 3);
                $this->insertRefundLog($query['tran_id'], $trans_amount, $val['refund_type'], $val['trans_content'], $upLoadFile, 1, 0);
                $countRC++;
            } else {
                $errorExport[] = array($val['transaction_id'], !empty($refundPayment->error_msg) ? $refundPayment->error_msg : 'Ho??n ti???n th???t b???i');
                $this->updateParam($query['id'], true, 4);
                $this->insertRefundLog($query['tran_id'], $trans_amount, $val['refund_type'], $val['trans_content'], null, 0, 0);
            }
        }else {
            $errorExport[] = array($val['transaction_id'], 'Vui l??ng nh???p s??? ti???n nh??? h??n s??? ti???n thanh to??n giao d???ch');
        }
    }

    public function executeRefundPerId(sfWebRequest $request)
    {
        $this->form = new vtRefundReportForm();

        $this->form->bind($request->getParameter($this->form->getName()));

        if ($this->form->isValid()) {
            $originalRequestId = $_POST['originalRequestId'];
            $refundType = $_POST['refundType'];
            $trans_content = $_POST['trans_content'];
            $trans_amount = $_POST['trans_amount'];
            $fileUpload = $_FILES['fileUpload'];
            $tranId = $_POST['tran_id'];
            $id = $_POST['id'];
            $results = $this->getRecordById($id);
            $results = !empty($results[0]) ? $results[0] : $results;
            if (in_array($results['channel'], VtCttChannelEnum::listChannelVnpay())) {
                $key = $this::validateNullparams(array(
                    'L?? do ho??n ti???n' => $trans_content,
                ));
            } else {
                $key = $this::validateNullparams(array(
                    'M?? giao d???ch thanh to??n ph??a ViettelPay' => $originalRequestId,
                    'L?? do ho??n ti???n' => $trans_content,
                ));
            }
            if ($key) {
                $response['error'] = sprintf('Truy???n thi???u %s', $key);
                return $this->renderText(json_encode($response));
            }
            if ($refundType == 1) {
                if (empty($trans_amount)) {
                    $response['error'] = 'Truy???n thi???u s??? ti???n';
                    return $this->renderText(json_encode($response));
                }
                if (!is_numeric($trans_amount)) {
                    $response['error'] = 'S??? ti???n kh??ng h???p l???';
                    return $this->renderText(json_encode($response));
                }
                if ($trans_amount >= $results['amount']) {
                    $response['error'] = 'Vui l??ng nh???p s??? ti???n nh??? h??n s??? ti???n thanh to??n giao d???ch';
                    return $this->renderText(json_encode($response));
                }
            } else {
                $trans_amount = $results['amount'];
            }
            $upLoadFile = null;
            if (!empty($fileUpload['tmp_name'])) {
                $arrMime = array(
                    'image/jpeg',
                    'image/png',
                    'application/pdf',
                );
                if (!in_array($fileUpload['type'], $arrMime)) {
                    $response['error'] = '?????nh d???ng file kh??ng h???p l???. Vui l??ng ch???n file c?? ?????nh d???ng .pdf, .jpeg, .jpg, .png';
                    return $this->renderText(json_encode($response));
                }
                $maxSizeUpload = 5;
                if (($fileUpload['size'] / (1024 * 1024)) > $maxSizeUpload) {
                    $response['error'] = 'File upload kh??ng qu?? ' . $maxSizeUpload . ' MB';
                    return $this->renderText(json_encode($response));
                }
                $upLoadDir = sfConfig::get('sf_web_dir') . '/upload/report/';
                $upLoadFile = $upLoadDir . date("Ymdhis") . '_' . basename($fileUpload['name']);
            }
            if (in_array($results['channel'], VtCttChannelEnum::listChannelVnpay())) {
                $paymentGateway = new PartnerWS("APPVT003");
                $checkPayment = $paymentGateway->refundVNPAYMoney($results['tran_id'], $trans_amount * 100, $trans_content, $refundType, $results['created_at'], $this->getUser()->getUsername());
                if ($checkPayment && $checkPayment['vnp_ResponseCode'] == "00") {
                    $this->updateParam($results['id'], true, 3);
                    if (!empty($fileUpload)) {
                        move_uploaded_file($fileUpload['tmp_name'], $upLoadFile);
                    }
                    $this->insertRefundLog($tranId, $trans_amount, $refundType, $trans_content, $upLoadFile, 1, null);
                    $response['error'] = '';
                    return $this->renderText(json_encode($response));
                } else {
                    $this->updateParam($results['id'], true, 4);

                    $this->insertRefundLog($tranId, $trans_amount, $refundType, $trans_content, null, 0, null);

                    $response['error'] = !empty($checkPayment->error_msg) ? $checkPayment->error_msg : 'G???i ho??n ti???n th???t b???i';
                    return $this->renderText(json_encode($response));
                }
            } else {
                if ($results['refund_status'] != 1 && $results['refund_status'] != 3) {
                    $paymentGateway = new PaymentGatewayWS();
                    $checkPayment = $paymentGateway->checkTransaction($results['tran_id']);
                    if ($checkPayment && $checkPayment->error_code == "00" && $checkPayment->payment_status == "1") {
                        $refundPayment = $paymentGateway->refundMoney($results['tran_id'], $originalRequestId, $refundType, $trans_amount, $trans_content);
                        if ($refundPayment["errorCode"] == 0) {
                            $this->updateParam($results['id'], true, 3);
                            if (!empty($fileUpload)) {
                                move_uploaded_file($fileUpload['tmp_name'], $upLoadFile);
                            }
                            $this->insertRefundLog($tranId, $trans_amount, $refundType, $trans_content, $upLoadFile, 1, $refundPayment["message"]);
                            $response['error'] = '';
                        } else {
                            $this->updateParam($results['id'], true, 4);
                            $this->insertRefundLog($tranId, $trans_amount, $refundType, $trans_content, null, 0, $refundPayment["message"]);
                            $response['error'] = 'G???i ho??n ti???n th???t b???i';
                        }
                        return $this->renderText(json_encode($response));
                    } else {
                        $response['error'] = !empty($checkPayment->error_msg) ? $checkPayment->error_msg : 'Giao d???ch kh??ng ????? ??i???u ki???n ho??n ti???n';
                        return $this->renderText(json_encode($response));
                    }
                }
            }
            $this->redirect('@vt_ctt_transaction_vtRefundReport');
        } else {
            $response['error'] = 'csrf token: CSRF attack detected.';
            return $this->renderText(json_encode($response));
        }

    }

    public function updateParam($id, $check, $status)
    {
        if (!$check) {
            $query = Doctrine_Query::create()
                ->from('VtCttTransaction')
                ->update()
                ->set('refund_status', '?', $status)
                ->whereIn('tran_id', $id)
                ->execute();
        } else {
            $query = Doctrine_Query::create()
                ->from('VtCttTransaction')
                ->update()
                ->set('refund_status', '?', $status)
                ->whereIn('id', $id)
                ->execute();
        }


        return $query;
    }

    public function getRecord($id)
    {
        $query = Doctrine_Query::create()
            ->from('VtCttTransaction')
            ->whereIn('tran_id', $id)
            ->fetchArray();

        return $query;
    }

    public function getRecordById($id)
    {
        $query = Doctrine_Query::create()
            ->from('VtCttTransaction')
            ->whereIn('id', $id)
            ->fetchArray();

        return $query;
    }

    public function insertRefundLog($tranId, $refundAmount, $refundType, $reason, $filePath, $status, $message)
    {
        try {
            $VtCttRefundLog = new VtCttRefundLog();
            $VtCttRefundLog->setTranId($tranId);
            $VtCttRefundLog->setRefundAmount((int)$refundAmount);
            $VtCttRefundLog->setRefundType($refundType);
            $VtCttRefundLog->setReason($reason);
            $VtCttRefundLog->setIp($_SERVER['REMOTE_ADDR']);
            $VtCttRefundLog->setUsername($this->getUser()->getUsername());
            $VtCttRefundLog->setFilePath($filePath);
            $VtCttRefundLog->setStatus($status);
            $VtCttRefundLog->setMessage($message);

            $b = $VtCttRefundLog->save();

            return $VtCttRefundLog;
        } catch (Exception $e) {
            return null;
        }
    }

    public static function validateNullParams($arrayParams)
    {
        foreach ($arrayParams as $key => $values) {
            if (!$values && $values != '0')
                return $key;
        }
        return false;
    }

    public function exportExcelFail($results)
    {
        $fileDesName = date('YmdHis') . "_import_channel_fail";
        $fileDes = sfConfig::get('sf_cache_dir') . '/' . $fileDesName . '.xlsx';
        $header = array('M?? giao d???ch', 'M?? t??? l???i');
        $writer = new spoutHelper($fileDes);
        $writer->writeHeaderRow($header);
        foreach ($results as $key => $result) {
            $writer->writeRow($result);
        }
        $writer->close();
        $this->getUser()->setFlash('fileImportFail', $fileDesName);
    }

    public function executeDownloadFileImportFail(sfWebRequest $request)
    {
        if ($fileName = $request->getParameter('file_name')) {
            $filePath = sprintf('%s/%s.xlsx', sfConfig::get('sf_cache_dir'), $fileName);
            if (is_file($filePath)) {
                $this->downloadFile($filePath, 'Refund_report_failed_' . date('YmdHis', strtotime('now')) . '.xlsx');
            } else {
                $this->redirect('@vt_ctt_transaction_vtRefundReport');
            }
        } else {
            $this->getUser()->setFlash('error', 'Thi???u tham s???');
            $this->redirect('@vt_ctt_transaction_vtRefundReport');
        }
    }

    public function executeDownloadFileSample(sfWebRequest $request)
    {
        $filePath = sprintf('%s/upload/sample/refund_sample.xlsx', sfConfig::get('sf_web_dir'));
        if (is_file($filePath)) {
            $this->downloadFile($filePath, 'refund_sample.xlsx', 0);
        } else {
            $this->redirect('@vt_ctt_transaction_vtRefundReport');
        }
    }

    public function downloadFile($filePath, $fileName, $isDeleteFile = 1)
    {
        header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Pragma: public");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header(sprintf('Content-Disposition: attachment; filename="%s"', $fileName));
        ob_end_clean();
        ob_start();
        readfile($filePath);
        $size = ob_get_length();
        header("Content-Length: $size");
        ob_end_flush();
        if ($isDeleteFile) unlink($filePath);
        return;
    }

    public function exportExcel()
    {
        $filterValues = $this->getFilters();
        $from = !empty($filterValues['process_time']['from']) ? date('d-m-Y', strtotime($filterValues['process_time']['from'])) : '';
        $to = !empty($filterValues['process_time']['to']) ? date('d-m-Y', strtotime($filterValues['process_time']['to'])) : '';
        $i18n = $this->getContext()->getI18N();
        $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', $i18n->__('TH???NG K?? B??O C??O HO??N TI???N'));
        $sheet->getStyle('A1')->getFont()->setBold(true);

        $sheet->setCellValue('A2', $i18n->__('T??? ng??y %from% ?????n ng??y %to%', ['%from%' => $from, '%to%' => $to]));
        $sheet->setCellValue('A4', $i18n->__('STT'));
        $sheet->setCellValue('B4', $i18n->__('H??? th???ng My Viettel'));
        $sheet->setCellValue('B5', $i18n->__('M?? giao d???ch'));
        $sheet->setCellValue('C5', $i18n->__('Thu?? bao'));
        $sheet->setCellValue('D5', $i18n->__('S??? ti???n'));
        $sheet->setCellValue('E5', $i18n->__('M?? t???'));
        $sheet->setCellValue('F5', $i18n->__('Lo???i giao d???ch'));
        $sheet->setCellValue('G5', $i18n->__('Tr???ng th??i giao d???ch'));
        $sheet->setCellValue('H5', $i18n->__('Ngu???n'));
        $sheet->setCellValue('I5', $i18n->__('K??nh'));
        $sheet->setCellValue('J5', $i18n->__('Tr???ng th??i ho??n ti???n'));


        $sheet->getStyle('A4:I5')->applyFromArray(
            array(
                'font' => array(
                    'bold' => true
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

        for ($i = 'B'; $i <= 'J'; $i++) {
            $sheet->getStyle($i . '5')->getAlignment()->setWrapText(true);
            if (in_array($i, ['I', 'H']))
                $sheet->getColumnDimension($i)->setWidth(20);
            else
                $sheet->getColumnDimension($i)->setWidth(15);
        }

        $results = $this->buildQuery()->fetchArray();
        $startRow = 5;
        foreach ($results as $key => $result) {
            $startRow++;
            if ($result['omni_error_code'] === null) {
                $statusName = '';
            } elseif ($result['omni_error_code'] == 1) {
                $statusName = $i18n->__('Th???t b???i');
            } else {
                $statusName = $i18n->__('Th??nh c??ng');
            }
            if ($result['refund_status'] == 1) {
                $refundName = $i18n->__('Ho??n ti???n th??nh c??ng');
            } elseif ($result['refund_status'] == 2) {
                $refundName = $i18n->__('Ho??n ti???n th???t b???i');
            } elseif ($result['refund_status'] == 3) {
                $refundName = $i18n->__('G???i ho??n ti???n th??nh c??ng');
            } elseif ($result['refund_status'] == 4) {
                $refundName = $i18n->__('G???i ho??n ti???n th???t b???i');
            } else {
                $refundName = '';
            }
//            $refundStatusArr = array('' => $i18n->__('T???t c???'), 1 => $i18n->__('Ho??n ti???n th??nh c??ng'), 2 => $i18n->__('Ho??n ti???n th???t b???i'), 3 => $i18n->__('G???i ho??n ti???n th??nh c??ng'), 4 => $i18n->__('G???i ho??n ti???n th???t b???i'));
            $sheet->setCellValue("A$startRow", ++$key);
            $sheet->setCellValue("B$startRow", $result['transaction_id']);
            $sheet->setCellValue("C$startRow", $result['calling']);
            $sheet->setCellValue("D$startRow", $result['amount']);
            $sheet->setCellValue("E$startRow", $result['description']);
            $sheet->setCellValue("F$startRow", $result['service_pay']);
            $sheet->setCellValue("G$startRow", $statusName);
            $sheet->setCellValue("H$startRow", $result['source']);
            $sheet->setCellValue("I$startRow", $result['channel']);
            $sheet->setCellValue("J$startRow", $refundName);
        }

        $sheet->getStyle('A4:J' . $startRow)->applyFromArray(
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

        $fileName = 'ctt_refund_' . date('YmdHis') . rand(10000, 99999) . '.xlsx';
        $filePath = sfConfig::get('sf_web_dir') . '/export/' . $fileName;
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
