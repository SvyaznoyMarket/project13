<?php

class myProductTagFormFilter extends myProductFormFilter
{
  public function configure()
  {
    //parent::configure();

    $this->setOption('mark_required', false);

    $this->disableCSRFProtection();

    if (!$productCategory = $this->getOption('productCategory'))
    {
      throw new InvalidArgumentException('You must provide a productCategory object.');
    }

    // виджет цены
    $this->createPriceWidget($productCategory);

    // виджет производителя
    $this->createCreatorWidget($productCategory);

    // виджет шильдиков
    $this->createLabelWidget($productCategory);

    // виджеты параметров
    $tagGroups =
      $this->getOption('count', false)
      ? $productCategory->getTagGroup()
      : $productCategory->getTagGroupForFilter(array('hydrate_array' => true, 'select' => 'tagGroup.id, tagGroup.name, tag.id, tag.name'))
    ;
    foreach ($tagGroups as $tagGroup/*$productCategoryTagGroupRelation*/)
    {
      //$tagGroup = $productCategoryTagGroupRelation->getTagGroup();
      //myDebug::dump($tagGroup); continue;
      if (!count($tagGroup['Tag'])) continue;

      if (!$widget = call_user_func(array($this, 'getWidgetChoice'), $tagGroup)) continue;

      $index = "tag-{$tagGroup['id']}";
      $this->setWidget($index, $widget);
      $this->setValidator($index, new sfValidatorPass());
      $this->widgetSchema[$index]->setLabel($tagGroup['name']);
    }

    $this->widgetSchema->setNameFormat('t[%s]');
  }

  public function buildQuery(myDoctrineQuery $q)
  {
    $productCategory = $this->getOption('productCategory');

    $filter = array(
      'category'   => $productCategory,
      'creator'    => isset($this->values['creator']) ? $this->values['creator'] : false,
      'label'      => isset($this->values['label']) ? $this->values['label'] : false,
      'price'      => (isset($this->values['price']['from']) && isset($this->values['price']['to'])) ? array(
        'from' => $this->values['price']['from'],
        'to'   => $this->values['price']['to'],
      ) : false,
      'parameters' => array(),
    );

    $productTagFilterList = $productCategory->TagGroup;
    $productTagFilterList->indexBy('id');
    foreach ($this->values as $id => $param)
    {
      if (0 !== strpos($id, 'tag-')) continue;

      $productTagFilter = $productTagFilterList->getByIndex('id', substr($id, 4));
      if (!$productTagFilter) continue;

      $filter['parameters'][] = array(
        'tag_group' => $productTagFilter->id,
        'values'    => $param,
      );
    }
    ProductTable::getInstance()->setQueryForTagFilter($q, $filter);
  }

  protected function getWidgetChoice($tagGroup)
  {
    $choices = array();
    foreach ($tagGroup['Tag'] as $tag)
    {
      $choices[$tag['id']] = $tag['name'];
    }

    return new myWidgetFormChoice(array(
      'choices'          => $choices,
      'multiple'         => false,
      'expanded'         => true,
      'renderer_class'   => 'myWidgetFormSelectCheckbox',
      'renderer_options' => array(
        'label_separator' => '',
        'formatter'       => array($this, 'show_part'),
      ),
    ));
  }

  public function show_part($widget, $inputs)
  {
    $rows = array();
    $shown = array_slice($inputs, 0, 5);
    foreach ($shown as $input)
    {
      $rows[] = $widget->renderContentTag('li', $input['input'].$widget->getOption('label_separator').$input['label']);
    }
    if (count($inputs) > 5)
    {
      $hidden = array_slice($inputs, 5);
      foreach ($hidden as $input)
      {
        $rows[] = $widget->renderContentTag('li', $input['input'].$widget->getOption('label_separator').$input['label'], array('class' => 'hf', ));
      }
      $rows[] = $widget->renderContentTag('li', 'еще...', array('class' => 'fm', ));
    }

    return !$rows ? '' : $widget->renderContentTag('ul', implode($widget->getOption('separator'), $rows), array('class' => $widget->getOption('class')));
  }

  protected function createCreatorWidget($productCategory)
  {
    $creator = $this->getOption('creator', null);

    if ($this->getOption('with_creator', false))
    {
      $choices = array();
      foreach (CreatorTable::getInstance()
                 ->getListByProductCategory($productCategory, array(
          'select'         => 'creator.id, creator.name',
          'with_descendat' => true,
          'for_filter'     => true,
          'order'          => 'creator.name',
          'hydrate_array'  => true,
        )
               ) as $v) {
        $choices[$v['id']] = $v['name'];
      }

      if (count($choices) > 1)
      {
        $this->widgetSchema['creator'] = new myWidgetFormChoice(array(
          'choices'          => $choices,
          'multiple'         => true,
          'expanded'         => true,
          'renderer_class'   => 'myWidgetFormSelectCheckbox',
          'renderer_options' => array(
            'label_separator' => '',
            'formatter'       => array($this, 'show_part'),
          ),
        ));
        $this->widgetSchema['creator']->setLabel('Производитель');
        $this->widgetSchema['creator']->setDefault($creator ? $creator->id : null);
        $this->validatorSchema['creator'] = new sfValidatorPass();
      }
    }
  }
}