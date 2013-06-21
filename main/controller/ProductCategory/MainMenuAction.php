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
            'content'         => \App::closureTemplating()->render('__mainMenu', ['menu' => (new \View\Menu())->generate()]),
            'catalogJsonBulk' => \RepositoryManager::productCategory()->getCatalogJsonBulk(),
        ]);
    }
}