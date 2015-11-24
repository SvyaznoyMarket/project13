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
            'kladrId'     => null,
            'zipCode'     => null,
            'regionId'    => null,
            'streetType'  => null,
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
                $responseData['errors']['street'] = ['field' => 'street', 'message' => 'Не указана улица'];
            }
            if (empty($formData['building'])) {
                $responseData['errors']['building'] = ['field' => 'building', 'message' => 'Не указан дом'];
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
                if ($error instanceof \EnterQuery\Exception) {
                    $detail = $error->getDetail();
                    if (isset($detail[0]['propertyPath']) && ('kladrId' === $detail[0]['propertyPath'])) {
                        $responseData['errors']['street'] = ['field' => 'street', 'message' => 'Выберите адрес из подсказок'];
                    }
                }

                throw $error;
            }
        } catch (\Exception $e) {
            \App::session()->flash(['errors' => $responseData['errors'], 'form' => $formData]);

            \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['user.address']);
        }

        $response =  new \Http\RedirectResponse(\App::router()->generate('user.address'));

        return $response;
    }
}