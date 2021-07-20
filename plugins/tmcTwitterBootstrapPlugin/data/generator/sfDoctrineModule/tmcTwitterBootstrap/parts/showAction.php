  public function executeShow(sfWebRequest $request)
  {
    $this->redirect(array('sf_route' => '<?php echo $this->getUrlForAction('edit') ?>', 'sf_subject' => $this->getRoute()->getObject()));
    $this->sidebar_status = $this->configuration->getShowSidebarStatus();
    $hide = array();
    $this-><?php echo $this->getSingularName() ?> = $this->getRoute()->getObject();
    $this->fields = array_diff($this-><?php echo $this->getSingularName() ?>->getTable()->getColumnNames(), $hide);
  }
