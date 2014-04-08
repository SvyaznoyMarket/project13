<?php

namespace Controller\ProductCategory;

class MainMenuAction {
    /**
     * @param \Http\Request $request
     * @param int $regionId
     * @return \Http\Response
     */
    public function execute(\Http\Request $request, $regionId = null) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $region = null;
        if (!empty($regionId)) {
            $region = \RepositoryManager::region()->getEntityById($regionId);
            if (!$region) {
                \App::logger()->warn(sprintf('Регион #"%s" не найден.', $regionId), ['region']);
            }
        }

        $catalogJsonBulk = \RepositoryManager::productCategory()->getCatalogJsonBulk();
        $promoHtmlBulk = \RepositoryManager::productCategory()->getPromoHtmlBulk($catalogJsonBulk);

        return new \Http\JsonResponse([
            'content' => \App::closureTemplating()->render('__mainMenu', [
                'menu'            => (new \View\Menu())->generate($region),
                'catalogJsonBulk' => $catalogJsonBulk,
                'promoHtmlBulk'   => $promoHtmlBulk,
            ]),
        ]);
    }
}