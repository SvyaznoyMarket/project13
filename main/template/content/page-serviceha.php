<select name="region_list" id="region_list" data-service="<?= $page->json($serviceJson) ?>">
  <? foreach (array_keys($serviceJson) as $region) { ?>
    <option value="<?= $region ?>"><?= $region ?></option>
  <? } ?>
</select>

<table class="bServicesTable">
  <thead>
    <tr>
      <th></th>
      <th><strong>Услуга</strong></th>
      <th><strong>Стоимость (руб.)</strong></th>
    </tr>
  </thead><!-- шапка таблицы -->
  <tbody>
    <tr id="row_template" class="hf">
      <td></td>
      <td></td>
      <td></td>
    </tr><!-- строка таблицы -->
  </tbody><!-- тело таблицы -->
</table>

<div class="pb20"></div>