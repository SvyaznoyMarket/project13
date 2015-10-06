<?php

namespace Controller\Wishlist;

use EnterQuery as Query;

class AddProductAction {
    use \EnterApplication\CurlTrait;

    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception
     */
    public function execute(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $curl = $this->getCurl();

        if (!$userEntity = \App::user()->getEntity()) {
            throw new \Exception\AccessDeniedException('Пользователь не авторизован');
        }

        $formData = [
            'id'         => null,
            'productUis' => null,
        ];
        if (is_array($request->get('wishlist'))) {
            $formData = array_merge($formData, $request->get('wishlist'));
        }

        $responseData = [
            'errors' => [],
        ];
        try {
            if (empty($formData['id'])) {
                $responseData['errors'][] = ['field' => 'id', 'message' => 'Не выбран список'];
            }
            $formData['productUis'] = is_string($formData['productUis']) ? explode(',', $formData['productUis']) : [];
            if (empty($formData['productUis'])) {
                $responseData['errors'][] = ['field' => 'productUis', 'message' => 'Не указаны товары'];
            }

            if ($responseData['errors']) {
                throw new \Exception('Форма заполнена неверно', 400);
            }

            $addQuery = new Query\User\Wishlist\AddProductList();
            $addQuery->userUi = $userEntity->getUi();
            $addQuery->data['id'] = $formData['id'];
            $addQuery->data['products'] = array_map(function($ui) {
                return [
                    'productUi' => $ui,
                ];
            }, $formData['productUis']);
            $addQuery->prepare();

            $curl->execute();

            if ($error = $addQuery->error) {
                throw $error;
            }
        } catch (\Exception $e) {
            \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['wishlist']);
        }

        if ($request->isXmlHttpRequest()) {
            // TODO
            $response =  new \Http\JsonResponse($responseData);
        } else {
            $response =  new \Http\RedirectResponse(\App::router()->generate('user.favorites'));
        }

        return $response;
    }
}