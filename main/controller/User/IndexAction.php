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
            }, function (\Exception $e) {
                \App::exception()->remove($e);
                $token = \App::user()->removeToken();
                throw new \Exception\AccessDeniedException(sprintf('Время действия токена %s истекло', $token));
            });
        }

        // запрашиваем текущий регион, если есть кука региона
        if ($user->getRegionId()) {
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

        if($user->getEntity()->getIsSubscribedViaSms() && !(bool)($user->getEntity()->getMobilePhone())) {
            $page->setParam('smsTmpCheck', true);
            $page->setParam('error', \Controller\User\SubscribeAction::EMPTY_PHONE_ERROR);
        }

        $form = new \View\User\ConsultationForm();
        $page->setParam('form', $form);

        return new \Http\Response($page->show());
    }
}