<?php
/**
 * Created by PhpStorm.
 * User: vadimkovalenko
 * Date: 22.07.14
 * Time: 14:57
 */
namespace Controller\Enterprize;

use Http\JsonResponse;
use Http\Response;

class ConfirmAction {

    /**
     * @var \Model\User\Entity
     */
    protected $user;

    public function __construct() {
        $this->user = \App::user()->getEntity();
    }

    /**
     * Получение формы для подтверждения
     * @param \Http\Request $request
     * @return JsonResponse|Response
     */
    public function form(\Http\Request $request) {
        if (!\App::config()->enterprize['enabled']) {
            throw new \Exception\NotFoundException();
        }

        if(!$request->isXmlHttpRequest()) {
            return new Response('Bad request',400);
        }

        if(!$this->user) {
            return new JsonResponse([
                'error' => [
                    'message'   => 'Необходимо авторизоваться',
                    'code'      => 401
                ]
            ]);
        }

        return new Response(\App::templating()->render(
            'enterprize/_contentConfirmAll', $this->getConfirmStatus()
        ));
    }

    /**
     * Получение состояния подтвержденнаемых данных
     * @param \Http\Request $request
     * @return JsonResponse|Response
     */
    public function state(\Http\Request $request) {
        if (!\App::config()->enterprize['enabled']) {
            throw new \Exception\NotFoundException();
        }

//        if(!$request->isXmlHttpRequest()) {
//            return new Response('Bad request',400);
//        }
//
//        if(!$this->user) {
//            return new JsonResponse([
//                'error' => [
//                    'message'   => 'Необходимо авторизоваться',
//                    'code'      => 401
//                ]
//            ]);
//        }

        var_dump($this->user);exit;
        return new JsonResponse(['status'=>$this->getConfirmStatus()]);
    }

    /**
     * Подтверждение email адреса
     * @param \Http\Request $request
     * @return JsonResponse|Response
     * @throws \Exception\NotFoundException
     */
    public function confirmEmail(\Http\Request $request){
        if (!\App::config()->enterprize['enabled']) {
            throw new \Exception\NotFoundException();
        }

        if(!$request->isXmlHttpRequest()) {
            return new Response('Bad request',400);
        }

        if(!$this->user) {
            return new JsonResponse([
                'error' => [
                    'message'   => 'Необходимо авторизоваться',
                    'code'      => 401
                ]
            ]);
        }

        $errors = [];
        if (!($email = $this->user->getEmail())) {
            $errors['email'] = 'Email не известен';
        }
        if (!($code = $request->get('code'))) {
            $errors['code'] = 'Нужно указать код подтверждения';
        }

        if(empty($errors)) {
            try{
                $result = \App::coreClientV2()->query(
                    'confirm/email', [
                        'client_id' => \App::config()->coreV2['client_id'],
                        'token'     => $this->user->getToken(),
                    ], [
                        'email'    => $email,
                        'code'     => $code,
                        'template' => 'enter_prize',
                    ], \App::config()->coreV2['hugeTimeout']
                );

                $response = [
                    'success'   => true,
                    'message'   => $result['message'],
                    'status'    => $this->getConfirmStatus()
                ];
            } catch (\Curl\Exception $e) {
                \App::exception()->remove($e);
                switch ($e->getCode()) {
                    case 402:
                        $error = [
                            'code'      => 401,
                            'message'   => 'Необходимо авторизоваться'
                        ];
                        break;
                    default:
                        $error = $e->getContent();
                        break;

                }
                $response = [
                    'success'   => false,
                    'error'     => $error
                ];
            } catch (\Exception $e) {
                \App::exception()->remove($e);
                $response = [
                    'success'   => false,
                    'error'     => [
                        'message'   => 'Извините, но сервис временно недоступен',
                        'code'      => 500
                    ]
                ];
            }
        } else {
            $response = [
                'success'   => false,
                'form'      => [
                    'error'     => $errors
                ],
                'error'     => [
                    'code'      => 0,
                    'message'   => 'Данные введены некорректно'
                ]
            ];
        }

        return new JsonResponse($response);
    }


