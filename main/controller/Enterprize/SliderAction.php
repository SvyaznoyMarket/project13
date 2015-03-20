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
        $client = \App::coreClientV2();

        if (!\App::config()->enterprize['enabled'] || !$enterprizeToken) {
            throw new \Exception\NotFoundException();
        }

        // получение купона
        /** @var $enterpizeCoupon \Model\EnterprizeCoupon\Entity|null */
        $enterpizeCoupon = $repository->getEntityByToken($enterprizeToken);
        if (!(bool)$enterpizeCoupon || !(bool)$enterpizeCoupon->getToken() || !$enterpizeCoupon instanceof \Model\EnterprizeCoupon\Entity) {
            throw new \Exception\NotFoundException(sprintf('Купон @%s не найден.', $enterprizeToken));
        }

        /** @var \Model\EnterprizeCoupon\DiscountCoupon\Entity[] $userDiscounts */
        $userDiscounts = [];
        if (\App::user()->getToken()) {
            try {
                $client->addQuery('user/get-discount-coupons', ['token' => \App::user()->getToken()], [],
                    function ($data) use (&$userDiscounts) {
                        if (isset($data['detail']) && is_array($data['detail'])) {
                            foreach ($data['detail'] as $item) {
                                $entity = new \Model\EnterprizeCoupon\DiscountCoupon\Entity($item);
                                $userDiscounts[] = $entity;
                            }
                        }
                    }, null, \App::config()->coreV2['timeout'] * 2
                );
            } catch (\Exception $e) {
                \App::logger()->error($e->getMessage(), ['enterprize']);
                \App::exception()->remove($e);
            }
        }

        $client->execute();

        foreach ($userDiscounts as $userDiscount) {
            if ($userDiscount->getSeries() === $enterpizeCoupon->getToken()) {
                $enterpizeCoupon->setDiscount($userDiscount);
            }
        }

        $products = \Controller\Enterprize\FormAction::getProducts($enterpizeCoupon);

        $helper = new \Helper\TemplateHelper();
        $cartButtonAction = new \View\Cart\ProductButtonAction();

        return new \Http\JsonResponse([
            'success' => true,
            'content' => $helper->renderWithMustache('enterprize/_slider', [
                'products'     => array_map(function(\Model\Product\Entity $product) use (&$helper, &$enterpizeCoupon, &$cartButtonAction) {
                    return [
                        'url'           => $product->getLink(),
                        'price'         => $helper->formatPrice($product->getPrice()),
                        'discountPrice' => $helper->formatPrice(
                            $enterpizeCoupon->getIsCurrency()
                            ? ($product->getPrice() - (($enterpizeCoupon->getPrice() > 0) ? $enterpizeCoupon->getPrice() : -$enterpizeCoupon->getPrice()))
                            : ceil($product->getPrice() - $product->getPrice() * $enterpizeCoupon->getPrice() / 100)
                        ),
                        'name'          => $product->getName(),
                        'image'         => $product->getImageUrl(),
                        'cartButton'    =>
                            $enterpizeCoupon->getDiscount()
                            ? $cartButtonAction->execute(
                                new \Helper\TemplateHelper(),
                                $product,
                                null,
                                false,
                                [],
                                false,
                                'slider'
                            )
                            : null
                        ,
                    ];
                }, $products),
                'user'        => [
                    'isMember' => $user->getEntity() && $user->getEntity()->isEnterprizeMember(),
                ],
                'isUserOwner' => (bool)$enterpizeCoupon->getDiscount(),
                'dataSlider'  => $helper->json([
                    'count'  => count($products),
                    'limit'  => 7,
                    'url'    => null,
                    'sender' => [
                        'name'     => 'enter',
                        'position' => 'Enterprize',
                    ],
                ]),
            ]),
        ]);
    }
}