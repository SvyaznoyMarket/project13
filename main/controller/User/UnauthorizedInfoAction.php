<?php

namespace Controller\User;

use EnterApplication\CurlTrait;
use EnterQuery as Query;

class UnauthorizedInfoAction {
    use CurlTrait;

    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception
     */
    public function execute(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http');
        }

        $responseData = [
            'orderCount' => 0,
            'favoriteCount' => 0,
            'couponCount' => 0,
            'subscribeCount' => 0,
            'addressCount' => 0,
            'messageCount' => 0,
        ];

        $curl = $this->getCurl();
        $userEntity = \App::user()->getEntity();

        if (!$userEntity) {
            throw new \Exception('Пользователь неавторизован', 400);
        }

        // запрос заказов
        $orderQuery = new Query\Order\GetByUserToken();
        $orderQuery->userToken = $userEntity->getToken();
        $orderQuery->offset = 0;
        $orderQuery->limit = 0;
        $orderQuery->prepare();

        // запрос избранного
        $favoriteQuery = new Query\User\Favorite\Get();
        $favoriteQuery->userUi = $userEntity->getUi();
        $favoriteQuery->prepare();

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

        // запрос подписок и каналов подписок
        $subscribeChannelQuery = new Query\Subscribe\Channel\Get();
        $subscribeChannelQuery->prepare();

        $subscribeQuery = new Query\Subscribe\GetByUserToken();
        $subscribeQuery->userToken = $userEntity->getToken();
        $subscribeQuery->prepare();

        $curl->execute();

        // количество текущих заказов
        if (!$orderQuery->error) {
            $responseData['orderCount'] = $orderQuery->response->currentCount;
        }

        // количество избранных товаров
        if (!$favoriteQuery->error) {
            $responseData['favoriteCount'] = count($favoriteQuery->response->products);
        }

        // количество купонов
        if (!$discountQuery->error && !$couponQuery->error) {
            $responseData['couponCount'] = call_user_func(function() use (&$discountQuery, &$couponQuery) {
                $return = 0;

                // купоны, сгруппированные по сериям
                $discountsGroupedByCoupon = [];
                foreach ($discountQuery->response->coupons as $item) {
                    $discount = new \Model\EnterprizeCoupon\DiscountCoupon\Entity($item);
                    $discountsGroupedByCoupon[$discount->getSeries()][] = $discount;
                }

                // подсчет купонов
                foreach ($couponQuery->response->couponSeries as $item) {
                    $token = isset($item['uid']) ? (string)$item['uid'] : null;
                    if (!$token || !isset($discountsGroupedByCoupon[$token])) {
                        continue;
                    }

                    $return++;
                }

                return $return;
            });
        }

        // количество адресов
        if (!$addressQuery->error) {
            $responseData['addressCount'] = count($addressQuery->response->addresses);
        }

        // количество подписок
        if (!$subscribeQuery->error && !$subscribeChannelQuery->error) {
            $responseData['subscribeCount'] = call_user_func(function() use (&$userEntity, &$subscribeQuery, &$subscribeChannelQuery) {
                $return = 0;

                // подписки и каналы подписок
                $userEmail = $userEntity->getEmail() ?: null;
                /** @var \Model\User\SubscriptionEntity[] $subscriptions */
                $subscriptions = [];
                /** @var \Model\Subscribe\Channel\Entity[] $channelsById */
                $channelsById = [];
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
                        $return++;
                    }
                }

                return $return;
            });
        }

        return new \Http\JsonResponse($responseData);
    }
}