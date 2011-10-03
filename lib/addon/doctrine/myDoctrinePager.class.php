<?php

class myDoctrinePager extends sfDoctrinePager
{
  public function getResults($hydrationMode = null)
  {
    //return $this->getQuery()->execute(array(), $hydrationMode);

    $table = Doctrine_Core::getTable($this->getClass());

    $ids = $table->getIdsByQuery($this->getQuery());
    return $table->createListByIds($ids, array('view' => 'list',));
  }
}