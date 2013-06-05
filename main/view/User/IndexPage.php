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
        if (!$this->hasParam('orderCount')) {
            $orderCount = 0;
            \RepositoryManager::order()->prepareCollectionByUserToken(\App::user()->getToken(), function($data) use(&$orderCount) {
                $orderCount = (bool)$data ? count($data) : 0;
            });
            \App::coreClientV2()->execute();

            $this->setParam('orderCount', $orderCount);
        }

        return $this->render('user/page-index', $this->params);
    }

    public function slotSidebar() {
        return $this->render('user/_sidebar', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'infopage';
    }
}
