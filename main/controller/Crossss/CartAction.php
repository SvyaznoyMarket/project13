<?php
namespace Controller\Crossss;

class CartAction {
    /**
     * @param \Model\Product\BasicEntity $product
     * @return \Http\JsonResponse
     * @throws \Exception
     */
    public function product(\Model\Product\BasicEntity $product) {
        \App::logger()->debug('Exec ' . __METHOD__, ['action', 'crossss']);

        $database = \App::database();

        try {
            if (!$database) {
                throw new \Exception('Нет доступа к бд');
            }

            $cartProduct = \App::user()->getCart()->getProductById($product->getId());
            if (!$cartProduct) {
                throw new \Exception(sprintf('Товар #%s не найден в корзине', $product->getId()));
            }

            $data = [
                'apikey'          => \App::config()->crossss['apiKey'],
                'userid'          => \App::user()->getEntity() ? \App::user()->getEntity()->getId() : null,
                'sessionid'       => session_id(),
                'itemid'          => $product->getId(),
                'itemdescription' => $product->getName(),
                'itemurl'         => \App::router()->generate('product', ['productPath' => $product->getPath()], true),
                'actiontime'      => time(),
                'itemtype'        => $product->getMainCategory() ? $product->getMainCategory()->getId() : null,
                'unitprice'       => $product->getPrice(),
                'quantity'        => $cartProduct->getQuantity(),
            ];

            $database->exec("INSERT INTO `queue` (`name`, `body`) VALUES ('crossss.push', '".addslashes(json_encode($data, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE))."')");
        } catch (\Exception $e) {
            \App::logger()->error($e, ['crossss']);

            //return new \Http\JsonResponse(['success' => false, 'error' => \App::config()->debug ? $e->getMessage() : 'Ошибка']);
        }

        //return new \Http\JsonResponse(['success' => true]);
    }
}