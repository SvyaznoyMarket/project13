<?php

namespace Controller\Promo;

/**
 * Class IndexAction
 * Страницы вида /promo/{token}
 * @package Controller\Promo
 */
class IndexAction {
    public function execute($promoToken, \Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $router = \App::router();
        $client = \App::coreClientV2();
        $user = \App::user();
        $region = $user->getRegion();

        $promo = \RepositoryManager::promo()->getEntityByToken($promoToken);
        if (!$promo) {
            throw new \Exception\NotFoundException(sprintf('Промо-каталог @%s не найден.', $promoToken));
        }

        $slideData = [];
        foreach ($promo->getPages() as $promoPage) {
            if (!$promoPage->getImageUrl()) continue;

            $slideData[] = [
                'imgUrl'  => $promoPage->getImageUrl(),
                'title'   => $promoPage->getName(),
                'linkUrl' =>
                    $promoPage->getLink()
                    ? ($promoPage->getLink() . ((false === strpos($promoPage->getLink(), '?')) ? '?' : '&'). 'from=' . $promo->getToken())
                    : null,
            ];
        }

        $page = new \View\Promo\IndexPage();
        $page->setParam('promo', $promo);
        $page->setParam('slideData', $slideData);

        return new \Http\Response($page->show());
    }
}