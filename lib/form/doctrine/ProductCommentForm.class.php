<?php

/**
 * ProductComment form.
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ProductCommentForm extends BaseProductCommentForm
{
  public function configure()
  {
    parent::configure();

    $product = $this->getOption('product');
    if (!$product)
    {
      throw new InvalidArgumentException('You must provide a product object.');
    }
    $user = $this->getOption('user');

    $this->object->Product = $product;
    $this->object->User = $user;

    $this->widgetSchema['content'] = new sfWidgetFormTextarea();

    $fields = array(
      'content',
    );
    if (!$this->isNew())
    {
      $fields = array_merge($fields, array(
        'helpful',
        'unhelpful',
      ));
    }

    $this->widgetSchema->setLabels(array(
      'content'   => 'Комментарий',
      'helpful'   => 'Полезный',
      'unhelpful' => 'Не полезный',
    ));

    $this->useFields($fields);

    $this->widgetSchema->setNameFormat('comment[%s]');
  }
}
