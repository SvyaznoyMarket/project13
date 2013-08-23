<?php
/**
 * @var $page         \View\Layout
 * @var $order        \Model\Order\Entity
 * @var $productsById \Model\Product\Entity[]
 */
?>

<? if (!empty($userForm) && $userForm instanceof \View\Order\Form && !empty($order)): ?>

<div id="jsOrderFlocktory" data-value="<?= $page->json([
    'order_id'     => $order->getId(),
    'email'        => $userForm->getEmail() ? $userForm->getEmail() : $userForm->getMobilePhone().'@mail.ru',
    'name'         => implode(' ', [$userForm->getFirstName(), $userForm->getLastName()]),
    'sex'          => $userForm->getFirstName() && preg_match('/[аяa]$/', $userForm->getFirstName()) ? 'f' : 'm',
    'price'        => $order->getProductSum(),
    'custom_field' => $order->getNumber(),
    'items'        => array_map(function(\Model\Order\Product\Entity $orderProduct) use (&$productsById) {
        /** @var $product \Model\Product\Entity|null */
        $product = isset($productsById[$orderProduct->getId()]) ? $productsById[$orderProduct->getId()] : null;

        if ($product) {
            return [
                'id'    => $product->getArticle(),
                'title' => $product->getName(),
                'price' => $product->getPrice(),
                'image' => $product->getImageUrl(),
                'count' => $orderProduct->getQuantity(),
            ];
        }
    }, $order->getProduct()),
]) ?>"></div>

<? endif ?>