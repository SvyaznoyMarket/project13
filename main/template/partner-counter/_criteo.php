<? if ( \App::config()->partners['criteo']['enabled'] ): ?>
    <? if ( !empty($criteoData) ) { ?>
        <? /* // https: and http: — works */ ?>
        <div id="criteo-data" data-value="<?= $page->json($criteoData) ?>"></div>
        <script type="text/javascript" src="//static.criteo.net/js/ld/ld.js" async="true"></script>

        <script type="text/javascript">
            window.criteo_q = window.criteo_q || [];
            var criteo_arr = $('#criteo-data').data('value');
            window.criteo_q.push(criteo_arr);
            <? /* //for (var i in criteo_arr){ ] */
            /*
            window.criteo_q.push(
                <?
                //$echrows = [];
                //foreach($criteoData as $row) $echrows[] = $page->helper->stringRowsParams4js($row);
                //echo implode(','.PHP_EOL, $echrows);
                ?>
            );
            */

            /*
            //example:
            { event: "setAccount", account: 10442 },
            { event: "setCustomerId", id: "<?= $userId ?>" },
            { event: "setSiteType", type: "m for mobile or t for tablet or d for desktop" },
            { event: "<?= $routeName ?>"},
            { event: "viewItem", item: "Your item id" },
            */
            ?>
        </script>
    <? }
    ?>
<? endif;