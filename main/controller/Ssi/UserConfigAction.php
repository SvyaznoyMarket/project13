<?php

namespace Controller\Ssi;

class UserConfigAction {
    /**
     * @param \Http\Request $request
     * @return \Http\Response
     */
    public function execute(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        return new \Http\Response(\App::helper()->render('__userConfig'));
    }
}
