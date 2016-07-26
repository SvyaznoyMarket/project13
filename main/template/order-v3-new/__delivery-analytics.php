<?php
return function(
    \Helper\TemplateHelper $helper,
    \Model\OrderDelivery\Entity $orderDelivery
) {
    $data = [];
    try {
        // SITE-6407
        $data[] = [
            'category' => 'order_delivery',
            'action'   => count($orderDelivery->orders),
        ];
    } catch (\Exception $e) {
        \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['template']);
    }
?>
    <div class="jsOrderDeliveryAnalytics" data-value="<?= $helper->json($data) ?>"></div>
<? };