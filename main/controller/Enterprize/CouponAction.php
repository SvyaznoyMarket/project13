<?php

namespace Controller\Enterprize;

class CouponAction {
    /**
     * @param \Http\Request $request
     */
    public function create(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!\App::config()->enterprize['enabled']) {
            throw new \Exception\NotFoundException();
        }

        $session = \App::session();
        $sessionName = \App::config()->enterprize['formDataSessionKey'];
        $data = $session->get($sessionName, []);
        $enterprizeToken = isset($data['enterprizeToken']) ? $data['enterprizeToken'] : null;

        if (!$enterprizeToken) {
            return new \Http\RedirectResponse(\App::router()->generate('enterprize', [], true));
        }

        $user = \App::user()->getEntity();
        $member = $user && $user->isEnterprizeMember() ? ['member' => 1] : [];

        $form = new \View\Enterprize\Form();
        $form->fromArray($data);

        $response = null;
        try {
            $result = \App::coreClientV2()->query(
                'coupon/enter-prize',
                [
                    'client_id' => \App::config()->coreV2['client_id'],
                    'token'     => !empty($data['token']) ? $data['token'] : \App::user()->getToken(),
                ],
                [
                    'name'   => $form->getName(),
                    'mobile' => $form->getMobile(),
                    'email'  => $form->getEmail(),
                    'guid'   => $form->getEnterprizeCoupon(),
                    'agree'  => true,
                ],
                \App::config()->coreV2['hugeTimeout']
            );
            \App::logger()->info(['core.response' => $result], ['coupon', 'create']);

            $response = new \Http\RedirectResponse(\App::router()->generate('enterprize.complete', $member, true));

        } catch (\Curl\Exception $e) {
            \App::exception()->remove($e);

            $errorContent = $e->getContent();
            $detail = isset($errorContent['detail']) && is_array($errorContent['detail']) ? $errorContent['detail'] : [];

            // Пользователь не подтвердил свои данные
            if (403 == $e->getCode()) {
                if (isset($detail['mobile_confirmed']) && !$detail['mobile_confirmed']) {
                    $response = (new \Controller\Enterprize\ConfirmPhoneAction())->create($request);
                } elseif (isset($detail['email_confirmed']) && !$detail['email_confirmed']) {
                    $response = (new \Controller\Enterprize\ConfirmEmailAction())->create($request);
                } else {
                    \App::session()->set('flash', ['errors' => [$e->getMessage()]]);
                    $response = new \Http\RedirectResponse(\App::router()->generate('enterprize.fail', [], true));
                }

                // обновляем сессионные данные
                $newData = [];
                if (isset($detail['mobile_confirmed'])) $newData['isPhoneConfirmed'] = $detail['mobile_confirmed'];
                if (isset($detail['email_confirmed'])) $newData['isEmailConfirmed'] = $detail['email_confirmed'];
                if (!empty($newData)) {
                    $data = array_merge($data, $newData);
                    $session->set($sessionName, $data);
                }

            // Ошибка валидации
            } elseif (600 == $e->getCode()) {
                $errors = [];
                foreach ($detail as $fieldName => $errors) {
                    foreach ($errors as $errorType => $errorMess) {
                        switch ($fieldName) {
                            case 'name':
                                if ('isEmpty' === $errorType) {
                                    $message = 'Не заполнено имя';
                                } else {
                                    $message = 'Некорректно введено имя';
                                }
                                break;
                            case 'mobile':
                                if ('isEmpty' === $errorType) {
                                    $message = 'Не заполнен номер телефона';
                                } elseif ('regexNotMatch' === $errorType) {
                                    $message = 'Некорректно введен номер телефона';
                                }
                                break;
                            case 'email':
                                if ('isEmpty' === $errorType) {
                                    $message = 'Не заполнен E-mail';
                                } else {
                                    $message = 'Некорректно введен E-mail';
                                }
                                break;
                            case 'guid':
                                if ('isEmpty' === $errorType) {
                                    $message = 'Не передан идентификатор серии купона';
                                } else {
                                    $message = 'Невалидный идентификатор серии купона';
                                }
                                break;
                            case 'agree':
                                $message = 'Необходимо согласие';
                                break;
                            default:
                                $message = 'Неизвестная ошибка';
                        }

//                        if (\App::config()->debug) {
//                            $message .= ': ' . print_r($errorMess, true);
//                        }

                        $errors[$fieldName] = $message;
                        $form->setError($fieldName, $message);
                    }
                }

                \App::session()->set('flash', ['errors' => $errors]);
//                $response = new \Http\RedirectResponse(\App::router()->generate('enterprize.form.show', ['enterprizeToken' => $enterprizeToken]));
                $response = new \Http\RedirectResponse(\App::router()->generate('enterprize.fail', [], true));

            } else {
                \App::session()->set('flash', ['errors' => [$e->getMessage()]]);
                $response = new \Http\RedirectResponse(\App::router()->generate('enterprize.fail', [], true));
            }
        }

        return $response;
    }


    public function fail(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!\App::config()->enterprize['enabled']) {
            throw new \Exception\NotFoundException();
        }

        $session = \App::session();
        $sessionName = \App::config()->enterprize['formDataSessionKey'];

        $data = $session->get($sessionName, []);
        $enterprizeToken = isset($data['enterprizeToken']) ? $data['enterprizeToken'] : null;

        if (!$enterprizeToken) {
            return new \Http\RedirectResponse(\App::router()->generate('enterprize', [], true));
        }

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

        if (!$enterpizeCoupon) {
            throw new \Exception\NotFoundException(sprintf('Купон @%s не найден.', $enterprizeToken));
        }

        $flash = $session->get('flash');
        $session->remove('flash');

        $page = new \View\Enterprize\CouponFailPage();
        $page->setParam('enterpizeCoupon', $enterpizeCoupon);
        $page->setParam('errors', !empty($flash['errors']) ? $flash['errors'] : null);

        return new \Http\Response($page->show());
    }


    /**
     * @param \Http\Request $request
     * @return \Http\Response
     */
    public function complete(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!\App::config()->enterprize['enabled']) {
            throw new \Exception\NotFoundException();
        }

        $session = \App::session();
        $sessionName = \App::config()->enterprize['formDataSessionKey'];

        $data = $session->get($sessionName, []);
        $enterprizeToken = isset($data['enterprizeToken']) ? $data['enterprizeToken'] : null;

        if (!$enterprizeToken) {
            return new \Http\RedirectResponse(\App::router()->generate('enterprize', [], true));
        }

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

        if (!$enterpizeCoupon) {
            throw new \Exception\NotFoundException(sprintf('Купон @%s не найден.', $enterprizeToken));
        }

        $page = new \View\Enterprize\CouponCompletePage();
        $page->setParam('enterpizeCoupon', $enterpizeCoupon);
        $page->setParam('member', (bool)$request->get('member', 0));

        return new \Http\Response($page->show());
    }
}