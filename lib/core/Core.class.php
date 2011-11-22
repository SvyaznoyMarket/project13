<?php

class Core
{
  protected
    $config = null,
    $connection = null,
    $error = false,
    $models = null,
    $logger = null,
    $token = null,
    $client_id = null
  ;
  protected static
    $instance = null;

  /**
   *
   * @return Core
   */
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

  public function getActions($name = null)
  {
    $actions = array(
      1 => 'create',
      2 => 'update',
      3 => 'delete',
    );

    return null == $name ? $actions : $actions[$name];
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


    if ($response = $this->query('order.create', array(), $data))
    {
      $order->number = $response['number']; // TODO: check
      $order->token = uniqid().'-'.$response['number'];
      $result = $response['id'];
    }
    //myDebug::dump($order->toArray(false), 1);

    return $result;
  }

  public function updateOrder(Order $order)
  {
    $result = false;

    $data = $this->getData($order);
    $params = array('id' => $data['id']);
    unset($data['id']);

    if ($this->query('order.update', $params, $data))
    {
      $result = true;
    }

    return $result;
  }

  public function getUser($id)
  {
    $result = false;

    if ($response = $this->query('user.get', array('id' => $id, 'count' => false, 'expand' => array('geo', 'address_list', 'network_list'))))
    {
      $result = $response[0];
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

    if ($this->query('user.update', array('id'=>$data['id']), $data))
    {
      $result = true;
    }

    return $result;
  }

  public function createUserProfile(UserProfile $profile)
  {
	  $result = false;

		$data = $this->getData($profile);

		if ($response = $this->query('user.network.create', array(), $data)) {
			$result = $response['id'];
		}

		return $result;
  }

  public function createUserTag($params = array(), $data = array())
  {
    $result = false;

    //$data = $this->getData($tag);

    if ($response = $this->query('user.mark.create', $params, $data))
    {
      $result = $response['id'];
    }

    return $result;
  }

  public function createUserProductRating(UserProductRating $rec)
  {
	$result = false;
    $data = $this->getData($rec);
    if (($response = $this->query('user.product.rating.create', array(), $data)))
    {
      $result = $response['confirmed'];
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

  public function getCreator($id)
  {
    $result = false;

    if ($response = $this->query('creator.get', array('id' => $id, 'count' => false)))
    {
      $result = $response[0];
    }

    return $result;
  }

  public function createCallback($callback)
  {
    $result = false;
    //$data = $this->getData($callback);
    //$this->getUser();
    $data = $callback->getData();
    $data['category_id'] = 21;
    $data['first_name'] = $data['name'];
    unset($data['user_id']);
    unset($data['name']);
		if ($response = $this->query('user.callback.create', array(), $data)) {
			$result = $response['id'];
		}
		return $result;
  }

  public function changePassword($coreId,$data){

    unset($data['id']);
		if ($response = $this->query('user.update', array('id'=>$coreId), $data)) {
			$result = $response['id'];
      return $result;
    }
    else{
      return false;
    }
  }


  public function getData($record)
  {
    return $record->exportToCore();
  }

  public function query($name, array $params = array(), array $data = array())
  {
    $action = '/'.str_replace('.', '/', $name).'/';

    if (empty($this->client_id) || empty($this->token))
    {
        if (!$this->auth())
        {
          return false;
        }
    }

    $data = json_encode(array(
      'action' => $action,
      'param'  => array_merge(array(
        'client_id' => $this->client_id,
        'token'     => $this->token,
      ), $params),
      'data'   => $data), JSON_FORCE_OBJECT);

    $this->logger->log("Request: ".$data);
    $response = $this->send($data);
    $this->logger->log("Response: ".$response);
    $response = json_decode($response, true);

    if (isset($response['error']))
    {
      $this->error = array($response['error']['code'] => $response['error']['message'], );
      if (isset($response['error']['detail'])) $this->error['detail'] = $response['error']['detail'];
      if (isset($response['error']['message'])) $this->error['message'] = $response['error']['message'];
      $response = false;
    }
    return $response;
  }

  public function getError()
  {
    return $this->error;
  }

  protected function auth()
  {
    $data = json_encode(array(
      'action' => '/auth/',
      'param'  => array(
        'consumer_key'  => $this->getConfig('consumer_key'),
        'signature'     => $this->getConfig('signature'),
      ),
    ), JSON_FORCE_OBJECT);
    $result = true;

    $this->logger->log('Trying to pass authentification... ');
    $response = $this->send($data);

	//$this->logger->log($response);
    $response = json_decode($response, true);

    if (isset($response['error']))
    {
      $this->logger->log('Authentification failed: ');
      $this->error = array($response['error']['code'] => $response['error']['message'], );
      $this->logger->log('Authentification failed: '.$response['error']['message']);
      $result = false;
    }
    else
    {
      $this->client_id = $response['id'];
      $this->token = $response['token'];
      $this->logger->log('Authentification passed');
    }

    return $result;
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