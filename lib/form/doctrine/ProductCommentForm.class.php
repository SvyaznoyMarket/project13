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

    $parent = $this->getOption('parent');

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

    $this->widgetSchema['content'] = new sfWidgetFormTextarea();

    $this->widgetSchema->setLabels(array(
      'content'   => 'Комментарий',
      'helpful'   => 'Полезный',
      'unhelpful' => 'Не полезный',
    ));

    $this->useFields($fields);

    $this->widgetSchema->setNameFormat('comment[%s]');
  }

  protected function doSave($con = null)
  {
    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $this->updateObject();

    $product = $this->getOption('product');
    if (!$product)
    {
      throw new InvalidArgumentException('You must provide a product object.');
    }
    $user = $this->getOption('user');
    if (!$user)
    {
      throw new InvalidArgumentException('You must provide a user object.');
    }

    $this->getObject()->product_id = $product->id;
    $this->getObject()->user_id = $user->id;

    $parent = $this->getOption('parent') ? $this->getOption('parent') : ProductCommentTable::getInstance()->getRoot($this->getOption('product')->id);
    $this->getObject()->getNode()->insertAsLastChildOf($parent);

    // embedded forms
    $this->saveEmbeddedForms($con);

  }
}
