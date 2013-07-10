<?php
/**
 * Created by JetBrains PhpStorm.
 * User: juljan
 * Date: 8.7.13
 * Time: 16.42
 * To change this template use File | Settings | File Templates.
 */

$params = [];

$params['_shopId'] = 76;
$params['_bannerId'] = 70;
$params['_customerFirstName'] = $form->getFirstName();
$params['_customerLastName'] = $form->getLastName();
$params['_customerEmail'] = $form->getEmail();
$params['_customerPhone'] = $form->getMobilePhone();
//$params['_customerGender'] = 'male';
$params['_customerGender'] = ( $params['_customerFirstName'] && preg_match('/[аяa]$/', $params['_customerFirstName']) )
    ? 'female' : 'male';
$params['_orderId'] = $order->getNumber();
$params['_orderValue'] = $order->getPaySum();
$params['_orderCurrency'] = 'RUB';
$params['_usedPromoCode'] = 'CVB456098'; // Код использованной скидки


if (
    !isset($params['_customerLastName']) or
    empty($params['_customerLastName'] ) or
    is_null($params['_customerLastName'])
    )
{
    $params['_customerLastName'] = ' ';
}

// Если юзер почему-то безымянный, то обратимся как "Уважаемый Покупатель"
if ( empty($params['_customerFirstName']) ) {
    $params['_customerFirstName'] = 'Покупатель';
    $params['_customerLastName'] = '';
    $params['_customerGender'] = 'male';
}

?>

<div id="promocode-element-container"></div>

<script type="text/javascript">
    var _iPromoBannerObj = function() {
        this.htmlElementId = 'promocode-element-container';
        this.params = <? echo $page->helper->stringRowsParams4js($params) .';'.PHP_EOL; ?>
            <? /* //example:
            {
            '_shopId': 76,
            '_bannerId': 70,
            '_customerFirstName': '<?= $usr['firstName'] ?>',
            '_customerLastName': '<?= $usr['lastName'] ?>',
            '_customerEmail': '<?= $usr['email'] ?>',
            '_customerPhone': '<?= $usr['phone'] ?>',
            '_customerGender': '<?= $usr['gender'] ?>',
            '_orderId': '<?= $order->getNumber() ?>',
            '_orderValue': 'ORDER_VALUE',
            '_orderCurrency': 'RUB',
            '_usedPromoCode': 'ORDER_PROMO_CODE'
            };
            */ ?>
        this.lS=function(s){document.write('<sc'+'ript type="text/javascript" src="'+s+'" async="true"></scr'+'ipt>');},
            this.gc=function(){return document.getElementById(this.htmlElementId);};
        var r=[];for(e in this.params){if(typeof(e)==='string'){r.push(e+'='+encodeURIComponent(this.params[e]));}}r.push('method=main');r.push('jsc=iPromoCpnObj');this.lS(('https:'==document.location.protocol ? 'https://':'http://')+'get4click.ru/wrapper.php?'+r.join('&'));};

    var iPromoCpnObj = new _iPromoBannerObj();
</script>