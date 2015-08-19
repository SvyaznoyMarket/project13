<?php

namespace Controller\Subscribe;

class FriendAction {
    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception
     */
    public function show(\Http\Request $request) {

        $page = new \View\Subscribe\Friend\ShowPage();

        $response = new \Http\Response($page->show());

        return $response;
    }

    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception
     */
    public function create(\Http\Request $request) {

    }

    /**
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception
     */
    public function confirm(\Http\Request $request) {
    }
}
