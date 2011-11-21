<?php

class myDoctrinePager extends sfDoctrinePager
{
  public function __construct($class, $maxPerPage = 10)
  {
    parent::__construct($class, $maxPerPage);

    $queryParams = func_num_args() > 2 ? func_get_arg(2) : array();
    $this->setParameter('query_params', is_array($queryParams) ? $queryParams : array());
  }

  public function init()
  {
    $this->resetIterator();

    $countQuery = $this->getCountQuery();
    $count = $countQuery->count();

    $this->setNbResults($count);

    $query = $this->getQuery();
    $query
      ->offset(0)
      ->limit(0)
    ;

    if (0 == $this->getPage() || 0 == $this->getMaxPerPage() || 0 == $this->getNbResults())
    {
      $this->setLastPage(0);
    }
    else
    {
      $offset = ($this->getPage() - 1) * $this->getMaxPerPage();

      $this->setLastPage(ceil($this->getNbResults() / $this->getMaxPerPage()));

      $query
        ->offset($offset)
        ->limit($this->getMaxPerPage())
      ;
    }
  }

  public function getResults($hydrationMode = null)
  {
    //return $this->getQuery()->execute(array(), $hydrationMode);

    $table = Doctrine_Core::getTable($this->getClass());

    $ids = $table->getIdsByQuery($this->getQuery());

    return $table->createListByIds($ids, $this->getParameter('query_params', array()));
  }
}