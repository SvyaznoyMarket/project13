<?php


namespace Controller\User;

use EnterQuery as Query;

class FavoriteAction {
    use \EnterApplication\CurlTrait;

    public function __construct() {
        if (!\App::user()->getToken()) {
            throw new \Exception\AccessDeniedException();
        }
    }

    /**
     * @return \Http\JsonResponse|\Http\Response
     */
    public function get() {
        $curl = $this->getCurl();

        $favoriteQuery = (new Query\User\Favorite\Get(\App::user()->getEntity()->getUi()))->prepare();

        $curl->execute();
        
        $favoriteProductsByUi = [];
        foreach ($favoriteQuery->response->products as $item) {
            $ui = isset($item['uid']) ? (string)$item['uid'] : null;
            if (!$ui) continue;

            $favoriteProductsByUi[$ui] = new \Model\Favorite\Product\Entity($item);
        }

        $products = [];
        if ($favoriteProductsByUi) {
            $productQuery = (new Query\Product\GetByUiList(array_keys($favoriteProductsByUi), \App::user()->getRegion()->getId()))->prepare();

            $curl->execute();

            foreach ($productQuery->response->products as $item) {
                $products[] = new \Model\Product\Entity($item);
            }
        }

        \RepositoryManager::product()->enrichProductsFromScms($products, 'media label category');
        $curl->execute();

        $page = new \View\User\FavoritesPage();
        $page->setParam('products', $products);
        $page->setParam('favoriteProductsByUi', $favoriteProductsByUi);

        return new \Http\Response($page->show());
    }
}