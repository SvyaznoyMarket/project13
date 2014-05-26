<?php

namespace EnterSite\Repository\Partial;

use Enter\Http;
use Enter\Util;
use EnterSite\Model;
use EnterSite\Model\Partial;

class ProductFilter {
    /**
     * @param Model\Product\Filter[] $filterModels
     * @param Model\Product\RequestFilter[] $requestFilterModels
     * @return Partial\ProductFilter[]
     */
    public function getList(
        array $filterModels,
        array $requestFilterModels = null
    ) {
        $filters = [];

        foreach ($filterModels as $filterModel) {
            $filter = new Partial\ProductFilter();
            $filter->name = $filterModel->name;
            $filter->isSliderType = in_array($filterModel->typeId, [Model\Product\Filter::TYPE_NUMBER, Model\Product\Filter::TYPE_SLIDER]);
            $filter->isListType = in_array($filterModel->typeId, [Model\Product\Filter::TYPE_LIST, Model\Product\Filter::TYPE_BOOLEAN]);
            $filter->isMultiple = $filterModel->isMultiple;

            if (($filterModel->typeId == Model\Product\Filter::TYPE_BOOLEAN) && !(bool)$filterModel->option) {
                foreach ([1 => 'да', 0 => 'нет'] as $id => $name) {
                    $optionModel = new Model\Product\Filter\Option();
                    $optionModel->id = $id;
                    $optionModel->token = $id;
                    $optionModel->name = $name;

                    $filterModel->option[] = $optionModel;
                }
            } else if (in_array($filterModel->typeId, [Model\Product\Filter::TYPE_SLIDER, Model\Product\Filter::TYPE_NUMBER])) {
                foreach ([$filterModel->min => 'from', $filterModel->max => 'to'] as $id => $token) {
                    $optionModel = new Model\Product\Filter\Option();
                    $optionModel->id = $id;
                    $optionModel->token = $token;

                    $filterModel->option[] = $optionModel;
                }
            }

            foreach ($filterModel->option as $optionModel) {
                $option = new Partial\ProductFilter\Element();
                $option->title = $optionModel->name;
                $option->name = self::getName($filterModel, $optionModel);
                $option->value = $optionModel->id;
                $option->id = 'id-productFilter-' . $filterModel->token . '-' . $optionModel->id;

                $filter->elements[] = $option;
            }

            $filters[] = $filter;
        }

        return $filters;
    }

    /**
     * @param Model\Product\Filter $filter
     * @param Model\Product\Filter\Option $option
     * @return string
     */
    public static function getName(Model\Product\Filter $filter, Model\Product\Filter\Option $option) {
        switch ($filter->typeId) {
            case Model\Product\Filter::TYPE_SLIDER:
            case Model\Product\Filter::TYPE_NUMBER:
            case Model\Product\Filter::TYPE_BOOLEAN:
                return 'f-' . $filter->token . ('-' . $option->token);
            case Model\Product\Filter::TYPE_LIST:
                return in_array($filter->token, ['shop', 'category'])
                    ? $filter->token
                    : ('label' === $filter->token && ('instore' === $option->token)
                        ? $option->token
                        : ('f-'
                            . $filter->token
                            . ($filter->isMultiple ? ('-' . Util\String::slugify($option->name)) : '')
                        ));
            default:
                return 'f-' . $filter->token;
        }
    }
}