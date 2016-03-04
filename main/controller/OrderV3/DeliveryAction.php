<?php

namespace Controller\OrderV3;

use Curl\TimeoutException;
use EnterApplication\CurlTrait;
use Model\OrderDelivery\Entity;
use Model\OrderDelivery\Error;
use Model\PaymentMethod\PaymentMethod\PaymentMethodEntity;
use Session\AbTest\ABHelperTrait;

class DeliveryAction extends OrderV3 {
    use ABHelperTrait, CurlTrait;

    /** Main function
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception
     */
    public function execute(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $response = parent::execute($request);
        if ($response) {
            return $response;
        }

        /** @var \Model\User\Address\Entity[] $userAddresses */
        $userAddresses = [];
        /** @var \Model\EnterprizeCoupon\Entity[] $userEnterprizeCoupons */
        $userEnterprizeCoupons = [];
        call_user_func(function() use(&$userAddresses, &$userEnterprizeCoupons) {
            $userEntity = \App::user()->getEntity();
            if (!$userEntity) {
                return;
            }

            $userAddressQuery = new \EnterQuery\User\Address\Get();
            $userAddressQuery->userUi = $userEntity->getUi();
            $userAddressQuery->prepare();

            /** @var \EnterQuery\Coupon\GetByUserToken $discountQuery */
            $discountQuery = null;
            /** @var \EnterQuery\Coupon\Series\Get $couponQuery */
            $couponQuery = null;
            if ($userEntity->isEnterprizeMember()) {
                $discountQuery = new \EnterQuery\Coupon\GetByUserToken();
                $discountQuery->userToken = $userEntity->getToken();
                $discountQuery->prepare();

                $couponQuery = new \EnterQuery\Coupon\Series\Get();
                $couponQuery->memberType = '1';
                $couponQuery->prepare();
            }

            $this->getCurl()->execute();

            call_user_func(function() use(&$userAddresses, $userAddressQuery) {
                foreach ($userAddressQuery->response->addresses as $item) {
                    $userAddress = new \Model\User\Address\Entity($item);
                    if ($userAddress->regionId && $userAddress->regionId === (string)$this->user->getRegion()->getId()) {
                        $userAddresses[] = $userAddress;
                    }
                }
            });

            call_user_func(function() use(&$userEnterprizeCoupons, $discountQuery, $couponQuery) {
                if ($discountQuery && $couponQuery) {
                    $discountsGroupedByCouponSeries = [];
                    foreach ($discountQuery->response->coupons as $item) {
                        $discount = new \Model\EnterprizeCoupon\DiscountCoupon\Entity($item);
                        $discountsGroupedByCouponSeries[$discount->getSeries()][] = $discount;
                    }

                    foreach ($couponQuery->response->couponSeries as $item) {
                        $token = isset($item['uid']) ? (string)$item['uid'] : null;
                        if (!$token || !isset($discountsGroupedByCouponSeries[$token])) {
                            continue;
                        }

                        foreach ($discountsGroupedByCouponSeries[$token] as $discount) {
                            $coupon = new \Model\EnterprizeCoupon\Entity($item);
                            $coupon->setDiscount($discount);
                            $userEnterprizeCoupons[] = $coupon;
                        }
                    }
                }
            });
        });

        $userInfoAddressAddition = new \Model\OrderDelivery\UserInfoAddressAddition($this->session->get(\App::config()->order['splitAddressAdditionSessionKey']));

        if ($request->isXmlHttpRequest()) {
            $cartRepository = new \Model\Cart\Repository();
            $data = $request->request->all();
            $previousSplit = $this->session->get($this->splitSessionKey);
            /** @var \Model\OrderDelivery\Entity|null $orderDelivery */
            $orderDelivery = null;
            $undoView = [];
            $result = [];
            $responseCode = 200;

            try {
                if ($previousSplit === null) {
                    throw new \Exception('Истекла сессия', 500);
                }

                if (isset($data['action'])) {
                    switch ($data['action']) {
                        case 'undo':
                            call_user_func(function() use(&$previousSplit, &$orderDelivery, $cartRepository) {
                                $undo = $this->session->get(\App::config()->order['splitUndoSessionKey']);

                                if (isset($undo['actions']) && is_array($undo['actions'])) {
                                    foreach ($undo['actions'] as $action) {
                                        switch ($action['type']) {
                                            case 'split/replace':
                                                if (!empty($action['previousSplit'])) {
                                                    $this->session->set($this->splitSessionKey, $action['previousSplit']);
                                                    $previousSplit = $action['previousSplit'];
                                                    $orderDelivery = new Entity($previousSplit);
                                                    \RepositoryManager::order()->prepareOrderDeliveryProducts($orderDelivery);
                                                    \App::coreClientV2()->execute();
                                                }
                                                break;
                                            case 'cart/delete':
                                                if (isset($action['products'])) {
                                                    $updateResultProducts = $this->cart->update($action['products']);
                                                    $cartRepository->prepareCrmCartUpdate($updateResultProducts);
                                                }
                                                break;
                                            case 'favorite/add':
                                                if (isset($action['products']) && is_array($action['products']) && \App::user()->getEntity()) {
                                                    foreach ($action['products'] as $product) {
                                                        if (!empty($product['ui'])) {
                                                            (new \EnterQuery\User\Favorite\Delete(\App::user()->getEntity()->getUi(), $product['ui']))->prepare();
                                                        }
                                                    }
                                                }
                                                break;
                                            case 'wishlist/add':
                                                if (!empty($action['id']) && \App::user()->getEntity()) {
                                                    (new \EnterQuery\User\Wishlist\Delete(\App::user()->getEntity()->getUi(), $action['id']))->prepare();
                                                    $this->getCurl()->execute();
                                                }
                                                break;
                                        }
                                    }

                                    $this->getCurl()->execute();
                                }

                                $this->session->remove(\App::config()->order['splitUndoSessionKey']);
                            });
                            break;

                        case 'stashOrder':
                            call_user_func(function() use(&$result, &$undoView, &$orderDelivery, $data, $previousSplit, $cartRepository) {
                                if (!\App::user()->getEntity()) {
                                    $result['needAuth'] = true;
                                    $orderDelivery = $this->getSplit();
                                    return;
                                }

                                if (!isset($data['params']['block_name'])) {
                                    throw new \Exception('Не передан параметр params[block_name]');
                                }

                                $previousSplitOrder = $previousSplit['orders'][$data['params']['block_name']];

                                $changes['orders'] = [
                                    $data['params']['block_name'] => $previousSplitOrder
                                ];

                                /** @var \Model\Product\Entity[] $affectedSplitProducts */
                                $affectedSplitProducts = [];
                                array_walk($changes['orders'][$data['params']['block_name']]['products'], function(&$product) use(&$affectedSplitProducts, $data) {
                                    $product['quantity'] = 0;
                                    $affectedSplitProducts[] = new \Model\Product\Entity(['ui' => $product['ui']]);
                                });

                                $splitException = null;
                                try {
                                    $orderDelivery = $this->getSplit($changes);
                                } catch (\Exception $splitException) {}

                                try {
                                    if ($orderDelivery || ($splitException && in_array($splitException->getCode(), [600, 708]))) {
                                        $updateResultProducts = $this->cart->update(array_map(function($product) {
                                            return ['ui' => $product['ui'], 'quantity' => 0];
                                        }, $previousSplitOrder['products']));
                                        $cartRepository->updateCrmCart($updateResultProducts);

                                        $createWishlistQuery = (new \EnterQuery\User\Wishlist\Create(\App::user()->getEntity()->getUi(), ['title' => date('d.m.Y H:i:s'),]));
                                        $createWishlistQuery->prepare();
                                        $this->getCurl()->execute();
                                        $wishlistId = $createWishlistQuery->response->id;

                                        if (!$wishlistId) {
                                            throw new \Exception('Отсутствует wishlist id');
                                        }

                                        (new \EnterQuery\User\Wishlist\AddProductList(\App::user()->getEntity()->getUi(), [
                                            'id' => $wishlistId,
                                            'products' => array_map(function($product) {
                                                return ['productUi' => $product['ui']];
                                            }, $previousSplitOrder['products']),
                                        ]))->prepare();
                                        $this->getCurl()->execute();

                                        $undoView['type'] = 'stashOrder';
                                        $undoView['order']['sum'] = $previousSplitOrder['total_cost'];

                                        if ($affectedSplitProducts) {
                                            // В шаблоне нужно кол-во товаров и данные для первого товара
                                            $affectedSplitProductsPart = [$affectedSplitProducts[0]];
                                            \RepositoryManager::product()->prepareProductQueries($affectedSplitProductsPart);
                                            \App::coreClientV2()->execute();
                                        }

                                        $undoView['products'] = [];
                                        foreach ($affectedSplitProducts as $affectedSplitProduct) {
                                            $undoView['products'][] = [
                                                'name' => $affectedSplitProduct ? $affectedSplitProduct->getName() : null,
                                            ];
                                        }

                                        $actions = [
                                            [
                                                'type' => 'split/replace',
                                                'previousSplit' => $previousSplit,
                                            ],
                                            [
                                                'type' => 'cart/delete',
                                                'products' => array_filter(array_map(function(\Session\Cart\Update\Result\Product $updateResultProduct) {
                                                    if ($updateResultProduct->setAction === 'delete') {
                                                        return ['ui' => $updateResultProduct->cartProduct->ui, 'quantity' => $updateResultProduct->cartProduct->quantity];
                                                    } else {
                                                        return null;
                                                    }
                                                }, $updateResultProducts)),
                                            ],
                                            [
                                                'type' => 'wishlist/add',
                                                'id' => $wishlistId,
                                            ],
                                        ];

                                        $this->session->set(\App::config()->order['splitUndoSessionKey'], [
                                            'type' => $undoView['type'],
                                            'actions' => $actions,
                                        ]);
                                    }
                                } catch(\Exception $e) {
                                    $orderDelivery = $this->restorePreviousSplit($previousSplit);
                                    $splitException = null;
                                }

                                if ($splitException) {
                                    throw $splitException;
                                }
                            });

                            break;

                        case 'moveProductToFavorite':
                            call_user_func(function() use(&$result, &$undoView, &$orderDelivery, $data, $previousSplit, $cartRepository) {
                                if (!\App::user()->getEntity()) {
                                    $result['needAuth'] = true;
                                    $orderDelivery = $this->getSplit();
                                    return;
                                }

                                if (!isset($data['params']['ui'])) {
                                    throw new \Exception('Не передан параметр params[ui]');
                                }

                                $changes['orders'] = [
                                    $data['params']['block_name'] => $previousSplit['orders'][$data['params']['block_name']]
                                ];

                                /** @var \Model\Product\Entity[] $affectedSplitProducts */
                                $affectedSplitProducts = [];
                                array_walk($changes['orders'][$data['params']['block_name']]['products'], function(&$product) use(&$affectedSplitProducts, $data) {
                                    if ($product['ui'] == $data['params']['ui']) {
                                        $product['quantity'] = 0;
                                        $affectedSplitProducts[] = new \Model\Product\Entity(['ui' => $product['ui']]);
                                    }
                                });

                                $splitException = null;
                                try {
                                    $orderDelivery = $this->getSplit($changes);
                                } catch (\Exception $splitException) {}

                                try {
                                    if ($orderDelivery || ($splitException && in_array($splitException->getCode(), [600, 708]))) {
                                        $updateResultProducts = $this->cart->update([['ui' => $data['params']['ui'], 'quantity' => 0]]);
                                        $cartRepository->updateCrmCart($updateResultProducts);

                                        (new \EnterQuery\User\Favorite\Set(\App::user()->getEntity()->getUi(), $data['params']['ui']))->prepare();
                                        $this->getCurl()->execute();

                                        $undoView['type'] = 'moveProductToFavorite';

                                        if ($affectedSplitProducts) {
                                            // В шаблоне нужно кол-во товаров и данные для первого товара
                                            $affectedSplitProductsPart = [$affectedSplitProducts[0]];
                                            \RepositoryManager::product()->prepareProductQueries($affectedSplitProductsPart);
                                            \App::coreClientV2()->execute();
                                        }

                                        $undoView['products'] = [];
                                        foreach ($affectedSplitProducts as $affectedSplitProduct) {
                                            $undoView['products'][] = [
                                                'name' => $affectedSplitProduct ? $affectedSplitProduct->getName() : null,
                                            ];
                                        }

                                        $this->session->set(\App::config()->order['splitUndoSessionKey'], [
                                            'type' => $undoView['type'],
                                            'actions' => [
                                                [
                                                    'type' => 'split/replace',
                                                    'previousSplit' => $previousSplit,
                                                ],
                                                [
                                                    'type' => 'cart/delete',
                                                    'products' => array_filter(array_map(function(\Session\Cart\Update\Result\Product $updateResultProduct) {
                                                        if ($updateResultProduct->setAction === 'delete') {
                                                            return ['ui' => $updateResultProduct->cartProduct->ui, 'quantity' => $updateResultProduct->cartProduct->quantity];
                                                        } else {
                                                            return null;
                                                        }
                                                    }, $updateResultProducts)),
                                                ],
                                                [
                                                    'type' => 'favorite/add',
                                                    'products' => [
                                                        ['ui' => $data['params']['ui']],
                                                    ],
                                                ],
                                            ],
                                        ]);
                                    }
                                } catch(\Exception $e) {
                                    $orderDelivery = $this->restorePreviousSplit($previousSplit);
                                    $splitException = null;
                                }

                                if ($splitException) {
                                    throw $splitException;
                                }
                            });
                            break;

                        case 'changeProductQuantity':
                            call_user_func(function() use(&$result, &$undoView, &$orderDelivery, $data, $previousSplit, $cartRepository) {
                                if (!isset($data['params']['ui'])) {
                                    throw new \Exception('Не передан параметр params[ui]');
                                }

                                $changes['orders'] = [
                                    $data['params']['block_name'] => $previousSplit['orders'][$data['params']['block_name']]
                                ];

                                /** @var \Model\Product\Entity[] $affectedSplitProducts */
                                $affectedSplitProducts = [];
                                array_walk($changes['orders'][$data['params']['block_name']]['products'], function(&$product) use(&$affectedSplitProducts, $data) {
                                    if ($product['ui'] == $data['params']['ui']) {
                                        $product['quantity'] = (int)$data['params']['quantity'];
                                        $affectedSplitProducts[] = new \Model\Product\Entity(['ui' => $product['ui']]);
                                    }
                                });

                                $splitException = null;
                                try {
                                    $orderDelivery = $this->getSplit($changes);
                                } catch (\Exception $splitException) {}

                                try {
                                    $newQuantity = null;
                                    if ($orderDelivery) {
                                        if (isset($orderDelivery->getProductsByUi()[$data['params']['ui']])) {
                                            // SITE-5442
                                            $newQuantity = $orderDelivery->getProductsByUi()[$data['params']['ui']]->quantity;
                                        } else {
                                            $newQuantity = 0;
                                        }
                                    } else if ($splitException && in_array($splitException->getCode(), [600, 708])) {
                                        $newQuantity = (int)$data['params']['quantity'];
                                    }

                                    if ($newQuantity !== null) {
                                        $updateResultProducts = $this->cart->update([['ui' => $data['params']['ui'], 'quantity' => $newQuantity]]);
                                        $cartRepository->updateCrmCart($updateResultProducts);

                                        if ($newQuantity == 0) {
                                            $undoView['type'] = 'deleteProduct';

                                            if ($affectedSplitProducts) {
                                                // В шаблоне нужно кол-во товаров и данные для первого товара
                                                $affectedSplitProductsPart = [$affectedSplitProducts[0]];
                                                \RepositoryManager::product()->prepareProductQueries($affectedSplitProductsPart);
                                                \App::coreClientV2()->execute();
                                            }

                                            $undoView['products'] = [];
                                            foreach ($affectedSplitProducts as $affectedSplitProduct) {
                                                $undoView['products'][] = [
                                                    'name' => $affectedSplitProduct ? $affectedSplitProduct->getName() : null,
                                                ];
                                            }

                                            $this->session->set(\App::config()->order['splitUndoSessionKey'], [
                                                'type' => $undoView['type'],
                                                'actions' => [
                                                    [
                                                        'type' => 'split/replace',
                                                        'previousSplit' => $previousSplit,
                                                    ],
                                                    [
                                                        'type' => 'cart/delete',
                                                        'products' => array_filter(array_map(function(\Session\Cart\Update\Result\Product $updateResultProduct) {
                                                            if ($updateResultProduct->setAction === 'delete') {
                                                                return ['ui' => $updateResultProduct->cartProduct->ui, 'quantity' => $updateResultProduct->cartProduct->quantity];
                                                            } else {
                                                                return null;
                                                            }
                                                        }, $updateResultProducts)),
                                                    ],
                                                ],
                                            ]);
                                        }
                                    }
                                } catch(\Exception $e) {
                                    $orderDelivery = $this->restorePreviousSplit($previousSplit);
                                    $splitException = null;
                                }

                                if ($splitException) {
                                    throw $splitException;
                                }
                            });
                            break;

                        case 'changeAddress':
                            if (!isset($data['params']) || !is_array($data['params'])) {
                                throw new \Exception('Не передан параметр "params"');
                            }

                            $dataToValidate = array_replace_recursive($previousSplit['user_info'], ['address' => array_intersect_key($data['params'], [
                                'street' => null,
                                'building' => null,
                                'apartment' => null,
                                'kladr_id' => null,
                            ])]);

                            $userInfo = $this->validateUserInfo($dataToValidate);

                            if (!isset($userInfo['error'])) {
                                $newSplit = array_replace_recursive($previousSplit, ['user_info' => $userInfo]);
                                $this->session->set($this->splitSessionKey, $newSplit);
                                $this->session->set(\App::config()->order['splitAddressAdditionSessionKey'], array_intersect_key($data['params'], [
                                    'kladrZipCode' => null,
                                    'kladrStreet' => null,
                                    'kladrStreetType' => null,
                                    'kladrBuilding' => null,
                                    'isSaveAddressChecked' => null,
                                    'isSaveAddressDisabled' => null,
                                ]));
                            } else {
                                throw new \Exception('Ошибка валидации данных пользователя');
                            }

                            $orderDelivery = new Entity($newSplit);
                            \RepositoryManager::order()->prepareOrderDeliveryProducts($orderDelivery);
                            \App::coreClientV2()->execute();
                            break;

                        case 'changeOrderComment':
                            if (!isset($data['params']['comment'])) {
                                throw new \Exception('Не передан параметр params[comment]');
                            }

                            $newSplit = $previousSplit;
                            foreach ($newSplit['orders'] as $key => $order) {
                                $newSplit['orders'][$key]['comment'] = (string)$data['params']['comment'];
                            }

                            $this->session->set($this->splitSessionKey, $newSplit);

                            $orderDelivery = new Entity($newSplit);
                            \RepositoryManager::order()->prepareOrderDeliveryProducts($orderDelivery);
                            \App::coreClientV2()->execute();
                            break;

                        case 'changeDelivery':
                            $changes['orders'] = [
                                $data['params']['block_name'] => array_merge(
                                    isset($previousSplit['orders'][$data['params']['block_name']]) ? $previousSplit['orders'][$data['params']['block_name']] : [],
                                    [
                                        'delivery' => ['delivery_method_token' => $data['params']['delivery_method_token']]
                                    ]
                                )
                            ];
                            $orderDelivery = $this->getSplit($changes);
                            break;

                        case 'changePoint':
                            $changes['orders'] = [
                                $data['params']['block_name'] => $previousSplit['orders'][$data['params']['block_name']]
                            ];
                            // SITE-5703 TODO remove
                            $true_token = strpos($data['params']['token'], '_postamat') !== false ? str_replace('_postamat', '', $data['params']['token']) : $data['params']['token'];
                            $changes['orders'][$data['params']['block_name']]['delivery']['point'] = ['id' => $data['params']['id'], 'token' => $true_token];
                            $orderDelivery = $this->getSplit($changes);
                            break;

                        case 'deletePoint':
                            $changes['orders'] = [
                                $data['params']['block_name'] => $previousSplit['orders'][$data['params']['block_name']]
                            ];
                            $changes['orders'][$data['params']['block_name']]['delivery']['point'] = null;
                            $orderDelivery = $this->getSplit($changes);
                            break;

                        case 'changeDate':
                            $this->logger(['action' => 'change-date']);
                            $changes['orders'] = [
                                $data['params']['block_name'] => $previousSplit['orders'][$data['params']['block_name']]
                            ];
                            $changes['orders'][$data['params']['block_name']]['delivery']['date'] = $data['params']['date'];
                            $orderDelivery = $this->getSplit($changes);
                            break;

                        case 'changeInterval':
                            $changes['orders'] = [
                                $data['params']['block_name'] => $previousSplit['orders'][$data['params']['block_name']]
                            ];
                            $changes['orders'][$data['params']['block_name']]['delivery']['interval'] = $data['params']['interval'];
                            $orderDelivery = $this->getSplit($changes);
                            break;

                        case 'changePaymentMethod':
                            $changes['orders'] = [
                                $data['params']['block_name'] => $previousSplit['orders'][$data['params']['block_name']]
                            ];
                            if (!empty($data['params']['payment_method_id'])) {
                                $paymentTypeId = $data['params']['payment_method_id'];
                            } else {
                                if (@$data['params']['by_credit_card'] == 'true') {
                                    $paymentTypeId = PaymentMethodEntity::PAYMENT_CARD_ON_DELIVERY;
                                } else if (@$data['params']['by_online_credit']== 'true') {
                                    $paymentTypeId = PaymentMethodEntity::PAYMENT_CREDIT;
                                } else if (@$data['params']['by_online'] == 'true') {
                                    $paymentTypeId = PaymentMethodEntity::PAYMENT_CARD_ONLINE;
                                } else {
                                    $paymentTypeId = PaymentMethodEntity::PAYMENT_CASH;
                                }
                            }

                            $changes['orders'][$data['params']['block_name']]['payment_method_id'] = $paymentTypeId;
                            $orderDelivery = $this->getSplit($changes);
                            break;

                        case 'applyDiscount':
                            $changes['orders'] = [
                                $data['params']['block_name'] => $previousSplit['orders'][$data['params']['block_name']]
                            ];
                            $changes['orders'][$data['params']['block_name']]['discounts'][] = ['number' => $data['params']['number'], 'name' => null, 'type' => null, 'discount' => null];
                            $orderDelivery = $this->getSplit($changes);
                            break;

                        case 'deleteDiscount':
                            $changes['orders'] = [
                                $data['params']['block_name'] => $previousSplit['orders'][$data['params']['block_name']]
                            ];
                            $changes['orders'][$data['params']['block_name']]['discounts'] = array_filter($changes['orders'][$data['params']['block_name']]['discounts'], function($discount) use ($data) {
                                return $discount['number'] != $data['params']['number'];
                            });
                            $orderDelivery = $this->getSplit($changes);
                            break;

                        case 'applyCertificate':
                            $changes['orders'] = [
                                $data['params']['block_name'] => $previousSplit['orders'][$data['params']['block_name']]
                            ];
                            $changes['orders'][$data['params']['block_name']]['certificate'] = ['code' => $data['params']['code'], 'pin' => $data['params']['pin']];
                            $orderDelivery = $this->getSplit($changes);
                            break;

                        case 'deleteCertificate':
                            $changes['orders'] = [
                                $data['params']['block_name'] => $previousSplit['orders'][$data['params']['block_name']]
                            ];
                            $changes['orders'][$data['params']['block_name']]['certificate'] = null;
                            $orderDelivery = $this->getSplit($changes);
                            break;
                    }
                }
            } catch (\Exception $e) {
                \App::exception()->remove($e);
                $result['error'] = ['message' => $e->getMessage()];
                if ($e->getCode() == 600 || $e->getCode() == 708 || $e->getCode() == 302 || !$previousSplit) {
                    if ($undoView) {
                        $undoView['redirectUrl'] = \App::router()->generate('cart');
                    } else {
                        $result['redirectUrl'] = \App::router()->generate('cart');
                    }
                } else {
                    $responseCode = 500;
                }
            }

            if (\App::debug()) {
                $result['OrderDeliveryModel'] = $orderDelivery;
            }

            $result['page'] = \App::closureTemplating()->render('order-v3-new/partial/delivery/content', [
                'orderDelivery'           => $orderDelivery,
                'userAddresses'           => $userAddresses,
                'userInfoAddressAddition' => $userInfoAddressAddition,
                'userEnterprizeCoupons'   => $userEnterprizeCoupons,
                'undo'                    => $undoView,
            ]);

            return new \Http\JsonResponse(['result' => $result], $responseCode);
        } else {
            $callbackPhrases = [];

            try {
                $this->pushEvent(['step' => 2]);
                $this->logger(['action' => 'view-page-delivery']);

                /** @var \Model\Config\Entity[] $configParameters */
                $configParameters = [];
                \RepositoryManager::config()->prepare(['site_call_phrases'], $configParameters, function(\Model\Config\Entity $entity) use (&$category, &$callbackPhrases) {
                    if ('site_call_phrases' === $entity->name) {
                        $callbackPhrases = !empty($entity->value['checkout_2']) ? $entity->value['checkout_2'] : [];
                    }

                    return true;
                });

                $previousSplit = $this->session->get($this->splitSessionKey);
                $userData = $this->session->get('user_info_split');

                if (!$userData) {
                    return new \Http\RedirectResponse(\App::router()->generate('cart'));
                }

                $userInfo = null;
                if ($previousSplit) {
                    $userInfo = $previousSplit['user_info'];
                }

                if (!$previousSplit || @$previousSplit['user_info']['phone'] === '') {
                    $userInfo = $userData;
                }

                if (\App::config()->useNodeMQ) {
                    $orderDelivery = new \Model\OrderDelivery\Entity(['user_info' => $userInfo], false);
                } else {
                    $orderDelivery = $this->getSplit(null, $userInfo);
                }

                foreach($orderDelivery->orders as $order) {
                    $this->logger(['delivery-self-price' => $order->delivery->price]);
                }

                $page = new \View\OrderV3\DeliveryPage();
                $page->setParam('step', 2);
                $page->setParam('orderDelivery', $orderDelivery);
                $page->setParam('userAddresses', $userAddresses);
                $page->setParam('userInfoAddressAddition', $userInfoAddressAddition);
                $page->setParam('userEnterprizeCoupons', $userEnterprizeCoupons);
                $page->setGlobalParam('callbackPhrases', $callbackPhrases);

                $response = new \Http\Response($page->show());

                // SITE-5294 При оформлении заказа отправлять письмо с подпиской сразу после 1-го экрана
                call_user_func(function() use(&$response, $userInfo) {
                    try {
                        if (empty($userInfo['email'])) {
                            return;
                        }

                        $subscribeResult = false;
                        $subscribeParams = [
                            'email'      => $userInfo['email'],
                            'geo_id'     => $this->user->getRegion()->getId(),
                            'channel_id' => 1,
                        ];

                        if ($userEntity = $this->user->getEntity()) {
                            $subscribeParams['token'] = $userEntity->getToken();
                        }

                        $this->client->addQuery('subscribe/create', $subscribeParams, [], function($data) use (&$subscribeResult) {
                            if (isset($data['subscribe_id']) && isset($data['subscribe_id'])) {
                                $subscribeResult = true;
                            }
                        }, function(\Exception $e) use (&$subscribeResult) {
                            \App::exception()->remove($e);
                            // 910 - Не удается добавить подписку, указанный email уже подписан на этот канал рассылок
                            if ($e->getCode() == 910) {
                                $subscribeResult = true;
                            }
                        });

                        $this->client->execute();

                        // сохраняем результаты подписки в куку
                        if ($subscribeResult === true) {
                            $response->headers->setCookie(new \Http\Cookie(
                                \App::config()->subscribe['cookieName2'],
                                json_encode(['1' => true]), strtotime('+30 days' ), '/',
                                \App::config()->session['cookie_domain'], false, false
                            ));
                        }
                    } catch (\Exception $e) {
                        \App::logger()->error($e->getMessage(), ['cart/split', 'subscribe']);
                    }
                });

                return $response;
            } catch (\Curl\Exception $e) {
                \App::exception()->remove($e);
                \App::logger()->error($e->getMessage(), ['curl', 'cart/split']);
                $page = new \View\OrderV3\ErrorPage();
                $page->setParam('error', $e->getMessage());
                $page->setParam('step', 2);
                $page->setGlobalParam('callbackPhrases', $callbackPhrases);

                // SITE-5862
                return new \Http\Response($page->show());
            } catch (\Exception $e) {
                if (708 === $e->getCode() || 302 === $e->getCode()) {
                    return new \Http\RedirectResponse(\App::router()->generate('cart'));
                }

                \App::logger()->error($e->getMessage(), ['cart/split']);
                $page = new \View\OrderV3\ErrorPage();
                $page->setParam('error', $e->getMessage());
                $page->setParam('step', 2);
                $page->setGlobalParam('callbackPhrases', $callbackPhrases);

                return new \Http\Response($page->show(), 500);
            }
        }
    }

