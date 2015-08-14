<?php

namespace Model\Banner;

class Repository {
    /** @var \Core\ClientInterface */
    private $client;

    /**
     * @param \Core\ClientInterface $client
     */
    public function __construct(\Core\ClientInterface $client) {
        $this->client = $client;
    }

    /**
     * @param \Model\Region\Entity|null $region
     * @return Entity[]
     */
    public function getCollection(\Model\Region\Entity $region = null) {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        $client = clone $this->client;

        $params = [];
        if ($region) {
            $params['geo_id'] = $region->getId();
        }

        $collection = [];
        $client->addQuery('promo/get', [], [], function ($data) use (&$collection) {
            foreach ($data as $item) {
                $collection[] = new Entity($item);
            }
        });

        $client->execute(\App::config()->coreV2['retryTimeout']['default']);

        return $collection;
    }

    /**
     * @param $callback
     */
    public function prepareCollection($callback) {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $params = [
            'tags' => ['site-web'],
        ];
        \App::scmsClient()->addQuery('api/promo/get', $params, [], $callback);
    }

    /**
     * @param Entity $entity
     * @param \Routing\Router $router
     * @param \Model\Product\Entity[] $productsById
     * @param \Model\Product\Category\Entity[] $categoriesById
     * @throws \Exception
     */
    public function setEntityUrl(Entity $entity, \Routing\Router $router, $productsById = [], $categoriesById = []) {
        $url = $entity->getUrl();
        if (!empty($url)) {
            return;
        }

        try {
            $items = $entity->getItem();
            /** @var $item \Model\Banner\Item\Entity */
            $item = reset($items);
            if (!$item) {
                return;
            }

            if ($item->getProductCategoryId()) {
                /** @var $product \Model\Product\Category\Entity */
                $category = ($item->getProductCategoryId() && isset($categoriesById[$item->getProductCategoryId()]))
                    ? $categoriesById[$item->getProductCategoryId()]
                    : null;
                if (!$category) {
                    throw new \Exception(sprintf('Категория #%s не найдена', $item->getProductCategoryId()));
                }

                $url = $router->generate('product.category', array('categoryPath' => $category->getPath()));
            }
        } catch (\Exception $e) {
            \App::logger()->error($e);
        }

        $entity->setUrl($url);
    }
}