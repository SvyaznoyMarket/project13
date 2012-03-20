<?php slot('title', $title) ?>
<?php slot('navigation') ?>
<?php include_component('default', 'navigation', $sf_data) ?>
<?php end_slot() ?>
<?php slot('page_breadcrumbs') ?>
<?php include_partial('page/breadcrumbs', array('addToBreadcrumbs' => (isset($addToBreadcrumbs)? $addToBreadcrumbs : null))) ?>
<?php end_slot() ?>

<div class="float100">
  <div class='bCreditLine'>

    <img class='bCreditLine__eLogo' src='/images/credit_logo.png'>
    <h2>Какие кредитные программы есть в Enter?</h2>
    <span>Мы предлагаем кредитные программы совместно с четырьмя банками:  &laquo;Хоум Кредит энд Финанс Банк&raquo;, &laquo;Ренессанс Капитал&raquo;, &laquo;ОТП Банк&raquo; и &laquo;Альфа-банк&raquo;. Получить кредит легко - просто приходите <a href="<?php echo url_for('shop') ?>">в любой магазин Enter</a>.</span><br>
    <b id="requirementsFullInfoHref">Требования к заемщику банков-партнеров минимальны</b><br/>
    <ul class='bCreditLine2' style="display: none;">
      <li><i>Хоум Кредит энд Финанс Банк.</i> Вы должны быть гражданином России* старше 18 лет, со стажем работы не менее трех месяцев.</li>
      <li><i>Ренессанс Капитал.</i> Вы должны быть гражданином России возрастом от 20 лет до 65 лет, имеющим ежемесячный доход не менее 6000 рублей и стаж работы не менее трех месяцев.</li>
      <li><i>ОТП Банк.</i> Вы должны быть гражданином России старше 18 лет, проживающим на территории России и имеющим постоянный доход.</li>
      <li><i>Альфа-банк.</i> Вы должны быть гражданином России* возрастом старше 19 лет (для женщин) или 22 лет (для мужчин).</li>
      <br />* обязательно наличие регистрации в регионе оформления кредита.
    </ul>
    <span>Для получения кредита необходим <strong>только паспорт Российской Федерации</strong> и хорошее настроение. Наличие второго документа (водительского или пенсионного удостоверения, заграничного паспорта или страхового свидетельства обязательного пенсионного страхования) увеличивает вероятность одобрения кредита банком и позволяет получить более выгодные условия.</span>
    <h2>Кредитные программы в Enter</h2>
    <select id="productSelector">
      <option value="0">Что вы будете брать в кредит</option>
    </select>

    <div id="bankProductInfoContainer"></div>

  </div>
</div>

<script type="text/html" id="bank_program_list_tmpl">
  <div class="line"></div>
  <dl>
    <dt><img src="<%=bankImage%>"></dt>
    <dd>
      <h3><%=bankName%></h3>
      <%=programNames%>
    </dd>
  </dl>
</script>

<input type="hidden" id="creditOptions" data-value='<?php echo json_encode($creditOptions); ?>'/>
<input type="hidden" id="bankInfo" data-value='<?php echo json_encode($banks); ?>'/>
<input type="hidden" id="relations" data-value='<?php echo json_encode($optionsToBanksRelations); ?>'/>