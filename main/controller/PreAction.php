<?php

namespace Controller;

use Session\AbTest\ABHelperTrait;

class PreAction {
    use ABHelperTrait;

    /**
     * Запрашивает редирект и АБ-тесты
     *
     * @param \Http\Request $request
     * @throws \Exception\NotFoundException
     * @return \Http\Response|null
     */
    public function execute(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        // cache
        try {
            if (\App::config()->curlCache['enabled']) {
                (new \Controller\CacheAction())->execute($request);
            }
        } catch (\Exception $e) {
            \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['curl-cache', 'controller', 'critical']);
        }

        $routeName = $request->routeName;
        $uri = urldecode($request->getPathInfo());
        $redirectUrl = null;

        if (
            \App::config()->redirect301['enabled']
            && ('/' != $uri) // если не главная страница, ...
            && !\App::config()->preview // ...если не preview.enter.ru
            && !$request->isXmlHttpRequest() // ...если не ajax-запрос
            && ('POST' != $request->getMethod()) // ... если не POST-запрос
            && (0 !== strpos($routeName, 'user') || $routeName === 'enterprize.show')
            && (0 !== strpos($routeName, 'cart'))
            && (0 !== strpos($routeName, 'order'))
            && (0 !== strpos($routeName, 'compare'))
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

                    if ($redirectUrl !== '/') {
                        $redirectUrl = rtrim($redirectUrl, '/');
                    }
                },
                function(\Exception $e) {
                    \App::exception()->remove($e);
                }
            );
        }

        if (\App::config()->abTest['enabled']) {
            \App::scmsClient()->addQuery(
                'api/ab_test/get-active',
                //('switch'  === $request->routeName) ? [] : ['tags' => ['site-web']],
                ['tags' => ['site-web']],
                [],
                function($data) {
                    if (isset($data[0])) {
                        $tests = [];
                        foreach ($data as $item) {
                            if (empty($item['token'])) {
                                continue;
                            }

                            $tests[$item['token']] = $item;
                        }

                        \App::config()->abTest['tests'] = $tests; // FIXME: нельзя модифицировать конфигурацию
                    }
                },
                function(\Exception $e) {
                    \App::exception()->remove($e);
                }
            );
        }

        \App::scmsSeoClient()->execute(\App::config()->scmsSeo['retryTimeout']['tiny']);

        if (!$redirectUrl) {
            try {
                // если пользователь авторизован, то подгружает серверную корзину
                if ($this->isCoreCart()) {
                    $userEntity = \App::user()->getEntity();

                    $controller = new \EnterApplication\Action\Cart\Update();
                    $controllerRequest = $controller->createRequest();
                    $controllerRequest->regionId = \App::user()->getRegionId();
                    $controllerRequest->userUi = $userEntity ? $userEntity->getUi() : null;

                    if ($controllerRequest->userUi && $controllerRequest->regionId) {
                        $controller->execute($controllerRequest);
                    }
                }
            } catch (\Exception $e) {
                \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['cart']);
            }

            // подмешиваем в запрос данные из куки urlParams SITE-6456
            try {
                if (
                    ($cookieValue = $request->cookies->get('urlParams'))
                    && ($cookieValue = json_decode($cookieValue, true))
                    && is_array($cookieValue)
                ) {
                    foreach ($cookieValue as $k => $v) {
                        if ($request->query->has($k)) {
                            continue;
                        }

                        $request->query->set($k, $v);
                    }
                }
            } catch (\Exception $e) {
                \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['partner']);
            }

            return null;
        }

        if ((false === strpos($redirectUrl, '?')) && $request->getQueryString()) {
            $redirectUrl .= '?' . $request->getQueryString();
        }

        return new \Http\RedirectResponse($redirectUrl, 301);
    }
}
