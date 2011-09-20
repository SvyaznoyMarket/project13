<style type="text/css">
.message {
  padding: 2px 4px;
  background: #91be3f;
  color: #ffffff;
}
.message.error {
  background: #cd0a0a;
}

table td {
  vertical-align: top;
  padding: 4px 6px;
}
</style>


<h1>Список задач</h1>


<?php if ($sf_user->hasFlash('error')): ?>
  <p class="message error"><?php echo $sf_user->getFlash('error') ?></p>
<?php elseif ($sf_user->hasFlash('message')): ?>
  <p class="message"><?php echo $sf_user->getFlash('message') ?></p>
<?php endif ?>


<table>
  <tr>
    <th>Тип</th>
    <th>Код</th>
    <th>Приоритет</th>
    <th>Статус</th>
    <th>Содержание</th>
    <th>Создана</th>
    <th>Модифицирована</th>
  </tr>
  <?php foreach ($taskList as $task): ?>
  <tr>
    <td><?php echo $task['type']?></td>
    <td><?php echo $task['token']?></td>
    <td><?php echo $task['priority']?></td>
    <td><?php echo $task['status']?></td>
    <td>
      <pre><?php echo $task['content']?></pre>
    </td>
    <td><?php echo $task['created_at']?></td>
    <td><?php echo $task['updated_at']?></td>
  </tr>
  <?php endforeach ?>
</table>


<?php echo link_to('Перегрузить данные', 'default_init', array(), array('confirm' => 'Выполнить перезагрузку данных?')) ?>