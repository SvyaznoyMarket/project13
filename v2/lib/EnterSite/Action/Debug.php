<?php

namespace EnterSite\Action;

use Enter\Http;
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

    public function execute(Http\Response $response, $startAt) {
        $logger = $this->getLogger();

        $debugData = [];

        if ($error = error_get_last()) {
            $debugData['error'] = $error;
        }

        $debugData['time'] = ['value' => round(microtime(true) - $startAt, 3), 'unit' => 'ms'];
        $debugData['memory'] = ['value' => round(memory_get_peak_usage() / 1048576, 2), 'unit' => 'Mb'];
        $debugData['curl'] = [
            'time'            => [
                'value' => 0,
                'unit'  => 'ms',
            ],
            'request_time'    => [
                'value' => 0,
                'unit'  => 'ms',
            ],
            'namelookup_time' => [
                'value' => 0,
                'unit'  => 'ms',
            ],
            'query'           => [],
        ];

        // curl query
        foreach ($logger as $message) {
            if (isset($message['tag'][0]) && in_array('curl', $message['tag'])) {
                /** @var Query|null $query */
                $query = $message['query'] instanceof Query ? $message['query'] : null;
                if (!$query) continue;

                $debugData['curl']['query'][] = $query;
                $debugData['curl']['time']['value'] += ($query->getEndAt() - $query->getStartAt());
                $debugData['curl']['request_time']['value'] += $query->getInfo()['total_time'];
                $debugData['curl']['namelookup_time']['value'] += $query->getInfo()['namelookup_time'];
            }
        }

        $response->content .= '<pre>' . json_encode($debugData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '<pre>';
    }
}