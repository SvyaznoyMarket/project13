<?php

namespace Controller\Command;

class CrossssAction {
    public function __construct() {
        if ('cli' !== PHP_SAPI) {
            throw new \Exception('Действие доступно только через CLI');
        }
    }

    /**
     * @param int $limit
     * @throws \Exception
     */
    public function execute($limit = 10000) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $curl = \App::curl();

        $manager = new \QueueManager(\App::config()->queue, \App::logger());
        $manager->process('crossss.push', function($taskName, $taskData = []) use ($curl) {
            foreach ($taskData as $data) {
                if (empty($data['apikey'])) {
                    //throw new \Exception(sprintf('task.%s: не содержит параметра apikey', $taskName));
                }

                $curl->query(\App::config()->crossss['apiUrl'] . '?' . http_build_query($data), [], \App::config()->crossss['timeout'] * 2);
            }
        }, $limit);
    }
}