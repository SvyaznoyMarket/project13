<?php

namespace Debug;

class ShowAction {
    public function execute(\Http\Request $request, \Http\Response $response = null) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $debug = \App::debug();
        $helper = new \Helper\TemplateHelper();

        if ($response && (200 != $response->getStatusCode())) {
            $debug->add('status', $response->getStatusCode(), 150, \Debug\Collector::TYPE_ERROR);
        }

        if ((bool)\App::exception()->all()) {
            $debug->add('error', array_map(function(\Exception $e) { return [
                'code'    => $e->getCode(),
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTrace(),
            ]; }, \App::exception()->all()), 149, \Debug\Collector::TYPE_ERROR);
        }

        $debug->add('id', \App::$id, 145);

        if ('main' != \App::$name) {
            $debug->add('app', \App::$name, 143);
        }

        // git
        $gitData = [
            'version' => trim(shell_exec(sprintf('cd %s && git rev-parse --abbrev-ref HEAD', realpath(\App::config()->appDir)))),
            'tag'     => trim(shell_exec(sprintf('cd %s && git describe --always --tag', realpath(\App::config()->appDir)))),
        ];
        $gitData['url'] = 'https://github.com/SvyaznoyMarket/project13/tree/' . $gitData['version'];
        $debug->add('git', $gitData, 144);
        $debug->add('env', \App::$env, 143);

        // query
        $queryData = [];
        foreach (\App::logger()->getMessages() as $message) {
            if (!in_array('curl', $message['_tag'])) continue;

            $url = !empty($message['url']) ? $message['url'] : null;
            $data = !empty($message['data']) ? $message['data'] : null;
            $startAt = isset($message['startAt']) ? $message['startAt'] : null;

            $index = md5($url . ':' . serialize($data));

            if ($url) {
                if ('Create curl' == $message['message']) {
                    $queryData[$index] = [
                        'url'        => $url,
                        'debug'      => $helper->url('debug.query', ['url' => $url, 'data' => (array)json_encode($data)]),
                        'escapedUrl' => $helper->escape(rawurldecode($url)),
                        'data'       => $data,
                        'timeout'    => isset($message['timeout']) ? $message['timeout'] : null,
                        'startAt'    => $startAt,
                        'count'      => isset($queryData[$index]['count']) ? ($queryData[$index]['count'] + 1) : 1,
                    ];
                } else if ((('Fail curl' == $message['message']) || ('End curl' == $message['message'])) && isset($queryData[$index])) {
                    if (isset($message['error'])) {
                        $queryData[$index]['error'] = $message['error'];
                    }
                    if (isset($message['response'])) {
                        $queryData[$index]['response'] = $message['response'];
                    }
                    if (isset($message['info'])) {
                        $queryData[$index]['info'] = $message['info'];
                        if (isset($queryData[$index]['info']['total_time'])) {
                            $queryData[$index]['info']['total_time'] = round($queryData[$index]['info']['total_time'], 3) * 1000;
                        }
                    }

                    $queryData[$index]['endAt'] = isset($message['endAt']) ? $message['endAt'] : null;
                    $queryData[$index]['spend'] = isset($message['spend']) ? round($message['spend'], 3) * 1000 : null;
                    $queryData[$index]['retryCount'] = isset($message['retryCount']) ? $message['retryCount'] : null;
                    $queryData[$index]['retryTimeout'] = isset($message['retryTimeout']) ? $message['retryTimeout'] : null;
                    $queryData[$index]['header'] = isset($message['header']) ? $message['header'] : null;
                }
            } else if ($startAt && ('End curl executing' == $message['message'])) {
                /*
                $queryData[] = [
                    'url'          => null,
                    'startAt'      => $startAt,
                    'endAt'        => isset($message['endAt']) ? $message['endAt'] : null,
                    'spend'        => isset($message['spend']) ? round($message['spend'], 3) * 1000 : null,
                    'retryCount'   => isset($message['retryCount']) ? $message['retryCount'] : null,
                    'retryTimeout' => isset($message['retryTimeout']) ? $message['retryTimeout'] : null,
                ];
                */
            }
        }
        $debug->add('query', array_values($queryData), 140);

