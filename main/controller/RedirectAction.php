<?php

namespace Controller;

class RedirectAction {
    /**
     * @param \Http\Request $request
     * @throws \Exception\NotFoundException
     * @return \Http\Response
     */
    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $uri = $request->getRequestUri();
        // если главная страница, то игнорируем
        if ('/' == $uri) {
            return;
        }

        $redirectUrl = null;
        \App::dataStoreClient()->addQuery('301.json', [], function($data) use(&$uri, &$redirectUrl) {
            $redirectUrl = isset($data[$uri]) ? trim((string)$data[$uri]) : null;

            if ($redirectUrl && (0 !== strpos($redirectUrl, '/'))) {
                $redirectUrl = null;
                \App::logger()->error(sprintf('Неправильный редирект %s -> %s', $uri, $redirectUrl), ['redirect']);
            }

            if ($redirectUrl && isset($data[$redirectUrl])) {
                $redirectUrl = null;
                \App::logger()->error(sprintf('Обнаружен зацикливающийся редирект %s -> %s', $uri, $redirectUrl), ['redirect']);
            }
        });
        \App::dataStoreClient()->execute(\App::config()->dataStore['retryTimeout']['tiny']);
        if (!$redirectUrl) {
            return;
        }

        return new \Http\RedirectResponse($redirectUrl, 301);
    }
}
