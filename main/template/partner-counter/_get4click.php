<?php
/**
 * Created by JetBrains PhpStorm.
 * User: juljan
 * Date: 8.7.13
 * Time: 16.42
 * To change this template use File | Settings | File Templates.
 */
?>
### // tmp
<div id="promocode-element-container"></div>
&&& // tmp
<script type="text/javascript">
    var _iPromoBannerObj = function() {
        this.htmlElementId = 'promocode-element-container';
        this.params = {
            '_shopId': 76,
            '_bannerId': 70,
            '_customerFirstName': 'CUSTOMER_FIRST_NAME',
            '_customerLastName': 'CUSTOMER_LAST_NAME',
            '_customerEmail': 'CUSTOMER_EMAIL',
            '_customerPhone': 'CUSTOMER_PHONE',
            '_customerGender': 'CUSTOMER_GENDER',
            '_orderId': '<?= $order->getNumber() ?>',
            '_orderValue': 'ORDER_VALUE',
            '_orderCurrency': 'RUB',
            '_usedPromoCode': 'ORDER_PROMO_CODE'
        };

        this.lS=function(s){document.write('<sc'+'ript type="text/javascript" src="'+s+'" async="true"></scr'+'ipt>');},
            this.gc=function(){return document.getElementById(this.htmlElementId);};
        var r=[];for(e in this.params){if(typeof(e)==='string'){r.push(e+'='+encodeURIComponent(this.params[e]));}}r.push('method=main');r.push('jsc=iPromoCpnObj');this.lS(('https:'==document.location.protocol ? 'https://':'http://')+'get4click.ru/wrapper.php?'+r.join('&'));};

    var iPromoCpnObj = new _iPromoBannerObj();
</script>