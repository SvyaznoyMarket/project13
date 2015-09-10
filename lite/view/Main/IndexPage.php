<?php

namespace View\Main;

use View\LiteLayout;

class IndexPage extends LiteLayout
{

    protected $layout = 'layout/main';

    public function __construct() {
        parent::__construct();
        $this->metatags[] = '<meta name="yandex-verification" content="5c7063a53fa016cf" />';
    }

    /** Блок с рекомендациями
     * @return string
     */
    public function blockRecommendations() {

        /**
         * @var $products               \Model\Product\Entity[]
         * @var $personalIds            int[]
         * @var $personalForWalkingIds  int[]
         */

        $return = '';
        $sender = ['name' => 'retailrocket'];

        $products = $this->getParam('productList');
        if (empty($products)) return '';
        $personalIds = @$this->getParam('rrProducts')['personal'];
        $personalForWalkingIds = @$this->getParam('rrProducts')['personal'];
        $names = [];

        // Удаление продуктов с одинаковыми именами из массива персональных рекомендаций
        array_walk ( $personalForWalkingIds , function ($id, $key) use (&$personalIds, &$names, $products) {
            // Имя продукта
            if (!$products[$id] instanceof \Model\Product\Entity) return;
            $currentProductName = trim($products[$id]->getName());
            if (array_search($currentProductName, $names) === false) {
                // Если такого имени нет в массиве имён, то добавляем имя в массив
                $names[$id] = $currentProductName;
            } else {
                // Если такое имя уже есть, то удаляем продукт из массива персональных рекомендаций
                unset($personalIds[$key]);
            }
        } );

        if (!empty($this->getParam('rrProducts')['popular'])) {
            $return .= $this->render('main/_recommendations', [
                'blockname' => 'Мы рекомендуем',
                'class' => 'slidesBox slidesBox-bg2 slidesBox-items slidesBox-items-r',
                'productList' => $this->getParam('productList'),
                'rrProducts' => (array)$personalIds,
                'sender' => $sender + ['position' => 'MainRecommended', 'method' => 'Personal']
            ]);
            $return .= $this->render('main/_recommendations', [
                'blockname' => 'Популярные товары',
                'class' => 'slidesBox slidesBox-items slidesBox-items-l',
                'productList' => $this->getParam('productList'),
                'rrProducts' => (array)@$this->getParam('rrProducts')['popular'],
                'sender' => $sender + ['position' => 'MainPopular', 'method' => 'ItemsToMain']
            ]);
        }

        return $return;
    }

}