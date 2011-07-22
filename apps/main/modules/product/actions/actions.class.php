<?php

/**
 * product actions.
 *
 * @package    enter
 * @subpackage product
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class productActions extends myActions
{
  public function executeChangeProduct()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Url'));

    //use_helper('url');
    $this->product = $this->getRoute()->getObject();
    $new_value = $this->getRequestParameter('value');
    $property_id = $this->getRequestParameter('property');

    $q = ProductTable::getInstance()->createBaseQuery()->addWhere('product.group_id = ?', array($this->product->group_id, ));
    $product_ids = ProductTable::getInstance()->getIdsByQuery($q);
    myDebug::dump($product_ids);
    //$q = ProductPropertyRelationTable::getInstance()->createBaseQuery();
    $q = ProductPropertyRelationTable::getInstance()->createBaseQuery()->addWhere('productPropertyRelation.product_id = ?', array($this->product->id, ));
    //$products_properties = $this->product->getPropertyRelation();
    $product_property_ids = ProductPropertyRelationTable::getInstance()->getIdsByQuery($q);
    myDebug::dump($product_property_ids);

    $q = ProductGroupPropertyRelationTable::getInstance()->createBaseQuery()->addWhere('productGroupPropertyRelation.product_group_id = ?', array($this->product->group_id, ))->select('productGroupPropertyRelation.product_property_id');

    //$group_property_ids = ProductGroupPropertyRelationTable::getInstance()->getIdsByQuery($q);
    $groups_properties = $q->fetchArray();
    myDebug::dump($groups_properties, true);

    $product = $this->product;

    //myDebug::dump($product);
    throw new sfException('We don\'t need a redirection');
    $this->redirect(url_for('productCard', $product));
    //myDebug::dump($this->product);
    //$this->forward('productCard', 'show');
  }
}
