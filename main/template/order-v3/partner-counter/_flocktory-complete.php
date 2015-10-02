<?php
return function (
    \Helper\TemplateHelper $helper,
    $orders,
    $products
) {

    if (!\App::config()->flocktory['postcheckout']) return '';

    /** @var $order \Model\Order\Entity */
    $order = reset($orders) ?: null;
    if (!$order) return '';

    $items = [];
    foreach ($order->getProduct() as $orderProduct) {
        /** @var $product \Model\Product\Entity|null */
        $product = isset($products[$orderProduct->getId()]) ? $products[$orderProduct->getId()] : null;
        if (!$product) continue;

        $items[] = [
            'id'    => $product->getArticle(),
            'title' => $product->getName(),
            'price' => $product->getPrice(),
            'image' => $product->getMainImageUrl('product_120'),
            'count' => $orderProduct->getQuantity(),
        ];
    }

    $user = [
        'name'  => $order->getFirstName(),
        'email' => $order->email ? $order->email : $order->getMobilePhone().'@enter.phone',
        'sex'   => $order->getFirstName() && preg_match('/[аяa]$/', $order->getFirstName()) ? 'f' : 'm'
    ];

    $order = [
        'id'  => $order->getId(),
        'price' => $order->getSum(),
        'custom_field' => $order->getNumber(),
        'items' => $items
    ];

    $data = [
        'user'  => $user,
        'order' => $order
    ];

?>

    <div id="flocktoryCompleteOrderJS" class="jsanalytics" data-value="<?= $helper->json($data) ?>"></div>

<? } ?>
