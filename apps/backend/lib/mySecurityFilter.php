<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of mySecurityFilter
 *
 * @author vas_huynq28
 */
class mySecurityFilter extends sfBasicSecurityFilter
{

  /**
   * Executes this filter.
   *
   * @param sfFilterChain $filterChain A sfFilterChain instance
   */
  public function execute($filterChain)
  {
    sfContext::getInstance()->getLogger()->info('agent filter request: ' . sfContext::getInstance()->getRequest()->getHttpHeader('User-Agent'));
    sfContext::getInstance()->getLogger()->info('agent filter session: ' . sfcontext::getInstance()->getUser()->getUserAgent());
    // disable security on login and secure actions
    if (
      (sfConfig::get('sf_login_module') == $this->context->getModuleName()) && (sfConfig::get('sf_login_action') == $this->context->getActionName())
      ||
      (sfConfig::get('sf_secure_module') == $this->context->getModuleName()) && (sfConfig::get('sf_secure_action') == $this->context->getActionName())
      ||
      ('sfCaptchaGD' == $this->context->getModuleName()) && ('GetImage' == $this->context->getActionName())
    ) {
      $filterChain->execute();
      return;
    }

    // NOTE: the nice thing about the Action class is that getCredential()
    //       is vague enough to describe any level of security and can be
    //       used to retrieve such data and should never have to be altered
    if (!$this->context->getUser()->isAuthenticated()) {
      if (sfConfig::get('sf_logging_enabled')) {
        $this->context->getEventDispatcher()->notify(new sfEvent($this, 'application.log', array(sprintf('Action "%s/%s" requires authentication, forwarding to "%s/%s"', $this->context->getModuleName(), $this->context->getActionName(), sfConfig::get('sf_login_module'), sfConfig::get('sf_login_action')))));
      }
      // the user is not authenticated
      $this->forwardToLoginAction();
    }

    // the user is authenticated
    $credential = $this->getUserCredential();
    $this->context->getEventDispatcher()->notify(new sfEvent($this, 'application.log', array(sprintf('Session ip "%s" | access ip "%s"', sfcontext::getinstance()->getuser()->getIpAddress(), sfContext::getInstance()->getRequest()->getHttpHeader('addr', 'remote')))));
    $this->context->getEventDispatcher()->notify(new sfEvent($this, 'application.log', array(sprintf('Session user-agent "%s" | access user-agent "%s"', sfcontext::getinstance()->getuser()->getUserAgent(), sfContext::getInstance()->getRequest()->getHttpHeader('User-Agent')))));
    //different ip
    if (sfContext::getInstance()->getRequest()->getHttpHeader('addr', 'remote') != sfcontext::getinstance()->getuser()->getIpAddress()) {
      if (sfConfig::get('sf_logging_enabled')) {
        $this->context->getEventDispatcher()->notify(new sfEvent($this, 'application.log', array(sprintf('Session hijacking, session ip "%s" | access ip "%s"', sfcontext::getinstance()->getuser()->getIpAddress(), sfContext::getInstance()->getRequest()->getHttpHeader('addr', 'remote')))));
      }
      //different ip
      $this->forwardToSecureAction();
    }
    //different user-agent
    if (sfContext::getInstance()->getRequest()->getHttpHeader('User-Agent') != sfcontext::getinstance()->getuser()->getUserAgent()) {
      if (sfConfig::get('sf_logging_enabled')) {
        $this->context->getEventDispatcher()->notify(new sfEvent($this, 'application.log', array(sprintf('Session hijacking, session user-agent "%s" | access user-agent "%s"', sfcontext::getinstance()->getuser()->getUserAgent(), sfContext::getInstance()->getRequest()->getHttpHeader('User-Agent')))));
      }
      //different user-agent
      $this->forwardToSecureAction();
    }
    //done' have access
    if (null !== $credential && !$this->context->getUser()->hasCredential($credential)) {
      if (sfConfig::get('sf_logging_enabled')) {
        $this->context->getEventDispatcher()->notify(new sfEvent($this, 'application.log', array(sprintf('Action "%s/%s" requires credentials "%s", forwarding to "%s/%s"', $this->context->getModuleName(), $this->context->getActionName(), sfYaml::dump($credential, 0), sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action')))));
      }
      // the user doesn't have access
      $this->forwardToSecureAction();
    }

    // the user has access, continue
    $filterChain->execute();
  }

}

