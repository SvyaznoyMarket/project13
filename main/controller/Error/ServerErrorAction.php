<?php

namespace Controller\Error;

class ServerErrorAction {
    public function execute() {

        if (\App::request()->headers->get('SSI') == 'on') {
            $content = '<b>!</b>';
        } else {
            $content = \App::templating()->render('error/page-500');
        }

        return new \Http\Response($content, 500);
    }
}
