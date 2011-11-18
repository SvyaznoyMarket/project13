<?php

class myProductSorting extends myBaseSorting
{
  protected $active = 'score'; // - это ключ массива getDefaults()

  public function getDefaults()
  {
    return array(
      'price_asc'   => array(
        'name'      => 'price',
        'title'     => 'по цене (сначала самые дешевые)',
        'direction' => 'asc',
      ),
      'price_desc'   => array(
        'name'      => 'price',
        'title'     => 'по цене (сначала самые дорогие)',
        'direction' => 'desc',
      ),
      'creator_asc' => array(
        'name'      => 'creator',
        'title'     => 'по производителю (А-Я)',
        'direction' => 'asc',
      ),
      'creator_desc' => array(
        'name'      => 'creator',
        'title'     => 'по производителю (Я-А)',
        'direction' => 'desc',
      ),
      'rating'  => array(
        'name'      => 'rating',
        'title'     => 'по рейтингу',
        'direction' => 'desc',
      ),
      'score'  => array(
        'name'      => 'score',
        'title'     => 'как для своих',
        'direction' => 'desc',
      ),
    );
  }
  
  protected function setQueryForScore(myDoctrineQuery $q)
  {
      
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
    if (!$q->hasAliasDeclaration('productPrice'))
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