<?php
namespace Controller\Crossss;

class OrderAction {
    /**
     * @param \Model\Order\Entity   $order
     * @param \Model\Order\Entity[] $productsById
     * @return \Http\JsonResponse
     */
    public function create(\Model\Order\Entity $order = null, array $productsById) {
        \App::logger()->debug('Exec ' . __METHOD__, ['action', 'crossss']);

        try {
            foreach ($order->getProduct() as $orderProduct) {
                /** @var $product \Model\Product\Entity|null */
                $product = isset($productsById[$orderProduct->getId()]) ? $productsById[$orderProduct->getId()] : null;
                if (!$product) continue;

                $data = [
                    'apikey'          => \App::config()->crossss['apiKey'],
                    'userid'          => \App::user()->getEntity() ? \App::user()->getEntity()->getId() : null,
                    'orderid'         => $order->getNumber(),
                    'sessionid'       => session_id(),
                    'itemid'          => $product->getId(),
                    'itemdescription' => $product->getName(),
                    'itemurl'         => \App::router()->generate('product', ['productPath' => $product->getPath()], true),
                    'actiontime'      => time(),
                    'itemtype'        => $product->getMainCategory() ? $product->getMainCategory()->getId() : null,
                    'unitprice'       => $product->getPrice(),
                    'quantity'        => $orderProduct->getQuantity(),
                ];

                \App::database()->exec("INSERT INTO `queue` (`name`, `body`) VALUES ('crossss.push', '".addslashes(json_encode($data, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE))."')");
            }
        } catch (\Exception $e) {
            \App::logger()->error($e, ['crossss']);

            //return new \Http\JsonResponse(['success' => false, 'error' => \App::config()->debug ? $e->getMessage() : 'Ошибка']);
        }

        //return new \Http\JsonResponse(['success' => true]);
    }
}