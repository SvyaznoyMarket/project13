<?php

class Core
{
  protected
    $config = null,
    $connection = null,
    $error = false
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

    $this->connection = curl_init();
    curl_setopt($this->connection, CURLOPT_URL, $this->getConfig('api_url'));
    curl_setopt ($this->connection, CURLOPT_HEADER, 0);
    curl_setopt($this->connection, CURLOPT_POST, true);
    curl_setopt($this->connection, CURLOPT_RETURNTRANSFER, true);
  }

  public function getConfig($name = null)
  {
    return null == $name ? $this->config->getAll() : $this->config->get($name);
  }

  public function getModel($name)
  {
    $models = array(
      'category' => 'ProductCategory',
      'order'    => 'Order',
      'product'  => 'Product',
      'shop'     => 'Shop',
    );
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

    if ($response = $this->query('user.create', $data))
    {
      $result = $response['id'];
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

    if ($response = $this->query('userAddress.create', $data))
    {
      $result = $response['id'];
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

  public function getData($record)
  {
    return $record->exportToCore();
  }

  public function query($name, $data = array())
  {
    $action = '/'.str_replace('.', '/', $name).'/';

    $data = json_encode(array('action' => $action, 'param' => array('client_id' => $this->getConfig('client_id'), 'token_id' => '', ), 'data' => $data, ), JSON_FORCE_OBJECT);

    $response = $this->send($data);
    myDebug::dump($response);
    $response = json_decode($response, true);

    return $response;
  }

  public function getError()
  {
    return $this->error;
  }

  protected function send($request)
  {
    $response = false;

    //myDebug::dump($request ,1);
    curl_setopt($this->connection, CURLOPT_POSTFIELDS, $request);
    $response = curl_exec($this->connection);
    //myDebug::dump($response);

    if (curl_errno($this->connection) > 0)
    {
      $this->error = array(curl_errno($this->connection) => curl_error($this->connection), );
      $response = false;
    }

    return $response;
  }

}