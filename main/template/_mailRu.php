<?php
/**
 * @var \View\DefaultLayout $page
 * @var array $productIds
 * @var string $pageType
 * @var string $price
 */
?>

<? if (\App::config()->analytics['enabled']): ?>
    <!-- Rating@Mail.ru counter -->
    <script type="text/javascript">
        var _tmr = _tmr || [];
        _tmr.push({id: 2553999, type: "pageView", start: (new Date()).getTime()});
        (function (d, w) {
            var ts = d.createElement("script"); ts.type = "text/javascript"; ts.async = true;
            ts.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//top-fwz1.mail.ru/js/code.js";
            var f = function () {var s = d.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ts, s);};
            if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); }
        })(document, window);
    </script>
    <noscript>
        <div style="position:absolute;left:-10000px;">
            <img src="//top-fwz1.mail.ru/counter?id=2553999;js=na" style="border:0;" height="1" width="1" alt="Рейтинг@Mail.ru" />
        </div>
    </noscript>
    <!-- //Rating@Mail.ru counter -->

    <!-- Rating@Mail.ru rem -->
    <script type="text/javascript">
        var _tmr = _tmr || [];
        _tmr.push({
            type: 'itemView',
            productid: [<?= count($productIds) ? "'" . implode("','", array_map(function($productId) use($page) { return $page->helper->escapeJavaScript($productId); }, $productIds)) . "'" : '' ?>],
            pagetype: '<?= $page->helper->escapeJavaScript($pageType) ?>',
            list: '',
            totalvalue: '<?= $page->helper->escapeJavaScript($price) ?>'
        });
    </script>
    <!-- Rating@Mail.ru rem -->
<? endif ?>