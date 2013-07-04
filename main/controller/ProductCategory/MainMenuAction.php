<?php

namespace Controller\ProductCategory;

class MainMenuAction {
    /**
     * @param \Http\Request $request
     * @throws \Exception
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

        if (mb_strlen($content) < 100) {
            throw new \Exception('Главное меню не получено');
        }

        return new \Http\JsonResponse(['content' => $content]);
    }
}