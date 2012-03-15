<?php
class CoreSoa
{

   protected
          $config = null,
          $error = false,
          $models = null,
          $logger = null,
          $token = null,
          $client_id = null,
          $cache = null,
          $action = null;

    protected static
        $instance = null;

    /**
     * Список запросов, которые будут отправлены на ядро при следующем запросе
     * @var array
     */
    private $_apiActionsList = array();

    /**
     * Данные, полученные от ядра при последнем запросе
     * @var array
     */
    private $_loadData = array();


    private function  __construct() {
    }

    static public function getInstance()
    {
        if (null == self::$instance)
        {
            self::$instance = new CoreSoa();
            self::$instance->initialize();
        }

        return self::$instance;
    }

    protected function initialize()
    {
        $this->_isLog = true;
        $this->_client_id = 7;
        $config = sfConfig::getAll();
        $this->_coreApiUrl = $config['app_core_config']['userapi_url'];


        $this->models = sfYaml::load(sfConfig::get('sf_config_dir').'/core/model.yml');

        $this->logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir').'/core_soa_lib.log'));


//        $redis = new sfRediskaCache(array('prefix' => str_replace(':', '', sfConfig::get('app_doctrine_result_cache_prefix'))));
//        if ($redis->has('core_api_client_id') && $redis->has('core_api_token')) {
//            $this->_client_id = $redis->get('core_api_client_id');
//            $this->token = $redis->get('core_api_token');
//        }
//        $this->cache = $redis;

    }


    /**
     * Очищает список запросов, которые будут отправлены на ядро
     */
    public function resetData() {
        $this->_apiActionsList = array();
        $this->_loadData = array();
    }

    /**
     * Возвращает данные, полученные от ядра при последнем запросе
     * @return array
     */
    public function getData() {
        return $this->_loadData;
    }

    /**
     * Добавляет в список запрос на получение статической информации о продуктах
     * @param array $data
     */
    public function prepareDataForStatic($data) {
        $this->_apiActionsList[] = array(
            'name' => 'product/get-static',
            'data' => $data
        );
    }

    /**
     * Добавляет в список запрос на получение динамической информации о продуктах
     * @param array $data
     */
    public function prepareDataForDynamic($data) {
        $this->_apiActionsList[] = array(
            'name' => 'product/get-dynamic',
            'data' => $data
        );
    }

    /**
     * Добавляет в список запрос на получение информации о доставках
     * @param array $data
     */
    public function prepareDataForDelivery($data) {
        if (!isset($data['product'])) {
            $data['product'] = array();
            if (isset($data['id'])) {
                if (is_array($data['id'])) {
                    foreach ($data['id'] as $id) {
                        $data['product'][] = array('id' => $id);
                    }
                } else {
                    $data['product'][] = array('id' => $data['id']);
                }
                unset($data['id']);
            }
            if (isset($data['slug'])) {
                if (is_array($data['slug'])) {
                    foreach ($data['slug'] as $slug) {
                        $data['product'][] = array('slug' => $slug);
                    }
                } else {
                    $data['product'][] = array('slug' => $data['slug']);
                }
                unset($data['slug']);
            }
        }

        $this->_apiActionsList[] = array(
            'name' => 'product/get-delivery',
            'post' => true,
            'data' => $data
        );
    }



