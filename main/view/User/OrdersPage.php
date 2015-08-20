<?php

namespace View\User;

use Session\AbTest\ABHelperTrait;

class OrdersPage extends \View\DefaultLayout {
    use ABHelperTrait;

    /** @var string */
    protected $layout  = 'layout-oneColumn';

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

        $this->setTitle('Личный кабинет -> Заказы - Enter');
        $this->setParam('title', 'Личный кабинет');
        $this->setParam('helper', new \Helper\TemplateHelper());
    }

    public function slotContent() {

        if (!$this->hasParam('orderCount')) {
            $orderCount = 0;
            \RepositoryManager::order()->prepareCollectionByUserToken(\App::user()->getToken(), function($data) use(&$orderCount) {
                $orderCount = isset($data['total']) ? $data['total'] : 0;
            });
            \App::coreClientV2()->execute();

            $this->setParam('orderCount', $orderCount);
        }

        return $this->render(
            $this->isOldPrivate() ? 'user/page-orders' : 'user/page-orders-1508',
            $this->params
        );
    }

    public function slotBodyDataAttribute() {
        return 'lk';
    }
}