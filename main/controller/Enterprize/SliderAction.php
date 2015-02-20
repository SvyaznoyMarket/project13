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

        $responseData = [
            'content' => (new \Helper\TemplateHelper())->renderWithMustache('enterprize/_slider', [
                'products' => array_map(function(\Model\Product\Entity $product) {
                    return [
                        'name'  => $product->getName(),
                        'image' => $product->getImageUrl(),
                    ];
                }, $products),
                'user' => [
                    'isMember' => $user->getEntity() ? $user->getEntity()->isEnterprizeMember() : false,
                ],
            ]),
        ];

        return new \Http\JsonResponse($responseData);
    }
}