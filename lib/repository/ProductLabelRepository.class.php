<?php

class ProductLabelRepository extends ObjectRepository
{
  public function get(array $ids, $index = null)
  {
    $entities = array();

    if (!count($ids)) {
      return $entities;
    }

    $q = new CoreQuery('product.label.get', array('id' => $ids));

    return $this->createList($q->getResult(), $index);
  }

  public function getAll($index = null)
  {
    $q = new CoreQuery('product.label.get', array());

    return $this->createList($q->getResult(), $index);
  }

  public function create($data)
  {
    return new ProductLabelEntity($data);
  }

  public function getByCategory(ProductLabelCriteria $criteria, $order = null)
  {
    $q = ProductTable::getInstance()->createBaseQuery(array('view' => 'list'))
      ->select('DISTINCT product.core_label_id')
      ->innerJoin('product.Category category')
      ->addWhere('category.root_id = ? and category.lft >= ? and category.rgt <= ?', array($criteria->getCategory()->root_id, $criteria->getCategory()->lft, $criteria->getCategory()->rgt,))
      ->addWhere('product.core_label_id IS NOT NULL')
      ->removeDqlQueryPart('orderby')
      ->setHydrationMode(Doctrine_Core::HYDRATE_SINGLE_SCALAR);

    $result = $q->execute();

    return $this->get(is_array($result) ? $result : array($result));
  }
}