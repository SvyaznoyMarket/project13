<?php

namespace Controller\Order;

class Action {
    const ORDER_COOKIE_NAME = 'last_order';

    /**
     * @param \Http\Request $request
     * @return \Http\Response
     */
    public function create(\Http\Request $request) {
        $client = \App::coreClientV2();
        $user = \App::user();
        $form = $this->getForm();

        try {
            $deliveryMap = $this->getDeliveryMap();
        } catch (\Exception $e) {
            $page = new \View\Order\ErrorPage();
            $page->setParam('exception', $e);

            return new \Http\Response($page->show());
        }

        if (!$deliveryMap) {
            \App::logger()->warn('Невозможно начать оформление заказа: в корзине нет товаров и услуг');
            return new \Http\RedirectResponse(\App::router()->generate('cart'));
        }

        if ($request->isMethod('post')) {
            $this->saveOrder();
        }

        // подготовка пакета запросов

        // запрашиваем список станций метро
        /** @var $subwayData array */
        $subwayData = array();
        if ($user->getRegion()->getHasSubway()) {
            \RepositoryManager::getSubway()->prepareCollectionByRegion($user->getRegion(), function($data) use (&$subwayData) {
                foreach ($data as $item) {
                    $subwayData[] = array('val' => $item['id'], 'label' => $item['name']);
                }
            }, function(\Exception $e) {
                \App::exception()->remove($e);
            });
        }

        // запрашиваем список способов оплаты
        /** @var $paymentMethods \Model\PaymentMethod\Entity[] */
        $paymentMethods = array();
        $selectedPaymentMethodId = null;
        $creditAllowed = \App::config()->payment['creditEnabled'] && ($user->getCart()->getTotalProductPrice()) < \App::config()->product['minCreditPrice'];
        \RepositoryManager::getPaymentMethod()->prepareCollection(null, function($data)
            use (
                &$paymentMethods,
                &$selectedPaymentMethodId,
                $creditAllowed,
                $user,
                $request
            ) {
                foreach ($data as $i => $item) {
                    $paymentMethod = new \Model\PaymentMethod\Entity($item);

                    // кредит
                    if ($paymentMethod->getIsCredit() && !$creditAllowed) {
                        continue;
                    }

                    // подарочный сертификат
                    if ($user->getRegion()->getHasTransportCompany() && $paymentMethod->isCertificate()) {
                        continue;
                    }

                    if ($creditAllowed && $request->cookies->get('credit_on')) {
                        if ($paymentMethod->getIsCredit()) {
                            $selectedPaymentMethodId = $paymentMethod->getId();
                        }
                    } elseif (null == $selectedPaymentMethodId) {
                        $selectedPaymentMethodId = $paymentMethod->getId();
                    }

                    $paymentMethods[] = $paymentMethod;
                }
        });

        // запрашиваем список кредитных банков
        /** @var $banks \Model\CreditBank\Entity[] */
        $banks = array();
        \RepositoryManager::getCreditBank()->prepareCollection(function($data) use (&$banks) {
            foreach ($data as $item) {
                $banks[] = new \Model\CreditBank\Entity($item);
            }
        });
        rsort($banks);
        $bankData = array();
        foreach ($banks as $bank) {
            $bankData[$bank->getId()]['name'] = $bank->getName();
            $bankData[$bank->getId()]['href'] = $bank->getLink();
        }

        // выполнение пакета запросов
        $client->execute();

        $page = new \View\Order\CreatePage();

        // ссылка "вернуться к покупкам"
        $backLink = $page->url('cart');
        // TODO: доделать расчет ссылки

        $page->setParam('form', $form);
        $page->setParam('deliveryMap', $deliveryMap);
        $page->setParam('subwayData', $subwayData);
        $page->setParam('banks', $banks);
        $page->setParam('bankData', $bankData);
        $page->setParam('backLink', $backLink);
        $page->setParam('paymentMethods', $paymentMethods);
        $page->setParam('selectedPaymentMethodId', $selectedPaymentMethodId);

        return new \Http\Response($page->show());
    }

