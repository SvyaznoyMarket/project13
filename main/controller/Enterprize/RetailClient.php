<?php

namespace Controller\Enterprize;

class RetailClient {
    /**
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception\NotFoundException
     */
    public function fishka(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!\App::config()->enterprize['enabled']) {
            throw new \Exception\NotFoundException();
        }

        $page = new \View\Enterprize\FishkaPage();

        return new \Http\Response($page->show());
    }
} 