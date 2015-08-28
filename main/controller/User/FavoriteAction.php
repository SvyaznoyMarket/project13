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

        $favoriteProductsByUi = [];
        foreach ($favoriteListQuery->response->products as $item) {
            $ui = isset($item['uid']) ? (string)$item['uid'] : null;
            if (!$ui) continue;

            $favoriteProductsByUi[$ui] = new \Model\Favorite\Product\Entity($item);
        }

        $wishlists = [];
        foreach ($wishlistListQuery->response->wishlists as $item) {
            if (!@$item['id']) continue;

            $wishlists[] = new \Model\Wishlist\Entity($item);
        }

        /** @var \Model\Product\Entity[] $products */
        $products = [];
        if ($favoriteProductsByUi) {
            $products = array_map(function($productUi) { return new \Model\Product\Entity(['ui' => $productUi]); }, array_keys($favoriteProductsByUi));
            \RepositoryManager::product()->prepareProductQueries($products, 'media');
            \App::coreClientV2()->execute();
        }

        $page = new \View\User\FavoritesPage();
        $page->setParam('products', $products);
        $page->setParam('favoriteProductsByUi', $favoriteProductsByUi);

        return new \Http\Response($page->show());
    }
}