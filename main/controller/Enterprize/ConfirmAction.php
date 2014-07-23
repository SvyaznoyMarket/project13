<?php
/**
 * Created by PhpStorm.
 * User: vadimkovalenko
 * Date: 22.07.14
 * Time: 14:57
 */
namespace Controller\Enterprize;

use Curl\Exception;
use Http\JsonResponse;
use Http\RedirectResponse;
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
                    'code'      => 301
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

        if(!$request->isXmlHttpRequest()) {
            return new Response('Bad request',400);
        }

        if(!$this->user) {
            return new JsonResponse([
                'error' => [
                    'message'   => 'Необходимо авторизоваться',
                    'code'      => 301
                ]
            ]);
        }

        return new JsonResponse($this->getConfirmStatus());
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
                    'code'      => 301
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

                if($result && $result['code']==200) {
                    return new JsonResponse([
                        'success'   => true,
                        'message'   => $result['message']
                    ]);
                }
            } catch (\Exception $e) {
                return new JsonResponse([
                    'success'   => false,
                    'error'     => [
                        'code'      => 0,
                        'message'   => 'Сервис временно не доступен'
                    ]
                ]);
            }
        } else {
            return new JsonResponse([
                'success'   => false,
                'form'      => [
                    'error'     => $errors
                ],
                'error'     => [
                    'code'      => 0,
                    'message'   => 'Данные введены некорректно'
                ]
            ]);
        }
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
                    'code'      => 301
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

                if($result && $result['code']==200) {
                    return new JsonResponse([
                        'success'   => true,
                        'message'   => $result['message']
                    ]);
                }
            } catch (\Exception $e) {
                return new JsonResponse([
                    'success'   => false,
                    'error'     => [
                        'code'      => 0,
                        'message'   => 'Сервис временно не доступен'
                    ]
                ]);
            }
        } else {
            return new JsonResponse([
                'success'   => false,
                'form'      => [
                    'error'     => $errors
                ],
                'error'     => [
                    'code'      => 0,
                    'message'   => 'Данные введены некорректно'
                ]
            ]);
        }
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
                    'code'      => 301
                ]
            ]);
        }

        try {
            $result = \App::coreClientV2()->query(
                '/coupon/quick-register-in-enter-prize', [
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
        } catch (\Exception $e) {
            \App::exception()->remove($e);
            switch ($e->getCode()) {
                case 402:
                    $response = [
                        'error' => [
                            'message'   => 'Необходимо авторизоваться',
                            'code'      => 301
                        ]
                    ];
                    break;
                case 403:
                    $response = [
                        'error' => [
                            'message'   => $e->getMessage(),
                            'code'      => $e->getCode(),
                            'detail'    => $this->getConfirmStatus()
                        ]
                    ];
                    break;
                case 409:
                    $response = [
                        'error' => [
                            'message'   => $e->getMessage(),
                            'code'      => $e->getCode()
                        ]
                    ];
                    break;
                case 600:
                    $response = [
                        'error' => [
                            'message'   => $e->getMessage(),
                            'code'      => $e->getCode(),
                            'detail'    => $e->getContent()['detail']
                        ]
                    ];
                    break;
                default:
                    $response = [
                        'error' => [
                            'message'   => 'Не удается зарегестрировать участие в программе Enter Prize',
                            'code'      => 0
                        ]
                    ];
            }
        }

        return new JsonResponse($response);
    }



    protected function getConfirmStatus() {
        return [
            'isEmailConfirmed' => ($this->user?$this->user->getIsEmailConfirmed():null),
            'isPhoneConfirmed' => ($this->user?$this->user->getIsPhoneConfirmed():null)
        ];
    }
}