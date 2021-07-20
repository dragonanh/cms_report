<?php

require_once dirname(__FILE__) . '/../lib/vtManageBackendUserGeneratorConfiguration.class.php';
require_once dirname(__FILE__) . '/../lib/vtManageBackendUserGeneratorHelper.class.php';

/**
 * vtManageBackendUser actions.
 *
 * @package     imuzik
 * @subpackage  vtManageBackendUser
 * @author      TungTD2
 * @editor      HuyNQ28
 */
class vtManageBackendUserActions extends autovtManageBackendUserActions
{
//
//  public function executeBatchDeactive(sfWebRequest $request) {
//    $i18n = $this->getContext()->getI18N();
//    if ($this->getUser()->hasCredential('adminBackendUser') || $this->getUser()->hasCredential('admin')) {
//      $request->checkCSRFProtection();
//      $ids = $request->getParameter('ids');
//      $count = sfGuardUserTable::deactive($ids);
//      $i18n = $this->getContext()->getI18N();
//      $errorRecordsNumber = (count($ids) - $count);
//      if (!$count) {
//        $this->getUser()->setFlash('error', $errorRecordsNumber . ' ' . $i18n->__('người dùng ở trạng thái không phù hợp!'), true);
//      } else {
//        if ($errorRecordsNumber > 0) {
//          $this->getUser()->setFlash('error', $errorRecordsNumber . ' ' . $i18n->__('người dùng ở trạng thái không phù hợp!'), true);
//        }
//        $this->getUser()->setFlash('success', $count . ' ' . $i18n->__('người dùng đã bị khóa!'), true);
//      }
//      $this->redirect('@sf_guard_user');
//    } else {
//      $this->getUser()->setFlash('error', $i18n->__('Bạn không có quyền truy cập chức năng vừa lựa chọn!'));
//      $this->redirect('@accessDeniedPage');
//    }
//  }
//
//  public function executeBatchActive(sfWebRequest $request) {
//    $i18n = $this->getContext()->getI18N();
//    if ($this->getUser()->hasCredential('adminBackendUser') || $this->getUser()->hasCredential('admin')) {
//      $request->checkCSRFProtection();
//      $ids = $request->getParameter('ids');
//      $count = sfGuardUserTable::active($ids);
//      $i18n = $this->getContext()->getI18N();
//      $numberErrorRecords = count($ids) - $count;
//      if (!$count) {
//        $this->getUser()->setFlash('error', $numberErrorRecords . ' ' . $i18n->__('người dùng đã chọn ở trạng thái không phù hợp'), true);
//      } else {
//        if ($numberErrorRecords > 0) {
//          $this->getUser()->setFlash('error', $numberErrorRecords . ' ' . $i18n->__('người dùng đã chọn ở trạng thái không phù hợp'), true);
//        }
//        $this->getUser()->setFlash('success', $count . ' ' . $i18n->__('người dùng đã được mở khóa'), true);
//      }
//      $this->redirect('@sf_guard_user');
//    } else {
//      $this->getUser()->setFlash('error', $i18n->__('Bạn không có quyền truy cập chức năng vừa lựa chọn!'));
//      $this->redirect('@accessDeniedPage');
//    }
//  }
//
//  protected function processForm(sfWebRequest $request, sfForm $form) {
//    $i18n = $this->getContext()->getI18N();
//    if ($this->getUser()->hasCredential('adminBackendUser') || $this->getUser()->hasCredential('admin')) {
//      $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
//      if ($form->isValid()) {
//        $notice = $form->getObject()->isNew() ? $i18n->__('Đã thêm người dùng mới. ') : $i18n->__('Đã cập nhật thông tin người dùng. ');
//        $check_pass_update = $form->getObject()->isNew() ? true : false;
//        try {
//          $formObj = $request->getParameter($form->getName());
//          $sf_guard_user = $form->save();
//          if ($check_pass_update == false)
//            if ((trim($formObj['password']))!= null){
//              $sf_guard_user->setPassUpdateAt(null);
//              $sf_guard_user->save();
//            }
//        } catch (Doctrine_Validator_Exception $e) {
//
//          $errorStack = $form->getObject()->getErrorStack();
//
//          $message = get_class($form->getObject()) . ' has ' . count($errorStack) . " field" . (count($errorStack) > 1 ? 's' : null) . " with validation errors: ";
//          foreach ($errorStack as $field => $errors) {
//            $message .= "$field (" . implode(", ", $errors) . "), ";
//          }
//          $message = trim($message, ', ');
//
//          $this->getUser()->setFlash('error', $message);
//          return sfView::SUCCESS;
//        }
//
//        $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('form' => $form, 'object' => $sf_guard_user)));
//        if ($request->hasParameter('_save_and_exit')) {
//          $this->getUser()->setFlash('success', $notice . $i18n->__(''));
//
//          $this->redirect('@sf_guard_user');
//        } elseif ($request->hasParameter('_save_and_add')) {
//          $this->getUser()->setFlash('success', $notice . '' . $i18n->__('Bạn có thể thêm người dùng mới!'));
//
//          $this->redirect('@sf_guard_user_new');
//        } else {
//          $this->getUser()->setFlash('success', $notice);
//          $this->redirect(array('sf_route' => 'sf_guard_user_edit', 'sf_subject' => $sf_guard_user));
//        }
//      } else {
//        $this->getUser()->setFlash('error', $i18n->__('Đã có lỗi xảy ra, vui lòng kiểm tra lại!'), false);
//      }
//    } else {
//      $this->getUser()->setFlash('error', $i18n->__('Bạn không có quyền truy cập chức năng này!'));
//      $this->redirect('@accessDeniedPage');
//    }
//  }
//
//  public function executeIndex(sfWebRequest $request) {
//    $i18n = $this->getContext()->getI18N();
//    //verify permission to access
//    if ($this->getUser()->hasCredential('adminBackendUser') || $this->getUser()->hasCredential('admin')) {
//      // sorting
//      if ($request->getParameter('sort') && $this->isValidSortColumn($request->getParameter('sort'))) {
//        $this->setSort(array($request->getParameter('sort'), $request->getParameter('sort_type')));
//      }
//
//      // pager
//      if ($request->getParameter('page')) {
//        $this->setPage($request->getParameter('page'));
//      }
//
//      // max per page
//      if ($request->getParameter('max_per_page')) {
//        $this->setMaxPerPage($request->getParameter('max_per_page'));
//      }
//
//      $this->sidebar_status = $this->configuration->getListSidebarStatus();
//
//      $this->pager = $this->getPager();
//      $this->sort = $this->getSort();
//
//      if (count($this->getUser()->getAttribute('vtManageBackendUser.filters', array(), 'admin_module')) > 0) {
////        $this->getUser()->setFlash('notice', $i18n->__('Chỉ hiện các kết quả phù hợp!'));
//      }
//    }
//    // if user have not permission to access, return to homepage (after Login)
//    else {
//      $this->getUser()->setFlash('error', $i18n->__('Bạn không có quyền truy cập chức năng này!'));
//      $this->redirect('@accessDeniedPage');
//    }
//  }
//
//  public function executeNew(sfWebRequest $request) {
//    $i18n = $this->getContext()->getI18N();
//    //verify permission to access
//    if ($this->getUser()->hasCredential('adminBackendUser')|| $this->getUser()->hasCredential('admin')) {
//      $this->sidebar_status = $this->configuration->getNewSidebarStatus();
//      $this->form = $this->configuration->getForm();
//      $this->sf_guard_user = $this->form->getObject();
//    }
//    // if user have not permission to access, return to homepage (after Login)
//    else {
//      $this->getUser()->setFlash('error', $i18n->__('Bạn không có quyền truy cập chức năng này!'));
//      $this->redirect('@accessDeniedPage');
//    }
//  }
//
//  public function executeCreate(sfWebRequest $request) {
//    $i18n = $this->getContext()->getI18N();
//    //verify permission to access
//    if ($this->getUser()->hasCredential('adminBackendUser')|| $this->getUser()->hasCredential('admin')) {
//      $this->sidebar_status = $this->configuration->getNewSidebarStatus();
//      $this->form = $this->configuration->getForm();
//      $this->sf_guard_user = $this->form->getObject();
//
//      $this->processForm($request, $this->form);
//
//      $this->setTemplate('new');
//    }
//    // if user have not permission to access, return to homepage (after Login)
//    else {
//      $this->getUser()->setFlash('error', $i18n->__('Bạn không có quyền truy cập chức năng này!'));
//      $this->redirect('@accessDeniedPage');
//    }
//  }
//
//  public function executeEdit(sfWebRequest $request) {
//    $i18n = $this->getContext()->getI18N();
//    //verify permission to access
//    if ($this->getUser()->hasCredential('adminBackendUser')|| $this->getUser()->hasCredential('admin')) {
//      $this->sidebar_status = $this->configuration->getEditSidebarStatus();
//      $this->sf_guard_user = $this->getRoute()->getObject();
//      $this->form = $this->configuration->getForm($this->sf_guard_user);
//      $this->fields = $this->sf_guard_user->getTable()->getColumnNames();
//    }
//    // if user have not permission to access, return to homepage (after Login)
//    else {
//      $this->getUser()->setFlash('error', $i18n->__('Bạn không có quyền truy cập chức năng này!'));
//      $this->redirect('@accessDeniedPage');
//    }
//  }
//
//  public function executeShow(sfWebRequest $request) {
//    $i18n = $this->getContext()->getI18N();
//    //verify permission to access
//    if ($this->getUser()->hasCredential('adminBackendUser')|| $this->getUser()->hasCredential('admin')) {
//      $this->redirect('@sf_guard_user');
//    }
//    // if user have not permission to access, return to homepage (after Login)
//    else {
//      $this->getUser()->setFlash('error', $i18n->__('Bạn không có quyền truy cập chức năng này!'));
//      $this->redirect('@accessDeniedPage');
//    }
//  }
//
//  public function executeUpdate(sfWebRequest $request) {
//    $i18n = $this->getContext()->getI18N();
//    //verify permission to access
//    if ($this->getUser()->hasCredential('adminBackendUser')|| $this->getUser()->hasCredential('admin')) {
//      $this->sidebar_status = $this->configuration->getEditSidebarStatus();
//      $this->sf_guard_user = $this->getRoute()->getObject();
//      $this->form = $this->configuration->getForm($this->sf_guard_user);
//      $this->fields = $this->sf_guard_user->getTable()->getColumnNames();
//
//      $this->processForm($request, $this->form);
//
//      $this->setTemplate('edit');
//    }
//    // if user have not permission to access, return to homepage (after Login)
//    else {
//      $this->getUser()->setFlash('error', $i18n->__('Bạn không có quyền truy cập chức năng này!'));
//      $this->redirect('@accessDeniedPage');
//    }
//  }
//
//  /*
//   * @author: noinh@viettel.com.vn
//   * Check supperAdmin before delete
//   */
//
//  public function executeDelete(sfWebRequest $request) {
//
//    $this->redirect('@sf_guard_user');
////    $request->checkCSRFProtection();
////
////    $id = $this->getRoute()->getObject()->getId();
////    $sf_guard_user = $this->getRoute()->getObject();
////    $checkSuperAdmin = sfGuardUserTable::checkSuperAdmin($id);
////    $i18n = $this->getContext()->getI18N();
////
////    if ($checkSuperAdmin == 0) {
////      parent::executeDelete($request);
////    } else {
////      $this->getUser()->setFlash('error', $i18n->__('Không thể xóa người dùng này!'));
////      $this->redirect(array('sf_route' => 'sf_guard_user_edit', 'sf_subject' => $sf_guard_user));
////    }
//  }
//
//  /*
//   * @author: noinh@viettel.com.vn
//   * Check supperAdmin before delete
//   */
//
//  protected function executeBatchDelete(sfWebRequest $request) {
//    $this->redirect('@sf_guard_user');
////    $request->checkCSRFProtection();
////
////    $ids = $request->getParameter('ids');
////
////    $currentPage = $this->getPager()->getPage();
////
////    $checkSuperAdmin = sfGuardUserTable::checkSuperAdmin($ids);
////    $i18n = $this->getContext()->getI18N();
////
////    if ($checkSuperAdmin == 0) {
////      parent::executeBatchDelete($request);
////    } else {
////      $this->getUser()->setFlash('error', $i18n->__('Không thể xóa người dùng này!'));
//////            $this->redirect('@sf_guard_user');
////    }
////
////    if ($currentPage == $this->getPager()->getLastPage() + 1)
////      $this->redirect('@vt_comment_composer');
////
////    if (in_array($currentPage, $this->getPager()->getLinks()))
////      $this->setPage($currentPage);
////    else
////      $this->setPage($currentPage - 1);
//  }
    public function executeIndex(sfWebRequest $request)
    {
        parent::executeIndex($request);
        $this->importForm = new vtManageBackendUserImportForm();
        $this->cancelImportForm = new vtManageBackendUserCancelImportForm();
    }

