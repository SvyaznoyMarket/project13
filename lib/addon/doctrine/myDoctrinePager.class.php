<?php

class myDoctrinePager extends sfDoctrinePager
{
  public function getResults($hydrationMode = null)
  {
    //return $this->getQuery()->execute(array(), $hydrationMode);

    $arg2 = func_num_args() > 1 ? func_get_arg(1) : false;
    $params = is_array($arg2) ? $arg2 : array();

    $table = Doctrine_Core::getTable($this->getClass());

    $ids = $table->getIdsByQuery($this->getQuery());
    return $table->createListByIds($ids, $params);
  }
}