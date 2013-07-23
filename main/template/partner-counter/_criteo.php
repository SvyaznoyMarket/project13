<? if ( \App::config()->partners['criteo']['enabled'] ): ?>
    <? if ( !empty($criteoData) ):  ?>

        <div id="criteo-data" data-value="<?= $page->json($criteoData) ?>"></div>

        <script src="//static.criteo.net/js/ld/ld.js" async="true"></script> <? /* // https: and http: — works */ ?>

        <script>
            window.criteo_q = window.criteo_q || [];

            var criteo_arr = $('#criteo-data').data('value');

            if ( criteo_arr && $.isArray(criteo_arr) ) {
                try{
                    window.criteo_q.push(criteo_arr);
                    console.log(criteo_arr);
                } catch(e) {

                }
            }



            <? /* //old: //for (var i in criteo_arr){ ] */
            /* //old:
            window.criteo_q.push(
                <?
                //$echrows = [];
                //foreach($criteoData as $row) $echrows[] = $page->helper->stringRowsParams4js($row);
                //echo implode(','.PHP_EOL, $echrows);
                ?>
            );
            */

            /* //old:
            //example:
            { event: "setAccount", account: 10442 },
            { event: "setCustomerId", id: "<?= $userId ?>" },
            { event: "setSiteType", type: "m for mobile or t for tablet or d for desktop" },
            { event: "<?= $routeName ?>"},
            { event: "viewItem", item: "Your item id" },
            */
            ?>
        </script>
    <? endif; ?>
<? endif;