<?php

namespace Controller\Cart;

class InfoAction {
    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception\NotFoundException
     */
    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        $cart = \App::user()->getCart();

        $data = [
            'product'     => [],
            'service'     => [],
            'certificate' => [],
            'sum'         => $cart->getSum(),
            'oldSum'      => $cart->getOriginalSum(),
        ];

        foreach ($cart->getProducts() as $cartProduct) {
            $productData = [
                'id'       => $cartProduct->getId(),
                'sum'      => $cartProduct->getSum(),
                'quantity' => $cartProduct->getQuantity(),
                'service'  => [],
                'warranty' => [],
            ];
            foreach ($cartProduct->getService() as $cartService) {
                $productData['service'][$cartService->getId()] = [
                    'id'       => $cartService->getId(),
                    'sum'      => $cartService->getSum(),
                    'quantity' => $cartService->getQuantity(),
                ];
            }
            foreach ($cartProduct->getWarranty() as $cartWarranty) {
                $productData['warranty'][$cartWarranty->getId()] = [
                    'id'       => $cartWarranty->getId(),
                    'sum'      => $cartWarranty->getSum(),
                    'quantity' => $cartWarranty->getQuantity(),
                ];
            }

            $data['product'][$cartProduct->getId()] = $productData;
        }

        foreach ($cart->getServices() as $cartService) {
            $data['service'][$cartService->getId()] = [
                'id'       => $cartService->getId(),
                'sum'      => $cartService->getSum(),
                'quantity' => $cartService->getQuantity(),
            ];
        }

        foreach ($cart->getCertificates() as $certificate) {
            $data['certificate'][$certificate->getNumber()] = [
                'number' => $certificate->getNumber(),
            ];
        }

        return new \Http\JsonResponse($data);
    }
}