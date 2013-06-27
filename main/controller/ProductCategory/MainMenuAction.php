<?php

namespace Controller\ProductCategory;

class MainMenuAction {
    /**
     * @param \Http\Request $request
     * @return \Http\Response
     */
    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $renderer = \App::closureTemplating();
        $catalogJsonBulk = \RepositoryManager::productCategory()->getCatalogJsonBulk();

        $content = $renderer->render('__mainMenu', [
            'menu'            => (new \View\Menu())->generate(),
            'catalogJsonBulk' => $catalogJsonBulk,
        ]);

        return new \Http\JsonResponse([$content]);
    }
}