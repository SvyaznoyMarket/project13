<?php

namespace Controller\Error;

class ServerErrorAction {
    public function execute(\Exception $e) {
        $content = \App::templating()->render('error/page-500');

        return new \Http\Response($content, 500);
    }
}
