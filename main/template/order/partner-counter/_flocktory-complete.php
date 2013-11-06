<?php
/**
 * @var $page         \View\Layout
 * @var $order        \Model\Order\Entity
 * @var $productsById \Model\Product\Entity[]
 */
?>

<?
$flocktoryData = [];
if (!empty($userForm) && $userForm instanceof \View\Order\Form && !empty($order)) {

    $items = [];
    foreach ($order->getProduct() as $orderProduct) {
        /** @var $product \Model\Product\Entity|null */
        $product = isset($productsById[$orderProduct->getId()]) ? $productsById[$orderProduct->getId()] : null;
        if (!$product) continue;

        $items[] = [
            'id'    => $product->getArticle(),
            'title' => $product->getName(),
            'price' => $product->getPrice(),
            'image' => $product->getImageUrl(),
            'count' => $orderProduct->getQuantity(),
        ];
    }

    $flocktoryData = [
        'order_id'     => $order->getId(),
        'email'        => $userForm->getEmail() ? $userForm->getEmail() : $userForm->getMobilePhone().'@enter.ru',
        'name'         => implode(' ', [$userForm->getFirstName(), $userForm->getLastName()]),
        'sex'          => $userForm->getFirstName() && preg_match('/[аяa]$/', $userForm->getFirstName()) ? 'f' : 'm',
        'price'        => $order->getProductSum(),
        'custom_field' => $order->getNumber(),
        'items'        => $items,
    ];
}
?>

<? if ((bool)$flocktoryData): ?>
    <div id="jsOrderFlocktory" class="jsanalytics" data-value="<?= $page->json($flocktoryData) ?>"></div>
<? endif ?>