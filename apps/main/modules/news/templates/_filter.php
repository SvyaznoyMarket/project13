<ul>
<?php foreach($filter as $item): ?>
  <li><?php echo $item['category']->name ?></li>
  <ul>
  <?php foreach ($item['year'] as $year => $months): ?>
    <li><?php echo link_to($year." (".$months['count'].")", 'newsCategory_year', array('newsCategory' => $item['category'], 'year' => $year, )) ?></li>
    <ul>
    <?php foreach ($months['months'] as $month => $count): ?>
      <?php echo link_to($month." (".$count.")", 'newsCategory_month', array('newsCategory' => $item['category'], 'year' => $year, 'month' => $month, )) ?><br />
    <?php endforeach; ?>
    </ul>
  <?php endforeach; ?>
  </ul>
<?php endforeach; ?>
</ul>