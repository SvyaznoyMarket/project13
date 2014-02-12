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
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'phone' => $user->getMobilePhone(),
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

        if ($request->isMethod('post')) {

        }
    }
}