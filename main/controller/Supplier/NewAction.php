<?php

namespace controller\Supplier;


use Http\Response;
use View\Supplier\NewPage;

/** Регистрация нового поставщика
 * Class NewAction
 * @package controller\Supplier
 */
class NewAction {

    public function execute() {
        $page = new NewPage();
        return new Response($page->show());
    }

}