<?php
/**
 * @var $page          \View\Layout
 * @var $productFilter \Model\Product\Filter
 */
?>

<?php
$formFilter = new \View\Product\FilterForm($productFilter);
$list = $formFilter->getSelected();

if(\App::config()->sphinx['showListingSearchBar']) {
    $filterValues = $productFilter->getValues();
    $sphinxFilter = isset($filterValues['text']) ? $filterValues['text'] : null;
    if($sphinxFilter) {
        $sphinxWords = explode(' ', $sphinxFilter);
        $sphinxList = array_map(function($sphinxWord) use ($sphinxWords) {
            return [
                'url' => (new \View\DefaultLayout())->helper->replacedUrl(['f[text]' => implode(' ', array_diff($sphinxWords, [$sphinxWord]))]),
                'title' => 'Поиск',
                'type' => 'string',
                'name' => $sphinxWord,
            ];
        }, $sphinxWords);
        $list = array_merge($sphinxList, $list);
    } ?>
    <div class="currentSearch" data-search-terms="<?= $sphinxFilter ?>"></div>
<? } ?>

<? if ((bool)$list): ?>
    <dd class="bSpecSel">
        <h3>Ваш выбор:</h3>
        <ul>
            <? foreach ($list as $item): ?>
                <? if (\App::request()->get('shop')) {
                        $item['url'] .= (false === strpos($item['url'], '?') ? '?' : '&') . 'shop='. \App::request()->get('shop');
                } ?>
                <li>
                    <a href="<?= $item['url'] ?>" title="<?= $item['title'] ?>"><b>x</b> <?= $item['name'] ?><?= ('price' == $item['type'] ? '&nbsp;<span class="rubl">p</span>' : '') ?></a>
                </li>
            <? endforeach ?>
        </ul>
        <a class="bSpecSel__eReset" href="<?= $page->url('product.category', array('categoryPath' => $productFilter->getCategory()->getPath())) ?>">сбросить все</a>
    </dd>
<? endif ?>