<?php

namespace Controller\Error;

class AccessDeniedAction {

    /**
     * @param \Exception $exception
     * @param \Http\Request $request
     *
     * @return \Http\RedirectResponse|\Http\Response
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws \RuntimeException
     */
    public function execute(\Exception $exception, \Http\Request $request) {

        \App::logger()->info([
            'message'   => 'Доступ запрещен.',
            'exception' => (string)$exception,
        ], ['security']);

        if ($request->isXmlHttpRequest()) {
            $response = new \Http\Response('', 403);
        } else {
            $response = new \Http\RedirectResponse(
                \App::router()->generate('user.login', $request->query->all())
            );
        }

        \App::user()->removeToken($response);

        return $response;
    }
}
