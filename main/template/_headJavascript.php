<? if (\App::config()->analytics['enabled']): ?>
    <!-- KISS -->
    <script type="text/javascript">
        var _kmq = _kmq || [];
        var _kmk = _kmk || '3cb5e4fbdc85838975bae7d45d9ee9a2e045399c';
        function _kms(u){
            setTimeout(function(){
                var d = document, f = d.getElementsByTagName('script')[0],
                    s = d.createElement('script');
                s.type = 'text/javascript'; s.async = true; s.src = u;
                f.parentNode.insertBefore(s, f);
            }, 1);
        }
        _kms('//i.kissmetrics.com/i.js');
        _kms('//doug1izaerwt3.cloudfront.net/' + _kmk + '.1.js');
    </script>
<? endif ?>


<? if (\App::config()->analytics['optimizelyEnabled']): ?>
    <!-- Optimizely -->
    <!-- <script src="//cdn.optimizely.com/js/204544654.js"></script>-->
<? endif ?>
