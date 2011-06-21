<?php

class myProductSorting extends myBaseSorting
{
  protected
    $active = 'creator'
  ;

  public function getDefaults()
  {
    return array(
      'creator' => array(
        'name'      => 'creator',
        'title'     => 'по бренду',
        'direction' => 'asc',
      ),
      'price'   => array(
        'name'      => 'price',
        'title'     => 'по цене',
        'direction' => 'asc',
      ),
      'rating'  => array(
        'name'      => 'rating',
        'title'     => 'по рейтингу',
        'direction' => 'asc',
      ),
    );
  }

  protected function setQueryForCreator(myDoctrineQuery $q)
  {
    if (!$q->hasAliasDeclaration('creator'))
    {
      $q->leftJoin('product.Creator creator');
    }

    $q->addOrderBy('creator.name '.$this->getDirection());
  }

  protected function setQueryForPrice(myDoctrineQuery $q)
  {
    $q->addOrderBy('product.price '.$this->getDirection());
  }

  protected function setQueryForRating(myDoctrineQuery $q)
  {
    $q->addOrderBy('product.rating '.$this->getDirection());
  }
}