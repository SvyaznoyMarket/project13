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
      ->select('DISTINCT product.core_label_id')
      ->innerJoin('product.Category category')
      ->addWhere('category.root_id = ? and category.lft >= ? and category.rgt <= ?', array($criteria->getCategory()->root_id, $criteria->getCategory()->lft, $criteria->getCategory()->rgt, ))
      ->addWhere('product.core_label_id IS NOT NULL')
      ->removeDqlQueryPart('orderby')
      ->setHydrationMode(Doctrine_Core::HYDRATE_SINGLE_SCALAR);

    $result = $q->execute();

    return $this->get(is_array($result) ? $result : array($result));
  }
}