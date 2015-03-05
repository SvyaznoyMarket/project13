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
    private $queriesById = [];
    /** @var int[] */
    private $delays = [];
    /** @var int */
    private $queryLimit = 100;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->mh = curl_multi_init();
        //curl_multi_setopt($this->mh, CURLMOPT_PIPELINING, 0);
    }

    /**
     * @return void
     */
    public function __destruct()
    {
        // завершить все соединения
        if ($this->queriesById) {
            //$this->execute();
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
        $query->handle = $handle;
        curl_setopt_array($handle, $request->options);
        $this->queriesById[$id] = $query;
        $query->rejectCallback = function() use ($id) {
            $this->removeRequest($id);
        };

        if ($request->delay) {
            $this->delays[$id] = microtime(true) + ($request->delay / 1000);
            var_dump((round((microtime(true) - $GLOBALS['startAt']) * 1000)) . ' | add delay ' . $id);
        } else {
            var_dump((round((microtime(true) - $GLOBALS['startAt']) * 1000)) . ' | add ' . $id . ' ' . $query->request);
            curl_multi_add_handle($this->mh, $handle);
        }

        var_dump('added');
        var_dump(array_map(function(Query $query) { return $query->handle; }, $this->queriesById));

        // принудительно отправить запросы на выполнение
        if (count($this->queriesById) >= $this->queryLimit) {
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
            if ($this->active && (curl_multi_select($this->mh, $selectTimeout) === -1)) {
                usleep($multiSelectTimeout); // выполнить задержку если curl_multi_select вернул -1 https://bugs.php.net/bug.php?id=61141
                var_dump((round((microtime(true) - $GLOBALS['startAt']) * 1000)) . ' | sleep... ' . $this->active);
                var_dump(array_map(function(Query $query) { return $query->handle; }, $this->queriesById));
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
                //usleep(500);
                usleep(50000);
                var_dump((round((microtime(true) - $GLOBALS['startAt']) * 1000)) . ' | has delays... ' . $this->active);
            }
        } while ($this->active || $this->queriesById);
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
                    $this->queriesById[$id]->handle
                );
                var_dump((round((microtime(true) - $GLOBALS['startAt']) * 1000)) . ' | add delayed ' . $this->queriesById[$id]->request);
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
            var_dump((round((microtime(true) - $GLOBALS['startAt']) * 1000)) . ' | done ' . $id);
            var_dump($done);

            $query = isset($this->queriesById[$id]) ? $this->queriesById[$id] : null;
            if (!$query) {
                var_dump('canceled');
                // вероятно, был отменен
                continue;
            }

            if ($done['result'] !== CURLM_OK) {
                $query->response->error = $this->createError($done);
                var_dump('error ' . $query->response->error);
            }

            $response = $query->response;
            $response->info = curl_getinfo($done['handle']);
            $response->statusCode = $query->response->info['http_code'];
            //$response->body = curl_multi_getcontent($done['handle']); // убрать, используем CURLOPT_WRITEFUNCTION
            //var_dump($query->request . ' ' . $response->info['total_time'] * 1000);
            var_dump($response->body);

            $callback = is_callable($query->resolveCallback) ? $query->resolveCallback : null;

            $this->removeRequest($id);
            //$this->removeRequest($id + 1);

            if ($callback) {
                try {
                    //call_user_func($query->resolveCallback);
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
        if ($query = (isset($this->queriesById[$id]) ? $this->queriesById[$id] : null)) {
            if (is_resource($query->handle)) {
                curl_multi_remove_handle(
                    $this->mh,
                    $query->handle
                );
                curl_close($query->handle);
                var_dump((round((microtime(true) - $GLOBALS['startAt']) * 1000)) . ' | remove ' . $id . ' ' . $this->queriesById[$id]->request);
            }

            unset($this->delays[$id], $this->queriesById[$id], $query);
        }
        var_dump('removed ' . $id);
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