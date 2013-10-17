<div class="hf">

  <select name="region_list" id="region_list" data-service="<?= $page->json($serviceJson) ?>">
    <? $regions = array_keys($serviceJson) ?>
    <? foreach ($regions as $key => $region) { ?>
      <? if(preg_match('/москва/ui', $region)) { ?>
        <option value="<?= $region ?>"><?= $region ?></option>
        <? unset($regions[$key]) ?>
      <? } ?>
    <? } ?>
    <? foreach ($regions as $region) { ?>
      <option value="<?= $region ?>"><?= $region ?></option>
    <? } ?>
  </select>

  <table id="bServicesTable" class="bServicesTable">
    <thead>
      <tr>
        <th></th>
        <th><strong>Услуга</strong></th>
        <th><strong>Стоимость (руб.)</strong></th>
      </tr>
    </thead><!-- шапка таблицы -->
      <tbody>
      </tbody><!-- тело таблицы -->
  </table>

  <script type="text/html" id="groupHeaderTemplate">
    <tr>
      <th></th>
      <th><strong><%=group%></strong></th>
      <th></th>
    </tr>
  </script><!-- заголовок группы -->

  <script type="text/html" id="rowTemplate">
    <tr>
      <td><%=counter%></td>
      <td><%=service%></td>
      <td><%=price%></td>
    </tr>
  </script><!-- строка таблицы -->

</div>

