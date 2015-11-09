<?

use Controller\Enterprize\CouponTrait;

/**
 * @param \Helper\TemplateHelper $helper
 * @param $coupon
 * @return string
 */
$f = function(
    \Helper\TemplateHelper $helper,
    $coupon
){

    if (!$coupon instanceof \Model\EnterprizeCoupon\Entity) {
        return '';
    }
    $discount = sprintf('%u%s', $coupon->getPrice(), $coupon->getIsCurrency() ? '<span class="rubl">p</span>' : '%');

?>
    <div class="ep">

        <div class="js-pp-ep-fishka js-enterprize-coupon"
             data-value="<?= $helper->json(array_merge(CouponTrait::getCouponData($coupon),['slider' => [], 'isProductCard' => true])) ?>">

            <div class="ep__fishka"<? if (!empty($coupon->getBackgroundImage())): ?> style="background-image: url('<?= $coupon->getBackgroundImage() ?>')"<? endif ?>>
                <?= $coupon->getIsCurrency() ? '<span class="ep__fishka-value"><span class="rubl">p</span></span>' : '<span class="ep__fishka-value ep__fishka-value--text">%</span>' ?>
            </div>

            <div class="ep__desc">Фишка со скидкой <?= $discount ?> на этот товар</div>

        </div>

        <div class="js-enterprize-coupon-hint-holder"></div>

        <script id="tplEnterprizeForm" type="text/html" data-partial="<?= $helper->json([]) ?>">
            <?= file_get_contents(\App::config()->templateDir . '/enterprize/form.mustache') ?>
        </script>

    </div>


<? }; return $f;