<?php

namespace Controller\ProductCategory;

class MainMenuAction {
    const ITEMS_PER_COLUMN = 4;

    /**
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception\NotFoundException
     */
    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        $repository = \RepositoryManager::getProductCategory();
        $repository->setEntityClass('\Model\Product\Category\BasicEntity');

        try {
            $categories = $repository->getTreeCollection(\App::user()->getRegion(), 3);
        } catch (\Exception $e) {
            \App::$exception = $e;
            \App::logger()->error($e);

            $categories = array();
        }

        $columnsByCategory = array();
        foreach ($categories as $category) {
            /** @var $category \Model\Product\Category\BasicEntity */

            $weightsByCategory = array();
            $totalWeight = 0;
            foreach ($category->getChild() as $child) {
                $weight = $this->getCategoryWeight($child);

                $totalWeight += $weight;
                $weightsByCategory[$child->getId()] = $weight;
            }

            if ((bool)$weightsByCategory) {
                $averageWeight = floor($totalWeight / self::ITEMS_PER_COLUMN);
                $maxWeight = max($weightsByCategory);
                if ($maxWeight < $averageWeight) {
                    $averageWeight = $maxWeight;
                }
            } else {
                $averageWeight = 0;
            }

            $columnWeight = 1;
            $column = 0;
            foreach ($category->getChild() as $child) {
                $columnsByCategory[$child->getId()] = $column;

                $columnWeight += $weightsByCategory[$child->getId()];
                if (($columnWeight > $averageWeight) && ($column < self::ITEMS_PER_COLUMN)) {
                    $columnWeight = 0;
                    $column++;
                }
            }
        }
        //var_dump($columnsByParentCategory); exit();

        $content = \App::templating()->render('product-category/_mainMenu', array(
            'page'               => new \View\Layout(),
            'categories'         => $categories,
            'columnsByCategory'  => $columnsByCategory,
        ));

        return new \Http\Response($content);
    }

    /**
     * @param \Model\Product\Category\BasicEntity $category
     * @return int
     */
    private function getCategoryWeight(\Model\Product\Category\BasicEntity $category) {
        $weight = 1;

        if (2 == $category->getLevel()) {
            $weight += 2;
            if (mb_strlen($category->getName()) > 24) {
                $weight += 2; // переползает на 2 строки
            }
            $weight += count($category->getChild());
        } else if (3 == $category->getLevel()) {
            if (mb_strlen($category->getName()) > 40) {
                $weight += 1; // переползает на 2 строки
            }
        }

        return $weight;
    }
}