<?php
namespace light;
use Exception;
/**
 * Created by JetBrains PhpStorm.
 * User: Trushina
 * Date: 30.08.12
 * Time: 12:18
 * To change this template use File | Settings | File Templates.
 */
class RequestLogger
{
    /**
     * Инстанс для синглтона
     * @var RequestLogger
     */
    static private $_instance = null;

    /**
     * Уникальный идентификатор запроса к сайту
     * @var null
     */
    private $_id = null;

    /**
     * Список запросов к ядру
     * @var array
     */
    private $_requestList = array();

    /**
     * Получит инстанс для синглтона
     * @static
     * @return RequestLogger
     */
    static public function getInstance() {
        if (empty(self::$_instance)) {
            self::$_instance = new RequestLogger();
        }
        return self::$_instance;
    }

    /**
     * Получить уникальный идентификатор запроса
     * @return null
     */
    public function getId() {
        if (empty($this->_id)) {
            $this->_id = uniqid();
        }
        return $this->_id;
    }

    private function __construct() {
    }

    public function __clone() {
        return false;
    }

    /**
     * Добавляет запрос в список запросов от ядра
     * @param string $log
     * @param array $getParams
     */
    public function addLog($log, $getParams) {
        $paramsList = array();
        foreach ($getParams as $key => $value) {
            if (!$value) {
                continue;
            } elseif (is_array($value)) {
                $value = implode(',', $value);
            }
            $paramsList[] = $key . '=' . $value;
        }

        $this->_requestList[] = array(
            'time' => date('H:i:s') . ' (' . microtime(true) . ')' ,
            'text' => $log . ' Params: '. implode(';', $paramsList)
        );
    }

  public function getStatistics(){
    $fullText = 'Request id: ' . $this->getId() . " | ";
    foreach ($this->_requestList as $log) {
      $fullText .= $log['time'] . ' ' . $log['text'] . " | ";
    }
    return $fullText;
  }
}