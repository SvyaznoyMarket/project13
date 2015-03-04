<?php

namespace EnterLab\Curl;

use EnterLab\Curl\Exception;

class Client
{
    /** @var Config */
    private $config;
    /** @var resource */
    private $mh;
    /** @var bool */
    private $active;
    /** @var resource[] */
    private $handlesById = [];
    /** @var Query[] */
    private $queriesById = [];
    /** @var int[] */
    private $delays = [];
    /** @var int */
    private $handleLimit = 100;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->mh = curl_multi_init();
    }

    /**
     * @return void
     */
    public function __destruct()
    {
        // завершить все соединения
        if ($this->handlesById) {
            $this->execute();
        }

        if ($this->mh) {
            curl_multi_close($this->mh);
            $this->mh = null;
        }
    }

    /**
     * @return Query
     */
    public function createQuery()
    {
        return new Query();
    }

    /**
     * Добавляет запрос
     *
     * @param Query $query
     */
    public function addQuery(Query $query)
    {
        $request = $query->request;

        $handle = curl_init();
        $id = (int)$handle;
        if (!$id) {
            throw new \RuntimeException(sprintf('Не удалось инициализировать запрос %s', $request));
        }
        curl_setopt_array($handle, $request->options);
        $this->handlesById[$id] = $handle;
        $this->queriesById[$id] = $query;
        $query->rejectCallback = function() use ($id) {
            $this->removeRequest($id);
        };

        if ($request->delay) {
            $this->delays[$id] = microtime(true) + ($request->delay / 1000);
        } else {
            curl_multi_add_handle($this->mh, $handle);
        }

        // принудительно отправить запросы на выполнение
        if (count($this->handlesById) >= $this->handleLimit) {
            $this->execute();
        }
    }

    /**
     * Выполняет запросы
     *
     * @return void
     */
    public function execute()
    {
        $selectTimeout = $this->config->selectTimeout / 1000;
        $multiSelectTimeout = $this->config->multiSelectTimeout;

        do {

            if (-1 === $this->active && curl_multi_select($this->mh, $selectTimeout)) {
                usleep($multiSelectTimeout); // выполнить задержку если curl_multi_select вернул -1 https://bugs.php.net/bug.php?id=61141
            }

            // добавить отложенные запросы, если это необходимо
            if ($this->delays) {
                $this->addDelays();
            }

            do {
                $mrc = curl_multi_exec($this->mh, $this->active);
            } while ($mrc === CURLM_CALL_MULTI_PERFORM);

            $this->processMessages();

            // if there are delays but no transfers, then sleep for a bit
            if (!$this->active && $this->delays) {
                usleep(500);
            }

            if ($this->handlesById) {
                $this->active = true;
            }
        } while ($this->active);
    }

    /**
     * Добавляет отложенные запросы
     *
     * @return void
     */
    private function addDelays()
    {
        $time = microtime(true);

        foreach ($this->delays as $id => $delay) {
            if ($time >= $delay) {
                unset($this->delays[$id]);
                curl_multi_add_handle(
                    $this->mh,
                    $this->handlesById[$id]
                );
            }
        }
    }

    /**
     * Обрабатывает сообщения
     *
     * @return void
     */
    private function processMessages()
    {
        while ($done = curl_multi_info_read($this->mh)) {
            $id = (int)$done['handle'];

            if (!isset($this->handlesById[$id])) {
                // вероятно, был отменен
                continue;
            }

            $query = $this->queriesById[$id];

            if ($done['result'] !== CURLM_OK) {
                $query->response->error = $this->createError($done);
            }

            $response = $query->response;
            $response->info = curl_getinfo($done['handle']);
            $response->statusCode = $query->response->info['http_code'];
            $response->body = curl_multi_getcontent($done['handle']);
            //var_dump($query->request . ' ' . $response->info['total_time'] * 1000);

            $callback = is_callable($query->resolveCallback) ? $query->resolveCallback : null;

            $this->removeRequest($id);

            if ($callback) {
                try {
                    call_user_func($query->resolveCallback);
                } catch (\Exception $e) {}
            }
        }
    }

    /**
     * Удаляет запрос по идентификатору
     *
     * @param int $id
     */
    private function removeRequest($id)
    {
        // удалить задержку, если есть
        if (isset($this->delays[$id])) {
            unset($this->delays[$id]);
        }

        /** @var Request|null $request */
        if ($handle = isset($this->handlesById[$id]) ? $this->handlesById[$id] : null) {
            curl_multi_remove_handle(
                $this->mh,
                $handle
            );
            curl_close($handle);

            unset($this->handlesById[$id], $this->queriesById[$id]);
        }
    }

    /**
     * Создает ошибку ответа
     *
     * @param $done
     * @return Exception\ConnectException|\RuntimeException
     */
    private function createError(&$done)
    {
        static $connectionErrors = [
            CURLE_OPERATION_TIMEOUTED  => true,
            CURLE_COULDNT_RESOLVE_HOST => true,
            CURLE_COULDNT_CONNECT      => true,
            CURLE_SSL_CONNECT_ERROR    => true,
            CURLE_GOT_NOTHING          => true,
        ];

        $code = (int)$done['result'];
        $message = curl_error($done['handle']);

        return
            isset($connectionErrors[$code])
            ? new Exception\ConnectException($message, $code)
            : new \RuntimeException($message, $code)
        ;
    }
}