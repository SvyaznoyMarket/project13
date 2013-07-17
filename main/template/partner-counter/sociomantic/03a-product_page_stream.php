<?
/**
 * var $product Model\Product\Entity
 * var $prod_cats string // [ 'Малая бытовая техника для кухни',  'Холодильники и морозильники' ]
 * from /main/view/DefaultLayout.php
 **/

$photo = $product->getPhoto()[0]->getUrl(2);
$brand = $product->getBrand()->getName();

$scr_product['identifier'] = $product->getTypeId();
$scr_product['fn'] = $product->getWebName(); // TODO: может getPrefix ?
$scr_product['category'] = $prod_cats;
$scr_product['description'] = $product->getTagline();
$scr_product['brand'] = $product->getBrand()->getName();
$scr_product['price'] = $product->getPrice(); //стоимость со скидкой
$scr_product['amount'] = $product->getPriceOld(); // стоимость без скидки // TODO: точно ли getPriceOld() есть ценой без скидки?
$scr_product['currency'] = 'RUB';
$scr_product['url'] = $product->getLink();
$scr_product['photo'] = $product->getPhoto()[0]->getUrl(2);
$i = 0;
?>

<script type="text/javascript">
var product = { <?
    foreach($scr_product as $key => $value):
        if (!empty($value)):
            $i++;
            if ($i>1) echo ","; // считаем, что identifier полуюбому существует у продукта, иначе запятая будет не в тему
            echo PHP_EOL;
            echo $key.": '".$value."'" ;
        endif;
    endforeach;
    echo PHP_EOL;
    ?>
};
</script>
<?

/* <!--
<script type="text/javascript">
    var product = {
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
<script type="text/javascript">
    (function () {
        var s = document.createElement('script');
        var x = document.getElementsByTagName('script')[0];
        s.type = 'text/javascript';
        s.async = true;
        s.src = ('https:' == document.location.protocol ? 'https://' : 'http://')
            + 'eu-sonar.sociomantic.com/js/2010-07-01/adpan/enter-ru';
        x.parentNode.insertBefore(s, x);
    })();
</script>