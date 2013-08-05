<? if ( \App::config()->partners['criteo']['enabled'] ): ?>
    <? if ( !empty($criteoData) && is_array($criteoData) ):  ?>
        <script src="//static.criteo.net/js/ld/ld.js" async="true"></script> <? /* // https: and http: — works */ ?>
        <div id="criteoJS" class="jsanalytics" data-value="<?= $page->json($criteoData) ?>"></div>
            <?
            /* <sxript> >// ### This variant not works in IE9 :-(

            var criteo_arr = $('#criteo-data').data('value');

            if ( criteo_arr && $.isArray(criteo_arr) ) {
                try{
                    window.criteo_q.push(criteo_arr);
                    console.log(criteo_arr);
                } catch(e) {

                }
            }
            */
            /*
            ?>


            window.criteo_q.push(
                <?
                $echrows = [];
                //TODO: проверить, используются ли методы потипу stringRowsParams4js() и удалить их
                foreach($criteoData as $row) $echrows[] = $page->helper->stringRowsParams4js($row);
                echo implode(','.PHP_EOL, $echrows);
                ?>
            );

            </sxript> //////


            <? */
            /* // example:
            { event: "setAccount", account: 10442 },
            { event: "setCustomerId", id: " $userId" },
            { event: "setSiteType", type: "m for mobile or t for tablet or d for desktop" },
            { event: "$routeName"},
            { event: "viewItem", item: "Your item id" },
            */
            ?>
    <? endif; ?>
<? endif;