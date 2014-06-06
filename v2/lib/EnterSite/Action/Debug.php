<?php

namespace EnterSite\Action;

use Enter\Http;
use Enter\Curl\Query;
use EnterSite\ConfigTrait;
use EnterSite\LoggerTrait;
use EnterSite\MustacheRendererTrait;
use EnterSite\SessionTrait;
use EnterSite\ViewHelperTrait;
use EnterSite\DebugContainerTrait;
use EnterSite\Model\Page\Debug as Page;

class Debug {
    use ConfigTrait, LoggerTrait, MustacheRendererTrait, SessionTrait, ViewHelperTrait, DebugContainerTrait {
        ConfigTrait::getConfig insteadof LoggerTrait, SessionTrait, MustacheRendererTrait, DebugContainerTrait;
        LoggerTrait::getLogger insteadof SessionTrait;
    }

    public function execute(Http\Request $request = null, Http\Response $response = null, \Exception $error = null, $startAt, $endAt) {
        $config = $this->getConfig();
        $logger = $this->getLogger();
        $viewHelper = $this->getViewHelper();

        if (!$config->debug) {
            return;
        }

        $totalTime = $endAt - $startAt;

        $page = new Page();

        // request id
        $page->requestId = $config->requestId;

        $page->path = $request ? ltrim($request->getPathInfo(), '/') : null;

        // error
        if ($error) {
            $page->error = new Page\Error([
                'message' => $error->getMessage(),
                'type'    => $error->getCode(),
                'file'    => $error->getFile(),
                'line'    => $error->getLine(),
            ]);
        }
        else if ($lastError = error_get_last()) {
            $page->error = new Page\Error($lastError);
        }

        // times
        $page->times['total'] = new Page\Time();
        $page->times['total']->value = round($endAt - $startAt, 3);
        $page->times['total']->unit = 'ms';

        // memory
        $page->memory = new Page\Memory();
        $page->memory->value = round(memory_get_peak_usage() / 1048576, 2);
        $page->memory->unit = 'Mb';

        // session
        if (isset($GLOBALS['EnterSite\SessionTrait::getSession'])) {
            $page->session = json_encode($this->getSession()->all(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        }

        // config
        $page->config = json_encode((array)$config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        // curl query
        $i = 0;
        foreach ($logger as $message) {
            if (isset($message['tag'][0]) && in_array('curl', $message['tag'])) {
                /** @var Query|null $curlQuery */
                $curlQuery = (isset($message['query']) && $message['query'] instanceof Query) ? $message['query'] : null;
                if (!$curlQuery) continue;

                $info = $curlQuery->getInfo();

                $query = new Page\Query();

                $query->url = urldecode((string)$curlQuery->getUrl());
                $query->path = ltrim(parse_url((string)$curlQuery->getUrl(), PHP_URL_PATH), '/');
                $query->call = $curlQuery->getCall();
                $query->time = round(($curlQuery->getEndAt() - $curlQuery->getStartAt()), 3) * 1000;

                $query->css = [
                    'top'          => $i * 24,
                    'left'         => ($curlQuery->getStartAt() - $startAt) / $totalTime * 100,
                    'width1'       => (($curlQuery->getEndAt() - $startAt) / $totalTime - ($curlQuery->getStartAt() - $startAt) / $totalTime) * 100,
                    'color1'       => $curlQuery->getError() ? '#cc0000' : '#00bce1',
                    'color2'       => $curlQuery->getError() ? '#ff0000' : '#43c6ed',
                ];
                $query->css['width2'] = $info['total_time'] / $totalTime * 100 / $query->css['width1'] * 100;

                $page->queries[] = $query;

                $i++;
            }
        }

        // данные из контейнера отладки
        foreach (get_object_vars($this->getDebugContainer()) as $key => $value) {
            if (isset($page->{$key})) {
                $logger->push(['type' => 'warn', 'error' => sprintf('Свойство %s уже существует', $key), 'action' => __METHOD__, 'tag' => ['debug']]);
                continue;
            }

            $page->{$key} = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        }

        if ($response) {
            if ($response instanceof Http\JsonResponse) {
                $response->data['debug'] = $page;
            } else {
                $response->content = str_replace('</body>', PHP_EOL . $this->getRenderer()->render('partial/debug', [
                    'requestId' => $page->requestId,
                    'debug'     => $this->getViewHelper()->json($page),
                ]) . PHP_EOL . '</body>', $response->content);
            }
        }
    }
}