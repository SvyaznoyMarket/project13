<?php

namespace Controller;

class RedirectAction {
    /**
     * @param \Http\Request $request
     * @throws \Exception\NotFoundException
     * @return \Http\Response|null
     */
    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $uri = $request->getPathInfo();
        // если главная страница, то игнорируем
        if ('/' == $uri) {
            return null;
        }
        // если preview.enter.ru, то редиректы тоже не нужны
        if (\App::config()->preview) {
            return null;
        }
        // если ajax-запрос, то игнорируем
        if ($request->isXmlHttpRequest()) {
            return null;
        }

        $redirectUrl = null;
        \App::scmsSeoClient()->addQuery(
            'redirect',
            ['from_url' => $uri],
            [],
            function($data) use(&$uri, &$redirectUrl) {
                $redirectUrl = isset($data['to_url']) ? trim($data['to_url']) : null;

                if ($redirectUrl && (0 !== strpos($redirectUrl, '/'))) {
                    $redirectUrl = null;
                    \App::logger()->error(sprintf('Неправильный редирект %s -> %s', $uri, $redirectUrl), ['redirect']);
                }
                $redirectUrl = preg_replace('/\/$/','',(string)$redirectUrl);
            },
            function(\Exception $e) {
                \App::exception()->remove($e);
            }
        );

        \App::scmsSeoClient()->execute(\App::config()->scmsSeo['retryTimeout']['tiny']);

        if (!$redirectUrl) {
            return null;
        }

        if ((false === strpos($redirectUrl, '?')) && $request->getQueryString()) {
            $redirectUrl .= '?' . $request->getQueryString();
        }

        return new \Http\RedirectResponse($redirectUrl, 301);
    }
}
