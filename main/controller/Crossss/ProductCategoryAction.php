<?php
namespace Controller\Crossss;

class ProductCategoryAction {
    /**
     * @param string        $categoryPath
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception\NotFoundException
     */
    public function recommended($categoryPath, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__, ['action', 'crossss']);

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        $curl = \App::curl();

        $categoryToken = explode('/', $categoryPath);
        $categoryToken = end($categoryToken);

        try {
            $region = \Controller\ProductCategory\Action::isGlobal() ? null : \App::user()->getRegion();

            $category = \RepositoryManager::productCategory()->getEntityByToken($categoryToken);
            if (!$category) {
                throw new \Exception\NotFoundException(sprintf('Категория товара @%s не найдена.', $categoryToken));
            }

            $result = $curl->query(\App::config()->crossss['apiUrl'] . '?' . http_build_query([
                'apikey'          => \App::config()->crossss['apiKey'],
                'userid'          => \App::user()->getEntity() ? \App::user()->getEntity()->getId() : null,
                'sessionid'       => session_id(),
                'categoryid'      => $category->getId(),
                'actiontime'      => time(),
            ]), [], \App::config()->crossss['timeout']);
            \App::logger()->debug(json_encode($result, JSON_UNESCAPED_UNICODE), ['crossss']);

            $title = !empty($result['title']) ? $result['title'] : 'Популярные товары';

            $ids = isset($result['recommendeditems']) ? (array)$result['recommendeditems'] : [];
            //$ids = [64731,64758,79353,84681,92316,79434,83844,91203,85422,85434,63945,45790,4033,16044,44156,80641,17448,14054,89155,54180,75376,88810,89344,73070,41947,41229,41120,57061,39185,41970,16232,26260,59451,734,31280,42162,17766,57702,35285,18779,29566,18034,31622,27771,34345,9582,16104,5242,9463,2815,57823,60950,66043,60966];
            if (!(bool)$ids) {
                throw new \Exception(sprintf('Для категории @%s не получены рекоммендации от crossss', $category->getToken()));
            }

            /** @var \Model\Product\CompactEntity[] $products */
            $products = [];
            \RepositoryManager::product()->prepareCollectionById($ids, $region,
                function($data) use (&$products) {
                    foreach ($data as $item) {
                        $products[] = new \Model\Product\CompactEntity($item);
                    }
                },
                function (\Exception $e) {
                    \App::exception()->remove($e);
                }
            );
            \App::coreClientV2()->execute();

            $return = [];
            foreach ($products as $product) {
                if (!$product->getIsBuyable()) continue;

                $return[] = [
                    'id'     => $product->getId(),
                    'name'   => $product->getName(),
                    'image'  => $product->getImageUrl(),
                    'rating' => $product->getRating(),
                    'link'   => $product->getLink(),
                    'price'  => $product->getPrice(),
                ];
            }
            if (!count($return)) {
                throw new \Exception();
            }

            return new \Http\JsonResponse($return);
        } catch (\Exception $e) {
            \App::exception()->remove($e);
            \App::logger()->error($e, ['crossss']);
        }

        return new \Http\JsonResponse();
    }
}