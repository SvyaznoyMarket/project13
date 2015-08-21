<?php

namespace Controller\Favorite;

use EnterQuery as Query;
use \Model\Media;
use \Model\Session\FavouriteProduct;

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
        $session = \App::session();
        $sessionKey = \App::config()->session['favouriteKey'];

        if (!$user->getEntity()) {
            throw new \Exception\AccessDeniedException('Пользователь не авторизован');
        }

        $productUi = $request->get('productUi') ?: null;
        if (!$productUi) {
            throw new \Exception('Не передан productUi');
        }

        // Запрашиваем продукт из ядра
        $coreProductQuery = new Query\Product\GetByUi($productUi, \App::user()->getRegionId());
        $coreProductQuery->prepare();

        // Запрашиваем картинки из SCMS
        $productQuery = new Query\Product\GetDescriptionByUiList();
        $productQuery->uis = [$productUi];
        $productQuery->filter->media = true;
        $productQuery->prepare();

        $curl->execute();

        // SITE-5975 Не отображать товары, по которым scms или ядро не вернуло данных
        if (empty($coreProductQuery->response->product) || empty($productQuery->response->products[0])) {
            throw new \Exception(sprintf('Товар %s не найден', $productUi));
        }

        $product = new \Model\Product\Entity($coreProductQuery->response->product);
        $product->importFromScms($productQuery->response->products[0]);

        $favoriteQuery = (new Query\User\Favorite\Set($user->getEntity()->getUi(), $product->getUi()))->prepare();

        $curl->execute();

        if ($favoriteQuery->error) {
            throw new $favoriteQuery->error;
        } else {
            $sessionFavourite = $session->get($sessionKey, []);
            if (!isset($sessionFavourite[$product->getId()])) {
                $session->set($sessionKey, $sessionFavourite += [ $product->getId() => (array) (new FavouriteProduct($product)) ] );
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
                            'favoriteProduct' => new \Model\Favorite\Product\Entity(['uid' => $product->getUi(), 'is_favorite' => true]),
                        ]
                    ),
                    '.favourite-userbar-popup-widget'    => \App::helper()->render(
                        'userbar/_favourite-widget',
                        [
                            'product'         => $product,
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