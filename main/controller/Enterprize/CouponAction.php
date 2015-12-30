<?php

namespace Controller\Enterprize;

class CouponAction {
    /**
     * @param \Http\Request $request
     * @param array $data
     * @return \Http\RedirectResponse|null
     */
    public function create(\Http\Request $request, $data = []) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        if (!\App::config()->enterprize['enabled']) {
            throw new \Exception\NotFoundException();
        }

        $session = \App::session();
        $sessionName = \App::config()->enterprize['formDataSessionKey'];
        if (!(bool)$data) {
            $data = $session->get($sessionName);
        }
        \App::logger()->info(['sender' => __FILE__ . ' ' .  __LINE__, 'data' => $data], ['enterprize']);

        $enterprizeToken = isset($data['enterprizeToken']) ? $data['enterprizeToken'] : null;

        if (!$enterprizeToken) {
            return new \Http\RedirectResponse(\App::router()->generate('enterprize', $request->query->all()));
        }

        $user = \App::user()->getEntity();
        $params = $user && $user->isEnterprizeMember() ? ['member' => 1] : [];

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

            // _utm метки
            $params['utm_source'] = $request->query->get('utm_source');
            $params['utm_term'] = $request->query->get('utm_term');
            $params['utm_medium'] = $request->query->get('utm_medium');
            $params['utm_content'] = $request->query->get('utm_content');
            $params['utm_campaign'] = $request->query->get('utm_campaign');
            $params['utm_nooverride'] = '1'; // SITE-3478

            $response = new \Http\RedirectResponse(\App::router()->generate('enterprize.complete', $params));

            // SITE-3931, SITE-3934
            $session->set($sessionName, array_merge($data, ['isCouponSent' => true]));

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
                    $response = new \Http\RedirectResponse(\App::router()->generate('enterprize.fail'));
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
                $errorList = [];
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

                        $errorList[$fieldName] = $message;
                        $form->setError($fieldName, $message);
                    }
                }

                \App::session()->set('flash', ['errors' => $errorList]);
//                $response = new \Http\RedirectResponse(\App::router()->generate('enterprize.form.show', ['enterprizeToken' => $enterprizeToken]));
                $response = new \Http\RedirectResponse(\App::router()->generate('enterprize.fail'));

            } else {
                \App::session()->set('flash', ['errors' => [$e->getMessage()]]);
                $response = new \Http\RedirectResponse(\App::router()->generate('enterprize.fail'));
            }
        }

        return $response;
    }


    public function fail(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        if (!\App::config()->enterprize['enabled']) {
            throw new \Exception\NotFoundException();
        }

        $session = \App::session();
        $sessionName = \App::config()->enterprize['formDataSessionKey'];
        $repository = \RepositoryManager::enterprize();

        $data = $session->get($sessionName, []);
        $enterprizeToken = isset($data['enterprizeToken']) ? $data['enterprizeToken'] : null;

        if (!$enterprizeToken) {
            return new \Http\RedirectResponse(\App::router()->generate('enterprize'));
        }

        /** @var $enterpizeCoupon \Model\EnterprizeCoupon\Entity|null */
        $enterpizeCoupon = $repository->getEntityByToken($enterprizeToken);

        if (!$enterpizeCoupon) {
            throw new \Exception\NotFoundException(sprintf('Купон @%s не найден.', $enterprizeToken));
        }

        $flash = $session->get('flash');
        $session->remove('flash');

        $page = new \View\Enterprize\CouponFailPage();
        $page->setParam('enterpizeCoupon', $enterpizeCoupon);
        $page->setParam('errors', !empty($flash['errors']) ? $flash['errors'] : null);
        $page->setParam('viewParams', ['showSideBanner' => false]);

        return new \Http\Response($page->show());
    }


    /**
     * @param \Http\Request $request
     * @return \Http\Response
     */
    public function complete(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        if (!\App::config()->enterprize['enabled']) {
            throw new \Exception\NotFoundException();
        }

        return new \Http\RedirectResponse(\App::router()->generate('enterprize', $request->query->all()));
    }
}