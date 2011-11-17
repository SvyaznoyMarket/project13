<?php

/**
 * product components.
 *
 * @package    enter
 * @subpackage product
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class productComponents extends myComponents
{

  /**
   * Executes show component
   *
   * @param Product $product Товар
   * @param view $view Вид
   */
  public function executeShow()
  {
    if (!$this->product)
    {
      return sfView::NONE;
    }

    if (!in_array($this->view, array('default', 'expanded', 'compact', 'description', 'line')))
    {
      $this->view = 'default';
    }
    $item = array(
      'article'  => $this->product->article,
      'name'     => (string) $this->product,
      'creator'  => (string) $this->product->Creator,
      'price'    => $this->product->formatted_price,
      'has_link' => $this->product['view_show'],
      'photo'    => $this->product->getMainPhotoUrl(2),
      'product'  => clone($this->product),
      'url'      => url_for('productCard', $this->product, array('absolute' => true)),
    );

    if ('compact' == $this->view)
    {
        $item['root_name'] = (string) $this->product->Category[0]->getRootCategory();
    }

    if ('default' == $this->view)
    {
      $item['photo'] = $this->product->getMainPhotoUrl(1);
      $item['stock_url'] = url_for('productStock', $this->product);
      $item['shop_url'] = url_for('shop_show', ShopTable::getInstance()->getMainShop());

        $this->delivery = Core::getInstance()->query('delivery.calc', array(), array(
            'date' => date('Y-m-d'),
            'geo_id' => $this->getUser()->getRegion('core_id'),
            'product' => array(
                array('id' => $this->product->core_id, 'quantity' => 1),
            )
        ));
        $this->delivery = current($this->delivery);
    }
    if (in_array($this->view, array('expanded')))
    {
      $item['preview'] = $this->product->preview;
    }
    if (in_array($this->view, array('description')))
    {
      $item['description'] = $this->product->description;
    }
    if ('line' == $this->view)
    {
      $item['url'] = url_for('lineCard', $this->product->Line, array('absolute' => true, ));
    }

    $this->setVar('item', $item, true);

    //$selectedServices = $this->getUser()->getCart()->getServicesByProductId($this->product->id);
    $u = $this->getUser();
    $c = $u->getCart();
    $selectedServices = $c->getServicesByProductId($this->product->id);
    //$this->setVar('selectedServices', $selectedServices, true);
    //myDebug::dump($this->product);
  }

  /**
   * Executes preview component
   *
   * @param Product $product Товар
   */
  public function executePreview()
  {

  }

  /**
   * Executes pager component
   *
   * @param myDoctrinePager $pager Листалка товаров
   */
  public function executePager()
  {
    $this->view = isset($this->view) ? $this->view : $this->getRequestParameter('view');
    if (!in_array($this->view, array('expanded', 'compact', 'line')))
    {
      $this->view = 'compact';
    }

    $this->setVar('list', $this->pager->getResults(null, array(
      'with_properties' => 'expanded' == $this->view ? true : false,
      'property_view'   => 'expanded' == $this->view ? 'list' : false,
      'with_line'       => 'line' == $this->view ? true : false,
      'view'            => 'list',
    )), true);
  }

  /**
   * Executes sorting component
   *
   * @param array $productSorting Сортировка списка товаров
   */
  public function executeSorting()
  {
    $list = array();

    $active = $this->productSorting->getActive();
    foreach ($this->productSorting->getList() as $item)
    {
      if ($active['name'] == $item['name'])
      {
        $item['direction'] = 'asc' == $item['direction'] ? 'desc' : 'asc';
      }
      $list[] = array_merge($item, array(
        'url' => replace_url_for('sort', implode('-', array($item['name'], $item['direction'])))
        ));
    }

    $this->setVar('list', $list, true);
    $this->setVar('active', $active, true);
  }

  /**
   * Executes list component
   *
   * @param myDoctrineCollection $list Список товаров
   */
  public function executeList()
  {
    $this->view = (isset($this->view) && !empty($this->view)) ? $this->view : $this->getRequestParameter('view');
    if (!in_array($this->view, array('expanded', 'compact', 'line', )))
    {
      $this->view = 'compact';
    }
  }

  /**
   * Executes pagination component
   *
   * @param myDoctrinePager $pager Листалка товаров
   */
  public function executePagination()
  {
    if (!$this->pager->haveToPaginate())
    {
      return sfView::NONE;
    }
  }

  /**
   * Executes property component
   *
   * @param Product $product Товар
   * @param string $view Вид
   */
  public function executeProperty()
  {
    if (!in_array($this->view, array('default', 'inlist')))
    {
      $this->view = 'default';
    }

    $list = array();
    foreach ($this->product['Parameter'] as $parameter)
    {
      $value = $parameter->getValue();

      if (empty($value)) continue;

      if ('inlist' == $this->view && !$parameter->isViewList()) continue;

      $list[] = array(
        'name' => $parameter->getName(),
        'value' => $value,
      );
    }

    $this->setVar('list', $list, true);
  }

  /**
   * Executes property_grouped component
   *
   * @param Product $product Товар
   */
  public function executeProperty_grouped()
  {
    if (!in_array($this->view, array('default', 'inlist')))
    {
      $this->view = 'default';
    }

    $list = array();
    foreach ($this->product['ParameterGroup'] as $parameterGroup)
    {
      $parameters = array();
      foreach ($parameterGroup->getParameter() as $parameter)
      {
        $value = $parameter->getValue();

        if (('inlist' == $this->view) && !$parameter->isViewList()) continue;

        $parameters[] = array(
          'name'        => $parameter->getName(),
          'value'       => $value,
          'description' => $parameter->getDescription(),
        );
      }
      if (0 == count($parameters)) continue;

      $list[] = array(
        'name'       => $parameterGroup->getName(),
        'parameters' => $parameters,
      );
    }

    $this->setVar('list', $list, true);
    $this->setVar('product', $this->product, true);
  }

  /**
   * Executes product_group component
   *
   * @param Product $product Товар
   */
  public function executeProduct_group()
  {
    $properties = $this->product->getGroup()->getProperty();
    $q = ProductTable::getInstance()->createBaseQuery()->addWhere('product.group_id = ?', array($this->product->group_id,));
    $product_ids = ProductTable::getInstance()->getIdsByQuery($q);

    $q = ProductPropertyRelationTable::getInstance()->createBaseQuery();
    $products_properties = $this->product->getPropertyRelation();

    foreach ($properties as $property)
    {
      $query = clone $q;
      $query->addWhere('productPropertyRelation.property_id = ?', array($property->id,));
      $query->andWhereIn('productPropertyRelation.product_id', $product_ids);
      $query->distinct();
      $value_ids = ProductPropertyRelationTable::getInstance()->getIdsByQuery($query);
      $values = ProductPropertyRelationTable::getInstance()->createListByIds($value_ids, array('index' => array('productPropertyRelation' => 'id',)));
      foreach ($products_properties as $products_property)
      {
        if ($property->id == $products_property->property_id)
        {
          $values[$products_property->id]->mapValue('is_selected', true);
        }
      }
      $value_to_map = array();
      foreach ($values as $id => $value)
      {
        $realValue = $value->getRealValue();
        $value_to_map[$realValue]['value'] = $realValue;
        switch ($property['type']):
          case 'select':
            //$value_to_map[$id]['value'] = $value['option_id'];
            foreach ($property['Option'] as $option)
            {
              if ($option['id'] == $value['option_id'])
              {
                $value_to_map[$realValue]['name'] = $option['value'];
                break;
              }
            }
            break;
          case 'string': case 'integer': case 'float': case 'text':
            $value_to_map[$realValue]['name'] = $value_to_map[$realValue]['value'];
            break;
          default:
            $value_to_map[$id] = array('name' => '', 'value' => '',);
            break;
        endswitch;
        if (isset($values[$id]['is_selected']))
        {
          $value_to_map[$realValue]['is_selected'] = $values[$id]['is_selected'];
        }
        elseif (!isset($value_to_map[$realValue]['is_selected']))
        {
          $value_to_map[$realValue]['is_selected'] = 0;
        }
      }

      //$property->mapValue('old_values', sort($values));
      sort($value_to_map);
      $property->mapValue('values', $value_to_map);
    }

    $this->properties = $properties;
  }

  /**
   * Executes list_view component
   *
   */
  public function executeList_ajax_view()
  {
      $this->executeList_view();
  }

  /**
   * Executes list_view component
   *
   */
  public function executeList_view()
  {
    $list = array(
      array(
        'name'  => 'compact',
        'title' => 'компактный',
        'class' => 'tableview',
      ),
      array(
        'name'  => 'expanded',
        'title' => 'расширенный',
        'class' => 'listview',
      ),
    );

    foreach ($list as &$item)
    {
      $item = array_merge($item, array(
        'url' => replace_url_for('view', $item['name']),
        'current' => $this->getRequestParameter('view', 'compact') == $item['name'],
        ));
    } if (isset($item))
      unset($item);

    $this->setVar('list', $list, true);
  }
  /**
   * Executes tags component
   *
   */
  public function executeTags()
  {
    if (!$this->product instanceof Product)
    {
      return sfView::NONE;
    }

    $this->tagList = TagTable::getInstance()->getByProduct($this->product->id);

    $list = array();
    foreach ($this->tagList as $tag)
    {
      $list[] = array(
        'token' => $tag->token,
        'url'   => url_for('tag_show', array('tag' => $tag->token)),
        'name'  => $tag->name,
      );
    }

    $this->count = count($list);
    if (0 == $this->count)
    {
      return sfView::NONE;
    }

    $this->setVar('list', $list);
    $this->limit = 6 < count($list) ? 6 : count($list);
  }
  /**
   * Executes f1_lightbox component
   *
   */
  public function executeF1_lightbox(){

  }
}
