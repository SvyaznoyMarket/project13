<?php

namespace Controller\Enterprize;

class Coupon {
    /**
     * @param \Http\Request $request
     */
    public function create(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!\App::config()->enterprize['enabled']) {
            throw new \Exception\NotFoundException();
        }

        $data = \App::session()->get(\App::config()->enterprize['formDataSessionKey'], []);
        $enterprizeToken = isset($data['enterprizeToken']) ? $data['enterprizeToken'] : null;

        $form = new \View\Enterprize\Form();
        $form->fromArray($data);

        $response = null;
        try {
            $result = \App::coreClientV2()->query(
                'coupon/enter-prize',
                [
                    'client_id' => \App::config()->coreV2['client_id'],
                    'token'     => isset($data['token']) ? $data['token'] : \App::user()->getToken(),
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

            $response = new \Http\RedirectResponse(\App::router()->generate('enterprize.complete'));

        } catch (\Curl\Exception $e) {
            \App::exception()->remove($e);

            $errorContent = $e->getContent();
            $detail = isset($errorContent['detail']) && is_array($errorContent['detail']) ? $errorContent['detail'] : [];

            // Пользователь не подтвердил свои данные
            if (403 == $e->getCode()) {
                if (!$detail['mobile_confirmed']) {
                    $response = (new \Controller\Enterprize\ConfirmPhoneAction())->create($request);
                } else {
                    $response = (new \Controller\Enterprize\ConfirmEmailAction())->create($request);
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
                $response = new \Http\RedirectResponse(\App::router()->generate('enterprize.fail'));

            } else {
                \App::session()->set('flash', ['errors' => $e->getMessage()]);
                $response = new \Http\RedirectResponse(\App::router()->generate('enterprize.fail'));
            }
        }

        return $response;
    }


    public function fail(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $flash = \App::session()->get('flash');
        \App::session()->remove('flash');

        $page = new \View\Enterprize\CouponFailPage();
        $page->setParam('errors', !empty($flash['errors']) ? $flash['errors'] : null);

        return new \Http\Response($page->show());
    }


    /**
     * @param \Http\Request $request
     * @return \Http\Response
     */
    public function complete(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $page = new \View\Enterprize\CouponCompletePage();

        return new \Http\Response($page->show());
    }
}