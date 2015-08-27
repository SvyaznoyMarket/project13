<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\OrderDelivery\Entity $orderDelivery
) {
    $data = [];
    try {
        // SITE-6016
        $testKey = \App::abTest()->getTest('order_delivery_type')->getChosenCase()->getKey();
        if (
            in_array(\App::user()->getRegion()->parentId, [76, 90])  // Воронеж, Ярославль
            && ('default' !== $testKey)
        ) {
            if (1 === count($orderDelivery->orders)) {
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
    } catch (\Exception $e) {
        \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['template']);
    }
?>
    <div class="jsOrderDeliveryAnalytics" data-value="<?= $helper->json($data) ?>"></div>
<? };