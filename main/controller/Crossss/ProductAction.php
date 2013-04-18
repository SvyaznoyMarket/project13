<?php
namespace Controller\Crossss;

class ProductAction {
    /**
     * @param \Http\Request $request
     * @param int           $productId
     * @throws \Exception
     * @return \Http\JsonResponse
     */
    public function recommended(\Http\Request $request, $productId) {
        \App::logger()->debug('Exec ' . __METHOD__, ['action', 'crossss']);

        $curl = \App::curl();

        try {
            $product = \RepositoryManager::product()->getEntityById($productId);
            if (!$product) {
                throw new \Exception(sprintf('Товар #%s не найден', $productId));
            }

            $curl->query(\App::config()->crossss['apiUrl'] . '?' . http_build_query([
                'sessionid'       => session_id(),
                'itemid'          => $product->getId(),
                'itemdescription' => $product->getName(),
                'itemurl'         => \App::router()->generate('product', ['productPath' => $product->getPath()], true),
                'actiontime'      => time(),
                'itemtype'        => $product->getMainCategory() ? $product->getMainCategory()->getId() : null,
                'userid'          => \App::user()->getEntity() ? \App::user()->getEntity()->getId() : null,
            ]));

            //return new \Http\JsonResponse(['success' => true]);
        }
        catch (\Exception $e) {
            \App::logger()->error($e, ['crossss']);

            //return new \Http\JsonResponse(['success' => false, 'error' => \App::config()->debug ? $e->getMessage() : 'Ошибка']);
        }
    }
}