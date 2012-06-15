<?php if ($step == 1) { ?>

<!--  AdRiver code START. Type:counter(zeropixel) Site: sventer SZ: step1 PZ: 0 BN: 0 -->
<script language="javascript" type="text/javascript"><!--
var RndNum4NoCash = Math.round(Math.random() * 1000000000);
var ar_Tail='unknown'; if (document.referrer) ar_Tail = escape(document.referrer);
document.write('<img src="http://ad.adriver.ru/cgi-bin/rle.cgi?' + 'sid=179070&sz=step1&bt=21&pz=0&rnd=' + RndNum4NoCash + '&tail256=' + ar_Tail + '" border=0 width=1 height=1>')
//--></script>
<noscript><img src="http://ad.adriver.ru/cgi-bin/rle.cgi?sid=179070&sz=step1&bt=21&pz=0&rnd=1342550588" border=0 width=1 height=1></noscript>
<!--  AdRiver code END  -->

<script type="text/javascript">
    (function(d){
        var HEIAS_PARAMS = [];
        HEIAS_PARAMS.push(['type', 'ppx'], ['ssl', 'auto'], ['n', '12564'], ['cus', '12675']);
        HEIAS_PARAMS.push(['pb', '1']);
        HEIAS_PARAMS.push(['order_article', '<?php echo $orderArticle; ?>']);
        if (typeof window.HEIAS === 'undefined') { window.HEIAS = []; }
        window.HEIAS.push(HEIAS_PARAMS);
        var scr = d.createElement('script');
        scr.async = true;
        scr.src = (d.location.protocol === 'https:' ? 'https:' : 'http:') + '//ads.heias.com/x/heias.async/p.min.js';
        var elem = d.getElementsByTagName('script')[0];
        elem.parentNode.insertBefore(scr, elem);
    }(document));
</script>

<?php } elseif ($step == 2) { ?>


<!--  AdRiver code START. Type:counter(zeropixel) Site: sventer SZ: step2 PZ: 0 BN: 0 -->
<script language="javascript" type="text/javascript"><!--
var RndNum4NoCash = Math.round(Math.random() * 1000000000);
var ar_Tail='unknown'; if (document.referrer) ar_Tail = escape(document.referrer);
document.write('<img src="http://ad.adriver.ru/cgi-bin/rle.cgi?' + 'sid=179070&sz=step2&bt=21&pz=0&rnd=' + RndNum4NoCash + '&tail256=' + ar_Tail + '" border=0 width=1 height=1>')
//--></script>
<noscript><img src="http://ad.adriver.ru/cgi-bin/rle.cgi?sid=179070&sz=step2&bt=21&pz=0&rnd=689779511" border=0 width=1 height=1></noscript>
<!--  AdRiver code END  -->

<script type="text/javascript">
    (function(d){
        var HEIAS_PARAMS = [];
        HEIAS_PARAMS.push(['type', 'ppx'], ['ssl', 'auto'], ['n', '12564'], ['cus', '12675']);
        HEIAS_PARAMS.push(['pb', '1']);
        HEIAS_PARAMS.push(['order_article', '<?php echo $orderArticle; ?>']);
        if (typeof window.HEIAS === 'undefined') { window.HEIAS = []; }
        window.HEIAS.push(HEIAS_PARAMS);
        var scr = d.createElement('script');
        scr.async = true;
        scr.src = (d.location.protocol === 'https:' ? 'https:' : 'http:') + '//ads.heias.com/x/heias.async/p.min.js';
        var elem = d.getElementsByTagName('script')[0];
        elem.parentNode.insertBefore(scr, elem);
    }(document));
</script>



<?php } elseif ($step == 3) { ?>

<!--  AdRiver code START. Type:counter(zeropixel) Site: sventer SZ: step3 PZ: 0 BN: 0 -->
<script language="javascript" type="text/javascript"><!--
var RndNum4NoCash = Math.round(Math.random() * 1000000000);
var ar_Tail='unknown'; if (document.referrer) ar_Tail = escape(document.referrer);
document.write('<img src="http://ad.adriver.ru/cgi-bin/rle.cgi?' + 'sid=179070&sz=step3&bt=21&pz=0&rnd=' + RndNum4NoCash + '&tail256=' + ar_Tail + '" border=0 width=1 height=1>')
//--></script>
<noscript><img src="http://ad.adriver.ru/cgi-bin/rle.cgi?sid=179070&sz=step3&bt=21&pz=0&rnd=1296167794" border=0 width=1 height=1></noscript>
<!--  AdRiver code END  -->
<?php } elseif ($step == 4) { ?>

<script language="JavaScript" type="text/javascript">
    HEIAS_T=Math.random(); HEIAS_T=HEIAS_T*10000000000000000000;
    var order_article='<?php echo $orderArticle; ?>';
    var order_id='<?php echo $order->number; ?>';
    var order_total='<?php echo $order->sum; ?>';
    var product_quantity='<?php echo $quantityString; ?>';
    var HEIAS_SRC='https://ads.heias.com/x/heias.cpa/count.px.v2/?PX=HT|' + HEIAS_T + '|cus|12675|pb|1|order_article|' + order_article + '|product_quantity|' + product_quantity + '|order_id|' + order_id + '|order_total|' + order_total + '';
    document.write('<img width="1" height="1" src="' + HEIAS_SRC + '" />');
</script>


<?php include_component('order','seo_admitad', $sf_data) ?>

<?php } ?>
