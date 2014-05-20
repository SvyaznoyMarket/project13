<?php

namespace EnterSite\Action;

use Enter\Http;
use Enter\Curl\Query;
use EnterSite\ConfigTrait;
use EnterSite\LoggerTrait;
use EnterSite\MustacheRendererTrait;
use EnterSite\SessionTrait;
use EnterSite\ViewHelperTrait;
use EnterSite\Model\Page\Debug as Page;

class Debug {
    use ConfigTrait, LoggerTrait, MustacheRendererTrait, SessionTrait {
        ConfigTrait::getConfig insteadof LoggerTrait, SessionTrait, MustacheRendererTrait;
        LoggerTrait::getLogger insteadof SessionTrait;
    }
    use ViewHelperTrait;

    public function execute(Http\Request $request = null, Http\Response $response = null, $startAt, $endAt) {
        $config = $this->getConfig();
        $logger = $this->getLogger();

        if (!$config->debug) {
            return;
        }

        $totalTime = $endAt - $startAt;

        $page = new Page();

        // request id
        $page->requestId = $config->requestId;

        // error
        if ($error = error_get_last()) {
            $page->error = new Page\Error($error);
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
            $page->session = $this->getSession()->all();
        }

        // config
        $page->config = (array)$config;

        // curl query
        $i = 0;
        foreach ($logger as $message) {
            if (isset($message['tag'][0]) && in_array('curl', $message['tag'])) {
                /** @var Query|null $curlQuery */
                $curlQuery = (isset($message['query']) && $message['query'] instanceof Query) ? $message['query'] : null;
                if (!$curlQuery) continue;

                $info = $curlQuery->getInfo();

                $query = new Page\Query();

                $query->url = (string)$curlQuery->getUrl();
                $query->path = parse_url((string)$curlQuery->getUrl(), PHP_URL_PATH);
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