<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param \Model\Product\Entity $product
 */
$f = function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product
) {
    $events = [];

    if ($product->isOnlyFromPartner()) {
        $events[] = [
            'class' => 'jsEvent_documentReady',
            'value' => [
                'name' => 'TL_marketplaceProduct_loaded',
            ],
        ];
    }
?>

<? foreach ($events as $event): ?>
    <div class="jsEvent <?= $event['class'] ?>" data-value="<?= $helper->json($event['value'])?>"></div>
<? endforeach ?>

<? }; return $f;