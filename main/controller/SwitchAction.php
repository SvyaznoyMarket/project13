<?php


namespace Controller;

use Exception\NotFoundException;

class SwitchAction {
    /**
     * @param \Http\Request $request
     * @return \Http\Response
     */
    public function execute(\Http\Request $request) {
        if ($request->getHost() == 'www.enter.ru') {
            throw new NotFoundException();
        }

        $tests = \App::abTest()->getTests();
        $page = new \View\SwitchView();
        $page->setParam('tests', $tests);

        return new \Http\Response($page->show());
    }

} 