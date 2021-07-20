<?php
/**
 * Created by JetBrains PhpStorm.
 * User: OS_loilv4
 * Date: 10/17/12
 * Time: 5:58 PM
 * To change this template use File | Settings | File Templates.
 */
class solrPager extends sfPager
{
  protected
    $query = '',
    $sort = '',
    $isArrayResult = false;

  public function __construct($class = null, $maxPerPage = 10)
  {
    parent::__construct($class, $maxPerPage);
  }

  public function setQuery($query){
    $this->query = $query;
  }
  public function setSort($sort){
    $this->sort = $sort;
  }

  public function isArrayResult($isArray){
    $this->isArrayResult = $isArray;
  }

  public function init()
  {
    $offset = ($this->getPage() - 1) * $this->getMaxPerPage();
    $isResultArray  = $this->isArrayResult;
    $result = json_decode(SolrUtils::processSearchSolr($this->query, $this->sort, $offset, $this->maxPerPage), $isResultArray);
    $total = $isResultArray ? $result['response']['numFound'] :$result->response->numFound;
    $this->setNbResults($total);
    $this->results = $isResultArray ? $result['response']['docs'] : $result->response->docs;

    if (($this->getPage() == 0 || $this->getMaxPerPage() == 0)) {
      $this->setLastPage(0);
    }
    else
    {
      $this->setLastPage(ceil($this->getNbResults() / $this->getMaxPerPage()));
    }
  }

  public function retrieveObject($offset)
  {
    $results = $this->results;
    foreach ($results as $key => $value){
      if($key == $offset)
        return $value;
    }
    return null;
  }

  public function getResults()
  {
    return $this->results;
  }

}
