<?php

namespace Controller\Bandit;

use Http\RedirectResponse;

class IndexAction {
    public function execute() {
        //\App::logger()->debug('Exec ' . __METHOD__);

        if (!\App::config()->bandit['enabled']) {
            return new \Http\RedirectResponse(\App::router()->generate('homepage'));
        }

        $banditJson = \RepositoryManager::gameBandit()->getBanditJson();

        $page = new \View\Bandit\IndexPage();
        $page->setParam('config', [
            'animations' => isset($banditJson['animations_config']) && is_array($banditJson['animations_config']) ? $banditJson['animations_config'] : [],
            'labels' => isset($banditJson['labels']) && is_array($banditJson['labels']) ? $banditJson['labels'] : [],
            'mainHost' => \App::config()->mainHost,
        ]);

        return new \Http\Response($page->show());
    }
}
