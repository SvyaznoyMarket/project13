<?php

namespace Controller\Enterprize;

class FormAction {

    /**
     * @param null $enterprizeToken
     * @return \Http\Response
     * @throws \Exception\NotFoundException
     */
    public function show($enterprizeToken = null) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!\App::config()->enterprize['enabled']) {
            throw new \Exception\NotFoundException();
        }

        if (!$enterprizeToken) {
            throw new \Exception\NotFoundException();
        }

        $user = \App::user()->getEntity();

        /** @var $enterpizeCoupon \Model\EnterprizeCoupon\Entity|null */
        $enterpizeCoupon = null;
        if ($enterprizeToken) {
            \App::dataStoreClient()->addQuery('enterprize/coupon-type.json', [], function($data) use (&$enterpizeCoupon, $enterprizeToken) {
                foreach ((array)$data as $item) {
                    if ($enterprizeToken == $item['token']) {
                        $enterpizeCoupon = new \Model\EnterprizeCoupon\Entity($item);
                    }
                }
            });
            \App::dataStoreClient()->execute();
        }

        $form = new \View\Enterprize\Form();
        // пользователь авторизован, заполняем данные формы
        if ($user) {
            $form->fromArray([
                'name'              => $user->getName(),
                'email'             => $user->getEmail(),
                'phone'             => $user->getMobilePhone(),
                'enterpize_coupon'  => $enterpizeCoupon ? $enterpizeCoupon->getToken() : null,
            ]);
        }

        $page = new \View\Enterprize\FormPage();
        $page->setParam('user', $user);
        $page->setParam('enterpizeCoupon', $enterpizeCoupon);
        $page->setParam('form', $form);

        return new \Http\Response($page->show());
    }

    /**
     * @param \Http\Request $request
     */
    public function update(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user()->getEntity();
        $form = new \View\Enterprize\Form();

        $userData = (array)$request->get('user');
        $form->fromArray($userData);

        if (!$form->getName()) {
            $form->setError('name', 'Не указано имя');
        }

        if (!$form->getPhone()) {
            $form->setError('phone', 'Не указан мобильный телефон');
        }

        if (!$form->getEmail()) {
            $form->setError('email', 'Не указан email');
        }

        if (!$form->getEnterprizeCoupon()) {
            $form->setError('enterprize_coupon', 'Не указан купон');
        }

        if ($form->isValid()) {
            try {
                // создание enterprize-купона
//                $result = $client->query(
//                    'coupon/enter-prize',
//                    [
//                        'client_id' => \App::config()->coreV2['client_id'],
//                        'token'     => \App::user()->getToken(),
//                    ],
//                    [
//                        'name'                      => $form->getName(),
//                        'phone'                     => $form->getPhone(),
//                        'email'                     => $form->getEmail(),
//                        'svyaznoy_club_card_number' => $user ? $user->getSclubCardnumber() : null,
//                        'guid'                      => $form->getEnterprizeCoupon(),
//                        'agree'                     => $form->getAgree(),
//                    ],
//                    \App::config()->coreV2['hugeTimeout']
//                );


            } catch (\Curl\Exception $e) {
                \App::exception()->remove($e);
                \App::logger()->error($e);
            }
        }
    }
}