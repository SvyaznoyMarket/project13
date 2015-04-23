<?php

namespace Controller\Favorite;

use EnterQuery as Query;

class SetAction {
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
        if (!count($productQuery->response->products)) {
            throw new \Exception(sprintf('Товар %s не найден', $productUi));
        }
        $product = new \Model\Product\Entity(reset($productQuery->response->products));

        $favoriteQuery = (new Query\User\Favorite\Set($user->getEntity()->getUi(), $product->getUi()))->prepare();

        $curl->execute();

        if ($favoriteQuery->error) {
            throw new $favoriteQuery->error;
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
                            'favoriteProduct' => new \Model\Favorite\Product\Entity(['uid' => $product->getUi()]),
                        ]
                    ),
                ],
            ]);
        } else {
            $response =  new \Http\RedirectResponse($request->headers->get('referer') ?: '/');
        }

        return $response;
    }
}