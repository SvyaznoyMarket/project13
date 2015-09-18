<?php

namespace Controller\Shop\Region;

class Show {
    /**
     * @param string $regionToken
     * @param string $shopToken
     * @return \Http\Response
     * @throws \Exception\AccessDeniedException
     * @throws \Exception\NotFoundException
     */
    public function execute($regionToken, $shopToken) {
        return new \Http\RedirectResponse(\App::router()->generate('shop.show', ['pointToken' => $shopToken]), 301);
    }
}