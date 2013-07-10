<?php
/**
 * Created by JetBrains PhpStorm.
 * User: juljan
 * Date: 10.7.13
 * Time: 10.40
 * To change this template use File | Settings | File Templates.
 */
//print_r($page);
//print_r($productFilter);
//print_r($productPagersByCategory);
?>

<? if ( \App::config()->partners['criteo']['enabled'] ): ?>
    <? /* // https: and http: — works */ ?>
    <script type="text/javascript" src="//static.criteo.net/js/ld/ld.js" async="true"></script>


    <? if ( !empty($criteo_q) ) { ?>
        <script type="text/javascript">
            window.criteo_q = window.criteo_q || [];
            window.criteo_q.push(
                <?  foreach($criteo_q as $row):
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


<? endif;