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


<? if ( \App::config()->partners['criteo']['enabled'] ): ?>
    <? /* // https: and http: — works */ ?>
    <script type="text/javascript" src="//static.criteo.net/js/ld/ld.js" async="true"></script>


    <? if ( !empty($criteo_q) ) { ?>
    <script type="text/javascript">
        window.criteo_q = window.criteo_q || [];
        window.criteo_q.push(
<?          foreach($criteo_q as $row):
                echo $page->helper->stringRowsParams4js($row).','.PHP_EOL;
            endforeach;
            ?>
            <?
            /* //example:
            { event: "setAccount", account: 10442 },
            { event: "setCustomerId", id: "<?= $userId ?>" },
            { event: "setSiteType", type: "m for mobile or t for tablet or d for desktop" },
            { event: "<?= $routeName ?>"},
            { event: "viewItem", item: "Your item id" },
            */
            ?>
        );
    </script>
    <? } ?>


    ###
    <? //tmp:
    //print_r( $user->getId() );
    ?> $$$ <?
    //print_r( $user->getId() );
    ?>
    &&&

<? endif ?>
