<?php

namespace Controller\Region;

class Action {
    public function init(\Http\Request $request) {

    }

    public function change($regionId, \Http\Request $request) {
        $regionId = (int)$regionId;

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
        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        $limit = 8;
        $keyword = $request->get('q');
        $router = \App::router();

        $data = array();
        if (mb_strlen($keyword) >= 3) {
            $result = \App::coreClientV2()->query('geo/autocomplete', array('letters' => $keyword));
            $i = 0;
            foreach ($result as $item) {
                if ($i >= $limit) break;

                $data[] = array(
                    'name'  => $item['name'] . ((!empty($item['region']['name']) && ($item['name'] != $item['region']['name'])) ? (" ({$item['region']['name']})") : ''),
                    'url'   => $router->generate('region.change', array('regionId' => $item['id'])),
                );
                $i++;
            }
        }

        return new \Http\JsonResponse(array('data' => $data));
    }
}