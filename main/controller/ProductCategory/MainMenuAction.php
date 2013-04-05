<?php

namespace Controller\ProductCategory;

class MainMenuAction {
    /**
     * @param \Http\Request $request
     * @return \Http\Response
     */
    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        return new \Http\JsonResponse([
            'content' => (new \View\Layout())->render('_mainMenu', array('menu' => (new \View\Menu())->generate())),
        ]);
    }
}