    /**
     * ВЫполняет многопоточный запрос к ядру используя подготовленные ранее данные
     * @return mixed
     */
    public function multiThreadQuery()
    {
        if (!$this->_apiActionsList || !is_array($this->_apiActionsList) || !count($this->_apiActionsList)) {
            return false;
        }

        $this->_loadData = array();

        // массив заданий для мультикурла
        $tasks = array();
        $actionNameList = array();
        foreach ($this->_apiActionsList as $action) {
            if (!isset($action['name']) && !isset($action['data'])) {
                continue;
            }
            $actionNameList[] = $action['name'];

            $postMethod = false;
            if (isset($action['post']) && $action['post']) {
                $postMethod = true;
            }

            if ($postMethod) {
                $paramStr = json_encode($action['data']);
                $url = $this->_coreApiUrl . $action['name'] . '/client_id/' . $this->_client_id;
            }else {
                $data = $action['data'];
                $data['client_id'] = $this->_client_id;
                $paramStr = http_build_query($data, '', '&');
                $url = $this->_coreApiUrl . $action['name'] . '?' .  $paramStr;
            }
            //echo $url .'<br>';

            // инициализируем отдельное соединение (поток)
            $ch = curl_init( $url );
            // если будет редирект - не будем переходить по нему
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
            // возвращать результат
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            // не возвращать http-заголовок
            curl_setopt($ch, CURLOPT_HEADER, 0);
            //если используем post. устанавливаем метод и передаём данные
            if ($postMethod) {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $paramStr);
            }
            // добавляем дескриптор потока в массив заданий
            $tasks[ $action['name'] ] = $ch;
            if ($this->_isLog) {
                $this->logger->log("Response: Action:" . $action['name'] . '. Method: ' .( ($postMethod) ? 'POST' : 'GET') . ". Data: ". $paramStr);
            }

        }
        $timeBefor = microtime(true);
        // инициализируем "контейнер" для отдельных соединений (мультикурл)
        $multicURL = curl_multi_init();
        //добавляем все соединения в контейнер
        foreach ($tasks as $ch) {
            curl_multi_add_handle($multicURL, $ch);
        }

        // количество активных потоков
        $active = null;
        // запускаем выполнение потоков
        do {
            $mrc = curl_multi_exec($multicURL, $active);
        }
        while ($mrc == CURLM_CALL_MULTI_PERFORM);

        // выполняем, пока есть активные потоки
        while ($active && ($mrc == CURLM_OK)) {
            // если какой-либо поток готов к действиям
            if (curl_multi_select($multicURL) != -1) {
                // ждем, пока что-нибудь изменится
                do {
                    $mrc = curl_multi_exec($multicURL, $active);
                    // получаем информацию о потоке
                    $info = curl_multi_info_read($multicURL);
                    // если поток завершился
                    if ($info['msg'] == CURLMSG_DONE) {
                        $ch = $info['handle'];
                        // ищем id файла по дескриптору потока в массиве заданий
                        $actionName = array_search($ch, $tasks);

                        // забираем содержимое
                        $coreAnswer = curl_multi_getcontent($ch);
                        $answerInfo = curl_getinfo($ch);
                        //print_r($answerInfo);
                        //обрабатываем полученные данные
                        $timeAfterAction = microtime(true);
                        if ($this->_isLog) {
                            $timeExecute = $timeAfterAction - $timeBefor;
                            $this->logger->log("Response: Execute time: ".$timeExecute.'. Action:'.$actionName. '.Answer: '.$coreAnswer);
                        }
                        $this->_answerHandler($coreAnswer, $answerInfo, $actionName);

                        // удаляем поток из мультикурла
                        curl_multi_remove_handle($multicURL, $ch);
                        // закрываем отдельное соединение (поток)
                        curl_close($ch);
                    }
                }
                while ($mrc == CURLM_CALL_MULTI_PERFORM);
            }
        }

        // закрываем мультикурл
        curl_multi_close($multicURL);
        $timeAfter = microtime(true);

        if ($this->_isLog) {
            $timeExecute = $timeAfter - $timeBefor;
            $this->logger->log("Multy Request Completed: Execute time: ".$timeExecute.'. Actions:'.implode(',', $actionNameList));
        }

    }

    /**
     * Обрабатывает данные, полученые в результате единичного curl запроса
     *
     * @param $answer - ответ, полученный от ядра
     * @param $answerInfo - curl информация о выполненном запросе
     * @param $action - имя действия
     * @throws ErrorException
     */
    private function _answerHandler($answer, $answerInfo, $action)
    {
        if ($answerInfo['http_code'] == 200) {
            $this->_loadData[] = array(
                'action' => $action,
                'success' => true,
                'result' => json_decode($answer, true)
            );
        } else {
            throw new ErrorException('Запрос '. $action. ' выполнить не удалось.');
        }

    }




}