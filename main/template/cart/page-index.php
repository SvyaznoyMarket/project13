<?
/**
 * @var $page         \View\Cart\IndexPage
 * @var $user         \Session\User
 * @var $selectCredit bool
 * @var $products         \Model\Product\Entity[]
 * @var $services         \Model\Product\Service\Entity[]
 * @var $cartProductsById \Model\Cart\Product\Entity[]
 * @var $cartServicesById \Model\Cart\Service\Entity[]
 */
?>

<?
$cart = $user->getCart();
$creditEnabled = ($cart->getTotalProductPrice() >= \App::config()->product['minCreditPrice']) && \App::config()->payment['creditEnabled'] && !$user->getRegion()->getHasTransportCompany();
?>

<? if (\App::config()->adFox['enabled']): ?>
<div style="float:left; width: 100%; padding-bottom: 20px">
    <div id="adfox920" class="adfoxWrapper"></div>
</div>
<? endif ?>

<? require __DIR__ . '/_show.php' ?>

<?= $page->render('cart/form-certificate') ?>

<div class="fl width345 font14">
    <? if ($creditEnabled): ?>
    <div id="creditFlag" style="display:none">
        <label class="bigcheck <? if ($selectCredit): ?> checked<? endif ?>" for="selectCredit">
            <b></b>Выбранные товары купить в кредит
            <input autocomplete="off" class="" type="checkbox" id="selectCredit"<? if ($selectCredit): ?> checked="checked"<? endif ?> name="selectCredit"/>
        </label>

        <div class="pl35 font11">
            Получение кредита происходит онлайн после оформления заказа на сайте: заполняете заявку на кредит в банк,
            и в течение нескольких минут получаете СМС о решении банка. Оригиналы документов мы привезем вместе с выбранными товарами!
        </div>
    </div>
    <? endif ?>

</div>
<div id="total" class="fr">
    <div class="left">
        <? if ($creditEnabled) : ?>
        <div id="creditSum" data-minsum="<?= \App::config()->product['minCreditPrice'] ?>" style="display:none">
            <div class="font14 width370 creditInfo pb10 grayUnderline">
                <div class="leftTitle">Сумма заказа:</div>
                <div class="font24">
                    <span class="price"><?= $page->helper->formatPrice($cart->getSum()) ?></span> <span class="rubl">p</span>
                </div>
            </div>
            <div style="display:none" id="blockFromCreditAgent">
                <div class="font14 width370 creditInfo pb10 pt10">
                    <div class="leftTitle">
                        <strong>Ежемесячный платеж<sup>*</sup>:</strong>
                    </div>
                    <div class="font24">
                        <strong>
                            <span id="creditPrice">(считаем...)</span>
                            <span class="rubl">p</span>
                        </strong>
                    </div>
                </div>
                <div class="font11 width370"><sup>*</sup> Кредит не распространяется на услуги F1 и доставку.
                    Сумма платежей предварительная и уточняется банком в процессе принятия кредитного решения.
                </div>
            </div>
        </div>
        <? endif; ?>

        <div id="commonSum">
            <div class="font14">
                Сумма заказа:
            </div>

            <div class="oldPrice font18 clearfix<? if (!$cart->getOriginalSum() || (abs($cart->getOriginalSum() - $cart->getSum()) == 0)): ?> hidden<? endif ?>">
                <span id="totalOldPrice">
                    <?= $page->helper->formatPrice($cart->getOriginalSum()) ?>
                </span>
                <span class="rubl">p</span>
            </div>

            <div class="font30"><strong>
				<span class="price"><?= $page->helper->formatPrice($cart->getSum()) ?></span>
                <span class="rubl">p</span></strong></div>
        </div>
    </div>

</div>

<div class="clear pb25"></div>
<div class="line pb30"></div>
<div class="fl font14 pt10">&lt; <a class="underline" href="/">Вернуться к покупкам</a></div>

<div class="width500 auto">
    <a href="<?= $page->url('order.create') ?>" class="bBigOrangeButton width345">Оформить заказ</a>
</div>
<div class="clear"></div>

<? if (\App::config()->analytics['enabled']): ?>
<!--  AdRiver code START. Type:counter(zeropixel) Site: sventer SZ: baskets PZ: 0 BN: 0 -->
<script language="javascript" type="text/javascript"><!--
var RndNum4NoCash = Math.round(Math.random() * 1000000000);
var ar_Tail='unknown'; if (document.referrer) ar_Tail = escape(document.referrer);
document.write('<img src="http://ad.adriver.ru/cgi-bin/rle.cgi?' + 'sid=179070&sz=baskets&bt=21&pz=0&rnd=' + RndNum4NoCash + '&tail256=' + ar_Tail + '" border=0 width=1 height=1>')
//--></script>
<noscript><img src="http://ad.adriver.ru/cgi-bin/rle.cgi?sid=179070&sz=baskets&bt=21&pz=0&rnd=1616108824" border=0 width=1 height=1></noscript>
<!--  AdRiver code END  -->

<div id="heiasOrder" data-vars="<?= $cart->getAnalyticsData() ?>" class="jsanalytics"></div>


<!--Трэкер "Корзина"-->
<script>document.write('<img src="http://mixmarket.biz/tr.plx?e=3779415&r='+escape(document.referrer)+'&t='+(new Date()).getTime()+'" width="1" height="1"/>');</script>
<!--Трэкер "Корзина"-->
<? endif ?>