    public function createConfirmEmail(\Http\Request $request){
        if (!\App::config()->enterprize['enabled']) {
            throw new \Exception\NotFoundException();
        }

        if(!$request->isXmlHttpRequest()) {
            return new Response('Bad request',400);
        }

        if(!$this->user) {
            return new JsonResponse([
                'error' => [
                    'message'   => 'Необходимо авторизоваться',
                    'code'      => 401
                ]
            ]);
        }

        $errors = [];
        if (!($email = $this->user->getEmail())) {
            $errors['email'] = 'Email не известен';
        }

        if(empty($errors)) {
            try{
                $result = \App::coreClientV2()->query(
                    'confirm/email', [
                        'client_id' => \App::config()->coreV2['client_id'],
                        'token'     => $this->user->getToken(),
                    ], [
                        'email'    => $email,
                        'template' => 'enter_prize',
                    ], \App::config()->coreV2['hugeTimeout']
                );

                $response = [
                    'success'   => true,
                    'message'   => $result['message']
                ];
            } catch (\Curl\Exception $e) {
                \App::exception()->remove($e);
                switch ($e->getCode()) {
                    case 402:
                        $error = [
                            'code'      => 401,
                            'message'   => 'Необходимо авторизоваться'
                        ];
                        break;
                    default:
                        $error = $e->getContent();
                        break;

                }
                $response = [
                    'success'   => false,
                    'error'     => $error
                ];
            } catch (\Exception $e) {
                \App::exception()->remove($e);
                $response = [
                    'success'   => false,
                    'error'     => [
                        'message'   => 'Извините, но сервис временно недоступен',
                        'code'      => 500
                    ]
                ];
            }
        } else {
            $response = [
                'success'   => false,
                'form'      => [
                    'error'     => $errors
                ],
                'error'     => [
                    'code'      => 0,
                    'message'   => 'Данные введены некорректно'
                ]
            ];
        }

        return new JsonResponse($response);
    }

    /**
     * Подтверждение телефона
     * @param \Http\Request $request
     * @return JsonResponse|Response
     * @throws \Exception\NotFoundException
     */
    public function confirmPhone(\Http\Request $request) {
        if (!\App::config()->enterprize['enabled']) {
            throw new \Exception\NotFoundException();
        }

        if(!$request->isXmlHttpRequest()) {
            return new Response('Bad request',400);
        }

        if(!$this->user) {
            return new JsonResponse([
                'error' => [
                    'message'   => 'Необходимо авторизоваться',
                    'code'      => 401
                ]
            ]);
        }

        $errors = [];
        if (!($mobile = $this->user->getMobilePhone())) {
            $errors['mobile'] = 'Телефон не известен';
        }
        if (!($code = $request->get('code'))) {
            $errors['code'] = 'Нужно указать код подтверждения';
        }

        if(empty($errors)) {
            try{
                $result = \App::coreClientV2()->query(
                    'confirm/mobile', [
                        'client_id' => \App::config()->coreV2['client_id'],
                        'token'     => $this->user->getToken(),
                    ], [
                        'mobile'   => $mobile,
                        'code'     => $code,
                    ], \App::config()->coreV2['hugeTimeout']
                );

                $response = [
                    'success'   => true,
                    'message'   => $result['message']
                ];
            } catch (\Curl\Exception $e) {
                \App::exception()->remove($e);
                switch ($e->getCode()) {
                    case 402:
                        $error = [
                            'code'      => 401,
                            'message'   => 'Необходимо авторизоваться'
                        ];
                        break;
                    default:
                        $error = $e->getContent();
                        break;

                }
                $response = [
                    'success'   => false,
                    'error'     => $error
                ];
            } catch (\Exception $e) {
                \App::exception()->remove($e);
                $response = [
                    'success'   => false,
                    'error'     => [
                        'message'   => 'Извините, но сервис временно недоступен',
                        'code'      => 500
                    ]
                ];
            }
        } else {
            $response = [
                'success'   => false,
                'form'      => [
                    'error'     => $errors
                ],
                'error'     => [
                    'code'      => 0,
                    'message'   => 'Данные введены некорректно'
                ]
            ];
        }

        return new JsonResponse($response);
    }

    /**
     * Запрос кода подтверждения
     *
     * @param \Http\Request $request
     * @return JsonResponse|Response
     * @throws \Exception\NotFoundException
     */
    public function createConfirmPhone(\Http\Request $request) {
        if (!\App::config()->enterprize['enabled']) {
            throw new \Exception\NotFoundException();
        }

        if(!$request->isXmlHttpRequest()) {
            return new Response('Bad request',400);
        }

        if(!$this->user) {
            return new JsonResponse([
                'error' => [
                    'message'   => 'Необходимо авторизоваться',
                    'code'      => 401
                ]
            ]);
        }

        $errors = [];
        if (!($mobile = $this->user->getMobilePhone())) {
            $errors['mobile'] = 'Телефон не известен';
        }

        if(empty($errors)) {
            try{
                $result = \App::coreClientV2()->query(
                    'confirm/mobile', [
                        'client_id' => \App::config()->coreV2['client_id'],
                        'token'     => $this->user->getToken(),
                    ], [
                        'mobile'   => $mobile,
                    ], \App::config()->coreV2['hugeTimeout']
                );

                $response = [
                    'success'   => true,
                    'message'   => $result['message']
                ];
            } catch (\Curl\Exception $e) {
                \App::exception()->remove($e);
                switch ($e->getCode()) {
                    case 402:
                        $error = [
                            'code'      => 401,
                            'message'   => 'Необходимо авторизоваться'
                        ];
                        break;
                    default:
                        $error = $e->getContent();
                        break;

                }
                $response = [
                    'success'   => false,
                    'error'     => $error
                ];
            } catch (\Exception $e) {
                \App::exception()->remove($e);
                $response = [
                    'success'   => false,
                    'error'     => [
                        'message'   => 'Извините, но сервис временно недоступен',
                        'code'      => 500
                    ]
                ];
            }
        } else {
            $response = [
                'success'   => false,
                'form'      => [
                    'error'     => $errors
                ],
                'error'     => [
                    'code'      => 0,
                    'message'   => 'Данные введены некорректно'
                ]
            ];
        }
        return new JsonResponse($response);
    }


