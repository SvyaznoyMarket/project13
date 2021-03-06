<?php

namespace Controller\Favorite;

use EnterQuery as Query;

class DeleteAction {
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

        $productUi = $request->get('productUi') ?: null;
        if (!$productUi) {
            throw new \Exception('Не передан productUi');
        }

        $productQuery = (new Query\Product\GetDescriptionByUiList([$productUi]))->prepare();
        $curl->execute();

        // проверяет, если такой товар, чтобы не пихать в избранное мусор
        if (empty($productQuery->response->products[0])) {
            throw new \Exception(sprintf('Товар %s не найден', $productUi));
        }
        $product = new \Model\Product\Entity($productQuery->response->products[0]);

        $favoriteQuery = (new Query\User\Favorite\Delete($user->getEntity()->getUi(), $product->getUi()))->prepare();

        $curl->execute();

        if ($favoriteQuery->error) {
            throw new $favoriteQuery->error;
        } else {
            // удаление продукта из сессии
            $sessionFavourite = $session->get($sessionKey, []);
            if (isset($sessionFavourite[$product->getUi()])) {
                unset($sessionFavourite[$product->getUi()]);
                $session->set($sessionKey, $sessionFavourite);
            }
        }

        if ($request->isXmlHttpRequest()) {
            $response = new \Http\JsonResponse([
                'success' => true,
                'widgets' => [
                    '.id-favoriteButton-' . $product->getUi() => \App::helper()->render(
                        'product/__favoriteButton',
                        [
                            'helper'          => \App::helper(),
                            'product'         => $product,
                            'favoriteProduct' => null,
                        ]
                    ),
                ],
                'favourite' => $sessionFavourite,
                'product'   => [
                    'imageUrl'  => $product->getMainImageUrl('product_60'),
                    'prefix'    => $product->getPrefix(),
                    'webName'   => $product->getWebName()
                ]
            ]);
        } else {
            $response =  new \Http\RedirectResponse($request->headers->get('referer') ?: '/');
        }

        return $response;
    }
}