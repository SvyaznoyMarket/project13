<?php

namespace Controller\Wishlist;

use EnterQuery as Query;

class CreateAction {
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
            'title' => null,
        ];
        if (is_array($request->get('wishlist'))) {
            $formData = array_merge($formData, $request->get('wishlist'));
        }

        $responseData = [
            'errors' => [],
        ];
        try {
            if (empty($formData['title'])) {
                $responseData['errors'][] = ['field' => 'title', 'message' => 'Не указано название'];
            }

            if ($responseData['errors']) {
                throw new \Exception('Форма заполнена неверно', 400);
            }

            $createQuery = new Query\User\Wishlist\Create();
            $createQuery->userUi = $userEntity->getUi();
            $createQuery->data['title'] = $formData['title'];
            $createQuery->prepare();

            $curl->execute();

            if ($error = $createQuery->error) {
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