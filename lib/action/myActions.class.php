<?php

class myActions extends sfActions
{
  public function getPager($model, myDoctrineQuery $q, array $params = array())
  {
    $params = myToolkit::arrayDeepMerge(array(
      'limit' => 20,
      'page'  => (int)$this->getRequest()->getParameter('page', 1),
    ), $params);
    
    $pager = new myDoctrinePager($model, $params['limit']);
    $pager->setQuery($q);
    $pager->setPage($params['page']);
    $pager->init();
    
    return $pager;
  }
}