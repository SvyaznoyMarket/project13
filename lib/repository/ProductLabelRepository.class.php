<?php

class ProductLabelRepository extends ObjectRepository
{
  public function get(array $ids, $index = null)
  {
    $entities = array();

    if (!count($ids))
    {
      return $entities;
    }

    $q = $this->createQuery('product.label.get', array('id' => $ids));
    $entities = $this->createList($q->getResult(), $index);

    return $entities;
  }

  public function getAll($index = null)
  {
    $q = $this->createQuery('product.label.get', array());
    $entities = $this->createList($q->getResult(), $index);

    return $entities;
  }

  public function create($data)
  {
    $entity = new ProductLabelEntity();
    $entity->setId($data['id']);
    $entity->setImage($data['media_image']);
    $entity->setName($data['name']);

    return $entity;
  }

  public function getByCategory(ProductLabelCriteria $criteria, $order = null)
  {
    $q = ProductTable::getInstance()->createBaseQuery(array('view' => 'list'))
      ->select('DISTINCT core_label_id')
      ->innerJoin('product.CategoryRelation categoryRelation')
      ->whereIn('categoryRelation.product_category_id', ProductCategoryTable::getInstance()->getDescendatIds($criteria->getCategory()))
      ->setHydrationMode(Doctrine_Core::HYDRATE_SINGLE_SCALAR);

    $result = $q->execute();

    return $this->get(is_array($result) ? $result : array());
  }
}