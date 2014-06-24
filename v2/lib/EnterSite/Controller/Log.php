<?php

namespace EnterSite\Controller;

use Enter\Http;
use EnterSite\ConfigTrait;
use EnterSite\LoggerTrait;
use EnterSite\MustacheRendererTrait;

class Log {
    use ConfigTrait, LoggerTrait, MustacheRendererTrait {
        ConfigTrait::getConfig insteadof LoggerTrait, MustacheRendererTrait;
    }

    public function execute(Http\Request $request) {
        $config = $this->getConfig();

        $id = trim((string)$request->query['id']);
        if (!$id) {
            throw new \Exception('Не передан id');
        }

        // страница
        $page = [
            'messages' => [],
        ];

        $result = shell_exec(sprintf('tail -n 10000 %s | grep %s -B 100', $config->logger->debugAppender->file, $id));
        $messages = [];
        foreach (explode(PHP_EOL, $result) as $line) {
            if (!$line) continue;

            $line = json_decode($line, true);
            if (isset($line['_id'])) unset($line['_id']);

            $messages[] = [
                'value' => json_encode($line, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            ];
        }
        $page['messages'] = $messages;
        //die(var_dump($page));

        // рендер
        $renderer = $this->getRenderer();
        $content = $renderer->render('page/log', $page);

        // http-ответ
        $response = new Http\Response($content);

        return $response;
    }
}