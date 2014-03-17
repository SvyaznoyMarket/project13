<?
/**
 * @var $criteoData array
 */
if ( \App::config()->partners['criteo']['enabled'] && !empty($criteoData) ):
/*
    ?><script src="//static.criteo.net/js/ld/ld.js" async="true"></script><?
*/
    ?><div id="criteoJS" class="jsanalytics" data-value="<?= $page->json($criteoData) ?>"></div><?

endif;