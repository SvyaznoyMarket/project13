<?php

namespace Model\Product\Category;

class Repository {
    /** @var \Core\ClientInterface */
    private $client;

    public function __construct(\Core\ClientInterface $client) {
        $this->client = $client;
    }

    public function getEntityByToken($token) {
        $data = $this->client->query('category/get', array(
            'slug'   => array($token),
            'geo_id' => \App::user()->getRegion()->getId(),
        ));
        $data = (bool)$data ? reset($data) : null;

        return $data ? new Entity($data) : null;
    }

    public function getCollectionById(array $ids) {
        $data = $this->client->query('category/get', array(
            'id'    => $ids,
            'geo_id' => \App::user()->getRegion()->getId(),
        ));

        $collection = array();
        foreach($data as $item){
            $collection[] = new Entity($item);
        }

        return $collection;
    }

    /**
     * @return Entity[]
     */
    public function getRootCollection() {
        $data = $this->client->query('category/tree', array(
            'max_level'       => 1,
            'is_load_parents' => false,
        ));

        $collection = array();
        foreach($data as $item){
            $collection[] = new Entity($item);
        }

        return $collection;
    }

    /**
     * Загружает предков (ancestors) и собственных детей (children) для данной категории
     *
     * @param Entity $entity
     */
    public function loadEntityBranch(Entity $entity) {
        $data = $this->client->query('category/tree', array(
            'root_id'         => $entity->getId(),
            'max_level'       => null,
            'is_load_parents' => true,
            'region_id'       => \App::user()->getRegion()->getId(),
        ));

        $loadBranch = function($data) use(&$loadBranch, $entity) {
            foreach ($data as $item) {
                // если наткнулись на текущую категорию, то закругляемся
                if ($entity->getId() == $item['id']) {
                    // только при загрузке дерева ядро может отдать нам количество товаров в ней
                    $entity->setProductCount($item['product_count']);
                    if (\App::config()->product['globalListEnabled']) {
                        $entity->setGlobalProductCount($item['product_count_global']);
                    }

                    // добавляем дочерние узлы
                    if (isset($item['children']) && (bool)$item['children']) {
                        foreach ($item['children'] as $childData) {
                            $entity->addChild(new Entity($childData));
                        }
                    }

                    return;
                }
            }

            $ancestorData = reset($data);
            $entity->addAncestor(new Entity($ancestorData));

            if (isset($data[0]['children'])) {
                $loadBranch($data[0]['children']);
            }
        };

        $loadBranch($data);
    }
}