<?php

namespace Controller\Tchibo;

class IndexAction {

    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $slideData = [];
        $repository = \RepositoryManager::promo();
        $promoToken = 'tchibo';

        $promo = $repository->getEntityByToken($promoToken);

        if (!$promo) {
            throw new \Exception\NotFoundException(sprintf('Промо-каталог @%s не найден.', $promoToken));
        }

        foreach ($promo->getImage() as $image) {
            $slideData[] = [
                'imgUrl'  => \App::config()->dataStore['url'] . 'promo/' . $promo->getToken() . '/' . trim($image->getUrl(), '/'),
                'title'   => $image->getName(),
                'linkUrl' => $image->getLink()?($image->getLink().'?from='.$promo->getToken()):'',
                // Пока не нужно, но в будущем, возможно понадобится делать $repository->setEntityImageLink() как в /main/controller/Promo/IndexAction.php
            ];
        }

        $page = new \View\Tchibo\IndexPage();
        $page->setParam('slideData', $slideData);

        return new \Http\Response($page->show());
    }
}