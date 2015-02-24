<?php

namespace Controller;

class PreAction {
    /**
     * Запрашивает редирект и АБ-тесты
     *
     * @param \Http\Request $request
     * @throws \Exception\NotFoundException
     * @return \Http\Response|null
     */
    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        // cache
        $route = $request->attributes->get('route');
        $repositoriesByRoute = [
            'product' => 'ProductCard\\Get',
        ];
        $repositoryClass = isset($repositoriesByRoute[$route]) ? ('\\EnterApplication\\Action\\' . $repositoriesByRoute[$route]) : null;
        if ($repositoryClass) {
            (new $repositoryClass)->execute();
        }

        $uri = $request->getPathInfo();
        $redirectUrl = null;

        if (
            \App::config()->redirect301['enabled']
            && ('/' != $uri) // если не главная страница, ...
            && !\App::config()->preview // ...если не preview.enter.ru
            && !$request->isXmlHttpRequest() // ...если не ajax-запрос
            && ('POST' != $request->getMethod()) // ... если не POST-запрос
        ) {
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
        }

        \App::scmsClient()->addQuery(
            'api/ab_test/get-active',
            [],
            [],
            function($data) {
                if (isset($data[0])) {
                    // FIXME: сомнительно
                    $tests = [];
                    foreach ($data as $item) {
                        if (empty($item['token'])) {
                            continue;
                        }

                        $tests[$item['token']] = $item;
                    }

                    \App::config()->abTest['tests'] = $tests;
                }
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
