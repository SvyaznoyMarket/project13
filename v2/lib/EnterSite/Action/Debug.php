<?php

namespace EnterSite\Action;

use Enter\Curl\Query;
use EnterSite\ConfigTrait;
use EnterSite\LoggerTrait;
use EnterSite\MustacheRendererTrait;

class Debug {
    use ConfigTrait;
    use LoggerTrait, MustacheRendererTrait {
        ConfigTrait::getConfig insteadof LoggerTrait;
        ConfigTrait::getConfig insteadof MustacheRendererTrait;
    }

    public function execute($response, $startAt) {
        $logger = $this->getLogger();

        $debugData = [
            'time'   => ['value' => round(microtime(true) - $startAt, 3), 'unit' => 'ms'],
            'memory' => ['value' => round(memory_get_peak_usage() / 1048576, 2), 'unit' => 'Mb'],

            'curl' => [
                'query' => [],
            ],
        ];

        // curl query
        foreach ($logger as $message) {
            if (isset($message['tag'][0]) && in_array('curl', $message['tag'])) {
                /** @var Query|null $query */
                $query = $message['query'] instanceof Query ? $message['query'] : null;
                if (!$query) continue;

                $debugData['curl']['query'][] = $query;
            }
        }

        echo '<pre>' . json_encode($debugData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '<pre>';
    }
}