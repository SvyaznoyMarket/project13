<?php

namespace View\Partial\ProductCategory\RootPage;

use Helper\TemplateHelper;
use Model\Product\Filter\Option\Entity;

class Brands {
    /**
     * @return array
     */
    public function execute(\Model\Product\Filter $productFilter) {
        $mainBrands = [];
        $otherBrands = [];
        $selectedBrandsCount = 0;
        $num = 0;

        foreach ($productFilter->getFilterCollection() as $property) {
            if ('Бренд' === $property->getName()) {
                $this->sortOptionsByQuantity($property);

                foreach ($property->getOption() as $option) {
                    $num++;

                    if ($num <= 6) {
                        $brands = &$mainBrands;
                    } else {
                        $brands = &$otherBrands;
                    }

                    $active = \App::request()->query->get(\View\Name::productCategoryFilter($property, $option)) == $option->getId();

                    if ($active) {
                        $selectedBrandsCount++;
                    }

                    $brands[] = [
                        'name' => $option->getName(),
                        'imageUrl' => $option->getImageUrl(),
                        'active' => $active,
                        'url' => '?' . urlencode(\View\Name::productCategoryFilter($property, $option)) . '=' . urlencode($option->getId()),
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
        ];
    }

    private function sortOptionsByQuantity(\Model\Product\Filter\Entity $property) {
        $options = $property->getOption();

        usort($options, function(Entity $a, Entity $b) {
            if ($a->getQuantity() == $b->getQuantity()) {
                return 0;
            }

            return ($a->getQuantity() > $b->getQuantity()) ? -1 : 1;
        });

        $property->setOption($options);
    }
}