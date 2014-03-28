<?php

namespace EnterSite\Action;

use Enter\Http;
use Enter\Curl\Query;
use EnterSite\ConfigTrait;
use EnterSite\LoggerTrait;
use EnterSite\MustacheRendererTrait;
use EnterSite\ViewHelperTrait;

class Debug {
    use ConfigTrait;
    use LoggerTrait, MustacheRendererTrait {
        ConfigTrait::getConfig insteadof LoggerTrait, MustacheRendererTrait;
    }
    use ViewHelperTrait;

    public function execute(Http\Request $request = null, Http\Response $response = null, $startAt, $endAt) {
        $logger = $this->getLogger();

        $debugData = [];

        if ($error = error_get_last()) {
            $debugData['error'] = $error;
        }

        $debugData['time'] = ['value' => round($endAt - $startAt, 3), 'unit' => 'ms'];
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

        if ($response) {
            if ($response instanceof Http\JsonResponse) {
                $response->data['debug'] = $debugData;
            } else {
                $response->content = str_replace('</body>', PHP_EOL . $this->getRenderer()->render('partial/debug', [
                    'debugData' => $this->getViewHelper()->json($debugData),
                ]) . PHP_EOL . '</body>', $response->content);
            }
        }
    }
}