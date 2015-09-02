<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param \Model\Order\Entity[] $orders
 */
$f = function(
    \Helper\TemplateHelper $helper,
    array $orders
) {

$data = [];
foreach ($orders as $order) {
    $isSale = false;
    $isListing = false;
    foreach ($order->getMeta() as $k => $v) {
        if (false !== strpos($k, '.position') && is_array($v) && in_array('Listing', $v)) {
            $isListing = true;
        }
        if (false !== strpos($k, '.from') && is_array($v)) {
            foreach ($v as $value) {
                if (false !== strpos($value, 'slices/all_labels') && is_array($v)) {
                    $isSale = true;
                    break;
                }
            }
        }
    }

    if ($isSale) {
        $data[] = [
            'category' => 'slices_sale',
            'action'   => 'buy',
            'label'    => $isListing ? 'basket' : 'product',
        ];
    }

    try {
        // SITE-6016
        $testKey = \App::abTest()->getTest('order_delivery_type')->getChosenCase()->getKey();
        if (
            in_array(\App::user()->getRegion()->parentId, [76, 90])  // Воронеж, Ярославль
            && in_array($testKey, ['self', 'delivery'])
            && $order->getDelivery()
        ) {
            $data[] = [
                'category' => 'delivery_option',
                'action'   => $order->getDelivery()->isShipping ? 'buy_delivery' : 'buy_pickup',
                'label'    => '',
            ];
        }
    } catch (\Exception $e) {
        \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['template']);
    }
}
?>

<div class="jsOrderSaleAnalytics" data-value="<?= $helper->json($data) ?>"></div>

<? }; return $f;