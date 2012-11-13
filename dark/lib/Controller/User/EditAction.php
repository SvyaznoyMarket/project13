<?php

namespace Controller\User;

class EditAction {
    public function __construct() {
        if (!\App::user()->getToken()) {
            throw new \Exception\AccessDeniedException();
        }
    }

    public function execute(\Http\Request $request) {
        $user = \App::user()->getEntity();

        $form = new \View\User\EditForm();
        $form->fromEntity($user);

        $page = new \View\User\EditPage();
        $page->setParam('form', $form);

        return new \Http\Response($page->show());
    }
}