<?php

namespace View\Shop;

class ShowPage extends \View\DefaultLayout {
    protected $layout  = 'layout-twoColumn';

    public function slotBodyDataAttribute() {
        return 'shop';
    }

    public function slotContent() {
        /** @var $point \Model\Point\ScmsPoint */
        $point = $this->getParam('point');
        $helper = new \Helper\TemplateHelper();

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
                'productCountText' => $point->productCount ? ($point->productCount . ' ' . $helper->numberChoice($point->productCount, ['товар', 'товара', 'товаров']) . ' можно забрать сегодня') : null,
                'town' => [
                    'names' => [
                        'locativus' => $point->town->names->locativus,
                    ],
                ],
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
        return $this->getParam('sidebar');
    }

    public function slotBodyClassAttribute() {
        return parent::slotBodyClassAttribute() . ' shopPrintPage';
    }
}