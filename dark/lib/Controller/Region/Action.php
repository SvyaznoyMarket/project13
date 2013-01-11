<?php

namespace Controller\Region;

class Action {
    public function init(\Http\Request $request) {

    }

    public function change($regionId, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $regionId = (int)$regionId;

        if (!$regionId) {
            return new \Http\RedirectResponse(\App::router()->generate('homepage'));
        }

        $response = new \Http\RedirectResponse($request->headers->get('referer') ?: \App::router()->generate('homepage'));

        $region = \RepositoryManager::region()->getEntityById($regionId);
        if (!$region) {
            throw new \Exception\NotFoundException(sprintf('Region #%s not found', $regionId));
        }

        \App::user()->changeRegion($region, $response);

        return $response;
    }

    public function autocomplete(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        $limit = 8;
        $keyword = mb_strtolower($request->get('q'));
        $keyword = strtr($keyword, array(
            'q'=>'й', 'w'=>'ц', 'e'=>'у', 'r'=>'к', 't'=>'е', 'y'=>'н', 'u'=>'г', 'i'=>'ш', 'o'=>'щ', 'p'=>'з', '['=>'х', ']'=>'ъ', 'a'=>'ф', 's'=>'ы', 'd'=>'в', 'f'=>'а', 'g'=>'п', 'h'=>'р', 'j'=>'о', 'k'=>'л', 'l'=>'д', ';'=>'ж', "'"=>'э', 'z'=>'я', 'x'=>'ч', 'c'=>'с', 'v'=>'м', 'b'=>'и', 'n'=>'т', 'm'=>'ь', ','=>'б', '.'=>'ю', '`'=>'ё', 'Q'=>'Й', 'W'=>'Ц', 'E'=>'У', 'R'=>'К', 'T'=>'Е', 'Y'=>'Н', 'U'=>'Г', 'I'=>'Ш', 'O'=>'Щ', 'P'=>'З', '{'=>'Х', '}'=>'Ъ', 'A'=>'Ф', 'S'=>'Ы', 'D'=>'В', 'F'=>'А', 'G'=>'П', 'H'=>'Р', 'J'=>'О', 'K'=>'Л', 'L'=>'Д', ':'=>'Ж', '"'=>'Э', 'Z'=>'Я', 'X'=>'Ч', 'C'=>'С', 'V'=>'М', 'B'=>'И', 'N'=>'Т', 'M'=>'Ь', '<'=>'Б', '>'=>'Ю', '~'=>'Ё',
        ));

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

    public function redirect(\Http\Request $request, $regionId, $redirectTo) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $regionId = (int)$regionId;

        if ('/' !== $redirectTo[0]) {
            $redirectTo = '/' . $redirectTo;
        }

        if (!$regionId) {
            return new \Http\RedirectResponse($redirectTo);
        }

        $response = new \Http\RedirectResponse($redirectTo);

        $region = \RepositoryManager::region()->getEntityById($regionId);
        if ($region) {
            \App::user()->changeRegion($region, $response);
        }

        return $response;
    }
}