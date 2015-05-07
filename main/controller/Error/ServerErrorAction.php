<?php

namespace Controller\Error;

class ServerErrorAction {
    public function execute() {
        //\App::logger()->debug('Exec ' . __METHOD__);

        if (\App::config()->ssi['enabled'] && (true === \App::request()->get('SSI'))) {
            $content = '<b>!</b>';
        } else {
            $content = \App::templating()->render('error/page-500');
        }

        return new \Http\Response($content, 500);
    }
}
