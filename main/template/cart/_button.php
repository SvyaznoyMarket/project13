<?php
/**
 * @var $page     \View\Layout
 * @var $user     \Session\User
 * @var $product  \Model\Product\Entity
 */

print \App::mustache()->render('cart/_button-product', (new \View\Cart\ProductButtonAction())->execute(new \Helper\TemplateHelper(), $product));
?>