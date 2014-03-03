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

        if (!\App::config()->enterprize['enabled']) {
            throw new \Exception\NotFoundException();
        }

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

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        // secure
        if (!\App::user()->getToken()) {
            throw new \Exception\AccessDeniedException('Пользователь не авторизован');
        }

        $couponRequest = (array)$request->get('coupon_request');
        if (!$couponRequest) {
            throw new \Exception\Exception('Не переданы данные запроса на получение купона');
        }

        $client = \App::coreClientV2();

        $responseData = [];
        try {
            $name = trim((string)$couponRequest['name']);
            if (!$name) {
                throw new \Exception('Не указано имя участника');
            }

            $phone = trim((string)$couponRequest['phone']);
            if (!$phone) {
                throw new \Exception('Не указан номер телефона');
            }

            $email = trim((string)$couponRequest['email']);
            if (!$email) {
                throw new \Exception('Не указан e-mail');
            }

//            $svyaznoy_club_card_number = trim((string)$couponRequest['svyaznoy_club_card_number']);
//            if (!$svyaznoy_club_card_number) {
//                throw new \Exception('Не указан номер карты "Связной-Клуб"');
//            }

            $guid = trim((string)$couponRequest['guid']);
            if (!$guid) {
                throw new \Exception('Не указан идентификатор серии купона');
            }

            $agree = (bool)$couponRequest['agree'];
            if (!$agree) {
                throw new \Exception('Не отмечено согласие с условиями оферты');
            }

            $requestData = array_merge([
                'name'                      => null,
                'phone'                     => null,
                'email'                     => null,
                'svyaznoy_club_card_number' => null,
                'guid'                      => null,
                'agree'                     => null,
            ], $couponRequest);

            $result = [];
            $client->addQuery(
                'coupon/enter-prize',
                [
                    'client_id' => \App::config()->coreV2['client_id'],
                    'token'     => \App::user()->getToken(),
                ],
                [
                    'name'                      => $requestData['name'],
                    'phone'                     => $requestData['phone'],
                    'email'                     => $requestData['email'],
                    'svyaznoy_club_card_number' => $requestData['svyaznoy_club_card_number'],
                    'guid'                      => $requestData['guid'],
                    'agree'                     => (bool)$requestData['agree'],
                ],
                function ($data) use (&$result) {
                    $result = $data;
                    \App::logger()->info(['core.response' => $result], ['enterprize']);
                },
                function(\Exception $e) use (&$result) {
                    \App::exception()->remove($e);
                    \App::logger()->error($e instanceof \Curl\Exception ? $e->getContent() : ['code' => $e->getCode(), 'message' => $e->getMessage()] , ['enterprize']);

                    $result = new \Exception('Не удалось активировать купон' . (\App::config()->debug ? sprintf('#%s %s', $e->getCode(), $e->getMessage()) : ''));
                }
            );
            $client->execute(\App::config()->coreV2['retryTimeout']['huge'], 1);

            if ($result instanceof \Exception) {
                throw $result;
            }

            $responseData = [
                'error'  => null,
                'notice' => ['message' => $result['message'], 'type' => 'info']
            ];
        } catch (\Curl\Exception $e) {
            $errorData = $e->getContent();
            $formError = [];
            if (isset($errorData['detail'])) {
                foreach ($errorData['detail'] as $field => $message) {
                    $formError[] = ['code' => 0, 'field' => $field, 'message' => $message];
                }
            }

            $responseData = [
                'error' => ['code' => $e->getCode(), 'message' => $e->getMessage()],
                'form'  => $formError,
            ];
        }

        return new \Http\JsonResponse($responseData);
    }

    /**
     * @param \Http\Request $request
     * @return \Http\RedirectResponse|\Http\Response
     */
    public function get(\Http\Request $request) {
        $helper = new \Helper\TemplateHelper();
        $user = \App::user()->getEntity();
        $couponToken = $request->get('enterprize_coupon');

        $response = null;

        // получаем ссылку на страницу личного кабинета на форму заполнения личных данных и передаем token купона
        $link = $helper->url('user.edit', ['enterprize_coupon' => $couponToken]);

        // пользователь авторизован
        if ($user) {
            // показываем заглушку, если у пользователя установлена галка "Участник Enter.Prize"
            if ($user->isEnterprizeMember()) {
                $page = new \View\Enterprize\EmptyPage();
                $response = new \Http\Response($page->show());

            // редиректим пользователя на страницу в личный кабинет на форму заполнения личных данных, для получения купона
            } else {
                $response = new \Http\RedirectResponse($link);
            }

        // пользователь неавторизован, редиректим на страницу логина
        } else {
            $response = new \Http\RedirectResponse($helper->url('user.login', ['redirect_to' => $link]));
        }

        return $response;
    }
}