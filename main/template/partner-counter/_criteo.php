<?
/**
 * @var $criteoData array
 */
if ( \App::config()->partners['criteo']['enabled'] && !empty($criteoData) ):
/*
    ?><script src="//static.criteo.net/js/ld/ld.js" async="true"></script><?
*/
    ?><div id="criteoJS" class="jsanalyticsParsed" data-value="<?= $page->json($criteoData) ?>"></div><?
    /* специально ставим jsanalyticsParsed,
     чтобы не грузился скрипт автоматом. Но подгрузим его вручную в web/js/dev/ports/VisitorSplit.js
     */
endif;