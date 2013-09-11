<?php

return function(
    \Helper\TemplateHelper $helper
) { ?>

    <div class="bSortingLine mPagerBottom clearfix">
        <ul class="bSortingList">
            <li class="bSortingList__eItem mTitle">Страницы</li>

            <li class="bSortingList__eItem mSortItem mActive"><a class="bSortingList__eLink" href="">1</a></li>
            <li class="bSortingList__eItem mSortItem"><a class="bSortingList__eLink" href="">2</a></li>
            <li class="bSortingList__eItem mSortItem"><a class="bSortingList__eLink" href="">3</a></li>
            <li class="bSortingList__eItem mSortItem mDotted">&#8230;</li>
            <li class="bSortingList__eItem mSortItem"><a class="bSortingList__eLink" href="">48</a></li>
            <li class="bSortingList__eItem mSortItem"><a class="bSortingList__eLink mMore" href="">&#8734;</a></li>
        </ul>
    </div>

<? };