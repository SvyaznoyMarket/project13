<?php

namespace Controller\Error;

class AccessDeniedAction {
    public function execute(\Exception $e, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        \App::logger()->info([
            'message'   => 'Доступ запрещен.',
            'exception' => (string)$e,
        ], ['security']);

        if ($request->isXmlHttpRequest()) {
            return new \Http\Response('', 403);
        }

        return new \Http\RedirectResponse(\App::router()->generate('user.login'));
    }
}
