<?php

namespace Controller\OrderV3;

class UpdateContactAction {

    /**
     * @param \Http\Request $request
     * @return \Http\Response
     */
    public function execute(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        return new \Http\RedirectResponse(\App::router()->generate('orderV3.delivery'));
    }
}