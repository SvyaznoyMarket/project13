<?php

namespace Controller\Product;

/**
 * Class AccessoryAction
 * @package Controller\Product
 * @deprecated
 */
class AccessoryAction {

    CONST NUM_RELATED_ON_PAGE = 4;

    /**
     * @param \Http\Request $request
     * @param string $productToken
     * @return \Http\JsonResponse
     */
    public function execute(\Http\Request $request, $productToken) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $page = $request->get('page', 1);

        $product = \RepositoryManager::product()->getEntityByToken($productToken);
        if (!$product)
            return new \Http\JsonResponse(array('success' => false, 'data' => 'Не найден товар ' . $productToken));

        $begin = self::NUM_RELATED_ON_PAGE * ($page - 1);
        $limit = $page == 1 ? self::NUM_RELATED_ON_PAGE * 2 : self::NUM_RELATED_ON_PAGE;

        $categoryToken = $request->get('categoryToken', 1);

        // фильтруем аксессуары для всех табов кроме "популярные"
        if(!empty($categoryToken)) {
            $accessoryItems = [];
            // фильтруем аксессуары согласно разрешенным в json категориям
            // и получаем аксессуары, сгруппированные по категориям
            $accessoriesGrouped = \Model\Product\Repository::filterAccessoryId($product, $accessoryItems, $categoryToken);

            if(!isset($accessoriesGrouped[$categoryToken]))
                return new \Http\JsonResponse(array('success' => false, 'data' => 'Не найдена категория ' . $categoryToken));

            $product->setAccessoryId(array_map(function($accessory){
                return $accessory->getId();
            }, $accessoriesGrouped[$categoryToken]['accessories']));
        }

        $accessoryIdList = array_slice($product->getAccessoryId(), $begin, $limit);
        $accessoryProductList = \RepositoryManager::product()->getCollectionById($accessoryIdList);

        $response = " ";
        $begin++;
        foreach ($accessoryProductList as $accessory) {
            $response .= \App::templating()->render('product/show/_extra_compact', array(
                'page'   => new \View\Layout(),
                'product'   => $accessory,
                'totalPages'   => (int)ceil(count($product->getAccessoryId()) / self::NUM_RELATED_ON_PAGE),
                'totalProducts'   => count($product->getAccessoryId()),
                'categoryToken'   => empty($categoryToken) ? '' : $categoryToken,
                'isHidden'  => false,
                'gaEvent'   => 'SmartEngine',
            ));
            $begin++;
        }
        return new \Http\Response($response);

    }
}