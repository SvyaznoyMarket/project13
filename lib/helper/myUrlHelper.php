<?php

function replace_url_for($name, $value, $route_name = null, $params = array())
{
  $context = sfContext::getInstance();

  $controller = $context->getController();
  $routing = $context->getRouting();
  $request = $context->getRequest();

  if (null == $route_name)
  {
    $route_name = $routing->getCurrentRouteName();
  }

  $parameters = $controller->convertUrlStringToParameters($routing->getCurrentInternalUri());
  $parameters[1][$name] = $value;

  return urldecode($routing->generate($route_name, array_merge($request->getGetParameters(), $parameters[1])));
}

function pager_url_for($page, $route_name = null, $params = array())
{
  return replace_url_for('page', $page, $route_name, $params);
}