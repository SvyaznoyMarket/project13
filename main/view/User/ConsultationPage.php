<?php

namespace View\User;

class ConsultationPage extends \View\DefaultLayout {
    public function prepare() {
        // breadcrumbs
        if (!$this->hasParam('breadcrumbs')) {
            $breadcrumbs = [];
            $breadcrumbs[] = array(
                'name' => 'Личный кабинет',
                'url'  => $this->url('user'),
            );
            $breadcrumbs[] = array(
                'name' => 'Юридическая помощь',
                'url'  => null,
            );

            $this->setParam('breadcrumbs', $breadcrumbs);
        }

        $this->setTitle('Юридическая помощь - Enter');
        $this->setParam('title', 'Юридическая помощь');
    }

    public function slotContent() {
        return $this->render('user/page-consultation', $this->params);
    }

    public function slotSidebar() {
        return $this->render('user/_sidebar', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'infopage';
    }
}
