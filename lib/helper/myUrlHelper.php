<?php

function replace_url_for($name, $value, $route_name = null, $params = array(), array $excluded = null)
{
  $excluded = null == $excluded
    ? array(
      //'view' => 'compact',
      'page' => '1',
    )
    : $excluded
  ;

  $context = sfContext::getInstance();

  $controller = $context->getController();
  $routing = $context->getRouting();
  $request = $context->getRequest();

  if (null == $route_name)
  {
    $route_name = $routing->getCurrentRouteName();
  }

  $parameters = $controller->convertUrlStringToParameters($routing->getCurrentInternalUri());

  $currentParams = $request->getGetParameters();
  $parameters[1][$name] = $value;
  foreach($excluded as $k => $val)
  {
    if ($name == $k && $value == $val )
    {
      unset($currentParams[$name]);
      unset($parameters[1][$name]);
    }
  }

  $currentParams = array_merge($currentParams, $parameters[1]);

  return htmlspecialchars(urldecode($routing->generate($route_name, $currentParams)));
}

function pager_url_for($page, $route_name = null, $params = array())
{
  return replace_url_for('page', $page, $route_name, $params);
}