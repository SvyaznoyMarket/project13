<?php

return function(
    \Helper\TemplateHelper $helper,
    array $links
) { ?>
    <!-- Хлебные крохи -->
    <ul class="bBreadcrumbs clearfix">
    <? $i = 1; $count = count($links); foreach ($links as $link): ?>
        <li class="bBreadcrumbs__eItem<? if ($i == $count): ?> mLast<? endif ?>">
            <a class="bBreadcrumbs__eLink" href="<?= $link['url'] ?>"><?= $link['name'] ?></a>
        </li>
    <? $i++; endforeach ?>
    </ul>
    <!-- /Хлебные крохи -->
<? };