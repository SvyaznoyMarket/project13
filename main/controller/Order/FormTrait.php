<?php

namespace Controller\Order;

use View\Order\NewForm\Form as Form;

trait FormTrait {
    /**
     * @return Form
     */
    protected function getForm() {
        $request = \App::request();
        $user = \App::user();
        $form = new Form();

        // если пользователь авторизован
        if ($userEntity = \App::user()->getEntity()) {
            $form->setFirstName($userEntity->getFirstName());
            $form->setLastName($userEntity->getLastName());
            $form->setMobilePhone((strlen($userEntity->getMobilePhone()) > 10)
                ? substr($userEntity->getMobilePhone(), -10)
                : $userEntity->getMobilePhone()
            );
            $form->setEmail($userEntity->getEmail());
            // иначе, если пользователь неавторизован, то вытащить из куки значения для формы
        } elseif (false) { // TODO: вернуть взад
            $cookieValue = $request->cookies->get(\App::config()->order['cookieName'], 'last_order');
            if (!empty($cookieValue)) {
                try {
                    $cookieValue = (array)unserialize(base64_decode(strtr($cookieValue, '-_', '+/')));
                } catch (\Exception $e) {
                    \App::logger()->error($e, ['order']);
                    $cookieValue = [];
                }
                $data = [];
                foreach ([
                    'recipient_first_name',
                    'recipient_last_name',
                    'recipient_phonenumbers',
                    'recipient_email',
                    'address_street',
                    'address_number',
                    'address_building',
                    'address_apartment',
                    'address_floor',
                    'subway_id',
                ] as $k) {
                    if (array_key_exists($k, $cookieValue)) {
                        if (('subway_id' == $k) && !$user->getRegion()->getHasSubway()) {
                            continue;
                        }
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

    /**
     * @param Form $form
     */
    protected function validateForm(Form $form) {
        // мобильный телефон
        if (!$form->getMobilePhone()) {
            $form->setError('recipient_phonenumbers', 'Не указан мобильный телефон');
        } else if (11 != strlen($form->getMobilePhone())) {
            $form->setError('recipient_phonenumbers', 'Номер мобильного телефона должен содержать 11 цифр');
        }

        // email
        if (('emails' === \App::abTest()->getCase()->getKey()) && !$form->getOneClick()) {
            $email = $form->getEmail();
            $emailValidator = new \Validator\Email();
            if (!$emailValidator->isValid($email)) {
                $form->setError('recipient_email', 'Укажите ваш e-mail');
            }
        }

        // способ доставки
        if (!$form->getDeliveryTypeId()) {
            $form->setError('delivery_type_id', 'Не указан способ получения заказа');
        } else if ($form->getDeliveryTypeId()) {
            $deliveryType = \RepositoryManager::deliveryType()->getEntityById($form->getDeliveryTypeId());
            if (!$deliveryType) {
                $form->setError('delivery_type_id', 'Способ получения заказа недоступен');
            } else if (\Model\DeliveryType\Entity::TYPE_STANDART == $deliveryType->getToken()) {
                if (!$form->getAddressStreet()) {
                    $form->setError('address_street', 'Укажите улицу');
                }
                if (!$form->getAddressBuilding()) {
                    $form->setError('address_building', 'Укажите дом');
                }
            }
        }

        // метод оплаты
        if (!$form->getPaymentMethodId()) {
            $form->setError('payment_method_id', 'Не указан способ оплаты');
        } else if ($form->getPaymentMethodId() && (\Model\PaymentMethod\Entity::CERTIFICATE_ID == $form->getPaymentMethodId())) {
            if (!$form->getCertificateCardnumber()) {
                $form->setError('cardnumber', 'Укажите номер карты');
            }
            if (!$form->getCertificatePin()) {
                $form->setError('cardpin', 'Укажите пин карты');
            }
        } else if ($form->getPaymentMethodId() && (\Model\PaymentMethod\Entity::QIWI_ID == $form->getPaymentMethodId())) {
            // номер телефона qiwi
            if (!$form->getQiwiPhone()) {
                $form->setError('qiwi_phone', 'Не указан мобильный телефон');
            } else if (11 != strlen($form->getQiwiPhone())) {
                $form->setError('qiwi_phone', 'Номер мобильного телефона должен содержать 11 цифр');
            }
        }
    }
}