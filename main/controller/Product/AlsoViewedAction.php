<?php

namespace Controller\Product;

class AlsoViewedAction {
    /**
     * @param string        $productId
     * @param \Http\Request $request
     * @return \Http\RedirectResponse|\Http\Response
     * @throws \Exception\NotFoundException
     */
    public function execute($productId, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        \App::logger()->debug('Exec ' . __METHOD__);

        try {
            if (\App::config()->crossss['enabled']) {
                (new \Controller\Crossss\ProductAction())->recommended($request, $productId);
            }

            $product = \RepositoryManager::product()->getEntityById($productId);
            if (!$product) {
                throw new \Exception(sprintf('Товар #%s не найден', $productId));
            }

            $client = \App::smartengineClient();
            $user = \App::user()->getEntity();

            $params = [
                'sessionid' => session_id(),
                'itemid'    => $product->getId(),
            ];
            if ($user) {
                $params['userid'] = $user->getId();
            }
            $params['itemtype'] = $product->getMainCategory() ? $product->getMainCategory()->getId() : null;
            $params['requesteditemtype'] = $product->getMainCategory() ? $product->getMainCategory()->getId() : null;

            $r = $client->query('otherusersalsoviewed', $params);

            if (isset($r['error'])) {
                if (isset($r['error'])) {
                    throw new \Exception($r['error']['@message'] . ': '. json_encode($r, JSON_UNESCAPED_UNICODE), (int)$r['error']['@code']);
                }
            }

            $ids = (is_array($r['recommendeditems']) && array_key_exists('id', $r['recommendeditems']['item']))
                ? [$r['recommendeditems']['item']['id']]
                : array_map(function($item) { return $item['id']; }, isset($r['recommendeditems']['item']) ? $r['recommendeditems']['item'] : []);
            if (!count($ids)) {
                throw new \Exception('Рекомендации не получены');
            }

            $products = \RepositoryManager::product()->getCollectionById($ids);
            foreach ($products as $i => $product) {
                if (!$product->getIsBuyable()) unset($products[$i]);
            }

            if (!(bool)$products) {
                throw new \Exception('Нет товаров');
            }
            $additionalData = [];
            foreach ($products as $i => $product) {
                $additionalData[$product->getId()] = \Kissmetrics\Manager::getProductEvent($product, $i+1, 'Also Viewed');
            }

            $categoryClass = empty($data['categoryClass']) ? '' : $data['categoryClass'] . '/';

            $layout = new \Templating\HtmlLayout();
            $layout->setGlobalParam('sender', \Smartengine\Client::NAME);

            return new \Http\JsonResponse([
                'success' => true,
                'content' => \App::closureTemplating()->render('product/__slider', [
                    'title'    => 'С этим товаром также смотрят',
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