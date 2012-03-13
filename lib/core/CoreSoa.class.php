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

    private $_apiActionsList = array();


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

        $this->client_id = 7;
        $auth = array(
            'client_id' => $this->client_id,
        );
        $data = array_merge($auth, $data);
        //$data = json_encode($data, JSON_FORCE_OBJECT);

        if ($isLog)
        {
            $this->logger->log("Request: " . $this->action. '. Data: '.json_encode($data));
        }
        $timeBefor = microtime(true);
        $response = $this->send($data);
        $timeAfter = microtime(true);
        //print_r($response);

        if ($isLog)
        {
            $timeExecute = $timeAfter - $timeBefor;
            //$this->logger->log("Response: ".$response, !empty($response['error']) ? sfLogger::ERR : sfLogger::INFO);
            $this->logger->log("Response: Execute time: ".$timeExecute.'. '.$response);
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
            if (is_array($val)) {
                $val = implode(',', $val);
            }
            $paramStrAr[] = $name . '=' . $val;
        }
        $paramStr = '?' . implode('&', $paramStrAr);
        $action = str_replace('.', '/', $this->action);
        //echo $this->getConfig('userapi_url') . $action . $paramStr;
        curl_setopt($this->connection, CURLOPT_URL, $this->getConfig('userapi_url') . $action . $paramStr);
        $response = curl_exec($this->connection);

        if (curl_errno($this->connection) > 0)
        {
            $this->error = array(curl_errno($this->connection) => curl_error($this->connection), );
            $response = false;
        }

        return $response;
    }

    public function getDeliveryCalc($data)
    {
        $this->action = 'product.get-delivery';
        if ($response = $this->queryCore($data))
        {
            if (count($response) ) {
                return $response;
            }
        }
        return array();
    }

    public function getProductStatic($data)
    {
        $this->action = 'product.get-static';
        if ($response = $this->queryCore($data))
        {
            if (count($response) ) {
                return $response;
            }
        }
        return array();
    }

    public function getProductDynamic($data)
    {
        if (!isset($data['geo_id'])) {
            $region = sfContext::getInstance()->getUser()->getRegion();
           // echo $region->id .'====';
            //$data['geo_id'] = $region->id;
        }
        $this->action = 'product.get-dynamic';
        if ($response = $this->queryCore($data))
        {
            if (count($response) ) {
                return $response;
            }
        }
        return array();
    }

//    public function getProductCard($id)
//    {
//        $this->_apiActionsList[] = array(
//            'action' => 'product.get-static',
//            'data' => array('id' => $id)
//        );
//        $this->_apiActionsList[] = array(
//            'action' => 'product.get-dynamic',
//            'data' => array('id' => $id)
//        );
//        $response = $this->queryCore1();
//    }

//    public function queryCore1()
//    {
//        $isLog = true;
//
//        $this->client_id = 7;
//        $auth = array(
//            'client_id' => $this->client_id,
//        );
//        //$data = array_merge($auth, $data);
//
//
//        // инициализируем "контейнер" для отдельных соединений (мультикурл)
//        $multicURL = curl_multi_init();
//        var_dump($multicURL);
//        // массив заданий для мультикурла
//        $tasks = array();
//        foreach ($this->_apiActionsList as $action) {
//
//            $paramStrAr = array();
//            $data = array_merge($auth, $action['data']);
//            foreach ($data as $name => $val) {
//                $paramStrAr[] = $name . '=' . $val;
//            }
//            $paramStr = '';// '?' . implode('&', $paramStrAr);
//            $url = $this->getConfig('userapi_url') . str_replace('.', '/', $action['action']) . $paramStr;
//
//            $url = 'http://fs01.enter.ru/1/1/500/d4/36010.jpg';
//            echo $url .'<br>';
//            // инициализируем отдельное соединение (поток)
//            $ch = curl_init( $url );
//            var_dump($ch);
////            // если будет редирект - не будем переходить по нему
////            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
////            //curl_setopt($ch, CURLOPT_POST, 0);
////            // возвращать результат
////            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//            // не возвращать http-заголовок
//            curl_setopt($ch, CURLOPT_HEADER, 0);
//            // таймаут соединения
//            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
//            // таймаут ожидания
//            curl_setopt($ch, CURLOPT_TIMEOUT, 300);
////            // добавляем дескриптор потока в массив заданий
////            $tasks[ $action['action'] ] = $ch;
//            // добавляем дескриптор потока в мультикурл
//            curl_multi_add_handle($multicURL, $ch);
//
//        }
//        //print_r($this->_apiActionsList);
//        die();
//
//        // количество активных потоков
//        $active = null;
//        // запускаем выполнение потоков
//        do {
//            $mrc = curl_multi_exec($multicURL, $active);
//        }
//        while ($mrc == CURLM_CALL_MULTI_PERFORM);
//
//        // выполняем, пока есть активные потоки
//        while ($active && ($mrc == CURLM_OK)) {
//            // если какой-либо поток готов к действиям
//            if (curl_multi_select($multicURL) != -1) {
//                // ждем, пока что-нибудь изменится
//                do {
//                    $mrc = curl_multi_exec($multicURL, $active);
//                    // получаем информацию о потоке
//                    $info = curl_multi_info_read($multicURL);
//                    // если поток завершился
//                    if ($info['msg'] == CURLMSG_DONE) {
//                        $ch = $info['handle'];
//                        // ищем id файла по дескриптору потока в массиве заданий
//                        $actionName = array_search($ch, $tasks);
//
//                        // забираем содержимое
//                        $coreAnswer = curl_multi_getcontent($ch);
//                        $answerInfo = curl_getinfo($ch);
//                        //print_r($answerInfo);
//                        //обрабатываем полученные данные
//                        $this->_answerHandler($actionName, $coreAnswer, $answerInfo);
//
//                        // удаляем поток из мультикурла
//                        curl_multi_remove_handle($multicURL, $ch);
//                        // закрываем отдельное соединение (поток)
//                        curl_close($ch);
//                    }
//                }
//                while ($mrc == CURLM_CALL_MULTI_PERFORM);
//            }
//        }
//        // закрываем мультикурл
//        curl_multi_close($multicURL);
//
//
//        //return $response;
//    }

//    private function _answerHandler($acton, $answer, $answerInfo)
//    {
//        dump($acton);
//        dump($answer);
//        dump($answerInfo);
//    }

}