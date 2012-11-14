<?php

namespace View\User;

class IndexPage extends \View\DefaultLayout {
    public function prepare() {
        // breadcrumbs
        if (!$this->hasParam('breadcrumbs')) {
            $breadcrumbs = array();
            $breadcrumbs[] = array(
                'name' => 'Личный кабинет',
                'url'  => null,
            );

            $this->setParam('breadcrumbs', $breadcrumbs);
        }

        $this->setTitle('Личный кабинет - Enter');
        $this->setParam('title', 'Личный кабинет');
    }

    public function slotContent() {
        $this->params['menu'] = array(
            array(
                'title' => 'Моя персональная информация',
                'links' => array(
                    array(
                        'name' => 'Изменить мои данные',
                        'url'  => $this->url('user.edit'),
                    ),
                    array(
                        'name' => 'Изменить пароль',
                        'url'  => $this->url('user.changePassword'),
                    ),
                )
            ),

            array(
                'title' => 'Мои товары',
                'links' => array(
                    array(
                        'name' => 'Мои заказы',
                        'num'  => \RepositoryManager::getOrder()->countByUserToken(\App::user()->getEntity()->getToken()),
                        'url'  => $this->url('user.order'),
                    ),
                )
            ),

            array(
                'title' => 'cEnter защиты прав потребителей ',
                'links' => array(
                    array(
                        'name' => 'Адвокат клиента',
                        'url'  => $this->url('user.consultation'),
                    ),
                )
            ),
        );

        return $this->render('user/page-index', $this->params);
    }

    public function slotSidebar() {
        return $this->render('user/_sidebar', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'infopage';
    }
}
