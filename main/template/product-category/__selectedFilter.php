<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter
) {

?>

    <!-- Списоки выбранных параметров -->
    <div class="bFilterFoot">
        <ul class="bFilterCheckedParams clearfix">
            <li class="bFilterCheckedParams__eItem mTitle">Цена</li>

            <li class="bFilterCheckedParams__eItem mParams"><a class="bDelete" href=""></a><span class="bParamsName">от 2 000p</span></li>

            <li class="bFilterCheckedParams__eItem mParams"><a class="bDelete" href=""></a><span class="bParamsName">до 1 000 000p</span></li>
        </ul>

        <ul class="bFilterCheckedParams clearfix mLast">
            <li class="bFilterCheckedParams__eItem mTitle">Бренд</li>

            <li class="bFilterCheckedParams__eItem mParams"><a class="bDelete" href=""></a><span class="bParamsName">Ahava</span></li>

            <li class="bFilterCheckedParams__eItem mParams"><a class="bDelete" href=""></a><span class="bParamsName">Bubchen</span></li>

            <li class="bFilterCheckedParams__eItem mParams"><a class="bDelete" href=""></a><span class="bParamsName">Агентство старинных развлечений "Работорцы"</span></li>

            <li class="bFilterCheckedParams__eItem mParams mClearAll"><a class="bDelete" href=""><strong class="bParamsName">Очистить все</strong></a></li> <!-- Добаялется только в списке идущем по очереди последним -->
        </ul>
    </div>
    <!-- /Списоки выбранных параметров -->

<? };