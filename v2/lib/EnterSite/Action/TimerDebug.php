<?php

namespace EnterSite\Action;

use Enter\Http;
use Enter\Curl\Query;
use EnterSite\ConfigTrait;
use EnterSite\LoggerTrait;
use EnterSite\MustacheRendererTrait;

class TimerDebug {
    use ConfigTrait;
    use LoggerTrait, MustacheRendererTrait {
        ConfigTrait::getConfig insteadof LoggerTrait, MustacheRendererTrait;
    }

    public function execute(Http\Response $response, $startAt, $endAt) {
        $logger = $this->getLogger();

        $total = $endAt - $startAt;
        $result = [
            'lines' => [],
            'time'  => round($total, 3) * 1000,
        ];

        // curl query
        $i = 0;
        foreach ($logger as $message) {
            if (isset($message['tag'][0]) && in_array('curl', $message['tag'])) {
                /** @var Query|null $query */
                $query = (isset($message['query']) && $message['query'] instanceof Query) ? $message['query'] : null;
                if (!$query) continue;

                $info = $query->getInfo();

                $line = [
                    'title'        => $query->getUrl(),
                    'name'         => parse_url($query->getUrl(), PHP_URL_PATH),
                    'time'         => round(($query->getEndAt() - $query->getStartAt()), 3) * 1000,
                    'call'         => $query->getCall(),
                    'css'          => [
                        'top'          => $i * 24,
                        'left'         => ($query->getStartAt() - $startAt) / $total * 100,
                        'width1'       => (($query->getEndAt() - $startAt) / $total - ($query->getStartAt() - $startAt) / $total) * 100,
                        'color1'       => $query->getError() ? '#cc0000' : '#00bce1',
                        'color2'       => $query->getError() ? '#ff0000' : '#43c6ed',
                    ],
                ];
                $line['css']['width2'] = $info['total_time'] / $total * 100 / $line['css']['width1'] * 100;

                $result['lines'][] = $line;

                $i++;
            }
        }
        //die(var_dump($result));

        $content = $this->getRenderer()->render('partial/debug/~timer', $result);

        $response->content .= $content;
    }
}