<? if ($url = \Partner\Counter\CityAds::getSubscribeLink()): ?>
    <!-- START CityAds -->
    <script id="cityadsAsync" type="text/javascript">
        (function () {
            var cascr = document.createElement('script');
            cascr.async = true;
            cascr.src = ( document.location.protocol === 'https:' ? 'https:' : 'http:' ) + '//<?php echo $url; ?>?md=2';
            var ca = document.getElementById('cityadsAsync');
            ca.parentNode.insertBefore(cascr, ca);
        }());
    </script>
    <noscript>
        <img src="<?= '//' . $url ?>" width="1" height="1">
    </noscript>
    <!-- END CityAds -->
<? endif;