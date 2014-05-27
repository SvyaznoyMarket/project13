<?php

namespace EnterSite\Repository\Partial;

use Enter\Http;
use Enter\Util;
use EnterSite\ViewHelperTrait;
use EnterSite\Model;
use EnterSite\Model\Partial;

class ProductFilter {
    use ViewHelperTrait;

    /**
     * @param Model\Product\Filter[] $filterModels
     * @param Model\Product\RequestFilter[] $requestFilterModels
     * @return Partial\ProductFilter[]
     */
    public function getList(
        array $filterModels,
        array $requestFilterModels = []
    ) {
        $viewHelper = $this->getViewHelper();

        $filters = [];

        /** @var Model\Product\RequestFilter[] $requestFilterModelsByName */
        $requestFilterModelsByName = [];
        foreach ($requestFilterModels as $requestFilterModel) {
            $requestFilterModelsByName[$requestFilterModel->name] = $requestFilterModel;
        }

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
                $element = new Partial\ProductFilter\Element();
                $element->title = $optionModel->name;
                $element->name = self::getName($filterModel, $optionModel);
                $element->id = 'id-productFilter-' . $filterModel->token . '-' . $optionModel->id;

                if (isset($requestFilterModelsByName[$element->name])) {
                    $element->value = $requestFilterModelsByName[$element->name]->value;
                    $element->isActive = $requestFilterModelsByName[$element->name]->value == $optionModel->id;
                } else {
                    $element->value = $optionModel->id;
                    $element->isActive = false;
                }

                // максимальное и минимальное значения для слайдера
                if (in_array($filterModel->typeId, [Model\Product\Filter::TYPE_SLIDER, Model\Product\Filter::TYPE_NUMBER])) {
                    $filter->dataValue = $viewHelper->json([
                        'min' => $filterModel->min,
                        'max' => $filterModel->max,
                    ]);
                }

                $filter->elements[] = $element;
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