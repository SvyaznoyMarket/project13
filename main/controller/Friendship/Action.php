<?php

namespace Controller\Friendship;

class Action {
    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception
     */
    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $page = new \View\Friendship\IndexPage();

        return new \Http\Response($page->show());
    }
}