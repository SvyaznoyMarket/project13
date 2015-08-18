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
    /** @var Query[] */
    private $queries = [];
    /** @var Delay[] */
    private $delays = [];

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->mh = curl_multi_init();
        curl_multi_setopt($this->mh, CURLMOPT_MAXCONNECTS, 25);
        //curl_multi_setopt($this->mh, CURLMOPT_PIPELINING, 0);
    }

    /**
     * @return void
     */
    public function __destruct()
    {
        // завершить все соединения
        if ($this->queries) {
            //$this->execute();
        }

        foreach ($this->queries as $id => $query) {
            //var_dump('close ' . $id);
            if (!is_resource($query->handle)) continue;

            curl_multi_remove_handle($this->mh, $query->handle);
            curl_close($query->handle);
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
        $query->id = uniqid();
        $query->rejectCallback = function() use ($query) {
            $this->removeQuery($query);
        };

        $create = function() use (&$query) {
            $handle = curl_init();
            $id = (int)$handle;
            if (!$id) {
                throw new \RuntimeException(sprintf('Не удалось инициализировать запрос %s', $query->request));
            }
            $query->handle = $handle;
            curl_setopt_array($handle, $query->request->options);

            $done = curl_multi_add_handle($this->mh, $handle);
            if (0 !== $done) {
                throw new \RuntimeException(curl_error($handle), (int)$done);
            }
            $this->queries[$id] = $query;
        };

        if ($query->request->delay) {
            $delay = new Delay();
            $delay->time = microtime(true) + ($query->request->delay / 1000);
            $delay->callback = $create;
            $this->delays[$query->id] = $delay;
        } else {
            call_user_func($create);
        }

        //var_dump('added');
        //var_dump(array_map(function(Query $query) { return $query->handle; }, $this->queriesById));

        // принудительно отправить запросы на выполнение
        if (count($this->queries) >= $this->config->queryLimit) {
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
        if (!$this->queries) {
            return;
        }

        $selectTimeout = $this->config->selectTimeout / 1000;
        $multiSelectTimeout = $this->config->multiSelectTimeout;

        do {
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
                //var_dump((round((microtime(true) - $GLOBALS['startAt']) * 1000)) . ' | has delays... ' . $this->active);
            }

            if ($this->active) {
                if (-1 === curl_multi_select($this->mh, $selectTimeout)) {
                    usleep($multiSelectTimeout); // выполнить задержку если curl_multi_select вернул -1 https://bugs.php.net/bug.php?id=61141
                    //var_dump((round((microtime(true) - $GLOBALS['startAt']) * 1000)) . ' | sleep... ' . $this->active);
                }
                //var_dump(array_map(function(Query $query) { return $query->handle; }, $this->queriesById));
            }

            if ($this->queries) {
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
            if ($time >= $delay->time) {
                unset($this->delays[$id]);
                call_user_func($delay->callback);
                //var_dump((round((microtime(true) - $GLOBALS['startAt']) * 1000)) . ' | add delayed ' . $this->queriesById[$id]->request);
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
            //var_dump((round((microtime(true) - $GLOBALS['startAt']) * 1000)) . ' | done ' . $id);
            //var_dump($done);

            $query = isset($this->queries[$id]) ? $this->queries[$id] : null;
            if (!$query) {
                // вероятно, был отменен
                continue;
            }

            if ($done['result'] !== CURLM_OK) {
                $query->response->error = $this->createError($done);
                //var_dump('error ' . $query->response->error);
            }

            $response = $query->response;
            $response->info = curl_getinfo($done['handle']);
            $response->statusCode = $query->response->info['http_code'];
            //$response->body = curl_multi_getcontent($done['handle']); // убрать, используем CURLOPT_WRITEFUNCTION
            //var_dump($response->body);

            $callback = is_callable($query->resolveCallback) ? $query->resolveCallback : null;

            $this->removeQuery($query);

            if ($callback) {
                try {
                    call_user_func($query->resolveCallback);
                } catch (\Exception $e) {}
            }
        }
    }

    /**
     * @param Query $query
     */
    public function removeQuery(Query $query)
    {
        // удалить задержку
        if (isset($this->delays[$query->id])) {
            unset($this->delays[$query->id]);
        }

        $id = (int)$query->handle;
        if (is_resource($query->handle)) {
            curl_multi_remove_handle($this->mh, $query->handle); // FIXME: сильно глючит при retry
            curl_close($query->handle);
            //var_dump((round((microtime(true) - $GLOBALS['startAt']) * 1000)) . ' | remove ' . $id . ' ' . $this->queriesById[$id]->request);
        }

        unset($this->queries[$id]/*, $query*/);
        //var_dump('removed ' . $id);
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