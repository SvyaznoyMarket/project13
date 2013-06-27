<?php

namespace Controller\User;

class OldInfoAction {
    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http');
        }

        $client = \App::coreClientV2();
        $user = \App::user();
        $cart = $user->getCart();
        $region = $user->getRegion();

        $actions = [];
        if (\App::config()->subscribe['enabled']) {
            $actions['subscribe'] = [
                'show'   => !$request->cookies->has(\App::config()->subscribe['cookieName']),
                'agrred' => 1 == (int)$request->cookies->get(\App::config()->subscribe['cookieName']),
            ];
        }

        $responseData = array(
            'name'             => '',
            'link'             => \App::router()->generate('user.login'),
            'vitems'           => 0,
            'sum'              => 0,
            'vwish'            => 0,
            'vcomp'            => 0,
            'productsInCart'   => [],
            'servicesInCart'   => [],
            'warrantiesInCart' => [],
            'bingo'            => false,
            'region_id'        => $region->getId(),
            'is_credit'        => 1 == $request->cookies->get('credit_on'),
            'is_subscribed'    => 0,
            'action'           => $actions,
        );

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

        // получаем токены товаров
        $productTokensById = [];
        if ((bool)$productData = $cart->getProductData()) {
            foreach ($productData as $item) {
                $productTokensById[$item['id']] = null;
            }

            \RepositoryManager::product()->prepareCollectionById(array_keys($productTokensById), $region, function($data) use(&$productTokensById) {
                foreach($data as $item) {
                    $productTokensById[$item['id']] = $item['token'];
                }
            });
        }

        // получаем токены услуг
        $serviceTokensById = [];
        if ((bool)$serviceData = $cart->getServiceData()) {
            foreach ($serviceData as $item) {
                $serviceTokensById[$item['id']] = null;
            }

            \RepositoryManager::service()->prepareCollectionById(array_keys($serviceTokensById), $region, function($data) use(&$serviceTokensById) {
                foreach($data as $item){
                    $serviceTokensById[$item['id']] = $item['token'];
                }
            });
        }

        $warrantyData = $cart->getWarrantyData();

        // получаем общую стоимость корзины
        if (((bool)$productData || (bool)$serviceData)) {
            try {
                $client->addQuery('cart/get-price',
                    array('geo_id' => $user->getRegion()->getId()),
                    array(
                        'product_list'  => $productData,
                        'service_list'  => $serviceData,
                        'warranty_list' => $warrantyData,
                    ), function($data) use (&$responseData) {
                        $responseData['sum'] = array_key_exists('price_total', $data) ? $data['price_total'] : 0;
                    }
                );
            } catch (\Exception $e) {
                \App::logger()->error($e);
            }
        }

        if ($productData || $serviceData || $user->getToken()) {
            $client->execute();

            // если пользователь авторизован
            if ($userEntity = $user->getEntity()) {
                $responseData['name'] = $userEntity->getName();
                $responseData['link'] = \App::router()->generate('user');
                $responseData['is_subscribed'] = $user->getEntity()->getIsSubscribed();
            }

            $totalQuantity = $cart->getProductsQuantity();

            // products
            foreach ($productData as $item) {
                if (!isset($productTokensById[$item['id']])) continue;

                $responseData['productsInCart'][$productTokensById[$item['id']]] = $item['quantity'];
            }

            // services
            foreach ($serviceData as $item) {
                if (!isset($responseData['servicesInCart'][$serviceTokensById[$item['id']]])) {
                    $responseData['servicesInCart'][$serviceTokensById[$item['id']]] = [];
                }

                if (!empty($item['product_id']) && isset($productTokensById[$item['product_id']])) {
                    $responseData['servicesInCart'][$serviceTokensById[$item['id']]][$productTokensById[$item['product_id']]] = $item['quantity'];
                } else {
                    $responseData['servicesInCart'][$serviceTokensById[$item['id']]][0] = $item['quantity'];
                    $totalQuantity++;
                }
            }

            // warranties
            foreach ($warrantyData as $warrantyId => $warrantiesByProduct) {
                foreach ($warrantiesByProduct as $productId => $item) {
                    if (!isset($productTokensById[$item['id']])) continue;
                    $responseData['warrantiesInCart'][$warrantyId][$productTokensById[$item['id']]] = $item['quantity'];
                }
            }

            $responseData['vitems'] = $totalQuantity;
        }

        return new \Http\JsonResponse(array(
            'success' => true,
            'data'    => $responseData,
        ));
    }
}