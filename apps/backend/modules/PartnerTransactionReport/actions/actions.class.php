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
                $this->getUser()->setFlash('error', 'File import không quá ' . $maxSizeImport . 'Mb.');
                $this->redirect('@partner_transaction_PartnerTransactionReport');
            }
            if (empty($files['file']) || !$files['file']['name']) {
                $this->getUser()->setFlash('error', 'Chọn file trước khi thực hiện import.');
                $this->redirect('@partner_transaction_PartnerTransactionReport');
            }
            if (!in_array($files['file']['type'], $arrMime)) {
                $this->getUser()->setFlash('error', 'Định dạng file không hợp lệ.');
                $this->redirect('@partner_transaction_PartnerTransactionReport');
            }

            $arrFileUpload = array(
                'image/jpeg',
                'image/png',
                'application/pdf',
            );
            $maxSizeUpload = 5;
            if (!empty($files['image']['size']) && $files['image']['size'] / (1024 * 1024) > $maxSizeUpload) {
                $this->getUser()->setFlash('error', 'File upload không quá ' . $maxSizeUpload . ' MB');
                $this->redirect('@partner_transaction_PartnerTransactionReport');
            }
            if (!empty($files['image']['type']) && !in_array($files['image']['type'], $arrFileUpload)) {
                $this->getUser()->setFlash('error', 'Định dạng file không hợp lệ. Vui lòng chọn file có định dạng .pdf, .jpeg, .jpg, .png');
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
                        $errorExport[] = array($value['transaction_id'], 'Truyền thiếu mã giao dịch');
                        continue;
                    }

                    if (!in_array($value['refund_type'], ['1', '0'])) {
                        $errorExport[] = array($value['transaction_id'], 'Hình thức hoàn tiền không hợp lệ');
                        continue;
                    }
                    if ($value['refund_type'] == '1') {
                        if (empty($value['trans_amount'])) {
                            $errorExport[] = array($value['transaction_id'], $type == "money" ? 'Truyền thiếu số tiền' : "Truyền thiếu số điểm");
                            continue;
                        }
                        if (!is_numeric($value['trans_amount'])) {
                            $errorExport[] = array($value['transaction_id'], $type == "money" ? 'Số tiền không hợp lệ' : "Số điểm không hợp lệ");
                            continue;
                        }
                    }
                    if (empty($value['trans_content'])) {
                        $errorExport[] = array($value['transaction_id'], $type == "money" ? 'Truyền thiếu lý do hoàn tiền' : "Truyền thiếu lý do hoàn điểm");
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
                                        $errorExport[] = array($val['transaction_id'], $type == "money" ? 'Vui lòng nhập số tiền nhỏ hơn số tiền thanh toán' : 'Vui lòng nhập số điểm nhỏ hơn số điểm thanh toán');
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
                                      $errorExport[] = array($val['transaction_id'], 'Truyền thiếu mã giao dịch thanh toán phía ViettelPay');
                                      continue;
                                    }
                                    $this->processRefundVtPay($query, $trans_amount, $val, $upLoadFile, $errorExport, $countRC);
                                  }
                                }else{
                                  $this->processRefundPoint($query, $trans_amount, $val, $upLoadFile, $errorExport, $countRC);
                                }

                            } else {
                                $errorExport[] = array($val['transaction_id'], 'Giao dịch đã hoàn hoặc không tồn tại');
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
                            $errorExport[] = array($val['transaction_id'], 'Giao dịch đã hoàn hoặc không tồn tại');
                        }
                    }
                    if(count($validData)){
                        foreach ($validData as $key => $item){
                            $errorExport[] = array($key, 'Giao dịch không tồn tại');
                        }
                    }
                }
                if ($errorExport) {
                    $this->exportExcelFail($errorExport);
                }
                VtHelper::writeLogValue('Process Import Channel|Validated Channel OK.');

                VtHelper::logActions(1, sprintf('Hoàn tiền thành công: %d giao dịch', $success), $logfile);

                $this->getUser()->setFlash('success', 'Gửi hoàn tiền thành công: ' . $success . ' giao dịch. Gửi hoàn tiền thất bại ' . $totalError . ' giao dịch');
            } else {
                $this->getUser()->setFlash('error', '\'Gửi hoàn tiền không thành công. Số lượng không được vượt quá ' . $limit_upload . ' giao dịch');
            }
        } catch (Exception $e) {
            VtHelper::writeLogValue('Error loading file "' . pathinfo($files['attach_file']['tmp_name'], PATHINFO_BASENAME) . '": ' . $e->getMessage());
            $this->getUser()->setFlash('error', 'Có lỗi trong quá trình upload file. Vui lòng kiểm tra lại!');
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
          $errorExport[] = array($val['transaction_id'], !empty($refundPayment->error_msg) ? $refundPayment->error_msg : 'Hoàn tiền thất bại');
          $this->updateParam($query['id'], true, PartnerRefundStatusEnum::CALL_REFUND_FAIL);
          $this->insertRefundLog($query['transaction_id'], $trans_amount, $val['refund_type'], $val['trans_content'], null, $query['pay_code'], 0);
        }
      }else {
        $errorExport[] = array($val['transaction_id'], 'Vui lòng nhập số tiền nhỏ hơn số tiền giao dịch');
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
            $errorExport[] = array($val['transaction_id'], !empty($refundPayment->error_msg) ? $refundPayment->error_msg : 'Hoàn tiền thất bại');
            $this->updateParam($query['id'], true, PartnerRefundStatusEnum::CALL_REFUND_FAIL);
            $this->insertRefundLog($query['transaction_id'], $trans_amount, $val['refund_type'], $val['trans_content'], null, $query['pay_code'], 0);
          }
        } else {
          $errorExport[] = array($val['transaction_id'], 'Vui lòng nhập số tiền nhỏ hơn số tiền giao dịch');
        }
      } else {
        $errorExport[] = array($val['transaction_id'], !empty($checkPayment->error_msg) ? $checkPayment->error_msg : 'Giao dịch không đủ điều kiện hoàn tiền');
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
            $errorExport[] = array($val['transaction_id'], !empty($refundPayment->error_msg) ? $refundPayment->error_msg : 'Hoàn điểm thất bại');
            $this->updateParamViettelId($query['id'], true, PartnerRefundPointStatusEnum::FAIL);
            $this->insertRefundLog($query['transaction_id'], 0, $val['refund_type'], $val['trans_content'], null, 'VIETTELID', $trans_amount);
          }
        } else {
          $errorExport[] = array($val['transaction_id'], 'Vui lòng nhập số điểm nhỏ hơn số điểm giao dịch');
        }
      } else {
        $errorExport[] = array($val['transaction_id'], !empty($checkPayment->error_msg) ? $checkPayment->error_msg : 'Giao dịch không đủ điều kiện hoàn điểm');
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
                $this->getUser()->setFlash('error', 'File import không quá ' . $maxSizeImport . 'Mb.');
                $this->redirect('@partner_transaction_PartnerTransactionReport');
            }
            if (empty($files['file']) || !$files['file']['name']) {
                $this->getUser()->setFlash('error', 'Chọn file trước khi thực hiện import.');
                $this->redirect('@partner_transaction_PartnerTransactionReport');
            }
            if (!in_array($files['file']['type'], $arrMime)) {
                $this->getUser()->setFlash('error', 'Định dạng file không hợp lệ.');
                $this->redirect('@partner_transaction_PartnerTransactionReport');
            }

            $arrFileUpload = array(
                'image/jpeg',
                'image/png',
                'application/pdf',
            );
            $maxSizeUpload = 5;
            if (!empty($files['image']['size']) && $files['image']['size'] / (1024 * 1024) > $maxSizeUpload) {
                $this->getUser()->setFlash('error', 'File upload không quá ' . $maxSizeUpload . ' MB');
                $this->redirect('@partner_transaction_PartnerTransactionReport');
            }
            if (!empty($files['image']['type']) && !in_array($files['image']['type'], $arrFileUpload)) {
                $this->getUser()->setFlash('error', 'Định dạng file không hợp lệ. Vui lòng chọn file có định dạng .pdf, .jpeg, .jpg, .png');
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
                $response['error'] = "Không tìm thấy thông tin giao dịch";
                return $this->renderText(json_encode($response));
            }

            $results = $results[0];

            if (!in_array($results['refund_status'], PartnerRefundStatusEnum::getStatusCanRefund())) {
                $response['error'] = "Trạng thái đơn hàng không hợp lệ";
                return $this->renderText(json_encode($response));
            }


            $key = $this::validateNullparams(array(
                'Lý do hoàn điểm' => $trans_content,
            ));
            if ($key) {
                $response['error'] = sprintf('Truyền thiếu %s', $key);
                return $this->renderText(json_encode($response));
            }
            if ($refundType == 1) {
                if (empty($trans_amount)) {
                    $response['error'] = 'Truyền thiếu số điểm';
                    return $this->renderText(json_encode($response));
                }
                $pos = strpos($trans_amount, '.');
                if (!is_numeric($trans_amount) || $pos !== false) {
                    $response['error'] = 'Số điểm không hợp lệ';
                    return $this->renderText(json_encode($response));
                }
                if ($trans_amount >= $results['viettelid_point']) {
                    $response['error'] = 'Vui lòng nhập số điểm nhỏ hơn số điểm thanh toán giao dịch';
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
                    $response['error'] = 'Định dạng file không hợp lệ. Vui lòng chọn file có định dạng .pdf, .jpeg, .jpg, .png';
                    return $this->renderText(json_encode($response));
                }
                $maxSizeUpload = 5;
                if (($fileUpload['size'] / (1024 * 1024)) > $maxSizeUpload) {
                    $response['error'] = 'File upload không quá ' . $maxSizeUpload . ' MB';
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
                    $response['error'] = !empty($refundPayment['responseWs']) ? $refundPayment['responseWs'] : 'Hoàn điểm thất bại';
                    return $this->renderText(json_encode($response));
                }
            } else {
                $response['error'] = !empty($checkPayment->error_msg) ? $checkPayment->error_msg : 'Giao dịch không đủ điều kiện hoàn điểm';
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
                $response['error'] = "Không tìm thấy thông tin giao dịch";
                return $this->renderText(json_encode($response));
            }

            $results = $results[0];

            if (!in_array($results['refund_status'], PartnerRefundStatusEnum::getStatusCanRefund())) {
                $response['error'] = "Trạng thái đơn hàng không hợp lệ";
                return $this->renderText(json_encode($response));
            }

            $key = $this::validateNullparams(array(
                'Lý do hoàn tiền' => $trans_content,
            ));
            if ($key) {
                $response['error'] = sprintf('Truyền thiếu %s', $key);
                return $this->renderText(json_encode($response));
            }


            if ($refundType == 1) {
                if (empty($trans_amount)) {
                    $response['error'] = 'Truyền thiếu số tiền';
                    return $this->renderText(json_encode($response));
                }
                $pos = strpos($trans_amount, '.');
                if (!is_numeric($trans_amount) || $pos !== false) {
                    $response['error'] = 'Số tiền không hợp lệ';
                    return $this->renderText(json_encode($response));
                }
                if ($trans_amount >= ($results['amount'] - $results['fee_payment'])) {
                    $response['error'] = 'Vui lòng nhập số tiền nhỏ hơn số tiền thanh toán giao dịch';
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
                    $response['error'] = 'Định dạng file không hợp lệ. Vui lòng chọn file có định dạng .pdf, .jpeg, .jpg, .png';
                    return $this->renderText(json_encode($response));
                }
                $maxSizeUpload = 5;
                if (($fileUpload['size'] / (1024 * 1024)) > $maxSizeUpload) {
                    $response['error'] = 'File upload không quá ' . $maxSizeUpload . ' MB';
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

                    $response['error'] = !empty($checkPayment->error_msg) ? $checkPayment->error_msg : 'Gửi hoàn tiền thất bại';
                    return $this->renderText(json_encode($response));
                }
            } else {
                if (empty($originalRequestId)) {
                    $response['error'] = 'Truyền thiếu Mã giao dịch thanh toán phía ViettelPay';
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

                        $response['error'] = !empty($refundPayment->error_msg) ? $refundPayment->error_msg : 'Gửi hoàn tiền thất bại';
                        return $this->renderText(json_encode($response));
                    }
                } else {
                    $response['error'] = !empty($checkPayment->error_msg) ? $checkPayment->error_msg : 'Giao dịch không đủ điều kiện hoàn tiền';
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
            $response['error'] = "Không tìm thấy thông tin giao dịch";
            return $this->renderText(json_encode($response));
        }
        $results = $results[0];
        $partnerWS = new PartnerWS();
        $resultTrans = $partnerWS->checkVNPAYtransaction($tranId, $trans_content, $results['request_id'], $results['created_at']);
        if ($resultTrans['vnp_ResponseCode'] == '00') {
            $resultTrans = (object)$resultTrans;

            $vnp_TransactionStatus = self::mapTransactionStatusName($resultTrans->vnp_TransactionStatus);
            if($resultTrans->vnp_TransactionType == '01'){
                $vnp_TransactionType = 'GD thanh toán';
            }elseif($resultTrans->vnp_TransactionType == '02'){
                $vnp_TransactionType = 'Giao dịch hoàn trả toàn phần';
            }elseif($resultTrans->vnp_TransactionType == '03'){
                $vnp_TransactionType = 'Giao dịch hoàn trả toàn phần';
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
            $response['error'] = "Hệ thống bận. Quý khách vui lòng thử lại sau";
            return $this->renderText(json_encode($response));
        }



    }

    public static function mapTransactionStatusName($type){
        switch ($type){
            case '00':
                $message =  'Thành công';
                break;
            case '01':
                $message =  'Giao dịch chưa hoàn tất';
                break;
            case '02':
                $message = 'Giao dịch bị lỗi';
                break;
            case '04':
                $message =  'Giao dịch đảo (Khách hàng đã bị trừ tiền tại Ngân hàng nhưng GD chưa thành công ở VNPAY)';
                break;
            case '05':
                $message =  'VNPAY đang xử lý giao dịch này (GD hoàn tiền)';
                break;
            case '06':
                $message =  'VNPAY đã gửi yêu cầu hoàn tiền sang Ngân hàng (GD hoàn tiền)';
                break;
            case '07':
                $message =  'Giao dịch bị nghi ngờ gian lận';
                break;
            case '09':
                $message =  'GD Hoàn trả bị từ chối';
                break;
            case '10':
                $message =  'Đã giao hàng';
                break;
            case '20':
                $message =  'Giao dịch đã được thanh quyết toán cho merchant';
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
        $header = array('Mã giao dịch', 'Mô tả lỗi');
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
            $this->getUser()->setFlash('error', 'Thiếu tham số');
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
        $sheet->setCellValue('A1', $i18n->__('THỐNG KÊ KH GIAO DỊCH HOÀN TIỀN MERCHANT'));
        $sheet->getStyle('A1')->getFont()->setBold(true);

        $sheet->setCellValue('A2', $i18n->__('Từ ngày %from% Đến ngày %to%', ['%from%' => $from, '%to%' => $to]));
        $sheet->setCellValue('A4', $i18n->__('STT'));
        $sheet->setCellValue('B4', $i18n->__('Hệ thống My Viettel'));
        $sheet->setCellValue('B5', $i18n->__('Mã giao dịch'));
        $sheet->setCellValue('C5', $i18n->__('Thuê bao'));
        $sheet->setCellValue('D5', $i18n->__('Số tiền thanh toán'));
        $sheet->setCellValue('E5', $i18n->__('Số điểm thanh toán'));
        $sheet->setCellValue('F5', $i18n->__('Mô tả'));
        $sheet->setCellValue('G5', $i18n->__('Loại giao dịch'));
        $sheet->setCellValue('H5', $i18n->__('Trạng thái giao dịch'));
        $sheet->setCellValue('I5', $i18n->__('Trạng thái hoàn tiền'));


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
            return $this->renderText(json_encode(['error' => 'Vui lòng thử lại sau']));
        }
    }
}
