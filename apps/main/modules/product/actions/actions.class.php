<?php

/**
 * product actions.
 *
 * @package    enter
 * @subpackage product
 * @author     РЎРІСЏР·РЅРѕР№ РњР°СЂРєРµС‚
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class productActions extends myActions
{
  public function executeChange()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Url'));

    //use_helper('url');
    $this->product = $this->getRoute()->getObject();
    $new_value = $this->getRequestParameter('value');
    $property_id = $this->getRequestParameter('property');

    $q = ProductTable::getInstance()->createBaseQuery()->addWhere('product.group_id = ?', array($this->product->group_id, ));
    //Продукты в серии
    $product_ids = ProductTable::getInstance()->getIdsByQuery($q);
    //myDebug::dump($product_ids);
    //$q = ProductPropertyRelationTable::getInstance()->createBaseQuery();
    //$q = ProductPropertyRelationTable::getInstance()->createBaseQuery()->addWhere('productPropertyRelation.product_id = ?', array($this->product->id, ));
    //$products_properties = $this->product->getPropertyRelation();
    //свойства текущего продукта
    //$product_property_ids = ProductPropertyRelationTable::getInstance()->getIdsByQuery($q);
    //myDebug::dump($product_property_ids);

    //Свойства группы
    //$q = ProductGroupPropertyRelationTable::getInstance()->createBaseQuery()->innerJoin('productGroupPropertyRelation.Property property')->addWhere('productGroupPropertyRelation.product_group_id = ?', array($this->product->group_id, ))->select('productGroupPropertyRelation.product_property_id, property.*');
    $q = ProductPropertyTable::getInstance()->createBaseQuery()->from('ProductProperty productProperty indexby productProperty.id')->innerJoin('productProperty.ProductGroupPropertyRelation productGroupPropertyRelation')->select('productProperty.*')->addWhere('productGroupPropertyRelation.product_group_id = ?', array($this->product->group_id, ));
    //$group_property_ids = ProductGroupPropertyRelationTable::getInstance()->getIdsByQuery($q);
    //свойства, которые различаются в группе
    $groups_properties = $q->fetchArray();
    //myDebug::dump($groups_properties);

    $product = $this->product;
    $group_property_ids = array_keys($groups_properties);
    /*foreach ($groups_properties as $groups_property)
    {
      $group_property_ids[] = $groups_property['id'];
    }*/

    $old_properties = array();
    foreach ($product->getPropertyRelation() as $property)
    {
      if (in_array($property->property_id, $group_property_ids))
      {
        $old_properties[$property->property_id]['value'] = $property->getRealValue();
        $old_properties[$property->property_id]['type'] = $property->getProperty()->getType();
        $old_properties[$property->property_id]['is_multiple'] = $property->getProperty()->is_multiple;
      }
    }
    $old_properties[$property_id]['value'] = $new_value;
    //myDebug::dump($old_properties);

    $q = ProductTable::getInstance()->createBaseQuery();
    $q->innerJoin('product.PropertyRelation propertyRelation');
    //$q->addSelect('SUM(IF(id = ' . $product->id . ', 1, 0)) as sum_id');
    $if_condition = "";
    foreach ($old_properties as $id => $value)
    {
      $if_condition .= strlen($if_condition) ? " OR " : "";
      $if_condition .= "(propertyRelation.property_id=".$id." AND propertyRelation.";
      $field = "";
      switch ($value['type']):
        case 'string': case 'integer': case 'float': case 'text':
          $field = 'value_'.$value['type'].'=';
        break;
        case 'select':
          if (!$value['is_multiple'])
          {
            $field = 'option_id=';
          }
          else
          {
            $field = 'option_id=';
          }
        break;
      endswitch;
      $if_condition .= $field."'".$value['value']."')";
      if ($id == $property_id)
      {
        $q->addWhere('propertyRelation.'.$field.'?', array($value['value']));
      }
    }
    $q->select("product.id, SUM(IF(".$if_condition.", 1, 0)) as matches");
    $q->addWhere('product.id IN ('.implode(', ', array_diff($product_ids, array($product->id,))).')');
    //$q->addWhere('');
    $q->groupBy('product.id');
    $q->orderBy('matches desc, rating desc');
    $matches = $q->fetchArray();
    //myDebug::dump($matches[0]);
    //myDebug::dump($q->fetchArray(), true);


    //throw new sfException('We don\'t need a redirection');
    $new_product = ProductTable::getInstance()->getById($matches[0]['id']);
    $this->redirect(url_for('productCard', $new_product));
    //myDebug::dump($this->product);
    //$this->forward('productCard', 'show');
  }
}
