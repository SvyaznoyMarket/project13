<?php

class Core
{
  protected static
    $connection,
    $error,
//  protected static
    $instance;

  static public function createInstance()
  {
    self::$instance = new Core();
    self::$connection = curl_init();
    curl_setopt(self::$connection, CURLOPT_URL, "http://core.ent3.ru/v1/json");
    curl_setopt (self::$connection, CURLOPT_HEADER, 0);
    curl_setopt(self::$connection, CURLOPT_POST, true);
    curl_setopt(self::$connection, CURLOPT_RETURNTRANSFER, true);
  }

  static public function getInstance()
  {
    return self::$instance;
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

  public function getData($record)
  {
    $map = $this->getMap(get_class($record));

    $data = array();
    foreach ($map as $k => $v)
    {
      $data[$k] = $record->get($v);
    }
	
	return $data;
  }

  public function getMap($name)
  {
    $map = array();

    $file = sfConfig::get('sf_config_dir').'/core/'.$name.'.yml';
    try {
      $map = sfYaml::load($file);
    }
    catch(Exception $e)
    {
      sfContext::getInstance()->getLogger()->err('{'.__CLASS__.'} can\'t open file '.$file);
    }

    return $map;
  }

  public function query($name, $data)
  {
    $action = "/".str_replace(".", "/", $name)."/";

    $data = json_encode(array('action' => $action, 'param' => array('client_id' => '', 'token_id' => '', ), 'data' => $data, ), JSON_FORCE_OBJECT);

    $response = $this->send($data);
    $response = json_decode($response, true);

	if (!isset($response['confirmed']) || !$response['confirmed'])
	{
	  self::$error = array($response['code'] => $response['promt']);
	  $response = false;
	}

    return $response;
  }

  public function getError()
  {
    return self::$error;
  }

  protected function send($request)
  {
    $response = "";

    curl_setopt(self::$connection, CURLOPT_POSTFIELDS, $request);
    $response = curl_exec(self::$connection);
	
    if (curl_errno(self::$connection) > 0)
    {
      self::$error = array(curl_errno(self::$connection) => curl_error(self::$connection), );
      $response = false;
    }

    return $response;
  }

}