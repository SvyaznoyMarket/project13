<?
/**
 * var $product Model\Product\Entity
 * var $smantic Views\Sociomantic
 * var $prod_cats string // [ 'Малая бытовая техника для кухни',  'Холодильники и морозильники' ]
 * from /main/view/DefaultLayout.php
 **/

$scr_product = []; $i = 0;

if ($product instanceof \Model\Product\Entity) {

    $domain = $_SERVER['HTTP_HOST'] ?: $_SERVER['SERVER_NAME'];
    $region_id = \App::user()->getRegion()->getId();

    $photo = null;
    $tmp = $product->getImageUrl(4);
    if ($tmp) $photo = $tmp;

    $brand = $product->getBrand() ? $product->getBrand()->getName() : null;
    $scr_product['identifier'] = $smantic->resetProductId($product);
    $scr_product['fn'] = $product->getWebName();
    $scr_product['category'] = $prod_cats;
    $scr_product['description'] = $product->getTagline();
    $scr_product['brand'] = $brand;
    $scr_product['price'] = $product->getPrice(); //стоимость со скидкой
    $scr_product['amount'] = $product->getPriceOld(); // стоимость без скидки
    $scr_product['currency'] = 'RUB';
    /*$scr_product['url'] = 'http://' . $domain . $product->getLink(); */
    $scr_product['url'] = 'http://' . $domain . $_SERVER['REQUEST_URI'];
    $scr_product['photo'] = $photo;
}

if (!empty($scr_product)):
?>
<script type="text/javascript">
var sonar_product = { <?
    foreach($scr_product as $key => $value):
        if (!empty($value)):
            $i++;
            if ($i>1) echo ","; // считаем, что identifier полуюбому существует у продукта, иначе запятая будет не в тему
            echo PHP_EOL;
            echo $key.": " . $smantic->wrapEscapeQuotes($value);
        endif;
    endforeach;
    echo PHP_EOL;
    ?>
};
</script>
<?
endif;
/* example: <!--
<script type="text/javascript">
    var sonar_product = {
        identifier: '<?= $product->getTypeId(); ?>',
        fn: '<?= $product->getWebName() //getPrefix ? ?>',
        category: <?= $prod_cats ?>,
        description: '<?= $product->getTagline() ?>',
        brand: '<?= $brand ?>',
        price: '<?= $product->getPrice() ?> ',  //стоимость со скидкой
        amount: '<?= $product->getPriceOld() ?>', // стоимость без скидки
        currency: 'RUB',
        url: '<?= $product->getLink() ?>',
        photo: '<?= $photo ?>'
    };
</script>
--> */
?>