<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\OrderDelivery\Entity $orderDelivery
) {
    $data = [];
    try {
        // SITE-6016
        $testKey = \App::abTest()->getOrderDeliveryType();
        /** @var \Model\OrderDelivery\Entity\Order|null $order */
        $order = reset($orderDelivery->orders) ?: null;
        if ($testKey && $order) {
            if (1 === count($order->possible_delivery_groups)) {
                $data[] = [
                    'category' => 'delivery_option',
                    'action'   => 'no_options',
                    'label'    => '',
                ];
            } else if ('self' === $testKey) {
                $data[] = [
                    'category' => 'delivery_option',
                    'action'   => 'pickup',
                    'label'    => '',
                ];
            } else if ('delivery' === $testKey) {
                $data[] = [
                    'category' => 'delivery_option',
                    'action'   => 'delivery',
                    'label'    => '',
                ];
            }
        }

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