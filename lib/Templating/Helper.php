<?php

namespace Templating;

use Model\Product\Filter\Entity as FilterEntity;

class Helper {
    /**
     * @param array $replaces
     * @param array $excluded
     * @param null $route
     * @return mixed
     * @throws \RuntimeException
     */
    public function replacedUrl(array $replaces, array $excluded = null, $route = null) {
        $request = \App::request();

        if (null == $route) {
            if (!$request->attributes->has('route')) {
                throw new \RuntimeException('В атрибутах запроса не задан параметр "route".');
            }

            $route = $request->attributes->get('route');
        }

        $excluded = (null == $excluded) ? ['page' => '1'] : $excluded;

        $params = [];
        foreach (array_diff(array_keys($request->attributes->all()), ['pattern', 'method', 'action', 'route', 'require']) as $k) {
            $params[$k] = $request->attributes->get($k);
        }
        foreach ($request->query->all() as $k => $v) {
            $params[$k] = $v;
        }
        foreach ($replaces as $k => $v) {
            if (null === $v) {
                if (isset($params[$k])) unset($params[$k]);
                continue;
            }

            $params[$k] = $v;
        }

        $params = array_diff_assoc($params, $excluded);

        return \App::router()->generate($route, $params);
    }

    /**
     * @param int   $number  Например: 1, 43, 112
     * @param array $choices Например: ['отзыв', 'отзыва', 'отзывов']
     * @return mixed
     */
    public function numberChoice($number, array $choices) {
        $cases = [2, 0, 1, 1, 1, 2];

        return $choices[ ($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
    }

    /**
     * @param $price
     * @return string
     */
    public function formatPrice($price, $numDecimals = 0, $decimalsDelimiter = ',', $thousandsDelimiter = ' ') {
        return number_format($price, $numDecimals, $decimalsDelimiter, $thousandsDelimiter);
    }

    /**
     * @param $category
     * @param $productFilter
     * @return string
     */
    public function getFilterItemAllLink($category, $productFilter, $filter) {
        $allLink = $category->getLink();
        foreach ($productFilter->dump() as $filterItem) {
            if(!in_array($filterItem[0], [$filter->getId(), 'is_view_list', 'category', 'is_model'])) {
                $allLink .= preg_match('/.*\?.*/', $allLink) ? '&' : '?';
                $allLink .= urlencode('f['.$filterItem[0].'][]').'='.reset($filterItem[2]);
            }
        }

        return $allLink;
    }

    /**
     * @param $allLink
     * @param $option
     * @param $filter
     * @return string
     */
    public function getFilterItemOptionLink($allLink, $option, $filter) {
        $id = $option->getId();
        $optionLink = preg_match('/.*\?.*/', $allLink) ? $allLink.'&' : $allLink.'?';

        switch ($filter->getTypeId()) {
            case FilterEntity::TYPE_NUMBER:
            case FilterEntity::TYPE_SLIDER:
                if (!isset($values['to'])) {
                    $values['to'] = null;
                }
                if (!isset($values['from'])) {
                    $values['from'] = null;
                }
                if ($filter->getMax() != $values['to'] || $filter->getMin() != $values['from']) {
                    $optionLink .= urlencode('f['.strtolower($filter->getId()).'][from]').'='.$values['from'];
                    $optionLink .= urlencode('f['.strtolower($filter->getId()).'][to]').'='.$values['to'];
                }
                break;
            default:
                $optionLink .= urlencode('f['.strtolower($filter->getId()).'][]').'='.$id;
                break;
        }

        return $optionLink;
    }

}
