<?php

namespace Controller\Product;

class RatingAction {
    public function createTotal($productId, $rating, \Http\Request $request) {
        if (!$productId) {
            return;
        }

        $client = \App::coreClientV2();

        try {
            //если пользователь авторизован
            if ($user = \App::user()->getEntity()) {

                //отправляем запос на голосование в ядро
                $result = $client->query('user/create-product-rating', array('token' => $user->getToken(), 'client_id' => $user->getId()), array(
                    'product_id' => $productId,
                    'ip' => $user->getIpAddress(),
                    'value' => $rating,
                ));
            }
            else
            {
                //если пользователь не авторизован - отправим запрос в ядро - вероятоно,
                //пользователь с таким ip голосовал и ядро запретит голосование
                $result = $client->query('user/create-product-rating', [], array(
                    'product_id' => $productId,
                    'ip' => \App::request()->getClientIp(),
                    'value' => $rating,
                ));
            }
        } catch (\Curl\Exception $e) {
            \App::exception()->remove($e);

            return new \Http\JsonResponse(array(
                'success' => false,
                'error'   => array('code' => 'invalid', 'message' => 'Не удалось установить оценку' . (\App::config()->debug ? (': ' . $e) : '')),
            ));
        }

        //отправляем ответ - голосование прошло успешно
        if ($request->isXmlHttpRequest()) {
            return  new \Http\JsonResponse(array(
                'success' => true,
                'data' => array(
                    'rating' => $result['rating'], //текущий рейтинг
                    'rating_quantity' =>$result['rating_count'], //количество проголосовавших
                ),
            ));
        }

        return new \Http\RedirectResponse($request->headers->get('referer') ?: \App::router()->generate('homepage'));
    }
}