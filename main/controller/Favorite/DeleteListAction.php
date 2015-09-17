<?php

namespace Controller\Favorite;

use EnterQuery as Query;

class DeleteListAction {
    use \EnterApplication\CurlTrait;

    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception
     */
    public function execute(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $curl = $this->getCurl();
        $user = \App::user();
        $session = \App::session();
        $sessionKey = \App::config()->session['favouriteKey'];

        if (!$user->getEntity()) {
            throw new \Exception\AccessDeniedException('Пользователь не авторизован');
        }

        $productUis = is_array($request->get('productUis')) ? $request->get('productUis') : explode(',', $request->get('productUis'));
        if (!$productUis) {
            throw new \Exception('Не передан productUis', 400);
        }

        /** @var Query\User\Favorite\Delete[] $deleteQueries */
        $deleteQueries = [];
        foreach ($productUis as $productUi) {
            $deleteQuery = (new Query\User\Favorite\Delete($user->getEntity()->getUi(), $productUi))->prepare();
            $deleteQueries[] = $deleteQuery;
        }

        $curl->execute();

        foreach ($deleteQueries as $deleteQuery) {
            if ($deleteQuery->error) {
                throw new $deleteQuery->error;
            } else {
                // удаление продукта из сессии
                $sessionFavourite = $session->get($sessionKey, []);
                if (isset($sessionFavourite[$deleteQuery->ui])) {
                    unset($sessionFavourite[$deleteQuery->ui]);
                    $session->set($sessionKey, $sessionFavourite);
                }
            }
        }

        if ($request->isXmlHttpRequest()) {
            //TODO
        } else {
            $response =  new \Http\RedirectResponse($request->headers->get('referer') ?: '/');
        }

        return $response;
    }
}