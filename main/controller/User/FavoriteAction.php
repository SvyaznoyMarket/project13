<?php


namespace Controller\User;

use EnterQuery as Query;

class FavoriteAction extends PrivateAction {
    use \EnterApplication\CurlTrait;

    /**
     * @return \Http\JsonResponse|\Http\Response
     */
    public function get() {
        $config = \App::config();
        $curl = $this->getCurl();

        $userEntity = \App::user()->getEntity();

        $favoriteListQuery = new Query\User\Favorite\Get();
        $favoriteListQuery->userUi = $userEntity->getUi();
        $favoriteListQuery->prepare();

        $wishlistListQuery = new Query\User\Wishlist\Get();
        $wishlistListQuery->userUi = $userEntity->getUi();
        $wishlistListQuery->filter->withProducts = true;
        $wishlistListQuery->prepare();

        // настройки из cms
        /** @var Query\Config\GetByKeys|null $configQuery */
        $configQuery =
            $config->userCallback['enabled']
            ? (new Query\Config\GetByKeys(['site_call_phrases']))->prepare()
            : null
        ;

        $curl->execute();

        $productUis = [];
        $favoriteProductsByUi = [];
        foreach ($favoriteListQuery->response->products as $item) {
            $ui = isset($item['uid']) ? (string)$item['uid'] : null;
            if (!$ui) continue;

            $favoriteProductsByUi[$ui] = new \Model\Favorite\Product\Entity($item);
            $productUis[] = $ui;
        }

        $wishlists = [];
        foreach ($wishlistListQuery->response->wishlists as $item) {
            if (!@$item['id']) continue;

            $wishlist = new \Model\Wishlist\Entity($item);
            $wishlists[] = $wishlist;
            foreach ($wishlist->products as $product) {
                $productUis[] = $product->ui;
            }
        }

        $productUis = array_unique($productUis);

        /** @var \Model\Product\Entity[] $productsByUi */
        $productsByUi = [];
        if ($productUis) {
            foreach ($productUis as $productUi) {
                $productsByUi[$productUi] = new \Model\Product\Entity(['ui' => $productUi]);
            }
            \RepositoryManager::product()->prepareProductQueries($productsByUi, 'media label');
        }

        \App::coreClientV2()->execute();

        // SITE-6622
        $callbackPhrases = [];
        if ($configQuery) {
            foreach ($configQuery->response->keys as $item) {
                if ('site_call_phrases' === $item['key']) {
                    $value = json_decode($item['value'], true);
                    $callbackPhrases = !empty($value['private']) ? $value['private'] : [];
                }
            }
        }

        $page = new \View\User\FavoritesPage();
        $page->setParam('productsByUi', $productsByUi);
        $page->setParam('favoriteProductsByUi', $favoriteProductsByUi);
        $page->setParam('wishlists', $wishlists);
        $page->setGlobalParam('callbackPhrases', $callbackPhrases);

        return new \Http\Response($page->show());
    }
}