<?php
namespace Controller\Smartengine;

class Action {
    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     */
    public function pushBuy(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $order = $request->request->get('order');

        try {
            $user = \App::user()->getEntity();
            $data = [
                'host'        => $request->getHost(),
                'time'        => date('d_m_Y_H_i_s'),
                'sessionid'   => session_id(),
                'user_id'     => $user ? $user->getId() : null,
                'order'       => $order,
            ];

            \App::database()->exec("INSERT INTO `queue` (`name`, `body`) VALUES ('smartengine.buy', '".addslashes(json_encode($data, JSON_HEX_APOS | JSON_HEX_QUOT))."')");

            return new \Http\JsonResponse(['success' => true]);
        }
        catch (\Exception $e) {
            return new \Http\JsonResponse(['success' => false, 'data' => $e->getMessage()]);
        }
    }

    /**
     * @param \Http\Request $request
     * @param int $productId
     * @return \Http\JsonResponse
     */
    public function pushView(\Http\Request $request, $productId) {
        \App::logger()->debug('Exec ' . __METHOD__);


        if (!$productId)
            return new \Http\JsonResponse(['success' => false, 'data' => 'Не найден товар ' . $request->request->getInt('productId')]);

        try {
            $user = \App::user()->getEntity();
            $data = [
                'host'       => $request->getHost(),
                'time'       => date('d_m_Y_H_i_s'),
                'sessionid'  => session_id(),
                'product_id' => (int)$productId,
                'user_id'    => $user ? $user->getId() : null,
            ];

            \App::database()->exec("INSERT INTO `queue` (`name`, `body`) VALUES ('smartengine.view', '".addslashes(json_encode($data, JSON_HEX_APOS | JSON_HEX_QUOT))."')");

            return new \Http\JsonResponse(['success' => true]);
        }
        catch (\Exception $e) {
            return new \Http\JsonResponse(['success' => false, 'data' => $e->getMessage()]);
        }
    }

    /**
     * @param \Http\Request $request
     * @param int $productId
     * @return \Http\Response
     * @throws \Exception
     */
    public function pullProductAlsoViewed(\Http\Request $request, $productId) {
        \App::logger()->debug('Exec ' . __METHOD__);

        try {
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
            $r = $client->query('otherusersalsoviewed', $params);

            if (isset($r['error'])) {
                if (isset($r['error'])) {
                    throw new \Exception($r['error']['@message'] . ': '. json_encode($r, JSON_UNESCAPED_UNICODE), (int)$r['error']['@code']);
                }
            }
            if (!(bool)$r['recommendeditems']) {
                throw new \Exception();
            }

            $ids = array_key_exists('id', $r['recommendeditems']['item'])
                ? [$r['recommendeditems']['item']['id']]
                : array_map(function($item) { return $item['id']; }, isset($r['recommendeditems']['item']) ? $r['recommendeditems']['item'] : []);
            if (!count($ids)) {
                throw new \Exception();
            }

            $products = \RepositoryManager::product()->getCollectionById($ids);
            foreach ($products as $i => $product) {
                if (!$product->getIsBuyable()) unset($products[$i]);
            }
            if (!count($products)) {
                throw new \Exception();
            }

            return new \Http\Response(\App::templating()->render('product/_slider', [
                'page'          => new \View\Layout(),
                'productList'   => array_values($products),
                'title'         => 'С этим товаром также смотрят',
                'itemsInSlider' => 5,
                'totalProducts' => count($products),
                'url'           => '',
                'gaEvent'       => 'SmartEngine',
            ]));
        } catch (\Exception $e) {
            \App::logger()->error($e, ['smartengine']);

            return new \Http\Response();
        }
    }

    /**
     * @param \Http\Request $request
     * @param int $productId
     * @return \Http\JsonResponse
     * @throws \Exception
     */
    public function pullProductSimilar(\Http\Request $request, $productId) {
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
            $params['itemtype'] = $product->getMainCategory()->getId();
            $r = $client->query('relateditems', $params);

            if (isset($r['error'])) {
                throw new \Exception($r['error']['@message'] . ': '. json_encode($r, JSON_UNESCAPED_UNICODE), (int)$r['error']['@code']);
            }
            if (!(bool)$r['recommendeditems']) {
                throw new \Exception();
            }

            $ids = array_key_exists('id', $r['recommendeditems']['item'])
                ? [$r['recommendeditems']['item']['id']]
                : array_map(function($item) { return $item['id']; }, isset($r['recommendeditems']['item']) ? $r['recommendeditems']['item'] : []);
            if (!count($ids)) {
                throw new \Exception();
            }

            $products = \RepositoryManager::product()->getCollectionById($ids);

            $return = [];
            foreach ($products as $product) {
                if (!$product->getIsBuyable()) continue;

                $return[] = [
                    'id'     => $product->getId(),
                    'name'   => $product->getName(),
                    'image'  => $product->getImageUrl(),
                    'rating' => $product->getRating(),
                    'link'   => $product->getLink(),
                    'price'  => $product->getPrice(),
                ];
            }
            if (!count($return)) {
                throw new \Exception();
            }

            return new \Http\JsonResponse($return);

        } catch (\Exception $e) {
            \App::logger()->error($e, ['smartengine']);

            return new \Http\JsonResponse();
        }
    }
}