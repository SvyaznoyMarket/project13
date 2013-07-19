<?php

namespace Controller\Product;

class SimilarAction {
    /**
     * @param string        $productId
     * @param \Http\Request $request
     * @return \Http\RedirectResponse|\Http\Response
     * @throws \Exception\NotFoundException
     */
    public function execute($productId, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        try {
            $product = \RepositoryManager::product()->getEntityById($productId);
            if (!$product) {
                throw new \Exception(sprintf('Товар #%s не найден', $productId));
            }

            $client = \App::smartengineClient();
            $user = \App::user()->getEntity();

            $params = [
                'sessionid'       => session_id(),
                'itemid'          => $product->getId(),
                'assoctype'       => 'IS_SIMILAR',
                'numberOfResults' => 15,
            ];
            if ($user) {
                $params['userid'] = $user->getId();
            }
            $params['itemtype'] = $product->getMainCategory() ? $product->getMainCategory()->getId() : null;
            $r = $client->query('relateditems', $params);

            if (isset($r['error'])) {
                throw new \Exception($r['error']['@message'] . ': '. json_encode($r, JSON_UNESCAPED_UNICODE), (int)$r['error']['@code']);
            }

            $ids = array_key_exists('id', $r['recommendeditems']['item'])
                ? [$r['recommendeditems']['item']['id']]
                : array_map(function($item) { return $item['id']; }, isset($r['recommendeditems']['item']) ? $r['recommendeditems']['item'] : []);
            if (!(bool)$ids) {
                throw new \Exception('Рекомендации не получены');
            }

            $products = \RepositoryManager::product()->getCollectionById($ids);

            foreach ($products as $i => $product) {
                if (!$product->getIsBuyable()) {
                    unset($products[$i]);
                }
            }
            if (!(bool)$products) {
                throw new \Exception('Нет товаров');
            }

            return new \Http\JsonResponse([
                'success' => true,
                'content' => \App::closureTemplating()->render('product/__slider', [
                    'title'    => 'Похожие товары',
                    'products' => $products,
                ]),
            ]);
        } catch (\Exception $e) {
            \App::logger()->error($e, ['smartengine']);

            return new \Http\JsonResponse([
                'success' => false,
                'error'   => ['code' => $e->getCode(), 'message' => $e->getMessage()],
            ]);
        }
    }
}