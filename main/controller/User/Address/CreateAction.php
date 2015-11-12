<?php

namespace Controller\User\Address;

use EnterQuery as Query;
use \Model\Media;
use \Model\Session\FavouriteProduct;

class CreateAction extends \Controller\User\PrivateAction {
    use \EnterApplication\CurlTrait;

    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception
     */
    public function execute(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $curl = $this->getCurl();

        $userEntity = \App::user()->getEntity();

        $formData = [
            'description' => null,
            //'regionId' => null,
            'street'      => null,
            'building'    => null,
            'apartment'   => null,
        ];
        if (is_array($request->get('address'))) {
            $formData = array_merge($formData, $request->get('address'));
        }

        $responseData = [
            'errors' => [],
        ];
        try {
            if (empty($formData['street'])) {
                $responseData['errors'][] = ['field' => 'street', 'message' => 'Не указана улица'];
            }

            if ($responseData['errors']) {
                throw new \Exception('Форма заполнена неверно', 400);
            }

            $createQuery = new Query\User\Address\Create();
            $createQuery->userUi = $userEntity->getUi();
            $createQuery->data = $formData;
            $createQuery->prepare();

            $curl->execute();

            if ($error = $createQuery->error) {
                throw $error;
            }
        } catch (\Exception $e) {
            \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['wishlist']);
        }

        $response =  new \Http\RedirectResponse(\App::router()->generate('user.address'));

        return $response;
    }
}