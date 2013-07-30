<?php
/**
 * @var $page     \View\Layout
 * @var $view     string
 * @var $request  \Http\Request
 * @var $category \Model\Product\Category\Entity|null
 */

$helper = new \View\Helper();
?>

<?php
if (!isset($view)) $view = $request->get('view', 'compact');

$list = array(
    array(
        'name'  => 'compact',
        'title' => '',
        'class' => 'mTableView',
    ),
    array(
        'name'  => 'expanded',
        'title' => '',
        'class' => 'mListView',
    ),
);

foreach ($list as &$item) {
    $excluded = ($category && ($item['name'] == $category->getProductView()))
        ? array('view' => $item['name'])
        : null;

    $item = array_merge($item, array(
        'url'     => $helper->replacedUrl(array('view' => $item['name']), $excluded),
        'current' => $view == $item['name'],
    ));
} if (isset($item)) unset($item);
?>

<!-- View -->
<div class="bViewPageMode">
    <span class="bTitle">Вид</span>
    <ul class="bViewPageModeList">
        <?php foreach ($list as $item): ?>
            <li class="bViewPageModeList__eItem">
                <a href="<?php echo $item['url'] ?>" class="bViewPageModeList__eItemLink <?php echo $item['class'] . ($item['current'] ? ' mActiveLink' : '') ?>" title="<?php echo $item['title'] ?>"><span class="bViewPageModeIcon"><?php echo $item['title'] ?></span></a>
            </li>
        <?php endforeach ?>
    </ul>
</div>
<!-- View -->