    private function getForm() {
        $request = \App::request();
        $form = new \View\Order\Form();

        // вытащить из куки значения для формы, если пользователь неавторизован
        if ($userEntity = \App::user()->getEntity()) {
            $form->setFirstName($userEntity->getFirstName());
            $form->setLastName($userEntity->getLastName());
            $form->setMobilePhone((strlen($userEntity->getMobilePhone()) > 10)
                ? substr($userEntity->getMobilePhone(), -10)
                : $userEntity->getMobilePhone()
            );
        } else {
            $cookieValue = $request->cookies->get(self::ORDER_COOKIE_NAME);
            if (!empty($cookieValue)) {
                $cookieValue = (array)unserialize(base64_decode(strtr($cookieValue, '-_', '+/')));
                $data = array();
                foreach ($this->getFormCookieKeys() as $k) {
                    if (array_key_exists($k, $cookieValue)) {
                        if (('recipient_phonenumbers' == $k) && (strlen($cookieValue[$k])) > 10) {
                            $cookieValue[$k] = substr($cookieValue[$k], -10);
                        }
                        $data[$k] = $cookieValue[$k];
                    }
                }
                $form->fromArray($data);
            }
        }

        return $form;
    }

    private function saveOrder() {
        $order = new \Model\Order\Entity();

        var_dump($_REQUEST); exit();
    }

