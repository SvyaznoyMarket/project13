<?php

namespace Controller\Enterprize;

class Action {
    /**
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception
     */
    public function index(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::dataStoreClient();

        /** @var $enterpizeCoupons \Model\EnterprizeCoupon\Entity[] */
        $enterpizeCoupons = [];
        $client->addQuery('enterprize/coupon-type.json', [], function($data) use (&$enterpizeCoupons) {
            foreach ((array)$data as $item) {
                if (empty($item['token'])) continue;
                $enterpizeCoupons[] = new \Model\EnterprizeCoupon\Entity($item);
            }
        });
        $client->execute();

        $page = new \View\Enterprize\IndexPage();
        $page->setParam('enterpizeCoupons', $enterpizeCoupons);

        return new \Http\Response($page->show());
    }

    public function create(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        // secure
        if (!\App::user()->getToken()) {
           throw new \Exception\AccessDeniedException('Пользователь не авторизован');
        }

        $client = \App::coreClientV2();


        $responseData = [
            'success' => false,
        ];

        try {
            $form = array_merge([
                'name'              => null,
                'phonenumber'       => null,
                'email'             => null,
                'sclub_card_number' => null,
                'agreed'            => null,
                'coupon_type'       => null,
            ], (array)$request->get('coupon_request'));

            try {
                $result = $client->query(
                    'coupon/enter-prize',
                    [
                        'token' => \App::user()->getToken(),
                    ],
                    [
                        'name'                      => $form['name'],
                        'phone'                     => $form['phonenumber'],
                        'email'                     => $form['email'],
                        'svyaznoy_club_card_number' => $form['sclub_card_number'],
                        'guid'                      => $form['coupon_type'],
                        'agree'                     => (bool)$form['agreed'],
                    ],
                    3
                );

                die(var_dump($result));
            } catch (\Curl\Exception $e) {
                \App::logger()->error(['code' => $e->getCode(), 'message' => $e->getMessage(), 'content' => $e->getContent()], ['enterprize']);

                if (422 === $e->getCode()) {
                    $errorContent = $e->getContent();
                    $fieldErrors = isset($errorContent['errors']) ? (array)$errorContent['errors'] : [];
                    if (!empty($fieldErrors['name'])) {
                        $form['name'] = new \Exception('Неверно указано имя');
                    }
                    if (!empty($fieldErrors['phone'])) {
                        $form['phonenumber'] = new \Exception('Неверно указано телефон');
                    }
                    if (!empty($fieldErrors['email'])) {
                        $form['email'] = new \Exception('Неверно указан email');
                    }
                    if (!empty($fieldErrors['svyaznoy_club_card_number'])) {
                        $form['sclub_card_number'] = new \Exception('Неверно указан номер карты "Связной клуб"');
                    }
                    if (!empty($fieldErrors['guid'])) {
                        $form['coupon_type'] = new \Exception('Неверно указан тип купона');
                    }
                    if (!empty($fieldErrors['agree'])) {
                        $form['agreed'] = new \Exception('Не приняты условия');
                    }

                    throw new \Exception('Форма заполнена неверно');
                }

                throw new \Exception('Произошла ошибка при получении купона');
            }

            $responseData['success'] = true;
        } catch (\Exception $e) {
            $responseData['success'] = false;
            $responseData['error'] = ['code' => $e->getCode(), 'message' => $e->getMessage()];
        }

        return new \Http\JsonResponse($responseData);
    }
}