<?php


namespace Controller\User\Notification;

use EnterApplication\CurlTrait;
use EnterQuery as Query;

class AddProductAction extends \Controller\User\PrivateAction {
    use CurlTrait;

    public function execute(\Http\Request $request) {
        $curl = $this->getCurl();

        $region = \App::user()->getRegion();

        $productId = $request->get('productId');
        $channelId = $request->get('channelId');

        $createQuery = new Query\Subscribe\CreateByProduct();
        $createQuery->userToken = \App::user()->getEntity()->getToken();
        $createQuery->channelId = $channelId;
        $createQuery->productId = $productId;
        $createQuery->regionId = $region->getId();
        $createQuery->prepare();

        $curl->execute();

        if ($error = $createQuery->error) {
            throw $error;
        }

        die(var_dump('ok'));
    }
}