    /** Разбиение заказа ядром
     * @param array|null $changes
     * @param null $userData
     *
     * @return \Model\OrderDelivery\Entity
     * @throws \Exception
     */
    private function getSplit(array $changes = null, $userData = null) {
        $previousSplit = $this->session->get($this->splitSessionKey);

         if ($changes) {
            $splitData = [
                'previous_split' => $previousSplit,
                'changes'        => $changes,
            ];
        } else if ($previousSplit) { // SITE-6571 Обновление страницы на втором шаге оформления заказа сбрасывает введённые данные
            $splitData = [
                'previous_split' => $previousSplit,
                'changes'        => [],
            ];
        } else {
            $splitData = [
                'cart' => [
                    'product_list' => array_map(function(\Model\Cart\Product\Entity $cartProduct) {
                        return [
                            'id' => $cartProduct->id,
                            'quantity' => $cartProduct->quantity,
                        ];
                    }, $this->cart->getProductsById()),
                ]
            ];

            if ($userData) {
                $splitData += ['user_info' => $userData];
            } elseif ($this->session->get('user_info_split')) {
                $splitData += ['user_info' => $this->session->get('user_info_split')];
            }

            if (!empty($this->cart->getCreditProductIds())) $splitData['payment_method_id'] = \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity::PAYMENT_CREDIT;

            try {
                switch (\App::abTest()->getOrderDeliveryType()) {
                    case 'self':
                        $splitData += ['delivery_type' => 'self'];
                        break;
                    case 'delivery':
                        $splitData += ['delivery_type' => 'standart'];
                        break;
                }
            } catch (\Exception $e) {
                \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['cart.split']);
            }

            // SITE-6513
            try {
                if (\App::abTest()->checkForFreeDelivery()) {
                    $splitData += ['check_for_free_delivery_discount' => true];
                }
            } catch (\Exception $e) {
                \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['cart.split']);
            }
        }


        $splitResponse = null;
        foreach ([2, 8] as $i) { // две попытки на расчет доставки: 2*4 и 8*4 секунды
            try {
                $splitResponse = $this->client->query(
                    'cart/split',
                    [
                        'geo_id'     => $this->user->getRegion()->getId(),
                        'request_id' => \App::$id, // SITE-4445
                    ],
                    $splitData,
                    $i * \App::config()->coreV2['timeout']
                );
            } catch (TimeoutException $e) {
                \App::exception()->remove($e);
            } catch (\Exception $e) {
                // Если в разбиении нет товаров (например, когда удалили последний товар)
                if ($e->getCode() == 600) {
                    throw $e;
                }

                // некорректный email
                if ($e->getCode() == 759) {
                    throw $e;
                }
            }

            if ($splitResponse) break; // если получен ответ прекращаем попытки
        }

        if (!$splitResponse) {
            throw new \Exception('Не удалось расчитать доставку. Повторите попытку позже.');
        }

        $orderDelivery = new \Model\OrderDelivery\Entity($splitResponse);

        // SITE-4389 Обрабатывать ошибку «товара нет в наличии» от ядра
        if (!$orderDelivery->orders) {
            foreach ($orderDelivery->errors as $error) {
                if (708 == $error->code) {
                    throw new \Exception('Товара нет в наличии', 708);
                }
            }

            throw new \Exception('Отстуствуют данные по заказам', 302);
        }

        \RepositoryManager::order()->prepareOrderDeliveryProducts($orderDelivery);
        \App::coreClientV2()->execute();

        // сохраняем в сессию расчет доставки
        $this->session->set($this->splitSessionKey, $splitResponse);
        $this->session->remove(\App::config()->order['splitUndoSessionKey']);

        return $orderDelivery;
    }

    /**
     * @param array $previousSplit
     * @return Entity
     */
    private function restorePreviousSplit($previousSplit) {
        $this->session->set($this->splitSessionKey, $previousSplit);
        $orderDelivery = new Entity($previousSplit);
        \RepositoryManager::order()->prepareOrderDeliveryProducts($orderDelivery);
        \App::coreClientV2()->execute();
        return $orderDelivery;
    }
}