<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__) . '/../lib/BasesfGuardAuthActions.class.php');

/**
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: actions.class.php 23319 2009-10-25 12:22:23Z Kris.Wallsmith $
 */
class sfGuardAuthActions extends BasesfGuardAuthActions
{

    //tuanbm ghi de ham logout//xoa tat ca session va cookie
    public function executeSignout($request)
    {
        $this->getUser()->signOut();

        //tuanbm: them ham xoa cookie va reset SESSION
        session_unset();
        session_destroy();

        //    $signoutUrl = sfConfig::get('app_sf_guard_plugin_success_signout_url', $request->getReferer());
        //    $signoutUrl = "http://localhost/backend_dev.php/login";
        //    $this->redirect('' != $signoutUrl ? $signoutUrl : '@homepage');
        //    $this->redirect('' != $signoutUrl ? $signoutUrl : '@sf_guard_signin');//tuanbm
        $this->redirectLogin($request); //tuanbm code
    }

    private function redirectLogin($request)
    {
        //tuanbm code them 1 doan de dam bao luon login theo dung duong dan:  http://localhost/backend.php/login
        $domain = $request->getUriPrefix();
        $linkLogin = $domain . sfcontext::getinstance()->getRouting()->generate("sf_guard_signin");
        $this->redirect($linkLogin);
    }

