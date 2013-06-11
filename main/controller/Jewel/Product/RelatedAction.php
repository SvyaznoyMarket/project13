<?php

namespace Controller\Jewel\Product;

class RelatedAction {

    CONST NUM_RELATED_ON_PAGE = 5;

    /**
     * @param \Http\Request $request
     * @param string $productToken
     * @return \Http\JsonResponse
     */
    public function execute(\Http\Request $request, $productToken) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $page = $request->get('page', 1);

        $product = \RepositoryManager::product()->getEntityByToken($productToken);
        if (!$product)
            return new \Http\JsonResponse(array('success' => false, 'data' => 'Не найден товар ' . $productToken));

        $begin = self::NUM_RELATED_ON_PAGE * ($page - 1);
        $accessoryIdList = array_slice($product->getRelatedId(), $begin, self::NUM_RELATED_ON_PAGE);
        $accessoryProductList = \RepositoryManager::product()->getCollectionById($accessoryIdList);

        $response = " ";
        $begin++;
        foreach ($accessoryProductList as $accessory) {
            $response .= \App::templating()->render('jewel/product/show/_extra_compact', array(
                'page'   => new \View\Layout(),
                'product'   => $accessory,
                'isHidden'  => false,
                'gaEvent'   => 'SmartEngine',
                'additionalData'    =>  \Kissmetrics\Manager::getProductEvent($accessory, $begin, 'Also Bought'),
            ));
            $begin++;
        }
        return new \Http\Response($response);

    }
}