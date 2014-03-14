<?php

namespace Controller\User;

class IndexAction {
    public function __construct() {
        if (!\App::user()->getToken()) {
            throw new \Exception\AccessDeniedException();
        }
    }

    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();

        // подготовка 1-го пакета запросов

        // запрашиваем пользователя, если он авторизован
        if ($user->getToken()) {
            \RepositoryManager::user()->prepareEntityByToken($user->getToken(), function($data) {
                if ((bool)$data) {
                    \App::user()->setEntity(new \Model\User\Entity($data));
                }
            }, function (\Exception $e) use (&$user) {
                \App::exception()->remove($e);
                throw new \Exception\AccessDeniedException(sprintf('Время действия токена %s истекло', $user->getToken()));
            });
        }

        // запрашиваем текущий регион, если есть кука региона
        $regionConfig = [];
        if ($user->getRegionId()) {
            \App::dataStoreClient()->addQuery("region/{$user->getRegionId()}.json", [], function($data) use (&$regionConfig) {
                if((bool)$data) {
                    $regionConfig = $data;
                }
            });

            \RepositoryManager::region()->prepareEntityById($user->getRegionId(), function($data) {
                $data = reset($data);
                if ((bool)$data) {
                    \App::user()->setRegion(new \Model\Region\Entity($data));
                }
            });
        }

        // запрашиваем список регионов для выбора
        $regionsToSelect = [];
        \RepositoryManager::region()->prepareShownInMenuCollection(function($data) use (&$regionsToSelect) {
            foreach ($data as $item) {
                $regionsToSelect[] = new \Model\Region\Entity($item);
            }
        });

        // выполнение 1-го пакета запросов
        $client->execute();

        $regionEntity = $user->getRegion();
        if ($regionEntity instanceof \Model\Region\Entity) {
            if (array_key_exists('reserve_as_buy', $regionConfig)) {
                $regionEntity->setForceDefaultBuy(false == $regionConfig['reserve_as_buy']);
            }
            $user->setRegion($regionEntity);
        }

        $region = $user->getRegion();

        // подготовка 2-го пакета запросов

        // TODO: запрашиваем меню

        // запрашиваем количество заказов пользователя
        /** @var $product \Model\Product\Entity */
        $orderCount = 0;
        \RepositoryManager::order()->prepareCollectionByUserToken($user->getToken(), function($data) use(&$orderCount) {
            $orderCount = (bool)$data ? count($data) : 0;
        });

        // выполнение 2-го пакета запросов
        $client->execute();

        $page = new \View\User\IndexPage();
        $page->setParam('regionsToSelect', $regionsToSelect);
        $page->setParam('orderCount', $orderCount);

        if ($userEntity = $user->getEntity()) {
            if($userEntity->getIsSubscribedViaSms() && !(bool)($userEntity->getMobilePhone())) {
                $page->setParam('smsTmpCheck', true);
                $page->setParam('error', \Controller\User\SubscribeAction::EMPTY_PHONE_ERROR);
            }

            if($userEntity->getIsSubscribed() && !(bool)($userEntity->getEmail())) {
                $page->setParam('emailTmpCheck', true);
                $page->setParam('error', \Controller\User\SubscribeAction::EMPTY_EMAIL_ERROR);
            }
        }

        $form = new \View\User\ConsultationForm();
        $page->setParam('form', $form);

        return new \Http\Response($page->show());
    }
}