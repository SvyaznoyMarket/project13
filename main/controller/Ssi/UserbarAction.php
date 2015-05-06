<?php

namespace Controller\Ssi;

class UserbarAction {
    /**
     * @param \Http\Request $request
     * @return \Http\Response
     */
    public function execute(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $page = new \View\DefaultLayout();

        $content = $page->render('userbar/_userbar', ['class' => 'header_i userbtn js-topbarfix']);

        return new \Http\Response($content);
    }
}
