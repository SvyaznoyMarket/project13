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
      <li><i>Хоум Кредит энд Финанс Банк</i> Вы должны быть гражданином России старше 18 лет, иметь постоянную регистрацию в регионе приобретения товара и стаж работы на нынешнем месте работы не менее трех месяцев.</li>
      <li><i>Ренессанс Капитал</i> вы должны быть гражданином России возрастом не менее 20 лет (для женщин) или 22 (для мужчин) и не более 65 лет. Кроме того, вы должны иметь постоянную регистрацию на территории России, ежемесячный доход не менее 6000 рублей и стаж работы на нынешнем месте работы не менее трех месяцев.</li>
      <li><i>ОТП Банк</i> Вы должны быть гражданином России старше 18 лет, проживать на территории России и иметь постоянный доход.</li>
      <li><i>Альфа-банк</i> вы должны быть гражданином России возрастом не менее 19 лет (для женщин) или 22 (для мужчин) и иметь постоянную регистрацию не территории России.</li>
    </ul>
    <span>Если вы не являетесь гражданином России, для получения кредита вам необходимо иметь постоянную регистрацию в регионе оформления кредита.</span>
    <span>Для получения кредита необходим <strong>только паспорт гражданина РФ</strong> и хорошее настроение, впрочем, наличие второго документа (водительского или пенсионного удостоверения, заграничного паспорта или страхового свидетельства обязательного пенсионного страхования) увеличивает вероятность одобрения кредита банком и позволяет получить более выгодные условия.</span>
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

  <script type="text/javascript">
    $(document).ready(function(){
        document.getElementById("requirementsFullInfoHref").style.cursor="pointer";
        $('#requirementsFullInfoHref').bind('click', function() {
          $('.bCreditLine2').toggle();
        });

        var creditOptions = $.parseJSON('<?php echo json_encode($creditOptions); ?>');
        var bankInfo = $.parseJSON('<?php echo json_encode($banks); ?>');
        var relations = $.parseJSON('<?php echo json_encode($optionsToBanksRelations); ?>');

        for (var i=0; i< creditOptions.length; i++){
          creditOption = creditOptions[i];
          $('<option>').val(creditOption.id).text(creditOption.name).appendTo("#productSelector");
        }

        $('#productSelector').change(function() {
          var key = $(this).val();
          var bankRelations = relations[key];
          $('#bankProductInfoContainer').empty();
          for(i in bankRelations){
            var dtmpl={}
            dtmpl.bankName = bankInfo[i].name;
            dtmpl.bankImage = bankInfo[i].image;

            programNames = '';

            for(j in bankRelations[i]){
              programNames += "<h4>" + bankInfo[i].programs[j].name + "</h4>\r\n<ul>";
              for(k in bankInfo[i].programs[j].params){
                programNames += "\t<li>" + bankInfo[i].programs[j].params[k] + "</li>\r\n";
              }
              programNames += "</ul>";
            }

            dtmpl.programNames = programNames;

            var show_bank = tmpl('bank_program_list_tmpl', dtmpl)
            $('#bankProductInfoContainer').append(show_bank);
          }
          $('#bankProductInfoContainer').append('<p class="ac mb25"><a class="bBigOrangeButton" href="'+creditOptions[key-1]['url']+'">'+creditOptions[key-1]['button_name']+'</a></p>');
        });
      }
    );

  </script>