<?php

namespace Controller\Error;

class NotFoundAction {
    public function execute(\Exception $e, \Http\Request $request) {
        \App::logger()->error(array(
            'message'   => 'Страница не найдена.',
            'exception' => (string)$e,
        ));

        if ($request->isXmlHttpRequest()) {
            return new \Http\Response('', 404);
        }

        $page = new \View\Error\IndexPage();
        $page->setParam('title', 'Ошибка 404');
        $page->setParam('message', 'Страница не найдена');

        return new \Http\Response($page->show(), 404);
    }
}
