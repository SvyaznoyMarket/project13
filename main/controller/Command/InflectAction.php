<?php

namespace Controller\Command;

class InflectAction {
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

        $manager = new \QueueManager(\App::config()->queue, \App::logger('task'));
        $manager->process('inflect', function($taskName, $taskData = []) {
            foreach ($taskData as $data) {
                $original = isset($data['original']) ? $data['original'] : null;
                $file = isset($data['file']) ? $data['file'] : null;

                if (empty($original)) {
                    throw new \Exception(sprintf('task.%s: не передана фраза', $taskName));
                }
                if (!$file) {
                    throw new \Exception(sprintf('task.%s: не передан файл', $taskName));
                }
                if (file_exists($file)) {
                    return;
                }

                $response = file_get_contents('http://export.yandex.ru/inflect.xml?' . http_build_query([
                    'name' => $original,
                ]));
                if (!$xml = simplexml_load_string($response)) {
                    throw new \Exception(sprintf('task.%s: невалидный xml %s', $taskName, (string)$xml));
                }

                $inflect = [];
                foreach ($xml->xpath('//inflection') as $inflection) {
                    $inflect[] = (string)$inflection;
                }

                if (!file_put_contents($file, json_encode($inflect, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT))) {
                    throw new \Exception(sprintf('task.%s: неудалось записать данные в %s', $taskName, $file));
                }
            }
        }, $limit);
    }
}