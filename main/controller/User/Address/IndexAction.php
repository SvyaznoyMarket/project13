<?php


namespace Controller\User\Address;

use EnterApplication\CurlTrait;
use EnterQuery as Query;

class IndexAction extends \Controller\User\PrivateAction {
    use CurlTrait;

    public function execute(\Http\Request $request) {
        $curl = $this->getCurl();

        $userEntity = \App::user()->getEntity();

        // запрос списка адресов пользователя
        $addressListQuery = (new Query\User\Address\Get($userEntity->getUi()))->prepare();

        $curl->execute();

        // проверка на ошибку
        if ($error = $addressListQuery->error) {
            throw $error;
        }

        /** @var \Model\User\Address\Entity[] $addresses */
        $addresses = [];
        /** @var $addressesByRegionId */
        $addressesByRegionId = [];
        foreach ($addressListQuery->response->addresses as $item) {
            if (empty($item['id'])) continue;

            $address = new \Model\User\Address\Entity($item);
            $addresses[] = $address;
            if ($address->regionId) {
                $addressesByRegionId[$address->regionId][] = $address;
            }
        }

        if ($addressesByRegionId) {
            $regionListQuery = (new Query\Region\GetByIdList(array_keys($addressesByRegionId)))->prepare();

            $curl->execute();

            foreach ($regionListQuery->response->regions as $item) {
                if (empty($item['id'])) continue;

                $region = new \Model\Region\Entity($item);

                if (isset($addressesByRegionId[$region->getId()])) {
                    foreach ($addressesByRegionId[$region->getId()] as $address) {
                        /** @var \Model\User\Address\Entity $address */
                        $address->region = $region;
                    }
                }
            }
        }

        $page = new \View\User\Address\IndexPage();
        $page->setParam('addresses', $addresses);

        return new \Http\Response($page->show());
    }
}