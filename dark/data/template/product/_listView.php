<?php
/**
 * @var $page \View\Layout
 * @var $view string
 * @var $request \Http\Request
 * @var $category \Model\Product\Category\Entity|null
 */

$helper = new \View\Helper();
?>

<?php
if (!isset($view)) $view = $request->get('view', 'compact');

$list = array(
    array(
        'name'  => 'compact',
        'title' => 'компактный',
        'class' => 'tableview',
    ),
    array(
        'name'  => 'expanded',
        'title' => 'расширенный',
        'class' => 'listview',
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
<div class="view">
    <span>Вид страницы:</span>
    <?php foreach ($list as $item): ?>
        <a href="<?php echo $item['url'] ?>" class="<?php echo $item['class'] . ($item['current'] ? ' active' : '') ?>" title="<?php echo $item['title'] ?>"><?php echo $item['title'] ?></a>
    <?php endforeach ?>
</div>
<!-- View -->