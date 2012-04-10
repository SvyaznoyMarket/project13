<?php

class myActions extends sfActions
{
  /**
   * Retrieves the current sfUser object.
   *
   * This is a proxy method equivalent to:
   *
   * <code>$this->getContext()->getUser()</code>
   *
   * @return myUser The current sfUser implementation instance
   */
  public function getUser()
  {
    return $this->context->getUser();
  }

  public function getPartial($templateName, $vars = null)
  {
    $this->getContext()->getConfiguration()->loadHelpers('myPartial');

    $vars = null !== $vars ? $vars : $this->varHolder->getAll();

    return get_partial($templateName, $vars);
  }

  public function getComponent($moduleName, $componentName, $vars = null)
  {
    $this->getContext()->getConfiguration()->loadHelpers('myPartial');

    $vars = null !== $vars ? $vars : $this->varHolder->getAll();

    return get_component($moduleName, $componentName, $vars);
  }

  public function preExecute()
  {
    parent::preExecute();

    if (in_array($this->getRequestParameter('frame'), array('1', 'true', 'on')))
    {
      $this->setLayout('frame');
    }
  }

  public function postExecute()
  {
    if ('debug' == sfConfig::get('sf_environment'))
    {
      $this->getResponse()->addStylesheet('debug.css');
    }
  }

  public function renderJson($value, $header = true, array $params = array())
  {
    $params = myToolkit::arrayDeepMerge(array(
      'debug' => true,
    ), $params);

    if ($header)
    {
      $this->getResponse()->setHttpHeader('Content-type', 'application/json');
    }

    if ($params['debug'] && is_array($value) && (true == sfConfig::get('sf_debug')))
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

  public function getPager($model, myDoctrineQuery $q, $limit = 20, array $params = array())
  {
    $page = (int)$this->getRequest()->getParameter('page', 1);

    $pager = new myDoctrinePager($model, $limit, $params);
    $pager->setQuery($q);
    $pager->setPage($page);
    $pager->init();

    return $pager;
  }

  public function getPagerForArray(array $data, $limit = 20, array $params = array())
  {
      $page = (int)$this->getRequest()->getParameter('page', 1);

      $first = ($page-1) * $limit;
      $first = $page * $limit;
      for ($i=$first; $i<$last; $i++) {
          $result[] = $data[$i];
      }


      return $result;
  }

  public function getCore()
  {
    return Core::getInstance();
  }

  public function getCoreService()
  {
    return Core::getInstance();
  }
}