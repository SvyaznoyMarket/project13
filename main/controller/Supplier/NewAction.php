<?php

namespace controller\Supplier;


use Http\RedirectResponse;
use Http\Response;
use View\Supplier\NewPage;

/** Регистрация нового поставщика
 * Class NewAction
 * @package controller\Supplier
 */
class NewAction {

    public function execute() {
        $page = new NewPage();
        if (\App::user()->getEntity()) return new RedirectResponse(\App::helper()->url('supplier.cabinet'));
        return new Response($page->show());
    }

}