    /**
     * @return \View\Order\DeliveryCalc\Map|null
     * @throws \Exception
     */
    private function getDeliveryMap() {
        $client = \App::coreClientV2();
        $user = \App::user();
        $region = $user->getRegion();
        $cart = $user->getCart();
        $router = \App::router();

        // товары и услуги в корзине
        $cartProductsById = $cart->getProducts();
        $cartServicesById = $cart->getServices();

        $productIds = array_keys($cartProductsById);
        $serviceIds = array_keys($cartServicesById);
        foreach ($cartProductsById as $cartProduct) {
            foreach ($cartProduct->getService() as $serviceCart) {
                $serviceIds[] = $serviceCart->getId();
            }
        }

        if (!(bool)$productIds && !(bool)$serviceIds) {
            return null;
        }

        // данные по товарам и услугам для запроса в ядро
        $productsInCart = array();
        foreach ($cartProductsById as $cartProduct) {
            $productsInCart[] = array('id' => $cartProduct->getId(), 'quantity' => $cartProduct->getQuantity());
        }
        $servicesInCart = array();
        // несвязанные услуги
        foreach ($cartServicesById as $cartService) {
            $servicesInCart[] = array('id' => $cartService->getId(), 'quantity' => $cartService->getQuantity());
        }
        // связанные услуги
        foreach ($cartProductsById as $cartProduct) {
            foreach ($cartProduct->getService() as $cartService) {
                $servicesInCart[] = array('id' => $cartService->getId(), 'quantity' => $cartProduct->getQuantity(), 'product_id' => $cartProduct->getId());
            }
        }

        // подготовка пакета запросов

        // магазины
        /** @var $shops \Model\Shop\Entity[] */
        $shops = array();
        // карта доставки
        $deliveryCalcResult = null;
        \App::coreClientV2()->addQuery('order/calc-tmp', array(
            'geo_id'  => $user->getRegion()->getId(),
        ), array(
            'product' => $productsInCart,
            'service' => $servicesInCart,
        ), function($data) use (&$deliveryCalcResult, &$shops) {
            $deliveryCalcResult = $data;
            $shops = array_map(function($data) { return new \Model\Shop\Entity($data); }, $deliveryCalcResult['shops']);
        }, function (\Exception $e) {
            //\App::exception()->remove($e);

            throw $e;
        });
        //$result = json_decode(file_get_contents(\App::config()->dataDir . '/core/v2-order-calc.json'), true);

        // запрашиваем список товаров
        $productsById = array();
        $servicesById = array();

        if ((bool)$productIds) {
            \RepositoryManager::getProduct()->prepareCollectionById($productIds, $region, function($data) use(&$productsById, $cartProductsById) {
                foreach ($data as $item) {
                    $productsById[$item['id']] = new \Model\Product\CartEntity($item);
                }
            });
        }

        // запрашиваем список услуг
        if ((bool)$serviceIds) {
            \RepositoryManager::getService()->prepareCollectionById($serviceIds, $region, function($data) use(&$servicesById, $cartServicesById) {
                foreach ($data as $item) {
                    $servicesById[$item['id']] = new \Model\Product\Service\Entity($item);
                }
            });
        }

        // выполнение пакета запросов
        $client->execute();

        if (!$deliveryCalcResult) {
            $e = new \Exception('Калькулятор доставки вернул пустой результат');
            \App::logger()->error($e->getMessage());

            throw $e;
        }

        // карта доставки
        $deliveryMapView = new \View\Order\DeliveryCalc\Map();

        $deliveryMapView->unavailable = array();
        if (array_key_exists('unavailable', $deliveryCalcResult)) {
            foreach ($deliveryCalcResult['unavailable'] as $itemType => $itemIds) {
                $deliveryMapView->unavailable = array_merge($deliveryMapView->unavailable, array_map(function($id) use ($itemType) {
                    return ('products' == $itemType ? 'product' : 'service') . '-' . $id;
                }, $itemIds));
            }
        }

        // сборка магазинов
        foreach ($shops as $shop) {
            $shopView = new \View\Order\DeliveryCalc\Shop();

            $shopView->id = $shop->getId();
            $shopView->address = $shop->getAddress();
            $shopView->latitude = $shop->getLatitude();
            $shopView->longitude = $shop->getLongitude();
            $shopView->name = $shop->getName();
            $shopView->regime = $shop->getRegime();

            $deliveryMapView->shops[$shopView->id] = $shopView;
        }

        // сборка товаров и услуг
        foreach (array('products', 'services') as $itemType) {
            foreach ($deliveryCalcResult[$itemType] as $itemData) {
                $itemData['id'] = (int)$itemData['id'];

                /** @var $cartItem \Model\Cart\Product\Entity|\Model\Cart\Service\Entity|null */
                $cartItem = null;
                if ('products' == $itemType) {
                    if (!isset($cartProductsById[$itemData['id']])) {
                        \App::logger()->error(sprintf('В корзине отсутсвует товар #%s', $itemData['id']));
                        continue;
                    }

                    $cartItem = $cartProductsById[$itemData['id']];
                } else if ('services' == $itemType) {
                    if (!isset($cartServicesById[$itemData['id']])) {
                        //\App::logger()->error(sprintf('В корзине отсутсвует услуга #%s', $itemData['id']));
                        continue;
                    }

                    $cartItem = $cartServicesById[$itemData['id']];
                }
                if (!$cartItem) {
                    \App::logger()->error(sprintf('Не найден элемент корзины %s-%s', $itemType, $itemData['id']));
                    continue;
                }

                $serviceTotal = 0; $serviceName = '';
                if ($cartItem instanceof \Model\Cart\Product\Entity) {

                    foreach ($cartItem->getService() as $cartService) {
                        if (!isset($servicesById[$cartService->getId()])) {
                            \App::logger()->error(sprintf('В индексном массиве услуга #%s отсутсвует', $cartService->getId()));
                            continue;
                        }

                        /** @var $service \Model\Product\Service\Entity */
                        $service = $servicesById[$cartService->getId()];
                        $serviceName .= sprintf(' + <span class="motton">%s (%s шт.)</span>', $service->getName(), $cartService->getQuantity());
                        $serviceTotal += ($cartService->getPrice() * $cartService->getQuantity());
                    }
                }

                // дополнительные гарантии для товара
                if ($cartItem instanceof \Model\Cart\Product\Entity && ($warrantyData = $user->getCart()->getWarrantyByProduct($cartItem->getId()))) {
                    /** @var $product \Model\Product\CartEntity */
                    $product = $productsById[$cartItem->getId()];
                    foreach ($product->getWarranty() as $warranty) {
                        // TODO: Внимание! $warrantyData['id'] переделать, когда $user->getCart()->getWarrantyByProduct будет возвращать сущность \Model\Cart\Warranty\Entity
                        if ($warranty->getId() == $warrantyData['id']) {
                            $serviceName .= sprintf(' + <span class="motton">%s (%s шт.)</span>', $warranty->getName(), $warrantyData['quantity']);
                            $serviceTotal += ($warrantyData['price'] * $warrantyData['quantity']);
                        }
                    }
                }

                $itemView = new \View\Order\DeliveryCalc\Item();
                $itemView->url = $itemData['link'];
                if ($cartItem instanceof \Model\Cart\Product\Entity) {
                    $itemView->deleteUrl = $router->generate('cart.product.delete', array('productId' => $itemData['id']));
                } else if ($cartItem instanceof \Model\Cart\Service\Entity) {
                    $itemView->deleteUrl = $router->generate('cart.service.delete', array('serviceId' => $itemData['id'], 'productId' => 0));
                }
                if ($cartItem instanceof \Model\Cart\Product\Entity) {
                    $itemView->addUrl = $router->generate('cart.product.add', array('productId' => $itemData['id'], 'quantity' => $itemData['stock']));
                } else if ($cartItem instanceof \Model\Cart\Service\Entity) {
                    $itemView->addUrl = $router->generate('cart.service.add', array('serviceId' => $itemData['id'], 'quantity' => 1, 'productId' => 0));
                }

                $itemView->id = $itemData['id'];
                $itemView->name = $itemData['name'] . $serviceName;
                $itemView->image = $itemData['media_image'];
                $itemView->price = $itemData['price'];
                $itemView->quantity = $cartItem->getQuantity();
                $itemView->total = ($cartItem->getPrice() * $cartItem->getQuantity()) + $serviceTotal;
                if ($cartItem instanceof \Model\Cart\Product\Entity) {
                    $itemView->type = \View\Order\DeliveryCalc\Item::TYPE_PRODUCT;
                } else if ($cartItem instanceof \Model\Cart\Service\Entity) {
                    $itemView->type = \View\Order\DeliveryCalc\Item::TYPE_SERVICE;
                }
                $itemView->token = $itemView->type . '-' . $itemView->id;
                $itemView->stock = isset($itemData['stock']) ? $itemData['stock'] : 0;

                foreach ($itemData['deliveries'] as $deliveryToken => $deliveryData) {
                    $deliveryView = new \View\Order\DeliveryCalc\Delivery();
                    $deliveryView->price = $deliveryData['price'];
                    $deliveryView->token = $deliveryToken;
                    $deliveryView->name = 0 === strpos($deliveryToken, 'self') ? 'В самовывоз' : 'В доставку';

                    foreach ($deliveryData['dates'] as $dateData) {
                        $dateView = new \View\Order\DeliveryCalc\Date();
                        $dateView->day = date('j', strtotime($dateData['date']));
                        $dateView->dayOfWeek = trim(strftime('%a', strtotime($dateData['date'])), '.');
                        $dateView->value = date('Y-m-d', strtotime($dateData['date']));
                        $dateView->timestamp = strtotime($dateData['date'], 0) * 1000;

                        foreach ($dateData['interval'] as $intervalData) {
                            $intervalView = new \View\Order\DeliveryCalc\Interval();
                            $intervalView->start_at = $intervalData['time_begin'];
                            $intervalView->end_at = $intervalData['time_end'];

                            $dateView->intervals[] = $intervalView;
                        }

                        $deliveryView->dates[] = $dateView;
                    }

                    $itemView->deliveries[$deliveryView->token] = $deliveryView;
                }

                $deliveryMapView->items[$itemView->token] = $itemView;
            }
        }

        // сборка типов доставки
        /** @var $deliveryTypesById \Model\DeliveryType\Entity[] */
        $deliveryTypesById = array();
        foreach (\RepositoryManager::getDeliveryType()->getCollection() as $deliveryType) {
            $deliveryTypesById[$deliveryType->getId()] = $deliveryType;
        }
        foreach ($deliveryCalcResult['deliveries'] as $deliveryTypeToken => $itemData) {
            $itemData['mode_id'] = (int)$itemData['mode_id'];

            $deliveryType = isset($deliveryTypesById[$itemData['mode_id']]) ? $deliveryTypesById[$itemData['mode_id']] : null;
            if (!$deliveryType) {
                \App::logger()->error(sprintf('Не найден тип доставки #%s', $itemData['mode_id']));
                continue;
            }

            $deliveryTypeView = new \View\Order\DeliveryCalc\Type();
            $deliveryTypeView->description = $deliveryType->getDescription();
            $deliveryTypeView->id = $itemData['mode_id'];
            $deliveryTypeView->name = $deliveryType->getName();
            $deliveryTypeView->type = $deliveryType->getToken();
            $deliveryTypeView->token = $deliveryTypeToken;
            $deliveryTypeView->shortName = 0 === strpos($deliveryTypeView->type, 'self') ? 'Самовывоз' : 'Доставим';

            $deliveryTypeView->shop =
                array_key_exists($itemData['shop_id'], $deliveryMapView->shops)
                    ? $deliveryMapView->shops[$itemData['shop_id']]
                    : null;

            foreach ($deliveryMapView->items as $itemView) {
                if (($itemView->type == \View\Order\DeliveryCalc\Item::TYPE_PRODUCT) && !in_array($itemView->id, $itemData['products'])) continue;
                if (($itemView->type == \View\Order\DeliveryCalc\Item::TYPE_SERVICE) && !in_array($itemView->id, $itemData['services'])) continue;

                $deliveryTypeView->items[] = $itemView->token;
            }

            $tmpDates = null;
            $dates = array();
            foreach ($deliveryTypeView->items as $itemToken) {
                $dates = array_map(function($i) { return $i->value; }, $deliveryMapView->items[$itemToken]->deliveries[$deliveryTypeView->token]->dates);
                $dates = is_array($tmpDates) ? array_intersect($dates, $tmpDates) : $dates;
                $tmpDates = $dates;
            }
            $deliveryTypeView->date = array_shift($dates);
            $deliveryTypeView->displayDate = \Util\Date::strftimeRu('%e %B2', strtotime($deliveryTypeView->date));

            /** @var $interval \View\Order\DeliveryCalc\Interval|null */
            $interval =
                (isset($deliveryTypeView->items[0]) && isset($deliveryMapView->items[$deliveryTypeView->items[0]]->deliveries[$deliveryTypeView->token]->dates[0]->intervals[0]))
                    ? $deliveryMapView->items[$deliveryTypeView->items[0]]->deliveries[$deliveryTypeView->token]->dates[0]->intervals[0]
                    : null
            ;
            if ($interval) {
                $deliveryTypeView->interval = $interval->start_at . ',' . $interval->end_at;
                $deliveryTypeView->displayInterval = 'с ' . $interval->start_at . ' по ' . $interval->end_at;
            }

            $deliveryMapView->deliveryTypes[$deliveryTypeView->token] = $deliveryTypeView;
        }

        foreach ($deliveryMapView->items as $itemView) {
            foreach ($itemView->deliveries as $deliveryView) {
                if (!isset($deliveryMapView->deliveryTypes[$deliveryView->token])) continue;

                $deliveryView->name .= ''
                    .(
                    $deliveryMapView->deliveryTypes[$deliveryView->token]->shop
                        ? (' '.str_replace('г. Москва,', '', $deliveryMapView->deliveryTypes[$deliveryView->token]->shop->address))
                        : ''
                    )
                ;
            }
        }

        return $deliveryMapView;
    }

    private function getFormCookieKeys() {
        return array(
            'recipient_first_name',
            'recipient_last_name',
            'recipient_phonenumbers',
            'address_street',
            'address_number',
            'address_building',
            'address_apartment',
            'address_floor',
            //'subway_id',
            //'address_metro',
        );
    }
}