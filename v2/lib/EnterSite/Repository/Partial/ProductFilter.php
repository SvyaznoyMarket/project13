<?php

namespace EnterSite\Repository\Partial;

use Enter\Http;
use Enter\Util;
use EnterSite\RouterTrait;
use EnterSite\UrlHelperTrait;
use EnterSite\ViewHelperTrait;
use EnterSite\Routing;
use EnterSite\Model;
use EnterSite\Model\Partial;

class ProductFilter {
    use RouterTrait, UrlHelperTrait, ViewHelperTrait;

    /**
     * @param Model\Product\Filter[] $filterModels
     * @param Model\Product\RequestFilter[] $requestFilterModels
     * @param bool $isOpened
     * @return Partial\ProductFilter[]
     */
    public function getList(
        array $filterModels,
        array $requestFilterModels = [],
        $isOpened = false
    ) {
        $viewHelper = $this->getViewHelper();

        $filters = [];

        $openedFilterTokens = [
            'price',
        ];

        /** @var Model\Product\RequestFilter[] $requestFilterModelsByName */
        $requestFilterModelsByName = [];
        foreach ($requestFilterModels as $requestFilterModel) {
            $requestFilterModelsByName[$requestFilterModel->name] = $requestFilterModel;
        }

        foreach ($filterModels as $filterModel) {
            if (
                ($isOpened && !in_array($filterModel->token, $openedFilterTokens))
                || (!$isOpened && in_array($filterModel->token, $openedFilterTokens))
            ) {
                continue;
            }

            $filter = new Partial\ProductFilter();
            $filter->token = $filterModel->token;
            $filter->name = $filterModel->name;
            $filter->unit = $filterModel->unit;

            $filter->isSliderType = false;
            $filter->isListType = false;
            $filter->isHiddenType = false;
            if (in_array($filterModel->typeId, [Model\Product\Filter::TYPE_NUMBER, Model\Product\Filter::TYPE_SLIDER])) {
                $filter->isSliderType = true;
            } else if (in_array($filterModel->typeId, [Model\Product\Filter::TYPE_LIST, Model\Product\Filter::TYPE_BOOLEAN])) {
                $filter->isListType = true;
            } else if ('q' === $filter->token) {
                $filter->isHiddenType = true;
            }

            $filter->isMultiple = $filterModel->isMultiple;
            $filter->isOpened = $isOpened;
            $filter->isPrice = 'price' == $filterModel->token;

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
                        'min'  => $filterModel->min,
                        'max'  => $filterModel->max,
                        'step' => $filterModel->step,
                    ]);

                    $element->minValue = $filterModel->min;
                    $element->maxValue = $filterModel->max;
                }

                $filter->elements[] = $element;
            }

            $filters[] = $filter;
        }

        return $filters;
    }

    /**
     * @param Model\Product\Filter[] $filterModels
     * @param Model\Product\RequestFilter[] $requestFilterModels
     * @param Routing\Route|null $route
     * @param Http\Request|null $httpRequest
     * @return Partial\ProductFilter[]
     */
    public function getSelectedList(
        array $filterModels,
        array $requestFilterModels = [],
        Routing\Route $route = null,
        Http\Request $httpRequest = null
    ) {
        $router = $this->getRouter();
        $urlHelper = $this->getUrlHelper();

        /** @var Partial\ProductFilter[] $filtersByToken */
        $filtersByToken = [];

        // TODO: оптимизировать
        if ((bool)$requestFilterModels) {
            /** @var Model\Product\Filter[] $filterModelsByToken */
            $filterModelsByToken = [];
            foreach ($filterModels as $filterModel) {
                $filterModelsByToken[$filterModel->token] = $filterModel;
            }

            foreach ($requestFilterModels as $requestFilterModel) {
                $filterModel = isset($filterModelsByToken[$requestFilterModel->token]) ? $filterModelsByToken[$requestFilterModel->token] : null;
                if (!$filterModel) {
                    continue;
                }

                $isSlider = in_array($filterModel->typeId, [Model\Product\Filter::TYPE_SLIDER, Model\Product\Filter::TYPE_NUMBER]);

                if (!isset($filtersByToken[$requestFilterModel->token])) {
                    $filter = new Partial\ProductFilter();
                    $filter->token = $filterModel->token;
                    $filter->name = $filterModel->name;

                    $filtersByToken[$requestFilterModel->token] = $filter;
                }
                $filter = $filtersByToken[$requestFilterModel->token];

                foreach ($filterModel->option as $optionModel) {
                    // Если фильтр - слайдер, то сравниваем по optionToken, иначе - по value
                    if (
                        ($optionModel->id == $requestFilterModel->value)
                        || ($isSlider && ($requestFilterModel->optionToken == $optionModel->token))
                    ) {
                        $element = new Partial\ProductFilter\Element();

                        if ($isSlider) {
                            // Игнорирование фильтра с мин или макс значением
                            if (($filterModel->min == $requestFilterModel->value) || ($filterModel->max == $requestFilterModel->value)) continue;

                            if ('price' == $filterModel->token) {
                                $element->title = $optionModel->name . ' ' . number_format((float)$requestFilterModel->value, 0, ',', ' ') . 'р';
                            } else {
                                $element->title = $optionModel->name . ' ' . $requestFilterModel->value . ($filterModel->unit ? $filterModel->unit : '');
                            }
                        } else {
                            $element->title = $optionModel->name;
                        }

                        $element->name = self::getName($filterModel, $optionModel);
                        if ($httpRequest && $route) {
                            $element->deleteUrl = $router->getUrlByRoute($route, $urlHelper->replace($route, $httpRequest, [
                                $element->name => null,
                            ]));
                        }

                        $filter->elements[] = $element;
                        break;
                    }
                }
            }
        }

        // удаление пустых фильтров
        foreach ($filtersByToken as $i => $filter) {
            if (!(bool)$filter->elements) {
                unset($filtersByToken[$i]);
                continue;
            }

            // TODO: сортировка элементов slider-фильтра: сначала from, потом to
        }

        return array_values($filtersByToken);
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
            case Model\Product\Filter::TYPE_STRING:
                return $filter->token;
            default:
                return 'f-' . $filter->token;
        }
    }
}