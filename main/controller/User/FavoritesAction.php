<?php


namespace Controller\User;


class FavoritesAction {

    public function __construct() {
        if (!\App::user()->getToken()) {
            throw new \Exception\AccessDeniedException();
        }
    }

    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse|\Http\Response
     */
    public function execute(\Http\Request $request) {
        $page = new \View\User\FavoritesPage();
        return new \Http\Response($page->show());

    }
}