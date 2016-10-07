<?php

namespace Controller\User\Address;

use EnterQuery as Query;
use \Model\Media;
use \Model\Session\FavouriteProduct;

class DeleteAction extends \Controller\User\PrivateAction {
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

        $addressId = $request->get('addressId');

        try {
            $deleteQuery = new Query\User\Address\Delete();
            $deleteQuery->userUi = $userEntity->getUi();
            $deleteQuery->id = $addressId;
            $deleteQuery->prepare();

            $curl->execute();

            if ($error = $deleteQuery->error) {
                throw $error;
            }
        } catch (\Exception $e) {
            \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['user.address']);
        }

        $response =  new \Http\RedirectResponse(\App::router()->generateUrl('user.address'));

        return $response;
    }
}