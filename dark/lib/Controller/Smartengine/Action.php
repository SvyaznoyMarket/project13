<?php
namespace Controller\Smartengine;

class Action {
    /** @var  \PDO */
    private $dbh = null;

    public function pushBuy(\Http\Request $request) {
        // TODO: доделать
        \App::logger()->debug('Exec ' . __METHOD__);

        $order = $request->request->get('order');

        try {
            $user = \App::user()->getEntity();
            $data = array(
                'host'        => $request->getHost(),
                'time'        => date('d_m_Y_H_i_s'),
                'sessionid'   => session_id(),
                'user_id'     => $user ? $user->getId() : null,
                'order'       => $order,
            );

            $this->getDbh()->exec("INSERT INTO `queue` (`name`, `body`) VALUES ('smartengine.buy', '".addslashes(json_encode($data, JSON_HEX_APOS | JSON_HEX_QUOT))."')");

            return new \Http\JsonResponse(array('success' => true, ));
        }
        catch (\Exception $e) {
            return new \Http\JsonResponse(array('success' => false, 'data' => $e->getMessage()));
        }
    }

    public function pushView(\Http\Request $request, $productId) {
        \App::logger()->debug('Exec ' . __METHOD__);


        if (!$productId)
            return new \Http\JsonResponse(array('success' => false, 'data' => 'Не найден товар ' . $request->request->getInt('productId')));

        try {
            $user = \App::user()->getEntity();
            $data = array(
                'host'       => $request->getHost(),
                'time'       => date('d_m_Y_H_i_s'),
                'sessionid'  => session_id(),
                'product_id' => (int)$productId,
                'user_id'    => $user ? $user->getId() : null,
            );

            $this->getDbh()->exec("INSERT INTO `queue` (`name`, `body`) VALUES ('smartengine.view', '".addslashes(json_encode($data, JSON_HEX_APOS | JSON_HEX_QUOT))."')");

            return new \Http\JsonResponse(array('success' => true, ));
        }
        catch (\Exception $e) {
            return new \Http\JsonResponse(array('success' => false, 'data' => $e->getMessage()));
        }
    }

    public function pullProductAlsoViewed(\Http\Request $request, $productId) {
        try {
            $product = \RepositoryManager::getProduct()->getEntityById($productId);
            if (!$product) {
                return new \Http\Response('');
            }

            $client = \App::smartengineClient();
            $user = \App::user()->getEntity();

            $params = array(
                'sessionid'         => session_id(),
                'itemid'            => $product->getId(),
            );
            if ($user) {
                $params['userid'] = $user->getId();
            }
            $params['itemtype'] = $product->getMainCategory()->getId();
            $r = $client->query('otherusersalsoviewed', $params);

            if (isset($r['error'])) {
                //$this->getLogger()->err('Smartengine: error #'.$r['error']['@code'].' '.$r['error']['@message']);

                return new \Http\Response();
            }

            $ids =
                array_key_exists('id', $r['recommendeditems']['item'])
                    ? array($r['recommendeditems']['item']['id'])
                    : array_map(function($item) { return $item['id']; }, isset($r['recommendeditems']['item']) ? $r['recommendeditems']['item'] : array());
            if (!count($ids)) {
                return new \Http\Response('');
            }

            $products = \RepositoryManager::getProduct()->getCollectionById($ids);
            foreach ($products as $i => $product) {
                if (!$product->getIsBuyable()) unset($products[$i]);
            }
            if (!count($products)) {
                return new \Http\Response('');
            }

            return new \Http\Response(\App::templating()->render('product/_slider', array(
                'page'   => new \View\Layout(),
                'productList'  => $products,
                'title'    => 'С этим товаром также смотрят',
                'itemsInSlider' => 5,
                'totalProducts' => count($products),
                'url' => '',
                'gaEvent'    => 'SmartEngine',
            )));
        } catch(Exception $e) {
            return new \Http\Response('');
        }
    }


    private function getDbh() {
        if (!$this->dbh) {
            $this->dbh = new \PDO(sprintf('mysql:dbname=%s;host=%s', \App::config()->database['name'], \App::config()->database['host']), \App::config()->database['user'], \App::config()->database['password'], array(
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            ));
        }

        return $this->dbh;
    }
}