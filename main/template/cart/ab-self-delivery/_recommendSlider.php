<? if (\Controller\Delivery\Action::isPaidSelfDelivery()) : ?>
<? $helper = new \Helper\TemplateHelper(); ?>

    <div class="basketLine">

        <?= $helper->render('product/__slider', [
            'type'      => 'alsoBought',
            'products'  => [],
            'url'       => $page->url('cart.recommended'),
        ]) ?>

    </div>

<? endif; ?>