    public function executeFilter(sfWebRequest $request)
    {
        $this->importForm = new vtManageBackendUserImportForm();
        $this->cancelImportForm = new vtManageBackendUserCancelImportForm();
        $this->setPage(1);

        if ($request->hasParameter('_reset')) {
            $this->setFilters($this->configuration->getFilterDefaults());

            $this->redirect('@sf_guard_user');
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

            $this->redirect('@sf_guard_user');
        }
        $this->sidebar_status = $this->configuration->getListSidebarStatus();
        $this->pager = $this->getPager();
        $this->sort = $this->getSort();

        $this->setTemplate('index');
    }

    public function executeImportExcel(sfWebRequest $request)
    {
        parent::executeIndex($request);
        $i18n = $this->getContext()->getI18N();
        $this->importForm = new vtManageBackendUserImportForm();

        if ($request->hasParameter('_import')) {
            $files = $request->getFiles($this->importForm->getName());
            $arrMime = array(
                'application/octet-stream',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            );
            $maxSizeImport = 2;
            if (($files['file']['size'] / (1024 * 1024)) > $maxSizeImport) {
                $this->getUser()->setFlash('error', $i18n->__('File import không quá ') . $maxSizeImport . 'MB.');
                $this->redirect('@sf_guard_user');
            }
            if (empty($files['file']) || !$files['file']['name']) {
                $this->getUser()->setFlash('error', $i18n->__('Chọn file trước khi thực hiện import.'));
                $this->redirect('@sf_guard_user');
            }
            if (!in_array($files['file']['type'], $arrMime)) {
                $this->getUser()->setFlash('error', $i18n->__('Định dạng file không hợp lệ.'));
                $this->redirect('@sf_guard_user');
            }
            $this->importForm->bind(($request->getParameter($this->importForm->getName())), $files);
            if ($this->importForm->isValid()) {
                $logfile = 'import_sf_user.log';
                $this->processImport($files, $logfile);
                $this->redirect('@sf_guard_user');
            }else{
                $this->getUser()->setFlash('error', $i18n->__('Có lỗi trong quá trình upload file. Vui lòng kiểm tra lại!'));
                $this->redirect('@sf_guard_user');
            }
        }
        $this->setTemplate('index');
    }

