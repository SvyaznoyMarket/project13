<script type="text/javascript">
    (function () {
        try {
            var orderSum = <?= $orderSum ?>;
            var orderNum = "<?= $orderNum ?>";

            var script = document.createElement('script');

            script.src = ('https:' == document.location.protocol ? 'https://' : 'http://') +
                    unescape('bn.adblender.ru%2Fpixel.js%3Fclient%3Denter%26cost%3D') + escape(orderSum) +
                    unescape('%26order%3D') + escape(orderNum) + unescape('%26r%3D') + Math.random();

            document.getElementsByTagName('head')[0].appendChild(script);
        } catch (e) {
        }
    })();
</script>