    /**
     * Делаем пользователя участником Enter Prize
     * @param \Http\Request $request
     * @return JsonResponse|Response
     * @throws \Exception\NotFoundException
     */
    public function setEnterprize(\Http\Request $request){
        if (!\App::config()->enterprize['enabled']) {
            throw new \Exception\NotFoundException();
        }

        if(!$request->isXmlHttpRequest()) {
            return new Response('Bad request',400);
        }

        if(!$this->user) {
            return new JsonResponse([
                'error' => [
                    'message'   => 'Необходимо авторизоваться',
                    'code'      => 401
                ]
            ]);
        }

        try {
            $result = \App::coreClientV2()->query(
                'coupon/quick-register-in-enter-prize', [
                    'client_id' => \App::config()->coreV2['client_id'],
                    'token'     => $this->user->getToken(),
                ], [
                    'mobile'    => $this->user->getMobilePhone(),
                    'email'     => $this->user->getEmail(),
                    'name'      => $this->user->getName(),
                    'agree'     => true
                ], \App::config()->coreV2['hugeTimeout']
            );

            if($result['code'] == 200) {
                $response = [
                    'success'   => true,
                    'result'    => [
                        'message'   => 'Поздравляем, Вы стали участником программы Enter Prize',
                        'code'      => $result['code']
                    ]
                ];
            } else { // оппа, вдруг нежданчик (пока не было)
                $response = [
                    'success'   => false,
                    'result'    => [
                        'message'   => 'Не удается зарегестрировать участие в программе Enter Prize',
                        'code'      => $result['code']
                    ]
                ];
            }
        } catch (\Curl\Exception $e) {
            \App::exception()->remove($e);
            switch ($e->getCode()) {
                case 402:
                    $error = [
                        'code'      => 401,
                        'message'   => 'Необходимо авторизоваться'
                    ];
                    break;
                case 403:
                    $error = [
                        'message'   => $e->getMessage(),
                        'code'      => $e->getCode(),
                        'detail'    => $this->getConfirmStatus()
                    ];
                    break;
                default:
                    $error = $e->getContent();
                    break;
            }

            $response = [
                'success'   => false,
                'error'     => $error
            ];
        } catch (\Exception $e) {
            \App::exception()->remove($e);
            $response = [
                'success'   => false,
                'error'     => [
                    'message'   => 'Извините, но сервис временно недоступен',
                    'code'      => 500
                ]
            ];
        }

        return new JsonResponse($response);
    }


    /**
     * Удаление авторизованного пользователя в отладочном режиме
     * @return JsonResponse
     * @throws \Exception\NotFoundException
     */
    public function deleteUser() {
        if (\App::config()->debug === true) {
            try {
                return new JsonResponse(\App::coreClientV2()->query(
                    '/user/delete/', [
                        'client_id' => \App::config()->coreV2['client_id'],
                        'token'     => $this->user->getToken(),
                    ], [], \App::config()->coreV2['hugeTimeout']
                ));
            } catch (\Exception $e) {
                \App::exception()->remove($e);
                return new JsonResponse($e->getContent());
            }
        } else {
            throw new \Exception\NotFoundException();
        }
    }


    public function debugOk() {
        return new JsonResponse([
            'success'   => true,
            'message'   => 'ВСЕ ЗБС',
            'status'    => [
                'isEmailConfirmed'  => 1,
                'isPhoneConfirmed'  => 1
            ]
        ]);
    }



    protected function getConfirmStatus() {
        return [
            'isEmailConfirmed' => ($this->user?$this->user->getIsEmailConfirmed():null),
            'isPhoneConfirmed' => ($this->user?$this->user->getIsPhoneConfirmed():null)
        ];
    }
}