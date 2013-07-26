<?php

$livetexID = null;

if (isset(\App::config()->partners['livetex']['liveTexID'])) {
    $livetexID = \App::config()->partners['livetex']['liveTexID'];
}

if ($livetexID && \App::config()->partners['livetex']['enabled']) :

    $user = \App::user();
    $user_entity = $user->getEntity();
    $userid = null;
    $username = null;
    if (isset($user_entity) and !empty($user_entity)) {
        $userid = $user_entity->getId();
        $username = $user_entity->getFirstName();
        $tmp = $user_entity->getLastName();
        if ($tmp) $username .= ' ' . $tmp;
        if (empty($username)) $username = 'Покупатель';
        $username = str_replace("'", "", $username);
    }

    $LiveTexData = [
        'livetexID' => $livetexID,
        'username' => $username,
        'userid' => $userid,
    ];

    ?>
    <div id="LiveTexJS" class="jsanalytics" data-value="<?= $page->json($LiveTexData) ?>"></div>
<? endif;