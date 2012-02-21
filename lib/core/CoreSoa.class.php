<?php
class CoreSoa extends Core
{

   protected
          $config = null,
          $connection = null,
          $error = false,
          $models = null,
          $logger = null,
          $token = null,
          $client_id = null,
          $cache = null,
          $action = null;

    protected static
        $instance = null;


    static public function getInstance()
    {
        if (null == self::$instance)
        {
            self::$instance = new CoreSoa();
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
        //echo $this->getConfig('userapi_url');
        //curl_setopt($this->connection, CURLOPT_URL, $this->getConfig('userapi_url'));
        curl_setopt ($this->connection, CURLOPT_HEADER, 0);
        curl_setopt($this->connection, CURLOPT_HTTPGET, true);
        curl_setopt($this->connection, CURLOPT_POST, false);
        curl_setopt($this->connection, CURLOPT_RETURNTRANSFER, true);

        $this->logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir').'/core_soa_lib.log'));

        $redis = new sfRediskaCache(array('prefix' => str_replace(':', '', sfConfig::get('app_doctrine_result_cache_prefix'))));
        if ($redis->has('core_api_client_id') && $redis->has('core_api_token')) {
            $this->client_id = $redis->get('core_api_client_id');
            $this->token = $redis->get('core_api_token');
        }
        $this->cache = $redis;

    }

    public function queryCore(array $data = array())
    {
        $isLog = true;
//        $this->client_id = 7;
//        if (empty($this->client_id) || empty($this->token))
//        {
//            if (!$this->auth())
//            {
//                return false;
//            }
//        }
        $this->client_id = 7;
        $auth = array(
            'client_id' => $this->client_id,
        );
        $data = array_merge($auth, $data);
        //print_r($data);
        //$data = json_encode($data, JSON_FORCE_OBJECT);

        if ($isLog)
        {
            $this->logger->log("Request: ".$data);
        }
        $response = $this->send($data);
        //print_r($response);

        if ($isLog)
        {
            //$this->logger->log("Response: ".$response, !empty($response['error']) ? sfLogger::ERR : sfLogger::INFO);
            $this->logger->log("Response: ".$response);
        }
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

    protected function send($request)
    {
        $response = false;

        //print_r($request);
        foreach ($request as $name => $val) {
            $paramStrAr[] = $name . '=' . $val;
        }
        $paramStr = '?' . implode('&', $paramStrAr);
        $action = str_replace('.', '/', $this->action);
        curl_setopt($this->connection, CURLOPT_URL, $this->getConfig('userapi_url') . $action . $paramStr);
        $response = curl_exec($this->connection);

        if (curl_errno($this->connection) > 0)
        {
            $this->error = array(curl_errno($this->connection) => curl_error($this->connection), );
            $response = false;
        }

        return $response;
    }

    public function getProduct($id)
    {
        $data['id'] = $id;
        $this->action = 'product.get';
        if ($response = $this->queryCore($data))
        {
            if (isset($response[0]) && isset($response[0]['id'])) {
                return $response[0];
            }
        }
        return array();
    }

}