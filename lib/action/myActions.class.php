<?php

class myActions extends sfActions
{
  public function preExecute()
  {
    parent::preExecute();

    if (in_array($this->getRequestParameter('frame'), array('1', 'true', 'on')))
    {
      $this->setLayout('frame');
    }
  }

  public function renderJson($value, $header = true)
  {
    if ($header)
    {
      $this->getResponse()->setHttpHeader('Content-type', 'application/json');
    }

    if (is_array($value) && (true == sfConfig::get('sf_debug')))
    {
      $value['debug'] = array(
        'request' => $this->getRequest()->getParameterHolder()->getAll(),
        'user'    => $this->getUser()->getAttributeHolder()->getAll(),
      );
    }

    $this->renderText(json_encode($value));

    return sfView::NONE;
  }

  public function setDefaults($values)
  {
    foreach ($values as $name => $value)
    {
      if (isset($this->$name) && (null !== $this->$name))
      {
        continue;
      }

      $this->$name = $value;
    }
  }

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