<?php

namespace View\User;

class IndexPage extends \View\DefaultLayout {
    public function prepare() {
        // breadcrumbs
        if (!$this->hasParam('breadcrumbs')) {
            $breadcrumbs = [];
            $breadcrumbs[] = [
                'name' => 'Личный кабинет',
                'url'  => null,
            ];

            $this->setParam('breadcrumbs', $breadcrumbs);
        }

        $this->setTitle('Личный кабинет - Enter');
        $this->setParam('title', 'Личный кабинет');
    }

    public function slotContent() {
        $orderCount = $this->getParam('orderCount');

        $this->params['menu'] = [
            [
                'title' => 'Моя персональная информация',
                'links' => [
                    [
                        'name' => 'Изменить мои данные',
                        'url'  => $this->url('user.edit'),
                    ],
                    [
                        'name' => 'Изменить пароль',
                        'url'  => $this->url('user.changePassword'),
                    ],
                    [
                        'name' => 'Регион: <strong>' . (\App::user()->getEntity()->getCity() ? \App::user()->getEntity()->getCity()->getName() : null) . '</strong> (<a class="jsChangeRegion" data-url="' . $this->url('region.init') . '" data-autoresolve-url="'. $this->url('region.autoresolve') .'">изменить</a>)',
                        'url'  => null,
                    ],
                ],
            ],

            [
                'title' => 'Мои товары',
                'links' => [
                    [
                        'name' => 'Мои заказы',
                        'num'  => $orderCount,
                        'url'  => $this->url('user.order'),
                    ],
                ],
            ],

            [
                'title' => 'cEnter защиты прав потребителей ',
                'links' => [
                    [
                        'name' => 'Адвокат клиента',
                        'url'  => 'http://my.enter.ru/community/pravo',
                    ],
                ],
            ],
        ];

        return $this->render('user/page-index', $this->params);
    }

    public function slotSidebar() {
        return $this->render('user/_sidebar', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'infopage';
    }
}
