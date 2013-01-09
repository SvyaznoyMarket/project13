<?php

namespace Controller\Cart;

class ServiceAction {
    /**
     * @param int           $serviceId
     * @param int           $productId
     * @param int           $quantity
     * @param \Http\Request $request
     * @return \Http\JsonResponse|\Http\RedirectResponse
     * @throws \Exception
     */
    public function add($serviceId, $productId, $quantity = 1, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $cart = \App::user()->getCart();

        try {
            $quantity = (int)$quantity;
            if ($quantity < 1) {
                throw new \Exception('Указано неверное количество услуг');
            }

            $serviceId = (int)$serviceId;
            if (!$serviceId) {
                throw new \Exception('Не получен ид услуги');
            }

            $product = null;
            if ($productId) {
                $product = \RepositoryManager::getProduct()->getEntityById($productId);
                if (!$product) {
                    throw new \Exception(sprintf('Товар #%s для услуги #%s не найден', $productId, $serviceId));
                }
            }

            if ($product) {
                $action = new ProductAction();
                $action->add($product->getId(), $quantity, $request);
            }

            $service = \RepositoryManager::getService()->getEntityById($serviceId, \App::user()->getRegion());
            if (!$service) {
                throw new \Exception(sprintf('Товар #%s не найден', $serviceId));
            }

            $cart->setService($service, $quantity, $productId);

            return $request->isXmlHttpRequest()
                ? new \Http\JsonResponse(array(
                    'success' => true,
                    'data'    => array(
                        'quantity'      => $quantity,
                        'full_quantity' => $cart->getProductsQuantity() + $cart->getServicesQuantity(),
                        'full_price'    => $cart->getTotalPrice(),
                        'link'          => \App::router()->generate('order.create'),
                    ),
                ))
                : new \Http\RedirectResponse($request->headers->get('referer') ?: \App::router()->generate('homepage'));
        } catch (\Exception $e) {
            return $request->isXmlHttpRequest()
                ? new \Http\JsonResponse(array(
                    'success' => false,
                    'data'    => array('error' => 'Не удалось добавить услугу в корзину', 'debug' => $e->getMessage()),
                ))
                : new \Http\RedirectResponse($request->headers->get('referer') ?: \App::router()->generate('homepage'));
        }
    }
}