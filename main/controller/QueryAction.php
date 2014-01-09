<?php

namespace Controller;

class QueryAction {
    /**
     * @param \Http\Request $request
     * @throws \Exception\NotFoundException
     * @return \Http\Response
     */
    public function index(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        \App::config()->debug = false;

        $url = urldecode(trim((string)$request->get('url')));
        $data = $request->get('data');
        if (is_string($data)) {
            $data = json_decode($data, true);
        }
        $data = (array)$data;

        $result = null;

        if ($url) {
            $domain = implode('.', array_slice(array_pad(explode('.', parse_url($url, PHP_URL_HOST)), 2, null), -2, 2));
            if (!in_array($domain, ['enter.ru', 'ent3.ru', 'enter.loc', 'ent3.dev', 'enter-cms.loc'])) {
                throw new \Exception\NotFoundException();
            }

            try {
                $result = \App::curl()->query($url, $data, 10);
                \App::logger('query')->info([
                    'url'    => $url,
                    'data'   => $data,
                    'result' => $result,
                ]);
            } catch (\Exception $e) {
                \App::exception()->remove($e);

                if ($e instanceof \Curl\Exception) {
                    $result = ['error' => $e->getContent()];
                } else {
                    $result = ['error' => ['code' => $e->getCode(), 'message' => $e->getMessage()]];
                }

                \App::logger('query')->error([
                    'url'    => $url,
                    'data'   => $data,
                    'result' => $result,
                ]);
            }

            return new \Http\RedirectResponse(\App::router()->generate('debug.query.show', ['queryToken' => \App::$id]));
        }

        return new \Http\Response(\App::closureTemplating()->render('page-query', [
            'url'    => $url,
            'data'   => $data,
            'result' => $result,
        ]));
    }

    /**
     * @param \Http\Request $request
     * @param $queryToken
     * @return \Http\Response
     */
    public function show(\Http\Request $request, $queryToken) {
        \App::logger()->debug('Exec ' . __METHOD__);

        \App::config()->debug = false;

        $queryToken = trim((string)$queryToken);

        $data = [];
        try {
            $log = shell_exec(sprintf('cd %s && tail -n 50000 query.log | grep %s',
                \App::config()->logDir,
                $queryToken
            ));

            $data = (array)json_decode($log, true);
        } catch(\Exception $e) {
            $data = [];
        }

        $data = array_merge(['url' => null, 'data' => null, 'result' => null], $data);

        return new \Http\Response(\App::closureTemplating()->render('page-query', $data));
    }
}
