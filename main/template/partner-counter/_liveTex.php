<?php
/**
 * Created by JetBrains PhpStorm.
 * User: juljan
 * Date: 12.7.13
 * Time: 10.15
 * To change this template use File | Settings | File Templates.
 */

$livetexID = null;

if ( isset( \App::config()->partners['livetex']['liveTexID'] ) ) {
    $livetexID = \App::config()->partners['livetex']['liveTexID'];
}


if ( $livetexID && \App::config()->partners['livetex']['enabled'] ) :


$user = \App::user();
$user_entity = $user->getEntity();
$userid = null;
$username = null;
if ( isset( $user_entity ) and !empty($user_entity) ) {
    $userid = $user_entity->getId();
    $username = $user_entity->getFirstName();
    $tmp = $user_entity->getLastName();
    if ($tmp) $username .= ' '.$tmp;
    $username = str_replace("'", "", $username);
}

/*
$cart = $user->getCart();

//$region = $user->getRegion();

$cartProductsById = $cart->getProducts(); //$productIds = array_keys($cartProductsById);
$productsInCart_str = '';

foreach ($cartProductsById as $cartProduct) {
    $id = $cartProduct->getId();
    $quantity = $cartProduct->getQuantity();

    //$productsInCart[] = ['id' => $id, 'quantity' => $quantity];
    $productsInCart_str .= "'$id': $quantity,";
}

$productsInCart_str = '[' .substr( $productsInCart_str, 0, -1) . ']'. "\n";
*/

// for debug:
//print '###{';
//print_r($user);
//print '}###';


/*
'CountProductsInBasket' : '<? // count($cartProductsById) ?>',
'ProductsInBasket' : <? // $productsInCart_str ?>
*/
?>
<script type='text/javascript'> /* build:::7 */
    <? /* var liveTexID = 50391, */ ?>
     var liveTexID = <?= $livetexID ?>,
        liveTex_object = true;
    var LiveTex = {
        onLiveTexReady: function() {
            LiveTex.setName('<?= $username ?>');
        },

        invitationShowing: false,

        addToCart: function(prodId, prodName, link) {
            LiveTex.setManyPrechatFields({
                'Department' : 'Marketing',
                'Product' : prodId,
                'Ref' : link
                <?= $userid ?  ",'userid' : ".$userid : '' ?>
            });

            if (!LiveTex.invitationShowing) {
                LiveTex.showInvitation('Здравствуйте! Вы добавили корзину '+prodName+'. Может, у вас возникли вопросы и я могу чем-то помочь?');
                LiveTex.invitationShowing = true;;
            }

        }
    };
    (function() {
        var lt = document.createElement('script');
        lt.type ='text/javascript';
        lt.async = true;
        lt.src = 'http://cs15.livetex.ru/js/client.js';
        var sc = document.getElementsByTagName('script')[0];
        if ( sc ) sc.parentNode.insertBefore(lt, sc);
        else  document.documentElement.firstChild.appendChild(lt);
    })();
</script>

<? endif;