<?php

namespace Controller\Cart;

class BlackcardAction {
    /**
     * @param \Http\Request $request
     * @throws \Exception|null
     * @throws \Exception\NotFoundException
     * @return \Http\Response
     */
    public function apply(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        $cart = \App::user()->getCart();

        $number = trim((string)$request->get('number'));

        $responseData = [];
        try {
            if (!$number) {
                throw new \Exception\ActionException('Не передан номер карты');
            }

            $cart->clearBlackcards();

            $blackcard = new \Model\Cart\Blackcard\Entity();
            $blackcard->setNumber($number);

            $cart->setBlackcard($blackcard);

            $result = (new \Controller\Order\DeliveryAction())->getResponseData(false);

            foreach ($cart->getBlackcards() as $blackcard) {
                if ($number === $blackcard->getNumber()) {
                    if ($blackcard->getError() instanceof \Exception) {
                        throw $blackcard->getError();
                    }
                }
            }

            $responseData['success'] = true;
        } catch (\Exception $e) {
            \App::exception()->remove($e);

            $message = \Model\Cart\Blackcard\Entity::getErrorMessage($e->getCode()) ?: 'Не удалось активировать карту';

            if (in_array($e->getCode(), [1000, 1005, 1006, 1007, 1008, 1009, 1010, 1011, 1012, 1013])) {
                $cart->clearBlackcards();
            }

            $responseData = [
                'success' => false,
                'error'   => ['code' => $e->getCode(), 'message' => $message],
            ];
        }

        return new \Http\JsonResponse($responseData);
    }

    /**
     * @param \Http\Request $request
     * @throws \Exception\NotFoundException
     * @return \Http\JsonResponse
     */
    public function delete(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $responseData = [];
        try {
            \App::user()->getCart()->clearBlackcards();

            $responseData['success'] = true;
        } catch (\Exception\ActionException $e) {
            \App::exception()->remove($e);

            $responseData = [
                'success' => false,
                'error'   => ['code' => $e->getCode(), 'message' => 'Не удалось удалить карту'],
            ];
        }

        return $request->isXmlHttpRequest() ? new \Http\JsonResponse($responseData) : new \Http\RedirectResponse($request->headers->get('referer') ?: \App::router()->generate('cart'));
    }
}