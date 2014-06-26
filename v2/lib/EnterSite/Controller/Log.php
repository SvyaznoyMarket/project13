<?php

namespace EnterSite\Controller;

use Enter\Http;
use Enter\Templating;
use Enter\Util\JsonDecoderTrait;
use EnterSite\ConfigTrait;
use EnterSite\LoggerTrait;
use EnterSite\MustacheRendererTrait;
use EnterSite\Controller;

class Log {
    use ConfigTrait, LoggerTrait, MustacheRendererTrait {
        ConfigTrait::getConfig insteadof LoggerTrait, MustacheRendererTrait;
    }
    use JsonDecoderTrait;

    public function execute(Http\Request $request) {
        $config = $this->getConfig();
        $logger = $this->getLogger();

        $id = trim((string)$request->query['id']);
        if (!$id) {
            throw new \Exception('Не передан id');
        }

        $offset = (int)$request->query['offset'] ?: 600;
        $before = (int)$request->query['before'];

        // страница
        $page = [
            'dataDebug' => $this->getConfig()->debugLevel ? 'true' : '',
            'id'        => $id,
            'date'      => null,
            'messages'  => [],
        ];

        $result = shell_exec(sprintf('tail -n %s %s | grep "\"_id\":\"%s\""%s',
            $offset,
            (2 == $config->debugLevel) ? $config->logger->debugAppender->file : $config->logger->fileAppender->file,
            $id,
            $before ? (' -B ' . $before) : ''
        ));
        $messages = [];
        foreach (explode(PHP_EOL, $result) as $i => $line) {
            if (!$line) continue;

            $line = json_decode($line, true);
            if (isset($line['date'])) {
                if (!isset($page['date'])) $page['date'] = $line['date'];
                unset($line['date']);
            }

            // query
            if (isset($line['query']['response'])) {
                try {
                    $line['query']['response'] = $this->jsonToArray($line['query']['response']);
                } catch (\Exception $e) {
                    $logger->push(['type' => 'warn', 'error' => $e, 'action' => __METHOD__, 'tag' => ['debug']]);
                }
            }

            $messages[] = [
                'id'    => $line['time'],
                'color' => $id == $line['_id'] ? '#ffffcc' : '#ededed',
                'value' => json_encode($line, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            ];
        }
        $page['messages'] = $messages;

        // рендер
        $rendererConfig = new \Enter\Templating\PhpClosure\Config();
        $rendererConfig->templateDir = $config->mustacheRenderer->templateDir;
        $renderer = new Templating\PhpClosure\Renderer($rendererConfig);
        $content = $renderer->render('page/log', $page);

        // http-ответ
        $response = new Http\Response($content);

        return $response;
    }
}