<?php

namespace Controller\Error;

use View\Error\NotFoundPage;

class NotFoundAction {
    /**
     * @param \Exception $e
     * @param \Http\Request $request
     * @return \Http\Response
     */
    public function execute(\Exception $e, \Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        \App::logger()->warn([
            'message'   => 'Страница не найдена.',
            'exception' => (string)$e,
        ]);

        if ($request->isXmlHttpRequest()) {
            return new \Http\Response('', 404);
        }

        $page = new NotFoundPage();

        return new \Http\Response($page->show(), 404);
    }
}
