<?php

class ProductLabelRepository extends BaseRepository
{
  public function get(array $ids, $index = null)
  {
    $entities = array();

    if (empty($ids))
    {
      return $entities;
    }

    $indexAccessor = $index ? 'get'.ucfirst($index) : null;

    $q = $this->createQuery('product.label.get', array('id' => $ids));
    foreach ($q->getResult() as $data)
    {
      $entity = new ProductLabelEntity();
      $entity->setId($data['id']);
      $entity->setImage($data['media_image']);
      $entity->setName($data['name']);

      if ($indexAccessor)
      {
        $entities[$entity->$indexAccessor()] = $entity;

      }
      else {
        $entities[] = $entity;
      }

    }

    return $entities;
  }
}