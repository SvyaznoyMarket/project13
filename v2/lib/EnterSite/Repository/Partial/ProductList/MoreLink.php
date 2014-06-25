<?php

namespace EnterSite\Repository\Partial\ProductList;

use EnterSite\TranslateHelperTrait;
use EnterSite\Routing;
use EnterSite\Repository;
use EnterSite\Model;
use EnterSite\Model\Partial;

class MoreLink {
    public function getObject(
        $pageNum,
        $limit,
        $count
    ) {
        $link = null;

        $rest = ($count - ($pageNum * $limit));
        if ($rest > 0) {
            $link = new Partial\Link();
            $link->widgetId = self::getWidgetId();

            //$link->name = sprintf('Показать еще %s', $rest < $limit ? $rest : $limit);
            $link->name = 'Показать еще';
        }

        return $link;
    }

    /**
     * @return string
     */
    public static function getId() {
        return 'id-productList-moreLink';
    }

    /**
     * @return string
     */
    public static function getWidgetId() {
        return self::getId() . '-widget';
    }
}