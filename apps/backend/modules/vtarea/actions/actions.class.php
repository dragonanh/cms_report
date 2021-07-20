<?php
/**
 * vtRefundReport actions.
 *
 * @package    cms_ctt
 * @subpackage vtRefundReport
 * @author     viettel
 * @version    SVN: $Id: actions.class.php_bak.bak 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class vtareaActions extends sfActions
{
    const STATUS_ACTIVE = 1;

    public function executeIndex(sfWebRequest $request)
    {
        $this->importForm = new vtareaFormImportExcel();
        $this->formId = new vtareaReportForm();
        $this->setTemplate('index');
    }

    public function executeConfirmImportExcel(sfWebRequest $request)
    {
        try{
            $this->importForm = new vtareaFormImportExcel();
            if ($request->hasParameter('_import')) {
                $files = $request->getFiles($this->importForm->getName());
//                $arrMime = array(
//                    'application/vnd.ms-excel',
//                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
//                    'application/wps-office.xlsx',
//                    'application/wps-office.xls'
//                );
                $maxSizeImport = 50;
                if (($files['file']['size'] / (1024 * 1024)) > $maxSizeImport) {
                    $this->getUser()->setFlash('error', 'File import không quá ' . $maxSizeImport . 'Mb.');
                    $this->redirect('@vt_area_VtAreaImport');
                }
                if (empty($files['file']) || !$files['file']['name']) {
                    $this->getUser()->setFlash('error', 'Chọn file trước khi thực hiện import.');
                    $this->redirect('@vt_area_VtAreaImport');
                }
//                if (!in_array($files['file']['type'], $arrMime)) {
//                    $this->getUser()->setFlash('error', 'Định dạng file không hợp lệ.');
//                    $this->redirect('@vt_area_VtAreaImport');
//                }

                $this->importForm->bind(($request->getParameter($this->importForm->getName())), $files);
                if ($this->importForm->isValid()) {
                    $data = $this->processImportErrors($files);
                    $countSuccess = $data['0'];
                    $dataErrors = $data['1'];
                    $this->countSuccess = $countSuccess;
                    $this->dataErrors = $dataErrors;
                    move_uploaded_file($files['file']['tmp_name'], './' . 'vt_area.xlsx');
                    $this->setTemplate('index');
                }
                $this->setTemplate('index');
            }
        }catch (Exception $e){
            $this->redirect('@vt_area_VtAreaImport');
        }
    }

    public function executeImportExcel(sfWebRequest $request)
    {
        try{
            $this->postCurl('http://10.58.45.146:8080/index.php/vtArea/crontab');
            $this->getUser()->setFlash('success', 'Import thành công');
            $this->redirect('@vt_area_VtAreaImport');
        }catch (Exception $e){
            $this->redirect('@vt_area_VtAreaImport');
        }
    }

    public function processImportErrors($files)
    {
        $inputFileType = PHPExcel_IOFactory::identify($files['file']['tmp_name']);
        // Read your Excel workbook
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($files['file']['tmp_name']);
        // Get worksheet dimensions
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        //    VtHelper::writeLogValue('Process Import Channel|Begin Import Channel...');
        for ($row = 2; $row <= $highestRow; $row++) {
            $imports[$row] = array(
                'code' => trim($sheet->getCell("A" . $row)->getValue()),
                'parent_code' => trim($sheet->getCell("B" . $row)->getValue()),
                'name' => trim($sheet->getCell("C" . $row)->getValue()),
                'full_name' => trim($sheet->getCell("D" . $row)->getValue()),
                'province' => trim($sheet->getCell("E" . $row)->getValue()),
                'district' => trim($sheet->getCell("F" . $row)->getValue()),
                'precinct' => trim($sheet->getCell("G" . $row)->getValue()),
                'street_block' => trim($sheet->getCell("H" . $row)->getValue()),
                'street' => trim($sheet->getCell("I" . $row)->getValue()),
                'province_name' => trim($sheet->getCell("J" . $row)->getValue()),
                'district_name' => trim($sheet->getCell("K" . $row)->getValue()),
                'precinct_name' => trim($sheet->getCell("L" . $row)->getValue()),
            );
        }
        $dataErrors = [];
        $countSuccess = 0;
        foreach ($imports as $key => $value) {
            if(self::rowEmptyExcel($value)){
                continue;
            }
            $errors = [];
            if (empty($value['code'])) {
                $errors[] = 'Truyền thiếu mã giao dịch code';
            }
            if (empty($value['name'])) {
                $errors[] = 'Truyền thiếu trường name';
            }
            if (empty($value['full_name'])) {
                $errors[] = 'Truyền thiếu trường full_name';
            }
            if ((!empty($value['province']) && empty($value['province_name'])) || (empty($value['province']) && !empty($value['province_name']))) {
                $errors[] = 'Truyền thiếu trường province hoặc province_name';
            }
            if ((!empty($value['district']) && empty($value['district_name'])) || (empty($value['district']) && !empty($value['district_name']))) {
                $errors[] = 'Truyền thiếu trường district hoặc district_name';
            }
            if ((!empty($value['precinct']) && empty($value['precinct_name'])) || (empty($value['precinct']) && !empty($value['precinct_name']))) {
                $errors[] = 'Truyền thiếu trường precinct hoặc precinct_name';
            }
            if(empty($errors)){
                $countSuccess++;
                continue;
            }
            $value['errors'] = $errors;
            $dataErrors[$key] = $value;
        }
        return [$countSuccess, $dataErrors];
    }
    public static function rowEmptyExcel($value)
    {
        $count = 0;
        foreach ($value as $key => $v){
            if(!$v && $v != '0'){
                $count++;
            }
        }
        if($count == count($value)){
            return true;
        }
        return false;
    }

    public function executeCrontab(sfWebRequest $request)
    {
        $command = '/home/wapusr/env/run/php-5.6.31/bin/php -c /home/wapusr/env/run/php-5.6.31/lib/php.ini /home/wapusr/apps/www/cms_report_ctt/lib/crontab/ImportArea.php > /home/wapusr/apps/www/cms_report_ctt/log/default.log';
        exec($command, $output);
        die('end');

    }

    public function postCurl($_url, $_data = array(), $method = 'POST', $timeoutSecond = 2)
    {
        try {
            $mfields = '';
            if (!empty($_data)) {
                foreach ($_data as $key => $val) {
                    $mfields .= $key . '=' . $val . '&';
                }
            }
            rtrim($mfields, '&');
            $pst = curl_init();
            curl_setopt($pst, CURLOPT_URL, $_url);
            curl_setopt($pst, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($pst, CURLOPT_POST, count($_data));
            curl_setopt($pst, CURLOPT_POSTFIELDS, $mfields);
            curl_setopt($pst, CURLOPT_RETURNTRANSFER, 1);
//            curl_setopt($pst, CURLOPT_CONNECTTIMEOUT_MS, 3000); //so milisecond timeout khi ket noi
            curl_setopt($pst, CURLOPT_TIMEOUT, 1); //so giay timeout hoac su dung milisecond voi CURLOPT_TIMEOUT_MS
            //curl_setopt($pst, CURLOPT_CONNECTTIMEOUT, 2); //so giay timeout khi ket noi
            curl_setopt($pst, CURLOPT_PROXY, false);
            if ($timeoutSecond != 0) {
                curl_setopt($pst, CURLOPT_TIMEOUT, $timeoutSecond); //so giay timeout hoac su dung milisecond voi CURLOPT_TIMEOUT_MS
            }
            $res = curl_exec($pst);
            curl_close($pst);
            return $res;
            return json_encode(array("errorCode" => 0, "message" => "Success"));
        } catch (Exception $ex) {
            return json_encode(array("errorCode" => -1, "message" => "Loi goi WS:" . $ex->getMessage()));
        }
    }


    public function searchCode($code)
    {
        $query = Doctrine_Core::getTable('VtArea')
            ->createQuery('va')
            ->where('va.code = ?', $code);
        return $query->execute();
    }

    public function executeDownloadFileSample(sfWebRequest $request){
        $filePath = sprintf('%s/upload/sample/vt_area.xlsx', sfConfig::get('sf_web_dir'));
        if (is_file($filePath)) {
            $this->downloadFile($filePath, 'vt_area.xlsx', 0);
        } else {
            $this->redirect('@vt_area_VtAreaImport');
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
        if($isDeleteFile) unlink($filePath);
        return;
    }
}
