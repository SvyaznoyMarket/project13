<?php
return function (
    $trustfactorContent
) {

    if (!count($trustfactorContent)) {
        return;
    }

    ?>
    <div class="mt15"><?
    foreach ((array)$trustfactorContent as $trustfactorItem) {
        ?>
        <div class="trustfactorContent">
            <?= empty($trustfactorItem) ? '' : $trustfactorItem ?>
        </div>
    <?
    }
    ?></div><?

};