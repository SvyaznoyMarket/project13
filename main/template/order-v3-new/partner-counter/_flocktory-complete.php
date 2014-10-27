<?php
return function (
    \Helper\TemplateHelper $helper,
    $orders,
    $products
) {
    /** @var $order \Model\Order\Entity */
    $order = reset($orders);

    $items = [];
    foreach ($order->getProduct() as $orderProduct) {
        /** @var $product \Model\Product\Entity|null */
        $product = isset($products[$orderProduct->getId()]) ? $products[$orderProduct->getId()] : null;
        if (!$product) continue;

        $items[] = [
            'id'    => $product->getArticle(),
            'title' => $product->getName(),
            'price' => $product->getPrice(),
            'image' => $product->getImageUrl(),
            'count' => $orderProduct->getQuantity(),
        ];
    }

    $data = [
        'order_id'     => $order->getId(),
        'email'        => $order->email ? $order->email : $order->getMobilePhone().'@enter.ru',
        'name'         => $order->getFirstName(),
        'sex'          => $order->getFirstName() && preg_match('/[аяa]$/', $order->getFirstName()) ? 'f' : 'm',
        'price'        => $order->getProductSum(),
        'custom_field' => $order->getNumber(),
        'items'        => $items,
    ];

?>

    <div id="jsOrderFlocktory" class="jsanalytics" data-value="<?= $helper->json($data) ?>"></div>

<? } ?>
