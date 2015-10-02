<?php

namespace View\Shop;

class ShowPage extends \View\DefaultLayout {
    protected $layout  = 'layout-twoColumn';

    public function slotBodyDataAttribute() {
        return 'shop';
    }

    public function slotContent() {
        /** @var \Model\Point\ScmsPoint $point */
        $point = $this->getParam('point');

        if ($point->wayWalkHtml || $point->wayAutoHtml) {
            $way = [
                'commonHtml' => call_user_func(function() use($point) {
                    if ($point->wayWalkHtml && !$point->wayAutoHtml) {
                        return $point->wayWalkHtml;
                    } else if (!$point->wayWalkHtml && $point->wayAutoHtml) {
                        return $point->wayAutoHtml;
                    }

                    return '';
                }),
                'walkHtml' => $point->wayWalkHtml,
                'autoHtml' => $point->wayAutoHtml,
            ];
        } else {
            $way = [];
        }

        return \App::mustache()->render('shop/show/content', [
            'backUrl' => \App::router()->generate('shop'),
            'town' => [
                'names' => [
                    'locativus' => \App::user()->getRegion()->names->locativus,
                ],
            ],
            'point' => [
                'partner' => [
                    'names' => $point->partner->names,
                ],
                'emailSendUrl' => \App::router()->generate('shop.send', ['pointUi' => $point->ui]),
                'address' => $point->address,
                'showMap' => $point->latitude && $point->longitude,
                'latitude' => $point->latitude,
                'longitude' => $point->longitude,
                'workingTime' => $point->workingTime,
                'phone' => $point->phone,
                'descriptionHtml' => $point->descriptionHtml,
                'way' => $way,
                'subway' => [
                    'name' => $point->subway ? $point->subway->getName() : null,
                ],
                'images' => array_values(array_filter(array_map(function(\Model\Media $media) {
                    if ($media->provider !== 'image') {
                        return null;
                    }

                    $smallSource = $media->getSource('shop_small');
                    $bigSource = $media->getSource('shop_big');
                    return [
                        'small' => [
                            'url' => $smallSource ? $smallSource->url : '',
                        ],
                        'big' => [
                            'url' => $bigSource ? $bigSource->url : '',
                        ],
                    ];
                }, $point->medias))),
            ],
        ]);
    }

    public function slotSidebar() {
        return $this->getParam('sidebarHtml');
    }

    public function slotBottombar() {
        /** @var \Model\Point\ScmsPoint $point */
        $point = $this->getParam('point');
        /** @var \Model\Product\Entity[] $products */
        $products = $this->getParam('products');
        $productShowAction = new \View\Product\ShowAction();
        $helper = new \Helper\TemplateHelper();
        $cartButtonAction = new \View\Cart\ProductButtonAction();
        $reviewAction = new \View\Product\ReviewCompactAction();

        return \App::mustache()->render('shop/show/bottombar', [
            'shopProductUrl' => $point->id ? \App::router()->generate('product.category', ['categoryPath' => 'shop', 'f-shop' => $point->id]) : '',
            'products' => array_values(array_map(function(\Model\Product\Entity $product) use($productShowAction, $helper, $cartButtonAction, $reviewAction) {
                return $productShowAction->execute(
                    $helper,
                    $product,
                    null,
                    true,
                    $cartButtonAction,
                    $reviewAction,
                    'product_200'
                );
            }, $products)),
        ]);
    }

    public function slotBodyClassAttribute() {
        return parent::slotBodyClassAttribute() . ' shopPrintPage';
    }
}