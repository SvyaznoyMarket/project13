<?
    $helper = new \Helper\TemplateHelper();
?>

<? if (\Session\AbTest\AbTest::isSelfPaidDelivery() && \App::config()->product['pullRecommendation']) : ?>
    <div class="basketLine">

        <?= $helper->render('product/__slider', [
            'type'      => 'alsoBought',
            'products'  => [],
            'url'       => $page->url('cart.recommended', [
                'sender' => [
                    'position' => 'Basket',
                ],
            ]),
        ]) ?>
    </div>
<? endif ?>