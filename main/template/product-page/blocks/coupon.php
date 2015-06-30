<?

use Controller\Enterprize\CouponTrait;

$f = function(
    \Helper\TemplateHelper $helper,
    $coupon
){

    if (!$coupon instanceof \Model\EnterprizeCoupon\Entity) return null;
    $discount = sprintf('%u%s', $coupon->getPrice(), $coupon->getIsCurrency() ? '<span class="rubl">p</span>' : '%');

?>
    <div class="ep">

        <div class="js-pp-ep-fishka js-enterprize-coupon"
             data-value="<?= $helper->json(array_merge(CouponTrait::getCouponData($coupon),['slider' => [], 'isProductCard' => true])) ?>">

            <div class="ep__fishka "
                 >
                <?= $coupon->getIsCurrency() ? '<span class="rubl">p</span>' : '%' ?></div>

            <div class="ep__desc">Фишка со скидкой <?= $discount ?> на этот товар</div>

        </div>

        <div class="js-enterprize-coupon-hint-holder"></div>

        <script id="tplEnterprizeForm" type="text/html" data-partial="<?= $helper->json([]) ?>">
            <?= file_get_contents(\App::config()->templateDir . '/enterprize/form.mustache') ?>
        </script>

    </div>


<? }; return $f;