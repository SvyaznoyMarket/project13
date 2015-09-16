<?php


namespace Controller\User;

use EnterQuery as Query;

class FavoriteAction extends PrivateAction {
    use \EnterApplication\CurlTrait;

    /**
     * @return \Http\JsonResponse|\Http\Response
     */
    public function get() {
        $curl = $this->getCurl();

        $userEntity = \App::user()->getEntity();

        $favoriteListQuery = new Query\User\Favorite\Get();
        $favoriteListQuery->userUi = $userEntity->getUi();
        $favoriteListQuery->prepare();

        $wishlistListQuery = new Query\User\Wishlist\Get();
        $wishlistListQuery->userUi = $userEntity->getUi();
        $wishlistListQuery->filter->withProducts = true;
        $wishlistListQuery->prepare();

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
            \RepositoryManager::product()->prepareProductQueries($productsByUi, 'media');
        }

        \App::coreClientV2()->execute();

        $page = new \View\User\FavoritesPage();
        $page->setParam('productsByUi', $productsByUi);
        $page->setParam('favoriteProductsByUi', $favoriteProductsByUi);
        $page->setParam('wishlists', $wishlists);

        return new \Http\Response($page->show());
    }
}