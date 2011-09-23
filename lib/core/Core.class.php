<?php

class Core
{
  protected
    $config = null,
    $connection = null,
    $error = false,
    $models = null,
    $logger = null
  ;
  protected static
    $instance = null;

  static public function getInstance()
  {
    if (null == self::$instance)
    {
      self::$instance = new Core();
      self::$instance->initialize(sfConfig::get('app_core_config'));
    }

    return self::$instance;
  }

  protected function initialize(array $config)
  {
    $this->config = new sfParameterHolder();
    $this->config->add($config);

    $this->models = sfYaml::load(sfConfig::get('sf_config_dir').'/core/model.yml');

    $this->connection = curl_init();
    curl_setopt($this->connection, CURLOPT_URL, $this->getConfig('api_url'));
    curl_setopt ($this->connection, CURLOPT_HEADER, 0);
    curl_setopt($this->connection, CURLOPT_POST, true);
    curl_setopt($this->connection, CURLOPT_RETURNTRANSFER, true);

    $this->logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir').'/core_lib.log'));
  }

  public function getConfig($name = null)
  {
    return null == $name ? $this->config->getAll() : $this->config->get($name);
  }

  public function getModels()
  {
    return $this->models;
  }

  public function getTable($name)
  {
    $table = false;

    foreach ($this->models as $k => $v)
    {
      if (in_array($name, $v))
      {
        $table = Doctrine_Core::getTable($k);
        break;
      }
    }

    return $table;
  }

  public function createOrder(Order $order)
  {
    $result = false;

    $data = $this->getData($order);

    if ($response = $this->query('order.create', $data))
    {
      $result = $response['id'];
    }

    return $result;
  }

  public function updateOrder(Order $order)
  {
    $result = false;

    $data = $this->getData($order);

    if ($this->query('order.update', $data))
    {
      $result = true;
    }

    return $result;
  }

  public function createUser(User $user)
  {
    $result = false;

    $data = $this->getData($user);

    if ($response = $this->query('user.create', array(), $data))
    {
      $result = $response['id'];
    }

    return $result;
  }

  public function updateUser(User $user)
  {
    $result = false;

    $data = $this->getData($user);

    if ($this->query('user.update', array(), $data))
    {
      $result = true;
    }

    return $result;
  }

  public function createUserTag(UserTag $tag)
  {
    $result = false;

    $data = $this->getData($tag);

    if ($response = $this->query('userTag.create', $data))
    {
      $result = $response['id'];
    }

    return $result;
  }

  public function createUserAddress(UserAddress $address)
  {
    $result = false;

    $data = $this->getData($address);

    if ($response = $this->query('user.address.create', array(), $data))
    {
      $result = $response['id'];
    }

    return $result;
  }

  public function updateUserAddress(UserAddress $address)
  {
    $result = false;

    $data = $this->getData($address);
    $params = array('id' => $data['id']);
    unset($data['id']);

    if ($response = $this->query('user.address.update', $params, $data))
    {
      $result = $response['confirmed'];
    }

    return $result;
  }

  public function deleteUserAddress($id)
  {
    $result = false;

    $params['id'] = $id;

    if ($response = $this->query('user.address.delete', $params))
    {
      $result = $response['confirmed'];
    }

    return $result;

  }

  public function createUserProductNotice(UserProductNotice $notice)
  {
    $result = false;

    $data = $this->getData($notice);

    if ($response = $this->query('userProductNotice.create', $data))
    {
      $result = $response['id'];
    }

    return $result;
  }

  public function createProductComment(ProductComment $comment)
  {
    $result = false;

    $data = $this->getData($comment);

    if ($response = $this->query('product.opinion.create', array(), $data))
    {
      $result = $response['id'];
    }

    return $result;
  }

  public function updateProductComment(ProductComment $comment)
  {
    $result = false;

    $data = $this->getData($comment);
    $params = array('id' => $data['id']);
    unset($data['id']);

    if ($response = $this->query('product.opinion.update', $params, $data))
    {
      $result = $response['confirmed'];
    }

    return $result;
  }

  public function getData($record)
  {
    return $record->exportToCore();
  }

  public function query($name, array $params = array(), array $data = array())
  {
    $action = '/'.str_replace('.', '/', $name).'/';

    $data = json_encode(array(
      'action' => $action,
      'param'  => array_merge(array(
        'client_id' => $this->getConfig('client_id'),
        'token_id'  => '',
      ), $params),
      'data'   => $data), JSON_FORCE_OBJECT);

    $this->logger->log("Request: ".$data);
    $response = $this->send($data);
    $this->logger->log("Response: ".$response);
    $response = json_decode($response, true);

    if (isset($response['code']))
    {
      $this->error = array($response['code'] => $response['promt'], );
      $response = false;
    }

    return $response;
  }

  public function getError()
  {
    return $this->error;
  }

  protected function send($request)
  {
    $response = false;

    curl_setopt($this->connection, CURLOPT_POSTFIELDS, $request);
    $response = curl_exec($this->connection);

    if (curl_errno($this->connection) > 0)
    {
      $this->error = array(curl_errno($this->connection) => curl_error($this->connection), );
      $response = false;
    }

    return $response;
  }

}