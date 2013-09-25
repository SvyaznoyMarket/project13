<?
/**
 * @var $criteoData array
 */
if ( \App::config()->partners['criteo']['enabled'] ):
    if ( !empty($criteoData) && is_array($criteoData) ):

        ?><script src="//static.criteo.net/js/ld/ld.js" async="true"></script><? /* // https: and http: — works */

        ?><div id="criteoJS" class="jsanalytics" data-value="<?= $page->json($criteoData) ?>"></div><?


            //TODO: проверить, используются ли методы потипу stringRowsParams4js() и удалить их

    endif;
endif;