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

    $orders = $q->getDqlPart('orderby');
    $orderByInstock = array_shift($orders);
    $q->orderBy($orderByInstock);
    $q->addOrderBy('creator.name '.$this->getDirection());
    foreach ($orders as $ob) {
        $q->addOrderBy($ob);
    }
  }

  protected function setQueryForPrice(myDoctrineQuery $q)
  {
    if (!$q->hasAliasDeclaration('price'))
    {
      $q->innerJoin('product.ProductPrice productPrice')
        ->innerJoin('productPrice.PriceList priceList with priceList.is_default=1');
    }
    $orders = $q->getDqlPart('orderby');
    $orderByInstock = array_shift($orders);
    $q->orderBy($orderByInstock);
    $q->addOrderBy('productPrice.price '.$this->getDirection());
    foreach ($orders as $ob) {
        $q->addOrderBy($ob);
    }
  }

  protected function setQueryForRating(myDoctrineQuery $q)
  {
    $orders = $q->getDqlPart('orderby');
    $orderByInstock = array_shift($orders);
    $q->orderBy($orderByInstock);
    $q->addOrderBy('product.rating '.$this->getDirection());
    foreach ($orders as $ob) {
        $q->addOrderBy($ob);
    }
  }
}