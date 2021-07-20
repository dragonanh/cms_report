<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PDOQueryDoctrinePager
 *
 * @author anhbhv
 */
class PDOQueryDoctrinePager extends sfDoctrinePager{
  protected $query = null;
  protected $param = array();
  protected $maxRows = null;
  protected $connection = 'doctrine';
  public function setParams($param){
    $this->param = $param;
  }
  
  public function setMaxRows($maxRows){
    $this->maxRows = $maxRows;
  }
  
  public function getCountRawQuery() {
    $query = $this->getQuery();
    if($this->maxRows){
      $query .= ' limit '.$this->maxRows;
    }
    
    return $query;
  }

  public function setConnection($connection){
    $this->connection = $connection ? $connection : 'doctrine';
  }
  
  public function init()
  {
    $this->resetIterator();
    $db = Doctrine_Manager::getInstance()->getConnection($this->connection);
    $queryCount = $this->getCountRawQuery();
    $count = $db->execute($queryCount, $this->param)->rowCount();

    $this->setNbResults($count);

    $query = $this->getQuery();

    if (0 == $this->getPage() || 0 == $this->getMaxPerPage() || 0 == $this->getNbResults())
    {
      $this->setLastPage(0);
      
    }
    else
    {
      $this->setLastPage(ceil($this->getNbResults() / $this->getMaxPerPage()));
      if(($this->getPage() - 1) <= 0 && $this->maxRows){
        $limit = ($this->getMaxPerPage() > $this->getNbResults())? $this->getNbResults() : $this->getMaxPerPage();
        $query.= ' limit '.$limit.' offset 0';
      }else{
        $offset = ($this->getPage() - 1) * $this->getMaxPerPage();
        $query.= ' limit '.$this->getMaxPerPage().' offset '.$offset;
      }
    }

    $this->setQueryRaw($query);
  }

  public function getQuery()
  {
    return $this->query;
  }

  public function getResults($hydrationMode = null)
  {
    if($this->getNbResults() > 0){
      $db = Doctrine_Manager::getInstance()->getConnection($this->connection);
      return $db->execute($this->getQuery(), $this->param)->fetchAll(Doctrine_Core::FETCH_ASSOC);
    }else{
      return false;
    }
  }

  public function setQueryRaw($query){
    $this->query = $query;
  }
}
