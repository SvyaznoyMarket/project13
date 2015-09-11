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

        if ($point->wayWalk || $point->wayAuto) {
            if ($point->wayWalk && !$point->wayAuto) {
                $commonWay = $point->wayWalk;
            } else if (!$point->wayWalk && $point->wayAuto) {
                $commonWay = $point->wayAuto;
            } else {
                $commonWay = '';
            }

            $way = [
                'common' => $commonWay,
                'walk' => $point->wayWalk,
                'auto' => $point->wayAuto,
            ];
        } else {
            $way = [];
        }

        return \App::mustache()->render('shop/show/content', [
            'backUrl' => \App::router()->generate('shop'),
            'point' => [
                'address' => $point->address,
                'showMap' => $point->latitude && $point->longitude,
                'latitude' => $point->latitude,
                'longitude' => $point->longitude,
                'workingTime' => $point->workingTime,
                'phone' => $point->phone,
                'name' => $point->description,
                'way' => $way,
                'town' => [
                    'names' => [
                        'locativus' => $point->town->names->locativus,
                    ],
                ],
                'subway' => [
                    'name' => $point->subway->getName(),
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