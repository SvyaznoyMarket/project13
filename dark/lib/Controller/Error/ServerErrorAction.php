<?php

namespace Controller\Error;

class ServerErrorAction {
    public function execute() {
        \App::logger()->debug('Exec ' . __METHOD__);

        $content = \App::templating()->render('error/page-500');

        return new \Http\Response($content, 500);
    }
}
