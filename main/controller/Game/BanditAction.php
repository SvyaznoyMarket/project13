<?php
namespace Controller\Game;

/**
 * @author vadim.kovalenko
 */
class BanditAction {
    const errorUnauthorized         = 301;  // не авторизован
    const errorTriesExceeded        = 311;  // приходит от API
    const errorWinExceeded          = 312;  // приходит от API
    const errorNotEnterprizeMember  = 612;  // пользователь не является участником программы enter prize
    const errorUndefined            = 500;

    protected $isAvailable = true;

    public function index() {
        if (!\App::config()->bandit['enabled']) {
            throw new \Exception\NotFoundException();
        }

        $banditJson = \RepositoryManager::gameBandit()->getBanditJson();

        $page = new \View\Game\BanditPage();
        $page->setParam('animationsConfig', (isset($banditJson['animations_config']) && !empty($banditJson['animations_config'])) ? $banditJson['animations_config'] : []);
        $page->setParam('labelsConfig', (isset($banditJson['labels']) && !empty($banditJson['labels'])) ? $banditJson['labels'] : []);

        return new \Http\Response($page->show());
    }


	public function init() {
        if (!\App::config()->bandit['enabled']) {
            throw new \Exception\NotFoundException();
        }

		$crm	= \App::crmClient();
        $user   = \App::user()->getEntity();

        try {
            $response  = $crm->query(
                'game/bandit/init',
                [
                    'uid'   => (isset($user)?$user->getUi():null)
                ]
            )['result'];
        } catch (\Exception $e) {
            //@todo отрефакторить согласно докам, не могу сходу найти
            if (($error = $this->getError($e->getCode()))) {
                \App::exception()->remove($e);
                return new \Http\JsonResponse([
                    'success'   => false,
                    'error'     => $error
                ]);
            }
        }

        $coupons = $this->getCoupons($response['slots']);
        $slots = [];
        foreach($coupons as $v) {
            $slots[] = $this->couponAsSlot($v);
        }

        $reels      = [$slots,$slots,$slots];
		return new \Http\JsonResponse([
            'success'       => true,
            'isAvailable'   => $this->isAvailable,
            'user'          => (isset($response['user'])?$response['user']:null ),
            'reels'         => $reels,
        ]);
	}


    /**
     * @return \Http\JsonResponse
     */
    public function play(){
        if (!\App::config()->bandit['enabled']) {
            throw new \Exception\NotFoundException();
        }

        $crm	= \App::crmClient();
        $user   = \App::user()->getEntity();

        try {
            if(!$user) {
                throw new \Exception(null,self::errorUnauthorized);
            }

            if(!$user->isEnterprizeMember()) {
                throw new \Exception(null,self::errorNotEnterprizeMember);
            }

            $response  = $crm->query(
                'game/bandit/play', [
                    'uid'   => $user->getUi()
                ]
            );
        } catch (\Exception $e) {
            if (($error = $this->getError($e->getCode()))) {
                \App::exception()->remove($e);
                return new \Http\JsonResponse([
                    'isAvailable'   => $this->isAvailable,
                    'success'       => false,
                    'error'         => $error
                ]);
            }
        }

        $coupons = $this->getCoupons($response['result']['line']);
        foreach($response['result']['line'] as &$v) {
            if(isset($coupons[$v])) {
                $v = $this->couponAsSlot($coupons[$v]);
            } else {
                $v = null;
            }
        }

        // отдаем купон клиенту
        if($response['state']==='win') {
            // добавляем плашку с сообщением о выигрыше
            $response['result']['prizes'] = [
                'type'      => $response['result']['prizes']['type'],
                'message'   => \App::templating()->render('game/coupon-message',
                        $this->couponAsWin($coupons[$response['result']['prizes']['coupon']])
                    ),
                'coupon'    => $this->couponAsWin($coupons[$response['result']['prizes']['coupon']])
            ];

            try {
                \App::coreClientV2()->query(
                    'coupon/enter-prize',[
                        'token' => $user->getToken()
                    ],[
                        'guid'      => $user->getUi(),
                        'name'      => $user->getFirstName(),
                        'mobile'    => $user->getMobilePhone(),
                        'email'     => $user->getEmail(),
                        'agree'     => true
                    ]
                );
            } catch (\Exception $e) {
                \App::exception()->remove($e);
                \App::logger()->error("Не удается отдать купон клиенту\n");
                // @todo определиться как поступать с данной ошибкой, пользователю необходимо что-то сообщить и при этом маякнуть про ошибку модераторам
                $response['result']['prizes']['message'] = \App::templating()->render('game/coupon-message',
                    array_merge($response['result']['prizes']['coupon'], [
                            'errorMessage'  => 'Вы сможете воспользоваться выигранным купоном после обработки нашими специалистами.'
                        ]
                    )
                );
            }
        }

		return new \Http\JsonResponse(
            array_merge([
                    'success'       => true,
                    'isAvailable'   => $this->isAvailable
                ],
                $response
            )
        );
	}


    protected function getError($code) {
        $err = [
            self::errorTriesExceeded => [
                'code'          => 'triesExceeded',
                'message'       => 'Выши попытки израсходованы. Приходите завтра.'
            ],
            self::errorWinExceeded => [
                'code'          => 'winExceeded',
                'message'       => 'Выши попытки израсходованы. Приходите завтра.' // Не тактично писать человеку что он слишком везучий )))
            ],
            self::errorUnauthorized => [
                'code'          => 'userUnauthorized',
                'message'       => 'Необходимо авторизоваться'
            ],
            self::errorNotEnterprizeMember => [
                'code'          => 'notEnterprizeMember',
                'message'       => 'Вам необходимо являться участником программы Enter Prize'
            ],
            self::errorUndefined => [
                'code'          => 'undefined',
                'message'       => 'Не удается выполнить операцию'
            ],
        ];

        if(isset($err[$code])) {
            return $err[$code];
        } else {
            return $err[self::errorUndefined];
        }
    }


    /**
     * Запрашиваем купоны и отдаем ассоциативный массив с uid в качестве ключа
     * @param array $uids
     * @return array
     */
    protected function getCoupons($uids=null){
        $response = [];
        $t = \App::scmsClientV2()->query('coupon/get');

        if(!$uids) {
            foreach($t as $v) {
                $response[$v['uid']]  = $v;
            }
        } else {
            // преобразуем массив к ассоциативному, т.к. проверка запрошенного дешевле
            $uids = array_combine($uids, array_pad([], count($uids), true));
            foreach($t as $v) {
                if(isset($uids[$v['uid']]) && true===$uids[$v['uid']]) {
                    $response[$v['uid']]  = $v;
                }
            }
        }
        return $response;
    }


    /**
     * @param array $coupon
     * @return array
     */
    protected function couponAsWin($coupon) {
        return array_merge(
            $this->couponAsSlot($coupon),[
                'startDate'     => (new \DateTime($coupon['start_date']))->format('d.m.Y'),
                'endDate'       => (new \DateTime($coupon['end_date']))->format('d.m.Y'),
                'minOrder'      => floatval($coupon['min_order_sum'])
            ]
        );
    }


    /**
     * @param array $coupon
     * @return array
     */
    protected function couponAsSlot($coupon) {
        return [
            'uid'           => $coupon['uid'],
            'label'         => $coupon['segment'],
            'url'           => $coupon['segment_url'],
            'icon'          => $coupon['segment_image_url'],
            'background'    => $coupon['background_image_url'],
            'value'         => floatval($coupon['value']),
            'is_currency'   => (bool)$coupon['is_currency']
        ];
    }
}
