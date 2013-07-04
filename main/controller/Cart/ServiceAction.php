<?php

namespace Controller\Cart;

class ServiceAction {
    /**
     * @param int           $serviceId
     * @param int           $productId
     * @param \Http\Request $request
     * @return \Http\JsonResponse|\Http\RedirectResponse
     * @throws \Exception
     */
    public function set($serviceId, $productId, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $cart = \App::user()->getCart();

        $serviceId = (int)$serviceId;
        $productId = (int)$productId;
        $quantity = (int)$request->get('quantity', 1);

        try {
            if ($quantity < 0) {
                $quantity = 0;
                \App::logger()->warn(sprintf('Указано неверное количество услуг. Запрос %s', json_encode($request->request->all(), JSON_UNESCAPED_UNICODE)));
            }

            if (!$serviceId) {
                throw new \Exception('Не получен ид услуги');
            }

            $product = null;
            if ($productId) {
                $product = \RepositoryManager::product()->getEntityById($productId);
                if (!$product) {
                    throw new \Exception(sprintf('Товар #%s для услуги #%s не найден', $productId, $serviceId));
                }
            }

            // если в корзине нет товара
            if ($product && !$cart->hasProduct($product->getId())) {
                (new ProductAction())->set($product->getId(), $request);
            }

            $service = \RepositoryManager::service()->getEntityById($serviceId, \App::user()->getRegion());
            if (!$service) {
                throw new \Exception(sprintf('Товар #%s не найден', $serviceId));
            }

            $cart->setService($service, $quantity, $productId);

            $cartService = null;
            if ($product && $cartProduct = $cart->getProductById($product->getId())) {
                $cart->fill(); // костыль
                $cartService = $cartProduct->getServiceById($service->getId());
            } else {
                $cartService = $cart->getServiceById($service->getId());
            }

            $productInfo = [];
            $serviceInfo = [];
            if ($product) {
                $productInfo = [
                    'name'  =>  $product->getName(),
                    'img'   =>  $product->getImageUrl(2),
                    'link'  =>  $product->getLink(),
                    'price' =>  $product->getPrice(),
                ];
            }
            if (\App::config()->kissmentrics['enabled']) {
                $kissInfo = \Kissmetrics\Manager::getCartEvent($product, $service);
                if (isset($kissInfo['product'])) $productInfo = array_merge($productInfo, $kissInfo['product']);
                if (isset($kissInfo['service'])) $serviceInfo = $kissInfo['service'];
            }

            $completeInfo = [
                'success'   => true,
                'cart'      => [
                    'sum'           => $cartService ? $cartService->getSum() : 0,
                    'quantity'      => $quantity,
                    'full_quantity' => $cart->getProductsQuantity() + $cart->getServicesQuantity() + $cart->getWarrantiesQuantity(),
                    'full_price'    => $cart->getSum(),
                    'old_price'     => $cart->getOriginalSum(),
                    'link'          => \App::router()->generate('order.create'),
                ],
            ];
            if ($productInfo) $completeInfo['product'] = $productInfo;
            if ($serviceInfo) $completeInfo['service'] = $serviceInfo;

            return $request->isXmlHttpRequest()
                ? new \Http\JsonResponse($completeInfo)
                : new \Http\RedirectResponse($request->headers->get('referer') ?: \App::router()->generate('homepage'));
        } catch (\Exception $e) {
            return $request->isXmlHttpRequest()
                ? new \Http\JsonResponse([
                    'success' => false,
                    'cart'    => ['error' => 'Не удалось добавить услугу в корзину', 'debug' => $e->getMessage()],
                ])
                : new \Http\RedirectResponse($request->headers->get('referer') ?: \App::router()->generate('homepage'));
        }
    }

    /**
     * @param \Http\Request $request
     * @param $serviceId
     * @param null $productId
     * @return \Http\JsonResponse|\Http\RedirectResponse
     */
    public function delete(\Http\Request $request, $serviceId, $productId = null) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $request->query->set('quantity', 0);

        return $this->set($serviceId, $productId, $request);
    }
}