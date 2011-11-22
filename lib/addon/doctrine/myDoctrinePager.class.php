<?php

class myDoctrinePager extends sfDoctrinePager
{
  public function __construct($class, $maxPerPage = 10)
  {
    parent::__construct($class, $maxPerPage);

    $queryParams = func_num_args() > 2 ? func_get_arg(2) : array();
    $this->setParameter('query_params', is_array($queryParams) ? $queryParams : array());
  }

  public function getResults($hydrationMode = null)
  {
    //return $this->getQuery()->execute(array(), $hydrationMode);

    $table = Doctrine_Core::getTable($this->getClass());

    $ids = $table->getIdsByQuery($this->getQuery());

    return $table->createListByIds($ids, $this->getParameter('query_params', array()));
  }
}