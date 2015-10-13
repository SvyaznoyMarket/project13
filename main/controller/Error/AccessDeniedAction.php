<?php

namespace Controller\Error;

class AccessDeniedAction {

    /**
     * @param \Exception $exception
     * @param \Http\Request $request
     * @param $redirectUrl
     *
     * @return \Http\RedirectResponse|\Http\Response
     */
    public function execute(\Exception $exception, \Http\Request $request, $redirectUrl) {

        \App::logger()->info([
            'message'   => 'Доступ запрещен.',
            'exception' => (string)$exception,
        ], ['security']);

        if ($request->isXmlHttpRequest()) {
            $response = new \Http\Response('', 403);
        } else {
            $response = new \Http\RedirectResponse(
                \App::router()->generate('user.login', ['redirect_to' => $redirectUrl])
            );
        }

        \App::user()->removeToken($response);

        return $response;
    }
}
