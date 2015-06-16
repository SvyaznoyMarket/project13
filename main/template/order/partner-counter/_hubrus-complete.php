<?php
/**
 * @var $productsById   \Model\Product\Entity[]
 * @var $orders         \Model\Order\Entity[]
 * @var $order          \Model\Order\Entity
 */

$order = reset($orders);
$items = array_values(array_map(function (\Model\Product\Entity $product){
    return [
        'id'        => $product->getId(),
        'category'  => $product->getRootCategory() ? $product->getRootCategory()->getId() : null,
        'price'     => $product->getPrice()
    ];
}, $productsById));

$helper = \App::helper();

?>
<script type="text/javascript">
    var smCustomVars = {
        ordered_items: <?= $helper->json($items) ?>,
        order_id: <?= $helper->json($order->getId()) ?>
    };
</script>
