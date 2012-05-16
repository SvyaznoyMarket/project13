<h2>Выбираем услуги</h2>
<div class="line pb10"></div>
<?php
foreach ($list as $item) {
  if ($item['level'] == 1) {
    $current1 = $item;
    $level1IsFree = true;
    $firstIsSet = false;
  } elseif ($item['level'] == 2) {
    $current2 = $item;
    $level2IsFree = true;
    $secondIsSet = false;
  } elseif ($item['level'] == 3) {
    if (count($item['ServiceRelation'])) {
      if (!$firstIsSet) {
        $result[] = $current1;
        $firstIsSet = true;
      }
      if (!$secondIsSet) {
        $result[] = $current2;
        $secondIsSet = true;
      }
      $level1IsFree = false;
      $level2IsFree = false;
    }
  }
}
# print_r( $result );
?>
<?php
$lastLevel = 0;
foreach ($result as $item) {
  if ($item['level'] == 1) {
    if ($lastLevel) echo '</ul>';
    echo '<h2>' . $item['name'] . '</h2>';
  } elseif ($item['level'] == 2) {
    if ($lastLevel == 1) echo '<ul class="leftmenu pb10">';
    echo '<li>';
    if ($serviceCategory['id'] == $item['id']) echo '<strong class="motton">' . $item['name'] . '</strong>';
    else echo '<a href="' . url_for('service_list') . '/' . $item['token'] . '">' . $item['name'] . '</a>';
    echo '</li>';
    $childrenAreFree = true;
  }
  $lastLevel = $item['level'];
}
?>
</ul>