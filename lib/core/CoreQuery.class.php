<?php

class CoreQuery
{
  protected
    $core = null,
    $query,
    $parameters = array(),
    $data = array(),
    $errors = array()
  ;

  public function __construct($query = null, array $parameters = array(), array $data = array())
  {
    $this->core = Core::getInstance();

    $this->query = $query;
    $parameters['uid'] = RequestLogger::getInstance()->getId();
    $this->parameters = $parameters;
    $this->data = $data;
  }

  public function setQuery($query)
  {
    $this->query = $query;

    return $this;
  }

  public function getQuery()
  {
    return $this->query;
  }

  public function setParameter($name, $value)
  {
    $this->parameters[$name] = $value;

    return $this;
  }

  public function getParameter($name, $value = null)
  {
    return array_key_exists($name, $this->parameters) ? $this->parameters[$name] : $value;
  }

  public function setParameters(array $parameters)
  {
    $this->parameters = $parameters;

    return $this;
  }

  public function setData(array $data)
  {
    $this->data = $data;

    return $this;
  }

  public function getData()
  {
    return $this->data;
  }

  public function addError($error)
  {
    $this->errors[] = $error;
  }

  public function getErrors()
  {
    return $this->errors;
  }

  public function hasErrors()
  {
    return count($this->errors) > 0;
  }

  public function execute()
  {
    $response = $this->core->query($this->query, $this->parameters, $this->data);

    if (false == $response)
    {
      $this->addError($this->core->getError());
    }
    else if (isset($response['result']) && ('empty' == $response['result']))
    {
      $response = false;
    }

    return $response;
  }

  public function count()
  {
    $query = clone $this;
    $query->setParameter('count', 'true');

    $response = $query->execute();

    return !empty($response['count']) ? $response['count'] : 0;
  }

  public function getResult()
  {
    $result = $this->execute();
    if (!is_array($result))
    {
      $result = array();
    }

    return $result;
  }
}