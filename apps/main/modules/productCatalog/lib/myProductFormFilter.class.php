<?php

class myProductFormFilter extends sfFormFilter
{
  public function configure()
  {
    $productCategory = $this->getOption('ProductCategory');
    
    $this->widgetSchema['price'] = new myWidgetFormRange();
  }
}