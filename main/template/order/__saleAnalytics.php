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
}
?>

<div class="jsOrderSaleAnalytics" data-value="<?= $helper->json($data) ?>"></div>

<? }; return $f;