<?php

namespace Controller\Error;

class AccessDeniedAction {
    public function execute(\Exception $e, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        \App::logger('security')->info(array(
            'message'   => 'Доступ запрещен.',
            'exception' => (string)$e,
        ));

        if ($request->isXmlHttpRequest()) {
            return new \Http\Response('', 403);
        }

        return new \Http\RedirectResponse(\App::router()->generate('user.login'));
    }
}
