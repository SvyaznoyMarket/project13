<?
/**
 * @var $sorting \Model\Product\Sorting
 * @var $helper \Helper\TemplateHelper
 */

$links = [];

$active = $sorting->getActive();
$active['url'] = $helper->replacedUrl(['sort' => implode('-', [$active['name'], $active['direction']]), 'ajax' => null]);

if ($active['name'] == 'default') {
    $active['url'] = $helper->replacedUrl(['sort' => null, 'ajax' => null]);
}

foreach ($sorting->getAll() as $item) {
    // SITE-2244
    // Убрал сортировку по брендам
    if ($item['name'] == 'creator') {
        continue;
    }

    $item['url'] = $helper->replacedUrl(['page' => '1', 'sort' => implode('-', [$item['name'], $item['direction']]), 'ajax' => null]);
    $item['datasort'] = implode('-', [$item['name'], $item['direction']]);

    if ($item['name'] == 'default') {
        $item['url'] = $helper->replacedUrl(['sort' => null, 'ajax' => null]);
        $item['default'] = true;
    }

    if ($active['name'] == $item['name'] && $active['direction'] == $item['direction']) {
        $item['active'] = true;
    }

    $links[] = $item;
}

?>

<ul class="sorting_lst fl-l js-category-sorting">
    <li class="sorting_i sorting_i-tl">Сортировать</li>

    <? foreach ($links as $item) : ?>
        <li  data-sort="<?= $item['datasort'] ?>" class="sorting_i <? if ($item['active']): ?>act js-category-sorting-activeItem<? endif ?> <? if ($item['default']): ?> js-category-sorting-defaultItem<? endif ?> js-category-sorting-item">
            <a class="sorting_lk jsSorting" data-sort="<?= $item['datasort'] ?>" href="<?= $item['url'] ?>"><?= $item['title'] ?></a>
        </li>
    <? endforeach ?>

</ul>
