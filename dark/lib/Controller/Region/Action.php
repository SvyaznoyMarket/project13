<?php

namespace Controller\Region;

class Action {
    public function init(\Http\Request $request) {

    }

    public function change(\Http\Request $request) {
        $regionId = (int)$request->get('region');

        if (!$regionId) {
            return new \Http\RedirectResponse(\App::router()->generate('homepage'));
        }

        $response = new \Http\RedirectResponse($request->headers->get('referer') ?: \App::router()->generate('homepage'));

        $region = \RepositoryManager::getRegion()->getEntityById($regionId);
        if (!$region) {
            throw new \Exception\NotFoundException(sprintf('Region #%s not found', $regionId));
        }

        \App::user()->setRegion($region, $response);

        return $response;
    }

    public function autocomplete(\Http\Request $request) {

    }
}