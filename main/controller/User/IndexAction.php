<?php

namespace Controller\User;

class IndexAction extends PrivateAction {



    public function execute(\Http\Request $request) {

        \App::logger()->warn(sprintf("Старый маршрут %s со страницы %s", $request->attributes->get('route'), $request->get('HTTP_REFERER')), ['route']);

        return new \Http\RedirectResponse(\App::router()->generate(\App::config()->user['defaultRoute']));

    }
}