<?php

namespace Controller\User;

class IndexAction {
    public function __construct() {
        if (!\App::user()->getToken()) {
            throw new \Exception\AccessDeniedException();
        }
    }

    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $form = new \View\User\ConsultationForm();

        $page = new \View\User\IndexPage();
        $page->setParam('form', $form);

        return new \Http\Response($page->show());
    }
}