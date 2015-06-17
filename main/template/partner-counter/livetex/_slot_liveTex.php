<?php
/**
 * @var $page   \View\DefaultLayout
 */

$livetexID = isset(\App::config()->partners['livetex']['liveTexID']) ? \App::config()->partners['livetex']['liveTexID'] : null;

if ($livetexID && \App::config()->partners['livetex']['enabled'] && \App::request()->getPathInfo() !== $page->url('compare')) :

    $LiveTexData = [
        'livetexID' => $livetexID,
        'username' => null,
        'userid' => null,
    ];

    ?>
    <div id="LiveTexJS" class="jsanalytics" data-value="<?= $page->json($LiveTexData) ?>"></div>
<? endif;