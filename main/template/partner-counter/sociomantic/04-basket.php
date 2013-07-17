<?
/**
 * // var $products \Model\Product\CartEntity[] //disabled
 * var $cart_prods
 * from /main/view/DefaultLayout.php
 **/

?>
<? if ( !empty($cart_prods) ): ?>
<script type="text/javascript">
var basket = {
    products: [
    <?
        $arr_count = count($cart_prods); $j = 0;
        foreach($cart_prods as $one_prod) {
            $j++;

            echo '{';
            $i = 0;
            foreach ($one_prod as $key => $value){
                if (!empty($value)):
                    $i++;
                    if ($i>1) echo ", "; // считаем, что identifier полуюбому существует у продукта, иначе запятая будет не в тему
                    echo $key.": '".$value."'" ;
                endif;
            }
            echo '}';

            if ($j<$arr_count) echo ',';
            echo PHP_EOL;
        }
    /* examples:
    { identifier: '461-1177_msk', amount: 4990.00, currency: 'RUB', quantity: 1 },
    { identifier: '452-9682_msk', amount: 23990.00, currency: 'RUB', quantity: 1 }
    */
?>    ]
};
</script>
<? endif; ?>

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