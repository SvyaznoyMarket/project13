<?php

namespace Controller\Region;

use View\Layout;

class Action {

    public function init() {
        $regions = \RepositoryManager::region()->getShownInMenuCollection();
        $html = (new Layout())->render('_regionSelection', [ 'regions' => $regions ]);
        return new \Http\JsonResponse(['result' => $html]);
    }

    /**
     * @param $regionId
     * @param \Http\Request $request
     * @param string|null $uri
     * @throws \Exception\NotFoundException
     * @return \Http\RedirectResponse
     */
    public function change($regionId, \Http\Request $request, $uri = null) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $regionId = (int)$regionId;

        if (!$regionId) {
            return new \Http\RedirectResponse(\App::router()->generate('homepage'));
        }

        $referer = $request->headers->get('referer');

        if (false !== strpos($referer, 'where_buy_tchibo')) {
            $link = \App::router()->generate('product.category', ['categoryPath' => 'tchibo']);
        } else if ($uri) {
            $link = $uri;
        } else {
            $link = parse_url($referer);

            // SITE-4003
            if (!isset($link['host']) || $link['host'] !== \App::config()->mainHost) {
                $link = \App::router()->generate('homepage', $request->query->all());
            } else if (isset($link['query']) && isset($link['path'])) {
                parse_str(urldecode($link['query']), $variables);
                unset($variables['shop']);
                $link = $link['path'] . (count($variables) ? '?' . http_build_query($variables) : '');
            } else {
                $link = $referer ?: \App::router()->generate('homepage');
            }
        }

        $response = new \Http\RedirectResponse($link);

        $regionConfig = (array)\App::dataStoreClient()->query("/region/{$regionId}.json");

        $region = null;
        \RepositoryManager::region()->prepareEntityById($regionId, function($data) use (&$region) {
            $data = reset($data);
            $region = $data ? new \Model\Region\Entity($data) : null;
        });
        \App::coreClientV2()->execute();

        if (!$region) {
            \App::logger()->error(sprintf('Регион #%s не найден', $regionId));
            $region = \RepositoryManager::region()->getDefaultEntity();
            if (!$region) {
                throw new \Exception\NotFoundException(sprintf('Регион #%s не найден', $regionId));
            }
        }

        if ($region instanceof \Model\Region\Entity) {
            if (array_key_exists('reserve_as_buy', $regionConfig)) {
                $region->setForceDefaultBuy(false == $regionConfig['reserve_as_buy']);
            }
        }

        \App::user()->changeRegion($region, $response);

        if (\App::user()->getToken()) {
            try {
                $response->headers->clearCookie(\App::config()->shop['cookieName']);
                \App::coreClientV2()->query('user/update', ['token' => \App::user()->getToken()], [
                    'geo_id' => \App::user()->getRegion() ? \App::user()->getRegion()->getId() : null,
                ]);
            } catch (\Exception $e) {
                \App::exception()->remove($e);
                \App::logger()->error(sprintf('Не удалось обновить регион у пользователя token=%s', \App::user()->getToken()), ['user']);
            }
        }

        return $response;
    }

    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception\NotFoundException
     */
    public function autocomplete(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        $limit = 8;
        $keyword = mb_strtolower($request->get('q'));
        $keyword = strtr($keyword, [
            'q'=>'й', 'w'=>'ц', 'e'=>'у', 'r'=>'к', 't'=>'е', 'y'=>'н', 'u'=>'г', 'i'=>'ш', 'o'=>'щ', 'p'=>'з', '['=>'х', ']'=>'ъ', 'a'=>'ф', 's'=>'ы', 'd'=>'в', 'f'=>'а', 'g'=>'п', 'h'=>'р', 'j'=>'о', 'k'=>'л', 'l'=>'д', ';'=>'ж', "'"=>'э', 'z'=>'я', 'x'=>'ч', 'c'=>'с', 'v'=>'м', 'b'=>'и', 'n'=>'т', 'm'=>'ь', ','=>'б', '.'=>'ю', '`'=>'ё', 'Q'=>'Й', 'W'=>'Ц', 'E'=>'У', 'R'=>'К', 'T'=>'Е', 'Y'=>'Н', 'U'=>'Г', 'I'=>'Ш', 'O'=>'Щ', 'P'=>'З', '{'=>'Х', '}'=>'Ъ', 'A'=>'Ф', 'S'=>'Ы', 'D'=>'В', 'F'=>'А', 'G'=>'П', 'H'=>'Р', 'J'=>'О', 'K'=>'Л', 'L'=>'Д', ':'=>'Ж', '"'=>'Э', 'Z'=>'Я', 'X'=>'Ч', 'C'=>'С', 'V'=>'М', 'B'=>'И', 'N'=>'Т', 'M'=>'Ь', '<'=>'Б', '>'=>'Ю', '~'=>'Ё',
        ]);

        $router = \App::router();

        $data = [];
        if (mb_strlen($keyword) >= 3) {
            \App::coreClientV2()->addQuery('geo/autocomplete', ['letters' => $keyword], [], function($result) use(&$data, $limit, $router){
                $i = 0;
                foreach ($result as $item) {
                    if ($i >= $limit) break;

                    $data[] = [
                        'name'  => $item['name'] . ((!empty($item['region']['name']) && ($item['name'] != $item['region']['name'])) ? (" ({$item['region']['name']})") : ''),
                        'url'   => $router->generate('region.change', ['regionId' => $item['id']]),
                    ];
                    $i++;
                }
            });
            \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['short'], \App::config()->coreV2['retryCount']);
        }

        return new \Http\JsonResponse(['data' => $data]);
    }

    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception\NotFoundException
     */
    public function autoresolve(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        $data = [];
        if ($region = \App::user()->getAutoresolvedRegion()) {
            $data[] = [
                'id'   => $region->getId(),
                'name' => $region->getName(),
                'url'  => \App::router()->generate('region.change', ['regionId' => $region->getId()]),
            ];
        }

        return new \Http\JsonResponse(['data' => $data]);
    }

    /**
     * @param \Http\Request $request
     * @param $regionId
     * @param $redirectTo
     * @return \Http\RedirectResponse
     */
    public function redirect(\Http\Request $request, $regionId, $redirectTo) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $regionId = (int)$regionId;

        if ('/' !== $redirectTo[0]) {
            $redirectTo = '/' . $redirectTo;
        }

        if ((bool)$request->getQueryString()) {
            $redirectTo .= '?' . $request->getQueryString();
        }

        $response = new \Http\RedirectResponse($redirectTo);

        $region = $regionId ? \RepositoryManager::region()->getEntityById($regionId) : null;
        if ($region) {
            \App::user()->changeRegion($region, $response);
        } else {
            \App::logger()->warn(sprintf('Region #%s not found for link %s', $regionId, $redirectTo));
        }

        return $response;
    }
}