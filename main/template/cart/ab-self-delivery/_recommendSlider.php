<?
    $helper = new \Helper\TemplateHelper();
?>

<? if (\Controller\Delivery\Action::isPaidSelfDelivery()) : ?>
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