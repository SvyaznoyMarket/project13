<?php

namespace Controller\Kitchen;

class RequestAction {

    /**
     * @param \Http\Request $request
     * @return \Http\Response
     */
    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        return new \Http\JsonResponse([
            ''
        ]);
    }
}