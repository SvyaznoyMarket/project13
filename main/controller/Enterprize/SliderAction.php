<?php

namespace Controller\Enterprize;

class SliderAction {

    /**
     * @param null $enterprizeToken
     * @param \Http\Request $request
     * @return \Http\RedirectResponse|\Http\Response
     * @throws \Exception\NotFoundException
     */
    public function execute($enterprizeToken = null, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $user = \App::user();
        $repository = \RepositoryManager::enterprize();

        if (!\App::config()->enterprize['enabled'] || !$enterprizeToken) {
            throw new \Exception\NotFoundException();
        }

        // получение купона
        /** @var $enterpizeCoupon \Model\EnterprizeCoupon\Entity|null */
        $enterpizeCoupon = $repository->getEntityByToken($enterprizeToken);
        if (!(bool)$enterpizeCoupon || !(bool)$enterpizeCoupon->getToken() || !$enterpizeCoupon instanceof \Model\EnterprizeCoupon\Entity) {
            throw new \Exception\NotFoundException(sprintf('Купон @%s не найден.', $enterprizeToken));
        }

        $products = \Controller\Enterprize\FormAction::getProducts($enterpizeCoupon);

        return new \Http\JsonResponse([
            'success' => true,
            'content' => \App::closureTemplating()->render('product/__slider', [
                'products'     => $products,
                'count'        => count($products),
                'class'        => '',
                'namePosition' => '',
                'sender'       => [],
                'title'        => '',
            ]),
        ]);
    }
}