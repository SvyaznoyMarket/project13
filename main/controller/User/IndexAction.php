<?php

namespace Controller\User;

use EnterApplication\CurlTrait;
use EnterQuery as Query;

class IndexAction extends PrivateAction {
    use CurlTrait;

    public function execute(\Http\Request $request) {
        $config = \App::config();
        $curl = $this->getCurl();
        $userEntity = \App::user()->getEntity();

        // запрос списка заказов
        $orderQuery = new Query\Order\GetByUserToken();
        $orderQuery->userToken = $userEntity->getToken();
        $orderQuery->offset = 0;
        $orderQuery->limit = 10;
        $orderQuery->prepare();

        // запрос купонов
        $discountQuery = new Query\Coupon\GetByUserToken();
        $discountQuery->userToken = $userEntity->getToken();
        $discountQuery->prepare();

        // запрос серий купонов
        $couponQuery = new Query\Coupon\Series\Get();
        $couponQuery->memberType = $userEntity->isEnterprizeMember() ? '1' : null;
        $couponQuery->prepare();

        // запрос списка адресов пользователя
        $addressQuery = new Query\User\Address\Get();
        $addressQuery->userUi = $userEntity->getUi();
        $addressQuery->prepare();

        // запрос избранного
        $favoriteListQuery = new Query\User\Favorite\Get();
        $favoriteListQuery->userUi = $userEntity->getUi();
        $favoriteListQuery->prepare();

        // запрос подписок и каналов подписок
        $subscribeChannelQuery = new Query\Subscribe\Channel\Get();
        $subscribeChannelQuery->prepare();

        $subscribeQuery = new Query\Subscribe\GetByUserToken();
        $subscribeQuery->userToken = $userEntity->getToken();
        $subscribeQuery->prepare();

        // настройки из cms
        /** @var Query\Config\GetByKeys|null $configQuery */
        $configQuery =
            $config->userCallback['enabled']
            ? (new Query\Config\GetByKeys(['site_call_phrases']))->prepare()
            : null
        ;

        $curl->execute();

        // номера заказов
        $orderNumberErps = [];

        // заказы
        /** @var \Model\Order\Entity[] $orders */
        $orders = [];
        foreach ($orderQuery->response->orders as $item) {
            if (empty($item['id'])) continue;

            $order = new \Model\Order\Entity($item);

            $orderNumberErps[] = $order->numberErp;
            $orders[] = $order;
        }

        // запрос методов оплат для заказов
        /** @var Query\PaymentMethod\GetByOrderNumberErp[] $paymentMethodQueries */
        $paymentMethodQueries = [];
        foreach (array_chunk($orderNumberErps, 10) as $numbersInChunk) {
            $paymentMethodQuery = new Query\PaymentMethod\GetByOrderNumberErp();
            $paymentMethodQuery->regionId = \App::user()->getRegionId();
            $paymentMethodQuery->numberErps = $numbersInChunk;
            $paymentMethodQuery->noDiscount = true;
            $paymentMethodQuery->prepare();
            $paymentMethodQueries[] = $paymentMethodQuery;
        }

        $curl->execute();

        /** @var \Model\PaymentMethod\PaymentEntity[] $paymentEntitiesByNumberErp */
        $paymentEntitiesByNumberErp = [];
        $onlinePaymentAvailableByNumberErp = [];
        foreach ($paymentMethodQueries as $paymentMethodQuery) {
            foreach ($paymentMethodQuery->response->dataByErp as $numberErp => $item) {
                $paymentEntity = new \Model\PaymentMethod\PaymentEntity($item);
                $paymentEntitiesByNumberErp[$numberErp] = $paymentEntity;
                foreach ($paymentEntity->methods as $paymentMethod) {
                    if ($paymentMethod->isOnline) {
                        $onlinePaymentAvailableByNumberErp[$numberErp] = true;
                        break;
                    }
                }
            }
        }

        // купоны, сгруппированные по сериям
        $discountsGroupedByCoupon = [];
        foreach ($discountQuery->response->coupons as $item) {
            $discount = new \Model\EnterprizeCoupon\DiscountCoupon\Entity($item);
            $discountsGroupedByCoupon[$discount->getSeries()][] = $discount;
        }

        // купоны
        /** @var \Model\EnterprizeCoupon\Entity[] $coupons */
        $coupons = [];
        foreach ($couponQuery->response->couponSeries as $item) {
            $token = isset($item['uid']) ? (string)$item['uid'] : null;
            if (!$token || !isset($discountsGroupedByCoupon[$token])) {
                continue;
            }

            foreach ($discountsGroupedByCoupon[$token] as $discount) {
                $coupon = new \Model\EnterprizeCoupon\Entity($item);
                $coupon->setDiscount($discount);
                $coupons[] = $coupon;
            }
        }
        // сортировка - самые горячие фишки
        uasort($coupons, function(\Model\EnterprizeCoupon\Entity $a, \Model\EnterprizeCoupon\Entity $b) {
            return ($a->getEndDate() ? $a->getEndDate()->getTimestamp() : 4132281600) - ($b->getEndDate() ? $b->getEndDate()->getTimestamp() : 4132281600);
        });
        // срез купонов
        $coupons = array_slice($coupons, 0, 2);

        // адреса
        /** @var \Model\User\Address\Entity[] $addresses */
        $addresses = [];
        /** @var $addressesByRegionId */
        $addressesByRegionId = [];
        foreach ($addressQuery->response->addresses as $item) {
            if (empty($item['id'])) continue;

            $address = new \Model\User\Address\Entity($item);
            $addresses[] = $address;
            if ($address->regionId) {
                $addressesByRegionId[$address->regionId][] = $address;
            }
        }

        if ($addressesByRegionId) {
            $regionQuery = new Query\Region\GetByIdList();
            $regionQuery->ids = array_keys($addressesByRegionId);
            $regionQuery->prepare();

            $curl->execute();

            foreach ($regionQuery->response->regions as $item) {
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

        // избранное
        $productUis = [];
        $favoriteProductsByUi = [];
        foreach ($favoriteListQuery->response->products as $item) {
            $ui = isset($item['uid']) ? (string)$item['uid'] : null;
            if (!$ui) continue;

            $favoriteProductsByUi[$ui] = new \Model\Favorite\Product\Entity($item);
            $productUis[] = $ui;
        }
        $productUis = array_unique($productUis);

        /** @var \Model\Product\Entity[] $productsByUi */
        $productsByUi = [];
        if ($productUis) {
            foreach ($productUis as $productUi) {
                $productsByUi[$productUi] = new \Model\Product\Entity(['ui' => $productUi]);
            }
            \RepositoryManager::product()->prepareProductQueries($productsByUi, 'media label');
        }

        \App::coreClientV2()->execute();

        // подписки и каналы подписок
        $userEmail = $userEntity->getEmail() ?: null;
        /** @var \Model\User\SubscriptionEntity[] $subscriptions */
        $subscriptions = [];
        /** @var \Model\Subscribe\Channel\Entity[] $channelsById */
        $channelsById = [];
        $subscriptionsGroupedByChannel = [];
        foreach ($subscribeChannelQuery->response->channels as $item) {
            if (empty($item['id'])) continue;

            $channel = new \Model\Subscribe\Channel\Entity($item);
            $channelsById[$channel->id] = $channel;
        }
        foreach ($subscribeQuery->response->subscribes as $item) {
            $subscriptions[] = new \Model\User\SubscriptionEntity($item);
        }
        foreach ($subscriptions as $subscription) {
            if (('email' === $subscription->type) && $userEmail && ($userEmail !== $subscription->email)) {
                // пропустить подписки, у которых email не совпадает с email-ом пользователя
                continue;
            }

            /** @var \Model\Subscribe\Channel\Entity|null $channel */
            $channel = ($subscription->channelId && isset($channelsById[$subscription->channelId])) ? $channelsById[$subscription->channelId] : null;
            if ($channel) {
                $subscription->channel = $channel;
                $subscriptionsGroupedByChannel[$channel->id][] = $subscription;
            }
        }

        // SITE-6622
        $callbackPhrases = [];
        if ($configQuery) {
            foreach ($configQuery->response->keys as $item) {
                if ('site_call_phrases' === $item['key']) {
                    $value = json_decode($item['value'], true);
                    $callbackPhrases = !empty($value['private']) ? $value['private'] : [];
                }
            }
        }

        $page = new \View\User\IndexPage();
        $page->setParam('orders', $orders);
        $page->setParam('onlinePaymentAvailableByNumberErp', $onlinePaymentAvailableByNumberErp);
        $page->setParam('paymentEntitiesByNumberErp', $paymentEntitiesByNumberErp);
        $page->setParam('coupons', $coupons);
        $page->setParam('addresses', $addresses);
        $page->setParam('favoriteProductsByUi', $favoriteProductsByUi);
        $page->setParam('productsByUi', $productsByUi);
        $page->setParam('subscriptionsGroupedByChannel', $subscriptionsGroupedByChannel);
        $page->setParam('channelsById', $channelsById);
        $page->setGlobalParam('callbackPhrases', $callbackPhrases);

        return new \Http\Response($page->show());
    }
}