    public function executeSignin($request)
    {
        //tuanbm code them 1 doan de dam bao luon login theo dung duong dan:  http://localhost/backend.php/login
        $domain = $request->getUriPrefix();
        $linkLogin = $domain . sfContext::getinstance()->getRouting()->generate("sf_guard_signin");
        if ($linkLogin !== $request->getUri()) {
            return $this->redirect($linkLogin);
        }

        $user = $this->getUser();
        if ($user->isAuthenticated()) {
            return $this->redirect('@homepage');
        }

        $class = sfConfig::get('app_sf_guard_plugin_signin_form', 'sfGuardFormAdminSignin');
        $this->form = new $class();
        $this->change_password = $change_password = $request->getParameter('change_password', 0);
        if ($change_password == 1) {
            $this->form = new VtSignInChangePasswordForm();
        }
        $form = $this->form;
        if ($request->isMethod('post')) {
            $i18n = sfContext::getInstance()->getI18N();
            $formValue = $request->getParameter($form->getName());
            $blacklist = sfConfig::get('app_blacklist', array());
            $form->bind($request->getParameter($form->getName()));
            //      $this->form->bind($request->getParameter('signin'));
            if ($change_password == 0) {
                $username = $form['username']->getValue();
                if ($username && $sf_user = sfGuardUserTable::getInstance()->getUserByUsername($username)) {

                    $is_lock = $sf_user->getIsLockSignin();
                    if ($is_lock == 1) {
                      $time_lock = time() - 600;
                      if ($sf_user->getLockedTime() >= $time_lock) {
                        $this->getUser()->setFlash('error', $i18n->__('Tài khoản của bạn đã bị khóa trong vòng 10 phút.'));

                        return;
                      } else {
                        sfGuardUserTable::getInstance()->setUnLockUser($username);
                        VtUserSigninLockTable::getInstance()->resetUserSigninLock($username);
                      }
                    }


                    if ($form->isValid()) {
                        $user->setIpAddress($this->getRequest()->getHttpHeader('addr', 'remote'));
                        $user->setUserAgent($this->getRequest()->getHttpHeader('User-Agent'));

                        if ((!$sf_user->getPassUpdateAt() || (time() - strtotime($sf_user->getPassUpdateAt()) > sfConfig::get('app_passuser_lifetime', 7776000)))) {
                          $this->form = new VtSignInChangePasswordForm();
                          $this->form->setUserName($username);
                          $this->change_password = 1;
                          $this->getUser()->setFlash('notice', $i18n->__('Mật khẩu của bạn đã quá 90 ngày không được thay đổi hoặc đã bị reset. Vui lòng thay đổi mật khẩu'));
                          return;
                        }

                        $this->getUser()->signin($form->getValue('user'), $form->getValue('remember'));
                        VtUserSigninLockTable::getInstance()->resetUserSigninLock($username);

                        // always redirect to a URL set in app.yml
                        // or to the referer
                        // or to the homepage
                        $this->redirect('@homepage');
                        //            return $this->redirect('@homepage');
                    } else {
                        $time_now = time();
                        $time_log = $time_now - 3600;
                        $failed_times = VtUserSigninLockTable::getInstance()->getCountUserSig($username, $time_log);
                        if ($failed_times >= 4) {
                            // check du 5 lan hay chua
                            // du 5 lan thong bao loi va insert vao
                            sfGuardUserTable::getInstance()->updateUserLog($username, $time_now);
                            $this->getUser()->setFlash('error', $i18n->__('Tài khoản của bạn đã bị khóa trong 10 phút.'));

                            return;
                        }
                        if ($form['username']->hasError()) {
                            // insert vao vt-user-log
                            $vt_user_log = new VtUserSigninLock();
                            $vt_user_log->setUserName($username);
                            $vt_user_log->setCreatedTime($time_now);
                            $vt_user_log->save();
                        }
                    }
                }
            } else {
                if (in_array($formValue['new_password'], $blacklist)) {
                    $this->getUser()->setFlash('error', 'Mật khẩu nằm trong blacklist', false);
                } else {
                    if ($form->isValid()) {
                        $user->setIpAddress($this->getRequest()->getHttpHeader('addr', 'remote'));
                        $user->setUserAgent($this->getRequest()->getHttpHeader('User-Agent'));
                        $username = $form['username']->getValue();
                        if ($username && $sf_user = sfGuardUserTable::getInstance()->getUserByUsername($username)) {
                            if ((time() - strtotime($sf_user->getPassUpdateAt()) > sfConfig::get('app_passuser_lifetime', 7776000))) {
                              $sf_user->setPassUpdateAt(date('Y-m-d H:i:s', strtotime('now')));
                              $sf_user->setPassword($form['new_password']->getValue());
                              $sf_user->save();
                              $this->getUser()->setFlash('info', $i18n->__('Bạn đã thay đổi mật khẩu thành công.'));
                            }
//                          }
                        }
                        $this->getUser()->signin($form->getValue('user'), $form->getValue('remember'));
                        // always redirect to a URL set in app.yml
                        // or to the referer
                        // or to the homepage
                        $this->redirect('@homepage');
                    }
                }
            }

            //      	echo $this->form->getErrorSchema();
        } else {
            if ($request->isXmlHttpRequest()) {
                $this->getResponse()->setHeaderOnly(true);
                $this->getResponse()->setStatusCode(401);

                return sfView::NONE;
            }

            // if we have been forwarded, then the referer is the current URL
            // if not, this is the referer of the current request
            $user->setReferer($this->getContext()->getActionStack()->getSize() > 1 ? $request->getUri() : $request->getReferer());

            $module = sfConfig::get('sf_login_module');
            if ($this->getModuleName() != $module) {
                $this->redirect($module . '/' . sfConfig::get('sf_login_action'));
            }

            $this->getResponse()->setStatusCode(401);
        }
    }

    public function executeSecure($request)
    {
        parent::executeSecure($request);
    }

    public function executeChangePassword($request)
    {
        $user = sfContext::getInstance()->getUser();
        if ($user->getGuardUser()->getIsVsaAccount()) {
            $this->redirect('@homepage');
        }

        $this->form = new VtGuardChangePasswordForm();
        if ($request->isMethod(sfRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $username = $this->form->getValue('username');
                if ($username && $sf_user = sfGuardUserTable::getInstance()->getUserByUsername($username)) {
                    $sf_user->setPassUpdateAt(date('Y-m-d H:i:s', time()));
                    $sf_user->setPassword($this->form->getValue('new_password'));
                    $sf_user->save();
                    $this->getUser()->setFlash('info', sfContext::getInstance()->getI18N()->__('Bạn đã thay đổi mật khẩu thành công.'));
                }
            }
        }
        $this->getResponse()->setTitle(sfContext::getInstance()->getI18N()->__('Thay đổi mật khẩu'));
    }

}

