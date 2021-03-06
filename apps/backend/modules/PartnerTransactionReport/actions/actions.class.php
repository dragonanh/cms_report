<?php

require_once dirname(__FILE__) . '/../lib/PartnerTransactionReportGeneratorConfiguration.class.php';
require_once dirname(__FILE__) . '/../lib/PartnerTransactionReportGeneratorHelper.class.php';

/**
 * PartnerTransactionReport actions.
 *
 * @package    cms_ctt
 * @subpackage PartnerTransactionReport
 * @author     viettel
 * @version    SVN: $Id: actions.class.php_bak.bak 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class PartnerTransactionReportActions extends autoPartnerTransactionReportActions
{
    public function executeIndex(sfWebRequest $request)
    {
        parent::executeIndex($request);
        $this->importForm = new PartnerTransactionReportImportForm();
        $this->formId = new PartnerTransactionReportForm();
    }


    public function executeFilter(sfWebRequest $request)
    {
        $this->importForm = new PartnerTransactionReportImportForm();
        $this->formId = new PartnerTransactionReportForm();
        $this->setPage(1);

        if ($request->hasParameter('_reset')) {
            $this->setFilters($this->configuration->getFilterDefaults());

            $this->redirect('@partner_transaction_PartnerTransactionReport');
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
            $this->redirect('@partner_transaction_PartnerTransactionReport');
        }
        $this->sidebar_status = $this->configuration->getListSidebarStatus();
        $this->pager = $this->getPager();
        $this->sort = $this->getSort();

        $this->setTemplate('index');
    }

    public function executeImportMoneyExcel(sfWebRequest $request)
    {
        parent::executeIndex($request);
        $this->importForm = new PartnerTransactionReportImportForm();
        if ($request->hasParameter('_import')) {
            $files = $request->getFiles($this->importForm->getName());
            $arrMime = array(
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/octet-stream',
                'application/zip'
            );
            $maxSizeImport = 2;
            if (($files['file']['size'] / (1024 * 1024)) > $maxSizeImport) {
                $this->getUser()->setFlash('error', 'File import kh??ng qu?? ' . $maxSizeImport . 'Mb.');
                $this->redirect('@partner_transaction_PartnerTransactionReport');
            }
            if (empty($files['file']) || !$files['file']['name']) {
                $this->getUser()->setFlash('error', 'Ch???n file tr?????c khi th???c hi???n import.');
                $this->redirect('@partner_transaction_PartnerTransactionReport');
            }
            if (!in_array($files['file']['type'], $arrMime)) {
                $this->getUser()->setFlash('error', '?????nh d???ng file kh??ng h???p l???.');
                $this->redirect('@partner_transaction_PartnerTransactionReport');
            }

            $arrFileUpload = array(
                'image/jpeg',
                'image/png',
                'application/pdf',
            );
            $maxSizeUpload = 5;
            if (!empty($files['image']['size']) && $files['image']['size'] / (1024 * 1024) > $maxSizeUpload) {
                $this->getUser()->setFlash('error', 'File upload kh??ng qu?? ' . $maxSizeUpload . ' MB');
                $this->redirect('@partner_transaction_PartnerTransactionReport');
            }
            if (!empty($files['image']['type']) && !in_array($files['image']['type'], $arrFileUpload)) {
                $this->getUser()->setFlash('error', '?????nh d???ng file kh??ng h???p l???. Vui l??ng ch???n file c?? ?????nh d???ng .pdf, .jpeg, .jpg, .png');
                $this->redirect('@partner_transaction_PartnerTransactionReport');
            }

            $this->importForm->bind(($request->getParameter($this->importForm->getName())), $files);
            if ($this->importForm->isValid()) {
                $logfile = 'import_channel.log';
                $this->processImport($files, $logfile);
                $this->redirect('@partner_transaction_PartnerTransactionReport');
            }
        }
        $this->setTemplate('index');
    }

    public function processImport($files, $logfile, $type = "money")
    {
        $listError = array();
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
                  if($type == "money") {
                    $name[] = array(
                      'transaction_id' => trim($sheet->getCell("A" . $row)->getValue()),
                      'originalRequestId' => trim($sheet->getCell("B" . $row)->getValue()),
                      'refund_type' => trim($sheet->getCell("C" . $row)->getValue()),
                      'trans_amount' => trim($sheet->getCell("D" . $row)->getValue()),
                      'trans_content' => trim($sheet->getCell("E" . $row)->getValue()),
                    );
                  }else{
                    $name[] = array(
                      'transaction_id' => trim($sheet->getCell("A" . $row)->getValue()),
                      'refund_type' => trim($sheet->getCell("B" . $row)->getValue()),
                      'trans_amount' => trim($sheet->getCell("C" . $row)->getValue()),
                      'trans_content' => trim($sheet->getCell("D" . $row)->getValue()),
                    );
                  }
                }

                foreach ($name as $key => $value) {
                    if (empty($value['transaction_id'])) {
                        $errorExport[] = array($value['transaction_id'], 'Truy???n thi???u m?? giao d???ch');
                        continue;
                    }

                    if (!in_array($value['refund_type'], ['1', '0'])) {
                        $errorExport[] = array($value['transaction_id'], 'H??nh th???c ho??n ti???n kh??ng h???p l???');
                        continue;
                    }
                    if ($value['refund_type'] == '1') {
                        if (empty($value['trans_amount'])) {
                            $errorExport[] = array($value['transaction_id'], $type == "money" ? 'Truy???n thi???u s??? ti???n' : "Truy???n thi???u s??? ??i???m");
                            continue;
                        }
                        if (!is_numeric($value['trans_amount'])) {
                            $errorExport[] = array($value['transaction_id'], $type == "money" ? 'S??? ti???n kh??ng h???p l???' : "S??? ??i???m kh??ng h???p l???");
                            continue;
                        }
                    }
                    if (empty($value['trans_content'])) {
                        $errorExport[] = array($value['transaction_id'], $type == "money" ? 'Truy???n thi???u l?? do ho??n ti???n' : "Truy???n thi???u l?? do ho??n ??i???m");
                        continue;
                    }
                    $validId[] = $value['transaction_id'];
                    $validData[$value['transaction_id']] = $value;
                }

                $totalRecord = count($name);
                $totalError = $totalRecord;

                if (!empty($validId) && !empty($validData)) {
                    $dataInvalid = $this->getRecord($validId);
                    if (!empty($dataInvalid)) {
                        $upLoadFile = null;
                        $upLoadDir = sfConfig::get('sf_web_dir') . '/upload/report/';
                        if (!empty($files['image']['name'])) {
                            $upLoadFile = $upLoadDir . date("Ymdhis") . '_' . basename($files['image']['name']);
                        }
                        foreach ($dataInvalid as $key => $query) {
                            $val = $validData[$query['transaction_id']];

                            unset($validData[$query['transaction_id']]);

                            if (!empty($query) && in_array($query['refund_status'], PartnerRefundStatusEnum::getStatusCanRefund())) {
                                $curAmount = $type == "money" ? $query['amount'] : $query['viettelid_point'];

                                if ($val['refund_type'] == 1) {
                                    $trans_amount = $val['trans_amount'];
                                    if ($val['trans_amount'] >= $curAmount) {
                                        $errorExport[] = array($val['transaction_id'], $type == "money" ? 'Vui l??ng nh???p s??? ti???n nh??? h??n s??? ti???n thanh to??n' : 'Vui l??ng nh???p s??? ??i???m nh??? h??n s??? ??i???m thanh to??n');
                                        continue;
                                    }
                                } else {
                                    $trans_amount = $curAmount;
                                }

                                if($type == "money") {
                                  $payCode = str_replace("VIETTELID", "", $query['pay_code']);
                                  $payCode = str_replace(",", "", $payCode);
                                  if (in_array($payCode, VnPayPayCodeEnum::getListPayCodeVnPay())) {
                                    $this->processRefundVnPay($query, $trans_amount, $val, $upLoadFile, $errorExport, $countRC);
                                  } else {
                                    if (empty($val['originalRequestId'])) {
                                      $errorExport[] = array($val['transaction_id'], 'Truy???n thi???u m?? giao d???ch thanh to??n ph??a ViettelPay');
                                      continue;
                                    }
                                    $this->processRefundVtPay($query, $trans_amount, $val, $upLoadFile, $errorExport, $countRC);
                                  }
                                }else{
                                  $this->processRefundPoint($query, $trans_amount, $val, $upLoadFile, $errorExport, $countRC);
                                }

                            } else {
                                $errorExport[] = array($val['transaction_id'], 'Giao d???ch ???? ho??n ho???c kh??ng t???n t???i');
                            }
                        }
                        $success = $countRC;
                        //upload file neu co ban ghi thanh cong
                        if ($success > 0 && !empty($files['image']['tmp_name'])) {
                            move_uploaded_file($files['image']['tmp_name'], $upLoadFile);
                        }
                        $totalError = $totalRecord - $success;
                    }else{
                        foreach ($validData as $val){
                            $errorExport[] = array($val['transaction_id'], 'Giao d???ch ???? ho??n ho???c kh??ng t???n t???i');
                        }
                    }
                    if(count($validData)){
                        foreach ($validData as $key => $item){
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
        } catch (Exception $e) {
            VtHelper::writeLogValue('Error loading file "' . pathinfo($files['attach_file']['tmp_name'], PATHINFO_BASENAME) . '": ' . $e->getMessage());
            $this->getUser()->setFlash('error', 'C?? l???i trong qu?? tr??nh upload file. Vui l??ng ki???m tra l???i!');
        }
    }

    public function processRefundVnPay($query, $trans_amount, $val, $upLoadFile, &$errorExport, &$countRC){
      if ($trans_amount <= $query['amount']) {
        $paymentGateway = new PartnerWS();
        $refundPayment = $paymentGateway->refundVNPAYMoney($query['transaction_id'], $trans_amount * 100, $val['refund_type'],$query['request_id'],$query['created_at'],$this->getUser()->getUsername());
        if ($refundPayment["vnp_ResponseCode"] == 0) {
          $this->updateParam($query['id'], true, PartnerRefundStatusEnum::APPROVE);
          $this->insertRefundLog($query['transaction_id'], $trans_amount, $val['refund_type'], $val['trans_content'], $upLoadFile, $query['pay_code'], 0);
          $countRC++;
        } else {
          $errorExport[] = array($val['transaction_id'], !empty($refundPayment->error_msg) ? $refundPayment->error_msg : 'Ho??n ti???n th???t b???i');
          $this->updateParam($query['id'], true, PartnerRefundStatusEnum::CALL_REFUND_FAIL);
          $this->insertRefundLog($query['transaction_id'], $trans_amount, $val['refund_type'], $val['trans_content'], null, $query['pay_code'], 0);
        }
      }else {
        $errorExport[] = array($val['transaction_id'], 'Vui l??ng nh???p s??? ti???n nh??? h??n s??? ti???n giao d???ch');
      }
    }

    public function processRefundVtPay($query, $trans_amount, $val, $upLoadFile, &$errorExport, &$countRC){
      $paymentGateway = new PaymentGatewayWS();
      $checkPayment = $paymentGateway->checkTransaction($query['transaction_id']);
      if ($checkPayment && $checkPayment->error_code == "00" && $checkPayment->payment_status == "1") {
        if ($trans_amount <= $query['amount']) {
          $refundPayment = $paymentGateway->refundMoney($query['transaction_id'], $val['originalRequestId'], $val['refundType'], $trans_amount, $val['trans_content']);
          if ($refundPayment["errorCode"] == 0) {
            $this->updateParam($query['id'], true, PartnerRefundStatusEnum::APPROVE);
            $this->insertRefundLog($query['transaction_id'], $trans_amount, $val['refund_type'], $val['trans_content'], $upLoadFile, $query['pay_code'], 0);
            $countRC++;
          } else {
            $errorExport[] = array($val['transaction_id'], !empty($refundPayment->error_msg) ? $refundPayment->error_msg : 'Ho??n ti???n th???t b???i');
            $this->updateParam($query['id'], true, PartnerRefundStatusEnum::CALL_REFUND_FAIL);
            $this->insertRefundLog($query['transaction_id'], $trans_amount, $val['refund_type'], $val['trans_content'], null, $query['pay_code'], 0);
          }
        } else {
          $errorExport[] = array($val['transaction_id'], 'Vui l??ng nh???p s??? ti???n nh??? h??n s??? ti???n giao d???ch');
        }
      } else {
        $errorExport[] = array($val['transaction_id'], !empty($checkPayment->error_msg) ? $checkPayment->error_msg : 'Giao d???ch kh??ng ????? ??i???u ki???n ho??n ti???n');
      }
    }

    public function processRefundPoint($query, $trans_amount, $val, $upLoadFile, &$errorExport, &$countRC){
      $partnerWs = new PartnerWS();
      $checkPayment = $partnerWs->checkTransactionRefundPoint($query['transaction_id']);
      if ($checkPayment["errorCode"] == 0) {
        if ($trans_amount <= $query['viettelid_point']) {
          $refundPayment = $partnerWs->_plusPoint($query['msisdn'], $trans_amount, $query['transaction_id']);
          if ($refundPayment && $refundPayment['error_code'] == 0) {
            $this->updateParamViettelId($query['id'], true, PartnerRefundPointStatusEnum::SUCCESS);
            $this->insertRefundLog($query['transaction_id'], 0, $val['refund_type'], $val['trans_content'], $upLoadFile, 'VIETTELID', $trans_amount);
            $countRC++;
          } else {
            $errorExport[] = array($val['transaction_id'], !empty($refundPayment->error_msg) ? $refundPayment->error_msg : 'Ho??n ??i???m th???t b???i');
            $this->updateParamViettelId($query['id'], true, PartnerRefundPointStatusEnum::FAIL);
            $this->insertRefundLog($query['transaction_id'], 0, $val['refund_type'], $val['trans_content'], null, 'VIETTELID', $trans_amount);
          }
        } else {
          $errorExport[] = array($val['transaction_id'], 'Vui l??ng nh???p s??? ??i???m nh??? h??n s??? ??i???m giao d???ch');
        }
      } else {
        $errorExport[] = array($val['transaction_id'], !empty($checkPayment->error_msg) ? $checkPayment->error_msg : 'Giao d???ch kh??ng ????? ??i???u ki???n ho??n ??i???m');
      }
    }

    public function executeImportPointExcel(sfWebRequest $request)
    {
        parent::executeIndex($request);
        $this->importForm = new PartnerTransactionReportImportForm();
        if ($request->hasParameter('_import')) {
            $files = $request->getFiles($this->importForm->getName());
            $arrMime = array(
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/octet-stream',
                'application/zip'
            );
            $maxSizeImport = 2;
            if (($files['file']['size'] / (1024 * 1024)) > $maxSizeImport) {
                $this->getUser()->setFlash('error', 'File import kh??ng qu?? ' . $maxSizeImport . 'Mb.');
                $this->redirect('@partner_transaction_PartnerTransactionReport');
            }
            if (empty($files['file']) || !$files['file']['name']) {
                $this->getUser()->setFlash('error', 'Ch???n file tr?????c khi th???c hi???n import.');
                $this->redirect('@partner_transaction_PartnerTransactionReport');
            }
            if (!in_array($files['file']['type'], $arrMime)) {
                $this->getUser()->setFlash('error', '?????nh d???ng file kh??ng h???p l???.');
                $this->redirect('@partner_transaction_PartnerTransactionReport');
            }

            $arrFileUpload = array(
                'image/jpeg',
                'image/png',
                'application/pdf',
            );
            $maxSizeUpload = 5;
            if (!empty($files['image']['size']) && $files['image']['size'] / (1024 * 1024) > $maxSizeUpload) {
                $this->getUser()->setFlash('error', 'File upload kh??ng qu?? ' . $maxSizeUpload . ' MB');
                $this->redirect('@partner_transaction_PartnerTransactionReport');
            }
            if (!empty($files['image']['type']) && !in_array($files['image']['type'], $arrFileUpload)) {
                $this->getUser()->setFlash('error', '?????nh d???ng file kh??ng h???p l???. Vui l??ng ch???n file c?? ?????nh d???ng .pdf, .jpeg, .jpg, .png');
                $this->redirect('@partner_transaction_PartnerTransactionReport');
            }

            $this->importForm->bind(($request->getParameter($this->importForm->getName())), $files);
            if ($this->importForm->isValid()) {
                $logfile = 'import_channel.log';
                $this->processImport($files, $logfile, "viettelid");
                $this->redirect('@partner_transaction_PartnerTransactionReport');
            }
        }
        $this->setTemplate('index');
    }

    public function executeRefundPointPerId(sfWebRequest $request)
    {
        $this->formId = new PartnerTransactionReportForm();
        $this->formId->bind($request->getParameter($this->formId->getName()));
        if ($this->formId->isValid()) {
            $originalRequestId = $_POST['originalRequestId'];
            $refundType = $_POST['refundType'];
            $trans_content = $_POST['trans_content'];
            $trans_amount = $_POST['trans_amount'];
            $fileUpload = $_FILES['fileUpload'];
            $tranId = $_POST['tran_id'];
            $id = $_POST['id'];
            $results = $this->getRecordById($id);
            if (empty($results[0])) {
                $response['error'] = "Kh??ng t??m th???y th??ng tin giao d???ch";
                return $this->renderText(json_encode($response));
            }

            $results = $results[0];

            if (!in_array($results['refund_status'], PartnerRefundStatusEnum::getStatusCanRefund())) {
                $response['error'] = "Tr???ng th??i ????n h??ng kh??ng h???p l???";
                return $this->renderText(json_encode($response));
            }


            $key = $this::validateNullparams(array(
                'L?? do ho??n ??i???m' => $trans_content,
            ));
            if ($key) {
                $response['error'] = sprintf('Truy???n thi???u %s', $key);
                return $this->renderText(json_encode($response));
            }
            if ($refundType == 1) {
                if (empty($trans_amount)) {
                    $response['error'] = 'Truy???n thi???u s??? ??i???m';
                    return $this->renderText(json_encode($response));
                }
                $pos = strpos($trans_amount, '.');
                if (!is_numeric($trans_amount) || $pos !== false) {
                    $response['error'] = 'S??? ??i???m kh??ng h???p l???';
                    return $this->renderText(json_encode($response));
                }
                if ($trans_amount >= $results['viettelid_point']) {
                    $response['error'] = 'Vui l??ng nh???p s??? ??i???m nh??? h??n s??? ??i???m thanh to??n giao d???ch';
                    return $this->renderText(json_encode($response));
                }
            } else {
                $trans_amount = $results['viettelid_point'];
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

            $paymentGateway = new PartnerWS();
            $checkPayment = $paymentGateway->checkTransactionRefundPoint($results['transaction_id']);
            if ($checkPayment["error_code"] == 0) {
                $refundPayment = $paymentGateway->_plusPoint($results['msisdn'], $trans_amount, $results['transaction_id']);
                if ($refundPayment && $refundPayment['error_code'] == 0) {
                    $this->updateParamViettelId($results['id'], true, PartnerRefundPointStatusEnum::SUCCESS);
                    if (!empty($fileUpload)) {
                        move_uploaded_file($fileUpload['tmp_name'], $upLoadFile);
                    }
                    $this->insertRefundLog($tranId, null, $refundType, $trans_content, $upLoadFile, 'VIETTELID', $trans_amount);

                    $response['error'] = '';
                    return $this->renderText(json_encode($response));
                } else {
                    $this->updateParamViettelId($results['id'], true, PartnerRefundPointStatusEnum::FAIL);
                    $this->insertRefundLog($tranId, null, $refundType, $trans_content, null, 'VIETTELID', $trans_amount);
                    $response['error'] = !empty($refundPayment['responseWs']) ? $refundPayment['responseWs'] : 'Ho??n ??i???m th???t b???i';
                    return $this->renderText(json_encode($response));
                }
            } else {
                $response['error'] = !empty($checkPayment->error_msg) ? $checkPayment->error_msg : 'Giao d???ch kh??ng ????? ??i???u ki???n ho??n ??i???m';
                return $this->renderText(json_encode($response));
            }

        } else {
            $response['error'] = 'csrf token: CSRF attack detected.';
            return $this->renderText(json_encode($response));
        }

    }

    public function executeRefundMoneyPerId(sfWebRequest $request)
    {
        $this->formId = new PartnerTransactionReportForm();
        $this->formId->bind($request->getParameter($this->formId->getName()));
        if ($this->formId->isValid()) {
            $originalRequestId = $_POST['originalRequestId'];
            $refundType = $_POST['refundType'];
            $trans_content = $_POST['trans_content'];
            $trans_amount = $_POST['trans_amount'];
            $fileUpload = $_FILES['fileUpload'];
            $tranId = $_POST['tran_id'];
            $id = $_POST['id'];
            $results = $this->getRecordById($id);
            if (empty($results[0])) {
                $response['error'] = "Kh??ng t??m th???y th??ng tin giao d???ch";
                return $this->renderText(json_encode($response));
            }

            $results = $results[0];

            if (!in_array($results['refund_status'], PartnerRefundStatusEnum::getStatusCanRefund())) {
                $response['error'] = "Tr???ng th??i ????n h??ng kh??ng h???p l???";
                return $this->renderText(json_encode($response));
            }

            $key = $this::validateNullparams(array(
                'L?? do ho??n ti???n' => $trans_content,
            ));
            if ($key) {
                $response['error'] = sprintf('Truy???n thi???u %s', $key);
                return $this->renderText(json_encode($response));
            }


            if ($refundType == 1) {
                if (empty($trans_amount)) {
                    $response['error'] = 'Truy???n thi???u s??? ti???n';
                    return $this->renderText(json_encode($response));
                }
                $pos = strpos($trans_amount, '.');
                if (!is_numeric($trans_amount) || $pos !== false) {
                    $response['error'] = 'S??? ti???n kh??ng h???p l???';
                    return $this->renderText(json_encode($response));
                }
                if ($trans_amount >= ($results['amount'] - $results['fee_payment'])) {
                    $response['error'] = 'Vui l??ng nh???p s??? ti???n nh??? h??n s??? ti???n thanh to??n giao d???ch';
                    return $this->renderText(json_encode($response));
                }
            } else {
                $trans_amount = $results['amount'] - $results['fee_payment'];
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


            $payCode = str_replace("VIETTELID", "", $results['pay_code']);
            $payCode = str_replace(",", "", $payCode);
            if (in_array($payCode, VnPayPayCodeEnum::getListPayCodeVnPay())) {
                $paymentGateway = new PartnerWS();
                $checkPayment = $paymentGateway->refundVNPAYMoney($results['transaction_id'],$trans_amount*100, $trans_content,$refundType,$results['created_at'],$this->getUser()->getUsername());
                if ($checkPayment && $checkPayment['vnp_ResponseCode'] == "00") {
                    $this->updateParam($results['id'], true, PartnerRefundStatusEnum::APPROVE);
                    if (!empty($fileUpload)) {
                        move_uploaded_file($fileUpload['tmp_name'], $upLoadFile);
                    }
                    $this->insertRefundLog($tranId, $trans_amount, $refundType, $trans_content, $upLoadFile, $payCode, null);
                    $response['error'] = '';
                    return $this->renderText(json_encode($response));
                } else {
                    $this->updateParam($results['id'], true, PartnerRefundStatusEnum::CALL_REFUND_FAIL);

                    $this->insertRefundLog($tranId, $trans_amount, $refundType, $trans_content, null, $payCode, null);

                    $response['error'] = !empty($checkPayment->error_msg) ? $checkPayment->error_msg : 'G???i ho??n ti???n th???t b???i';
                    return $this->renderText(json_encode($response));
                }
            } else {
                if (empty($originalRequestId)) {
                    $response['error'] = 'Truy???n thi???u M?? giao d???ch thanh to??n ph??a ViettelPay';
                    return $this->renderText(json_encode($response));
                }
                $paymentGateway = new PaymentGatewayWS($results['transaction_id'], $results['transaction_id'], $trans_amount, "", "TRANS_INQUIRY", "MYVIETTEL5");
                $checkPayment = $paymentGateway->checkTransaction($results['transaction_id']);
                if ($checkPayment && $checkPayment->error_code == "00" && $checkPayment->payment_status == "1") {
                    $refundPayment = $paymentGateway->refundMoney($results['transaction_id'], $originalRequestId, $refundType, $trans_amount, $trans_content);
                    if ($refundPayment["errorCode"] == 0) {
                        $this->updateParam($results['id'], true, PartnerRefundStatusEnum::APPROVE);
                        if (!empty($fileUpload)) {
                            move_uploaded_file($fileUpload['tmp_name'], $upLoadFile);
                        }
                        $this->insertRefundLog($tranId, $trans_amount, $refundType, $trans_content, $upLoadFile, $payCode, null);
                        $response['error'] = '';
                        return $this->renderText(json_encode($response));
                    } else {
                        $this->updateParam($results['id'], true, PartnerRefundStatusEnum::CALL_REFUND_FAIL);

                        $this->insertRefundLog($tranId, $trans_amount, $refundType, $trans_content, null, $payCode, null);

                        $response['error'] = !empty($refundPayment->error_msg) ? $refundPayment->error_msg : 'G???i ho??n ti???n th???t b???i';
                        return $this->renderText(json_encode($response));
                    }
                } else {
                    $response['error'] = !empty($checkPayment->error_msg) ? $checkPayment->error_msg : 'Giao d???ch kh??ng ????? ??i???u ki???n ho??n ti???n';
                    return $this->renderText(json_encode($response));
                }
            }


        } else {
            $response['error'] = 'csrf token: CSRF attack detected.';
            return $this->renderText(json_encode($response));
        }

    }

    public function executeViewDetailTransaction(sfWebRequest $request)
    {
        $trans_content = 'Kiem tra giao dich';
        $tranId = $_POST['tran_id'];
        $results = $this->getRecord($tranId);

        if (empty($results[0])) {
            $response['error'] = "Kh??ng t??m th???y th??ng tin giao d???ch";
            return $this->renderText(json_encode($response));
        }
        $results = $results[0];
        $partnerWS = new PartnerWS();
        $resultTrans = $partnerWS->checkVNPAYtransaction($tranId, $trans_content, $results['request_id'], $results['created_at']);
        if ($resultTrans['vnp_ResponseCode'] == '00') {
            $resultTrans = (object)$resultTrans;

            $vnp_TransactionStatus = self::mapTransactionStatusName($resultTrans->vnp_TransactionStatus);
            if($resultTrans->vnp_TransactionType == '01'){
                $vnp_TransactionType = 'GD thanh to??n';
            }elseif($resultTrans->vnp_TransactionType == '02'){
                $vnp_TransactionType = 'Giao d???ch ho??n tr??? to??n ph???n';
            }elseif($resultTrans->vnp_TransactionType == '03'){
                $vnp_TransactionType = 'Giao d???ch ho??n tr??? to??n ph???n';
            }else{
                $vnp_TransactionType = '';
            }

            $html = $this->getPartial("PartnerTransactionReport/vnpay_detail", [
              "resultTrans" => $resultTrans,
              "vnp_TransactionStatus" => $vnp_TransactionStatus,
              "vnp_TransactionType" => $vnp_TransactionType
            ]);
            $template = $html;
            $errorCode = 0;
            $message = 'success';

            $return = array(
                'errorCode' => $errorCode,
                'error' => '',
                'message' => $message,
                'template' => $template
            );


            return $this->renderText(json_encode($return));
        } else {
            $response['error'] = "H??? th???ng b???n. Qu?? kh??ch vui l??ng th??? l???i sau";
            return $this->renderText(json_encode($response));
        }



    }

    public static function mapTransactionStatusName($type){
        switch ($type){
            case '00':
                $message =  'Th??nh c??ng';
                break;
            case '01':
                $message =  'Giao d???ch ch??a ho??n t???t';
                break;
            case '02':
                $message = 'Giao d???ch b??? l???i';
                break;
            case '04':
                $message =  'Giao d???ch ?????o (Kh??ch h??ng ???? b??? tr??? ti???n t???i Ng??n h??ng nh??ng GD ch??a th??nh c??ng ??? VNPAY)';
                break;
            case '05':
                $message =  'VNPAY ??ang x??? l?? giao d???ch n??y (GD ho??n ti???n)';
                break;
            case '06':
                $message =  'VNPAY ???? g???i y??u c???u ho??n ti???n sang Ng??n h??ng (GD ho??n ti???n)';
                break;
            case '07':
                $message =  'Giao d???ch b??? nghi ng??? gian l???n';
                break;
            case '09':
                $message =  'GD Ho??n tr??? b??? t??? ch???i';
                break;
            case '10':
                $message =  '???? giao h??ng';
                break;
            case '20':
                $message =  'Giao d???ch ???? ???????c thanh quy???t to??n cho merchant';
                break;
            default:
                $message =  '';
                break;
        }
        return $message;
    }

    public function updateParam($id, $check, $status)
    {
        if (!$check) {
            $query = Doctrine_Query::create()
                ->from('PartnerTransaction')
                ->update()
                ->set('refund_status', '?', $status)
                ->whereIn('transaction_id', $id)
                ->execute();
        } else {
            $query = Doctrine_Query::create()
                ->from('PartnerTransaction')
                ->update()
                ->set('refund_status', '?', $status)
                ->whereIn('id', $id)
                ->execute();
        }


        return $query;
    }
    public function updateParamViettelId($id, $check, $status)
    {
        if (!$check) {
            $query = Doctrine_Query::create()
                ->from('PartnerTransaction')
                ->update()
                ->set('refund_viettel_id', '?', $status)
                ->whereIn('transaction_id', $id)
                ->execute();
        } else {
            $query = Doctrine_Query::create()
                ->from('PartnerTransaction')
                ->update()
                ->set('refund_viettel_id', '?', $status)
                ->whereIn('id', $id)
                ->execute();
        }


        return $query;
    }

    public function getRecord($id)
    {
        $query = Doctrine_Query::create()
            ->from('PartnerTransaction')
            ->whereIn('transaction_id', $id)
            ->fetchArray();

        return $query;
    }

    public function getRecordById($id)
    {
        $query = Doctrine_Query::create()
            ->from('PartnerTransaction')
            ->whereIn('id', $id)
            ->fetchArray();

        return $query;
    }

    public function insertRefundLog($tranId, $refundAmount, $refundType, $reason, $filePath, $paycode, $viettelIDpoint)
    {
        try {
            $PartnerRefundLog = new PartnerRefundLog();
            $PartnerRefundLog->setTranId($tranId);
            $PartnerRefundLog->setRefundAmount((int)$refundAmount);
            $PartnerRefundLog->setRefundType($refundType);
            $PartnerRefundLog->setReason($reason);
            $PartnerRefundLog->setIp($_SERVER['REMOTE_ADDR']);
            $PartnerRefundLog->setUsername($this->getUser()->getUsername());
            $PartnerRefundLog->setFilePath($filePath);
            $PartnerRefundLog->setPayCode($paycode);
            $PartnerRefundLog->setViettelidPoint($viettelIDpoint);

            $PartnerRefundLog->save();
            return $PartnerRefundLog;
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
                $this->redirect('@partner_transaction_PartnerTransactionReport');
            }
        } else {
            $this->getUser()->setFlash('error', 'Thi???u tham s???');
            $this->redirect('@partner_transaction_PartnerTransactionReport');
        }
    }

    public function downloadFile($filePath, $fileName)
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
        unlink($filePath);
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
        $sheet->setCellValue('A1', $i18n->__('TH???NG K?? KH GIAO D???CH HO??N TI???N MERCHANT'));
        $sheet->getStyle('A1')->getFont()->setBold(true);

        $sheet->setCellValue('A2', $i18n->__('T??? ng??y %from% ?????n ng??y %to%', ['%from%' => $from, '%to%' => $to]));
        $sheet->setCellValue('A4', $i18n->__('STT'));
        $sheet->setCellValue('B4', $i18n->__('H??? th???ng My Viettel'));
        $sheet->setCellValue('B5', $i18n->__('M?? giao d???ch'));
        $sheet->setCellValue('C5', $i18n->__('Thu?? bao'));
        $sheet->setCellValue('D5', $i18n->__('S??? ti???n thanh to??n'));
        $sheet->setCellValue('E5', $i18n->__('S??? ??i???m thanh to??n'));
        $sheet->setCellValue('F5', $i18n->__('M?? t???'));
        $sheet->setCellValue('G5', $i18n->__('Lo???i giao d???ch'));
        $sheet->setCellValue('H5', $i18n->__('Tr???ng th??i giao d???ch'));
        $sheet->setCellValue('I5', $i18n->__('Tr???ng th??i ho??n ti???n'));


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

        for ($i = 'B'; $i <= 'I'; $i++) {
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
            $sheet->setCellValue("A$startRow", ++$key);
            $sheet->setCellValue("B$startRow", $result['transaction_id']);
            $sheet->setCellValue("C$startRow", $result['msisdn']);
            $sheet->setCellValue("D$startRow", $result['amount']);
            $sheet->setCellValue("E$startRow", $result['viettelid_point']);
            $sheet->setCellValue("F$startRow", $result['description']);
            $sheet->setCellValue("G$startRow", $result['pay_code']);
            $sheet->setCellValue("H$startRow", PartnerTransaction::getName($result['status']));
            $sheet->setCellValue("I$startRow", PartnerTransaction::getArr($result['refund_status']));
        }

        $sheet->getStyle('A4:I' . $startRow)->applyFromArray(
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
        $random = PartnerTransaction::genRandomNumber(4);
        $fileName = 'partner_refund_' . date('YmdHis') . '_' . $random . '.xlsx';
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

    public function executeCancelRefund(sfWebRequest $request)
    {
        $id = $request->getParameter('id');
        $refund = $this->updateParam($id, true, 4);
        if ($refund) {
            return $this->renderText(json_encode(['error' => '']));
        } else {
            return $this->renderText(json_encode(['error' => 'Vui l??ng th??? l???i sau']));
        }
    }
}
