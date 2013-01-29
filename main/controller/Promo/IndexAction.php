<?php

namespace Controller\Promo;

class IndexAction {
    public function execute($promoToken, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $promo = \RepositoryManager::promo()->getEntityByToken($promoToken);
        if (!$promo) {
            throw new \Exception\NotFoundException(sprintf('Промо-каталог @%s не найден.', $promoToken));
        }

        $repository = \RepositoryManager::promo();

        $slides = [];
        foreach ($promo->getImage() as $image) {
            $repository->setEntityImageLink($image);

            $slides[] = [
                'imgUrl'  => $image->getUrl(),
                'title'   => $image->getName(),
                'linkUrl' => $image->getLink(),
            ];
        }

        $page = new \View\Promo\IndexPage();
        $page->setParam('promo', $promo);

        return new \Http\Response($page->show());
    }
}