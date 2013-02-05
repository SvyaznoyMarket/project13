<?php

namespace Controller\Error;

class NotFoundAction {
    public function execute(\Exception $e, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        \App::logger()->error([
            'message'   => 'Страница не найдена.',
            'exception' => (string)$e,
        ]);

        if ($request->isXmlHttpRequest()) {
            return new \Http\Response('', 404);
        }

        $content = \App::templating()->render('error/page-404', [
            'page'      => new \View\Layout(),
            'exception' => $e,
        ]);

        return new \Http\Response($content, 404);
    }
}