    public function processImport($files, $logfile)
    {
        $i18n = $this->getContext()->getI18N();
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
                VtHelper::writeLogValue('Process Import SfUser|Begin Import SfUser...');
                $countRC = 0;
                for ($row = 2; $row <= $highestRow; $row++) {
                    $arrValue[] = array(
                        'email' => strtolower(trim($sheet->getCell("A" . $row)->getValue())),
                        'user' => trim($sheet->getCell("B" . $row)->getValue()),
                        'phone' => trim($sheet->getCell("C" . $row)->getValue()),
                    );
                }
                $regexEmail = '/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i';
                foreach ($arrValue as $key => $value) {
                    if (empty($value['email'])) {
                        $errorExport[] = array($value['email'], $value['user'], $value['phone'], $i18n->__('Email không được bỏ trống'));
                        continue;
                    }
                    if (!preg_match($regexEmail, $value['email'])) {
                        $errorExport[] = array($value['email'], $value['user'], $value['phone'], $i18n->__('Email không đúng định dạng'));
                        continue;
                    }
                    if (!empty($value['phone']) && !is_numeric($value['phone'])) {
                        $errorExport[] = array($value['email'], $value['user'], $value['phone'], $i18n->__('Số điện thoại không đúng định dạng'));
                        continue;
                    }

//                    $getUserFromEmail = substr($value['email'], 0, strpos($value['email'], '@'));
                    $getUserFromEmail = stristr($value['email'], '@',true);
                    $validEmail[] = $getUserFromEmail;
                    $validData[] = $value;
                }
                $totalRecord = count($arrValue);
                $totalError = $totalRecord;

                if (!empty($validEmail)) {
                    $dataValid = $this->getRecord($validEmail);
                    if (!empty($dataValid)) {
                        foreach ($dataValid as $key => $query) {
                            $existUsername[$query['username']] = strtolower($query['username']);
                            $existEmail[$query['email_address']] = strtolower($query['email_address']);
                        }
                    }

                    if (count($validData)) {
                        foreach ($validData as $key => $item) {
                            $username = stristr($item['email'], '@',true);
                            if(!in_array($username, $existUsername)) {
                                $insert = $this->insertNewUser($item['email'],$username,'123456aA@');
                                if($insert != null){
                                    $countRC++;
                                }else{
                                    $errorExport[] = array($item['email'], $item['user'], $item['phone'], $i18n->__('Email đã tồn tại trong hệ thống'));
                                }
                            }else{
                                if(in_array($item['email'],$existEmail)){
                                    $errorExport[] = array($item['email'], $item['user'], $item['phone'], $i18n->__('Email đã tồn tại trong hệ thống'));
                                }else{
                                    $errorExport[] = array($item['email'], $item['user'], $item['phone'], $i18n->__('Tài khoản đã tồn tại trong hệ thống'));
                                }
                            }

                        }
                    }
                    $success = $countRC;

                    $totalError = $totalRecord - $success;
                }

                if ($errorExport) {
                    $this->exportExcelFail($errorExport);
                }
                VtHelper::writeLogValue('Process Import SfUser|Validated SfUser OK.');

                $this->getUser()->setFlash('success', $i18n->__('Lưu thành công: ' . $success . ' bản ghi. Lưu thất bại ' . $totalError . ' bản ghi'));
            } else {
                $this->getUser()->setFlash('error', 'Lưu không thành công. Số lượng không được vượt quá ' . $limit_upload . ' tài khoản');
            }
        } catch
        (Exception $e) {
            VtHelper::writeLogValue('Error loading file "' . pathinfo($files['attach_file']['tmp_name'], PATHINFO_BASENAME) . '": ' . $e->getMessage());
            $this->getUser()->setFlash('error', $i18n->__('Có lỗi trong quá trình upload file. Vui lòng kiểm tra lại!'));
        }
    }

    public function getRecord($id)
    {
        $query = Doctrine_Query::create()
            ->from('sfGuardUser')
            ->whereIn('username', $id)
            ->fetchArray();

        return $query;
    }

    public function insertNewUser($email, $username, $password)
    {
        try {
            $user = new sfGuardUser();
            $user->setEmailAddress($email);
            $user->setUsername($username);
            $user->setPassword($password);
//            $user->setFirstName($arguments['first_name']);
//            $user->setLastName($arguments['last_name']);
            $user->setIsActive(true);
            $user->setIsSuperAdmin(0);
            $user->setPassUpdateAt(date('Y-m-d H:i:s'));
//            $user->setSfGuardUserPermission(2);
            $user->save();
            $permission = new sfGuardUserPermission();
            $permission->setUserId($user->getId());
            $permission->setPermissionId(2);
            $permission->save();
            return $user;
        } catch (Exception $e) {
            return null;
        }
    }

    public function exportExcelFail($results)
    {
        $i18n = $this->getContext()->getI18N();
        $fileDesName = date('YmdHis') . "_import_user_fail";
        $fileDes = sfConfig::get('sf_cache_dir') . '/' . $fileDesName . '.xlsx';
        $header = array('Email(*)', $i18n->__('Tên'), 'SĐT', $i18n->__('Mô tả lỗi'));
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
        $i18n = $this->getContext()->getI18N();
        if ($fileName = $request->getParameter('file_name')) {
            $filePath = sprintf('%s/%s.xlsx', sfConfig::get('sf_cache_dir'), $fileName);
            if (is_file($filePath)) {
                $this->downloadFile($filePath, 'Fail_Create_User_' . date('YmdHis', strtotime('now')) . '.xlsx');
            } else {
                $this->redirect('@sf_guard_user');
            }
        } else {
            $this->getUser()->setFlash('error', $i18n->__('Thiếu tham số'));
            $this->redirect('@sf_guard_user');
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

    protected function buildQuery()
    {

        $query = parent::buildQuery();

        if (sfContext::getInstance()->getUser()->checkPermission('admin')) {
            $query = parent::buildQuery();
        } else {
            $alias = $query->getRootAlias();
            $query->select('*')
                ->innerJoin($alias . '.sfGuardUserPermission sfgp');
            $query->andWhere($alias . '.id = sfgp.user_id');

            $query->andWhereIn('sfgp.permission_id', ['2', '3', '6']);
        }

        return $query;
    }

    protected function processForm(sfWebRequest $request, sfForm $form)
    {
        $i18n = $this->getContext()->getI18N();
        $formValue = $request->getParameter($form->getName());
        $blacklist = sfConfig::get('app_blacklist', array());
        $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
        if (in_array($formValue['password'], $blacklist)) {
            $this->getUser()->setFlash('error', $i18n->__('Mật khẩu nằm trong blacklist'), false);
        } else {
            if ($form->isValid()) {
                $notice = $form->getObject()->isNew() ? 'The item was created successfully.' : 'The item was updated successfully.';

                try {
                    $sf_guard_user = $form->save();
                } catch (Doctrine_Validator_Exception $e) {

                    $errorStack = $form->getObject()->getErrorStack();

                    $message = get_class($form->getObject()) . ' has ' . count($errorStack) . " field" . (count($errorStack) > 1 ? 's' : null) . " with validation errors: ";
                    foreach ($errorStack as $field => $errors) {
                        $message .= "$field (" . implode(", ", $errors) . "), ";
                    }
                    $message = trim($message, ', ');

                    $this->getUser()->setFlash('error', $message);
                    return sfView::SUCCESS;
                }

                $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('form' => $form, 'object' => $sf_guard_user)));

                if ($request->hasParameter('_save_and_exit')) {
                    $this->getUser()->setFlash('success', $notice);
                    $this->redirect('@sf_guard_user');
                } elseif ($request->hasParameter('_save_and_add')) {
                    $this->getUser()->setFlash('success', $notice . ' You can add another one below.');

                    $this->redirect('@sf_guard_user_new');
                } else {
                    $this->getUser()->setFlash('success', $notice);

                    $this->redirect(array('sf_route' => 'sf_guard_user_edit', 'sf_subject' => $sf_guard_user));
                }
            } else {
                $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
            }
        }

    }

    public function executeImportExcelCancel(sfWebRequest $request)
    {
        parent::executeIndex($request);
        $this->cancelImportForm = new vtManageBackendUserCancelImportForm();
        $i18n = $this->getContext()->getI18N();
        if ($request->hasParameter('_import')) {
            $files = $request->getFiles($this->cancelImportForm->getName());
            $arrMime = array(
                'application/octet-stream',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            );
            $maxSizeImport = 2;
            if (($files['file']['size'] / (1024 * 1024)) > $maxSizeImport) {
                $this->getUser()->setFlash('error', $i18n->__('File import không quá ' . $maxSizeImport . 'MB.'));
                $this->redirect('@sf_guard_user');
            }
            if (empty($files['file']) || !$files['file']['name']) {
                $this->getUser()->setFlash('error', $i18n->__('Chọn file trước khi thực hiện import.'));
                $this->redirect('@sf_guard_user');
            }
            if (!in_array($files['file']['type'], $arrMime)) {
                $this->getUser()->setFlash('error', $i18n->__('Định dạng file không hợp lệ.'));
                $this->redirect('@sf_guard_user');
            }

            $this->cancelImportForm->bind(($request->getParameter($this->cancelImportForm->getName())), $files);
            if ($this->cancelImportForm->isValid()) {
                $logfile = 'import_sf_cancel_user.log';
                $this->processImportCanel($files, $logfile);
                $this->redirect('@sf_guard_user');
            }else{
                $this->getUser()->setFlash('error', $i18n->__('Có lỗi trong quá trình upload file. Vui lòng kiểm tra lại!'));
                $this->redirect('@sf_guard_user');
            }
        }
        $this->setTemplate('index');
    }

    public function processImportCanel($files, $logfile)
    {
        $i18n = $this->getContext()->getI18N();
        $limit_upload = sfConfig::get("app_limit_channel_upload", '1000');
        try {
            $errorExport = [];
            // Read your Excel workbook
            $inputFileType = PHPExcel_IOFactory::identify($files['file']['tmp_name']);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($files['file']['tmp_name']);
            // Get worksheet dimensions
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            if ($highestRow <= ($limit_upload + 1)) {//+1 vi dong dau tien cua file la tieu de
                $success = 0;
                VtHelper::writeLogValue('Process Import cancel SfUser|Begin Import cancel SfUser...');
                $totalRecord = 0;
                $arrValue = [];
                $totalError = 0;

                for ($row = 2; $row <= $highestRow; $row++) {
                    $totalRecord++;
                    $username = strtolower(trim($sheet->getCell("A" . $row)->getValue()));
                    if(!empty($username)) {
                        $arrValue[] = $username;
                    }else{
                        $errorExport[] = array($username, $i18n->__('Tên đăng nhập không được bỏ trống'));
                    }
                }

                $existUsername = [];
                if (!empty($arrValue)) {
                    $dataValid = $this->getRecord($arrValue);

//                    if (!empty($dataValid)) {
                        foreach ($dataValid as $query) {
                            $existUsername[$query['username']] = $query['username'];
                        }

                        foreach ($arrValue as $key => $item){
                            if(!in_array($item, $existUsername)) {
                                $errorExport[] = array($item, $i18n->__('Tên đăng nhập không tồn tại'));
                                unset($arrValue[$key]);
                            }
                        }

                        $this->updateParam($existUsername);
//                    }

                    $success = count($arrValue);
                    $totalError = count($errorExport);
                }

                if ($errorExport) {
                    $this->exportExcelCancelFail($errorExport);
                }
                VtHelper::writeLogValue('Process Import cancel SfUser|Validated SfUser OK.');

                VtHelper::logActions(1, sprintf($i18n->__('Lưu thành công: %d bản ghi'), $success), $logfile);

                $this->getUser()->setFlash('success', $i18n->__('Lưu thành công: ' . $success . ' bản ghi. Lưu thất bại ' . $totalError . ' bản ghi'));
            } else {
                $this->getUser()->setFlash('error', $i18n->__('Lưu không thành công. Số lượng không được vượt quá ' . $limit_upload . ' tài khoản'));
            }
        } catch
        (Exception $e) {
            VtHelper::writeLogValue('Error loading file "' . pathinfo($files['attach_file']['tmp_name'], PATHINFO_BASENAME) . '": ' . $e->getMessage());
            $this->getUser()->setFlash('error', $i18n->__('Có lỗi trong quá trình upload file. Vui lòng kiểm tra lại!'));
        }
    }

    public function updateParam($username)
    {
        if(!is_array($username))
            $username = array($username);
        $query = Doctrine_Query::create()
            ->from('SfGuardUser')
            ->update()
            ->set('is_active', '?', 0)
            ->andWhereIn('username', array_values($username))
            ->execute();


        return $query;
    }

    public function exportExcelCancelFail($results)
    {
        $i18n = $this->getContext()->getI18N();
        $fileDesName = date('YmdHis') . "_Fail_Block_User";
        $fileDes = sfConfig::get('sf_cache_dir') . '/' . $fileDesName . '.xlsx';
        $header = array('username(*)', $i18n->__('Mô tả lỗi'));
        $writer = new spoutHelper($fileDes);
        $writer->writeHeaderRow($header);
        foreach ($results as $key => $result) {
            $writer->writeRow($result);
        }
        $writer->close();
        $this->getUser()->setFlash('fileImportCancelFail', $fileDesName);
    }

    public function executeDownloadFileCancelImportFail(sfWebRequest $request)
    {
        $i18n = $this->getContext()->getI18N();
        if ($fileName = $request->getParameter('file_name')) {
            $filePath = sprintf('%s/%s.xlsx', sfConfig::get('sf_cache_dir'), $fileName);
            if (is_file($filePath)) {
                $this->downloadFile($filePath, 'Fail_Block_User_' . date('YmdHis', strtotime('now')) . '.xlsx');
            } else {
                $this->redirect('@sf_guard_user');
            }
        } else {
            $this->getUser()->setFlash('error', $i18n->__('Thiếu tham số'));
            $this->redirect('@sf_guard_user');
        }
    }

    public function executeDownloadFileSample(sfWebRequest $request)
    {
        $filePath = sprintf('%s/upload/sample/create_user_sample.xlsx', sfConfig::get('sf_web_dir'));
        if (is_file($filePath)) {
            $this->downloadFile($filePath, 'Create_User_' . date('YmdHis') . '.xlsx', 0);
        } else {
            $this->redirect('@sf_guard_user');
        }
    }

    public function executeDownloadFileSampleCancel(sfWebRequest $request)
    {
        $filePath = sprintf('%s/upload/sample/block_user_sample.xlsx', sfConfig::get('sf_web_dir'));
        if (is_file($filePath)) {
            $this->downloadFile($filePath, 'Block_User_' . date('YmdHis') . '.xlsx', 0);
        } else {
            $this->redirect('@sf_guard_user');
        }
    }

}
