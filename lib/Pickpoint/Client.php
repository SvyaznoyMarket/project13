<?php

namespace Pickpoint;

class Client {
    /** @var array */
    private $config;
    /** @var \Curl\Client */
    private $curl;
    /** @var array */
    private $daysNames;

    /**
     * @param array $config
     * @param \Curl\Client $curl
     */
    public function __construct(array $config, \Curl\Client $curl) {
        $this->config = array_merge([
            'url'          => null,
            'timeout'      => null,
            'retryTimeout' => null,
            'retryCount'   => null,
        ], $config);

        $this->curl = $curl;

        $this->daysNames = \App::config()->daysShortNames;
    }

    public function __clone() {
        $this->curl = clone $this->curl;
    }

    /**
     * @param string     $action
     * @param array      $params
     * @param array      $data
     * @param float|null $timeout
     * @return mixed
     */
    public function query($action, array $params = [], array $data = [], $timeout = null) {
        \Debug\Timer::start('Pickpoint');

        $response = $this->curl->query($this->getUrl($action, $params), $data, $timeout);

        \Debug\Timer::stop('Pickpoint');

        return $response;
    }

    /**
     * @param string        $action
     * @param array         $params
     * @param array         $data
     * @param callback      $successCallback
     * @param callback|null $failCallback
     * @param float|null    $timeout
     * @return bool
     */
    public function addQuery($action, array $params = [], array $data = [], $successCallback, $failCallback = null, $timeout = null) {
        \Debug\Timer::start('Pickpoint');

        if (null === $timeout) {
            $timeout = $this->config['timeout'];
        }

        $result = $this->curl->addQuery($this->getUrl($action, $params), $data, $successCallback, $failCallback, $timeout);

        \Debug\Timer::stop('Pickpoint');

        return $result;
    }

    /**
     * @param int $retryTimeout
     * @param int $retryCount
     * @return void
     */
    public function execute($retryTimeout = null, $retryCount = null) {
        \Debug\Timer::start('Pickpoint');

        if (null === $retryTimeout) {
            $retryTimeout = isset($this->config['retryTimeout']['default']) ? $this->config['retryTimeout']['default'] : 0;
        }
        if (null === $retryCount) {
            $retryCount = $this->config['retryCount'];
        }

        $this->curl->execute($retryTimeout, $retryCount);

        \Debug\Timer::stop('Pickpoint');
    }

    /**
     * @param string $action
     * @param array  $params
     * @return resource
     */
    private function getUrl($action, array $params = []) {
        return $this->config['url'] . $action . '?' . http_build_query($params);
    }


    /**
     * Преобразовывает строку времени работы постамата в человекочитаемый вид
     *
     * @param       string      $workTimesString
     * @return      string
     */
    public function worksTimePrepare($workTimesString)
    {
        $workTimes = explode(',', $workTimesString);
        $ppTimes = $ppDays =[];

        $countTimes = count($workTimes);
        for ($i = 0; $i < $countTimes; $i++) {
            $key = array_search($workTimes[$i], $ppTimes);
            if (false === $key) {
                $ppTimes[] = $workTimes[$i];
                $ppDays[] = [ $this->daysNames[$i] ];
            } else {
                $ppDays[$key][] = $this->daysNames[$i];
            }
        }


        if (empty($ppDays) || empty($ppTimes)) {

            // При ошибке вернём почти первоночальное значение
            return preg_replace('/noday/i', 'выходной', $workTimesString);

        } elseif (7 == count($ppDays[0])) {

            // При ежедневной работе постомата
            $ret = $ppTimes[0] . ', eжедневно';

        } else {

            // Во всех остальных случаях выведем время работы постамата, разбитое по дням
            $ret = '';
            $countTimes = count($ppTimes);
            for ($i = 0; $i < $countTimes; $i++) {
                $countDays = count($ppDays[$i]);
                $ppTimes[$i] = preg_replace('/noday/i', 'выходной', $ppTimes[$i]);
                if ($countDays > 3) {
                    // Интервал дней через "-"
                    $ret .= $ppDays[$i][0] . '-' . $ppDays[$i][$countDays - 1] . ': ' . $ppTimes[$i];
                } else {
                    // Перечисление дней через запятую
                    $ret .= implode(', ', $ppDays[$i]) . ': ' . $ppTimes[$i];
                }
                if ($countTimes - $i - 1 > 0) $ret .= '; ' /*. "<br/>" */;
            }

        }

        return $ret;
    }
}