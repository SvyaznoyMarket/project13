<?php

namespace View\Partial\ProductCategory\RootPage;

use Helper\TemplateHelper;
use Templating\Helper;

class Brands {
    /**
     * @return array
     */
    public function execute(\Model\Product\Filter $productFilter) {
        $mainBrands = [];
        $otherBrands = [];
        $selectedBrandsCount = 0;
        $selectedOtherBrandsCount = 0;
        $num = 0;

        foreach ($productFilter->getFilterCollection() as $property) {
            if ('brand' === $property->getId()) {
                $optionsCount = count($property->getOption());
                foreach ($property->getOption() as $option) {
                    $num++;

                    $active = \App::request()->query->get(\View\Name::productCategoryFilter($property, $option)) == $option->getId();

                    if ($active) {
                        $selectedBrandsCount++;
                    }

                    if ($num <= 6 || $optionsCount == 7) {
                        $brands = &$mainBrands;
                    } else {
                        $brands = &$otherBrands;

                        if ($active) {
                            $selectedOtherBrandsCount++;
                        }
                    }

                    $brands[] = [
                        'name' => $option->getName(),
                        'imageUrl' => $option->getImageUrl(),
                        'active' => $active,
                        'paramName' => \View\Name::productCategoryFilter($property, $option),
                        'paramValue' => $option->getId(),
                        'url' => \App::router()->generate('product.category', ['categoryPath' => \App::request()->attributes->get('categoryPath')] + [\View\Name::productCategoryFilter($property, $option) => $option->getId()]),
                    ];
                }

                break;
            }
        }

        $helper = new TemplateHelper();
        return [
            'mainBrands' => $mainBrands,
            'otherBrands' => $otherBrands,
            'otherBrandsCount' => count($otherBrands),
            'showOtherBrandsText' => 'Ещё ' . count($otherBrands) . ' ' . $helper->numberChoice(count($otherBrands), ['бренд', 'бренда', 'брендов']),
            'brandsCount' => count($mainBrands) + count($otherBrands),
            'selectedBrandsCount' => $selectedBrandsCount,
            'selectedOtherBrandsCount' => $selectedOtherBrandsCount,
        ];
    }
}