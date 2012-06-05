<?php

class ProductCorePagerContainer extends sfPager
{
  public function __construct($maxPerPage = 10)
  {
    parent::__construct('Product', $maxPerPage);
  }

  public function setResult($results, $count){
    $this->results = (array)$results;
    $this->setNbResults((int)$count);
  }

  public function init()
  {
    if($this->getMaxPerPage()){
      $this->setLastPage(ceil($this->getNbResults() / $this->getMaxPerPage()));
    }
  }

  public function getResults()
  {
    return $this->results;
  }

  protected function retrieveObject($offset)
  {
    return $this->results[$offset];
  }
}