        // timers
        $appTimer = \Debug\Timer::get('app');
        $coreTimer = \Debug\Timer::get('core');
        $contentTimer = \Debug\Timer::get('content');
        $dataStoreTimer = \Debug\Timer::get('data-store');
        $timerData = [
            ['name' => 'core', 'value' => round($coreTimer['total'], 3) * 1000, 'count' => $coreTimer['count'], 'unit' => 'ms'],
            ['name' => 'data-store', 'value' => round($dataStoreTimer['total'], 3) * 1000, 'count' => $dataStoreTimer['count'], 'unit' => 'ms'],
            ['name' => 'content', 'value' => round($contentTimer['total'], 3) * 1000, 'count' => $contentTimer['count'], 'unit' => 'ms'],
            ['name' => 'total', 'value' => round($appTimer['total'], 3) * 1000, 'count' => $appTimer['count'], 'unit' => 'ms'],
        ];
        $debug->add('timer', $timerData, 138);

        if (\App::user()->getToken()) {
            $debug->add('user', \App::user()->getToken(), 137);
        }

        // route
        $debug->add('route', \App::request()->attributes->get('route'), 136);
        $action =implode('.', (array)\App::request()->attributes->get('action', []));
        // action
        $debug->add('act', $action ?: 'undefined', 135, $action ? \Debug\Collector::TYPE_INFO : \Debug\Collector::TYPE_ERROR);

        // session
        $debug->add('session', \App::session()->all(), 133);

        // memory
        $debug->add('memory', ['value' => round(memory_get_peak_usage() / 1048576, 2), 'unit' => 'Mb'], 132);

        // config
        $reflection = new \ReflectionClass(\App::config());
        $configData = [];
        foreach ($reflection->getProperties() as $property) {
            $docblock = $property->getDocComment();
            if (false === strpos($docblock, '@hidden')) {
                $configData[$property->getName()] = [
                    //'name'  => $docblock, // TODO: вычленить из докблока название
                    'value' => $property->getValue(\App::config()),
                ];
            }
        }
        $reflection = null;
        $debug->add('config', $configData, 90);
        //die(var_dump($debug->getAll()));


        // ab test
        $abTestData = [];
        if ((bool)\App::config()->abtest['enabled']) {
            $abTestData['endAt'] = date('d-m-Y H:i', strtotime(\App::config()->abtest['bestBefore']));
            foreach (\App::abTest()->getOption() as $option) {
                $abTestData[$option->getKey()] = [
                    'name'    => $option->getName(),
                    'traffic' => $option->getTraffic(),
                    'enabled' => $option->getKey() == \App::abTest()->getCase()->getKey(),
                ];
            }
        }
        $debug->add('abTest', $abTestData, 89);

        // ab test json
        $abTestJsonData = [];
        if (\App::abTestJson() && (bool)\App::abTestJson()->isActive() && \App::abTestJson()->hasEnoughData()) {
            $abTestJsonData['endAt'] = date('d-m-Y H:i', strtotime(\App::abTestJson()->getConfig()['bestBefore']));
            foreach (\App::abTestJson()->getOption() as $option) {
                $abTestJsonData[$option->getKey()] = [
                    'name'    => $option->getName(),
                    'traffic' => $option->getTraffic(),
                    'enabled' => $option->getKey() == \App::abTestJson()->getCase()->getKey(),
                ];
            }
        }
        $debug->add('abTestJson', $abTestJsonData, 88);

        // log
        if ('live' != \App::$env) {
            //$debug->add('log', \App::logger()->dump(), 87);
        }

        // server
        if ('live' != \App::$env) {
            $debug->add('server', isset($_SERVER) ? $_SERVER : [], 86);
        }



        $debugData = [];
        foreach ($debug->getAll() as $item) {
            $debugData[$item['name']] = ['value' => $item['value'], 'type' => $item['type']];
        }

        if ($response instanceof \Http\JsonResponse) {
            $contentData = $response->getData();
            $contentData['debug'] = $debugData;

            $response->setData($contentData);
        } else if ($response instanceof \Http\Response) {
            $response->setContent(
                $response->getContent()
                . "\n\n"
                . \App::templating()->render('_debug', ['debugData' => $debugData, 'helper' => new \Helper\TemplateHelper()])
            );
        }
    }
}