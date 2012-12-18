<?php

namespace Model\Product\Category;

class Repository {
    /** @var \Core\ClientInterface */
    private $client;
    /** @var string */
    private $entityClass = '\Model\Product\Category\Entity';

    /**
     * @param \Core\ClientInterface $client
     */
    public function __construct(\Core\ClientInterface $client) {
        $this->client = $client;
    }

    /**
     * @param string $class
     */
    public function setEntityClass($class) {
        $this->entityClass = $class;
    }

    /**
     * @param string $token
     * @return Entity|null
     */
    public function getEntityByToken($token) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        $data = $this->client->query('category/get', array(
            'slug'   => array($token),
            'geo_id' => \App::user()->getRegion()->getId(),
        ));
        $data = (bool)$data ? reset($data) : null;

        return $data ? new $this->entityClass($data) : null;
    }

    /**
     * @param string               $token
     * @param \Model\Region\Entity $region
     * @param                      $callback
     */
    public function prepareEntityByToken($token, \Model\Region\Entity $region = null, $callback) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        $params = array(
            'slug' => array($token),
        );
        if ($region instanceof \Model\Region\Entity) {
            $params['geo_id'] = $region->getId();
        }

        $this->client->addQuery('category/get', $params, array(), $callback);
    }

    /**
     * @param int $id
     * @return Entity|null
     */
    public function getEntityById($id) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        $data = $this->client->query('category/get', array(
            'id'     => array($id),
            'geo_id' => \App::user()->getRegion()->getId(),
        ));
        $data = (bool)$data ? reset($data) : null;

        return $data ? new $this->entityClass($data) : null;
    }

    /**
     * @param array $ids
     * @return Entity[]
     */
    public function getCollectionById(array $ids) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        $data = $this->client->query('category/get', array(
            'id'    => $ids,
            'geo_id' => \App::user()->getRegion()->getId(),
        ));

        $collection = array();
        foreach($data as $item){
            $collection[] = new $this->entityClass($item);
        }

        return $collection;
    }

    /**
     * @param array                $ids
     * @param \Model\Region\Entity $region
     * @param                      $done
     * @param                      $fail
     */
    public function prepareCollectionById(array $ids, \Model\Region\Entity $region = null, $done, $fail = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        if (!(bool)$ids) return;

        $params = array(
            'id' => $ids,
        );
        if ($region instanceof \Model\Region\Entity) {
            $params['geo_id'] = $region->getId();
        }
        $this->client->addQuery('category/get', $params, array(), $done, $fail);
    }

    /**
     * @return Entity[]
     */
    public function getRootCollection() {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        // TODO: добавить регион
        $data = $this->client->query('category/tree', array(
            'max_level'       => 1,
            'is_load_parents' => false,
        ));

        $collection = array();
        foreach($data as $item){
            $collection[] = new $this->entityClass($item);
        }

        return $collection;
    }

    /**
     * @param \Model\Region\Entity $region
     * @param                      $callback
     */
    public function prepareRootCollection(\Model\Region\Entity $region = null, $callback) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        $params = array(
            'max_level'       => 1,
            'is_load_parents' => false,
        );
        if ($region instanceof \Model\Region\Entity) {
            $params['region_id'] = $region->getId();
        }

        $this->client->addQuery('category/tree', $params, array(), $callback);
    }

    /**
     * @param int $maxLevel
     * @return Entity[]
     */
    public function getTreeCollection(\Model\Region\Entity $region = null, $maxLevel = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        $params = array(
            'is_load_parents' => false,
        );
        if (null !== $maxLevel) {
            $params['max_level'] = $maxLevel;
        }
        if ($region instanceof \Model\Region\Entity) {
            $params['region_id'] = $region->getId();
        }
        $data = $this->client->query('category/tree', $params);

        $collection = array();
        foreach($data as $item){
            $collection[] = new $this->entityClass($item);
        }

        return $collection;
    }

    /**
     * @param Entity               $category
     * @param \Model\Region\Entity $region
     */
    public function prepareEntityBranch(Entity $category, \Model\Region\Entity $region = null) {
        $params = array(
            'root_id'         => $category->getHasChild() ? $category->getId() : $category->getParentId(),
            'max_level'       => 5,
            'is_load_parents' => true,
        );
        if ($region) {
            $params['region_id'] = $region->getId();
        }
        $this->client->addQuery('category/tree', $params, array(), function($data) use (&$category, &$region) {
            /**
             * Загрузка дочерних и родительских узлов категории
             *
             * @param \Model\Product\Category\Entity $category
             * @param array $data
             * @use \Model\Region\Entity $region
             */
            $loadBranch = function(\Model\Product\Category\Entity $category, array $data) use (&$region) {
                // только при загрузке дерева ядро может отдать нам количество товаров в ней
                if ($region && isset($data['product_count'])) {
                    $category->setProductCount($data['product_count']);
                }
                if (\App::config()->product['globalListEnabled'] && isset($data['product_count_global'])) {
                    $category->setGlobalProductCount($data['product_count_global']);
                }

                // добавляем дочерние узлы
                if (isset($data['children']) && is_array($data['children'])) {
                    foreach ($data['children'] as $childData) {
                        $category->addChild(new \Model\Product\Category\Entity($childData));
                    }
                }
            };

            /**
             * Перебор дерева категорий на данном уровне
             *
             * @param $data
             * @use $iterateLevel
             * @use $loadBranch
             * @use $category     Текущая категория каталога
             */
            $iterateLevel = function($data) use(&$iterateLevel, &$loadBranch, $category) {
                $item = reset($data);
                if (!(bool)$item) return;

                $level = (int)$item['level'];
                if ($level < $category->getLevel()) {
                    // если текущий уровень меньше уровня категории, загружаем данные для предков и прямого родителя категории
                    $ancestor = new \Model\Product\Category\Entity($item);
                    if (1 == ($category->getLevel() - $level)) {
                        $loadBranch($ancestor, $item);
                        $category->setParent($ancestor);
                    }
                    $category->addAncestor($ancestor);
                } else if ($level == $category->getLevel()) {
                    // если текущий уровень равен уровню категории, пробуем найти данные для категории
                    foreach ($data as $item) {
                        // ура, наконец-то наткнулись на текущую категорию
                        if ($item['id'] == $category->getId()) {
                            $loadBranch($category, $item);
                            return;
                        }
                    }
                }

                $item = reset($data);
                if (isset($item['children'])) {
                    $iterateLevel($item['children']);
                }
            };

            $iterateLevel($data);
        });
    }
}