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

    private function getDbh() {
        if (!$this->dbh) {
            $this->dbh = new \PDO(sprintf('mysql:dbname=%s;host=%s', \App::config()->database['name'], \App::config()->database['host']), \App::config()->database['user'], \App::config()->database['password'], array(
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            ));
        }

        return $this->dbh;
    }
}