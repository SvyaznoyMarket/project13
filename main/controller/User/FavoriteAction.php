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

        $favoriteQuery = (new Query\User\Favorite\Get(\App::user()->getEntity()->getUi()))->prepare();

        $curl->execute();
        
        $favoriteProductsByUi = [];
        foreach ($favoriteQuery->response->products as $item) {
            $ui = isset($item['uid']) ? (string)$item['uid'] : null;
            if (!$ui) continue;

            $favoriteProductsByUi[$ui] = new \Model\Favorite\Product\Entity($item);
        }

        /** @var \Model\Product\Entity[] $products */
        $products = [];
        if ($favoriteProductsByUi) {
            $products = array_map(function($productUi) { return new \Model\Product\Entity(['ui' => $productUi]); }, array_keys($favoriteProductsByUi));
            \RepositoryManager::product()->prepareProductQueries($products, 'media label category');
            \App::coreClientV2()->execute();
        }

        $page = new \View\User\FavoritesPage();
        $page->setParam('products', $products);
        $page->setParam('favoriteProductsByUi', $favoriteProductsByUi);

        return new \Http\Response($page->show());
    }
}