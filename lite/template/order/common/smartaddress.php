<?php return function(

) { ?>

    <ul class="orderCol_addrs_fld textfield clearfix jsAddressRootNode" style="height: inherit">

        <li class="orderCol_addrs_fld_i" data-bind="visible: streetName().length > 0 " style="display: none">
            <span class="orderCol_addrs_fld_n" data-bind="text: streetType"></span><span class="orderCol_addrs_fld_val jsSmartAddressEditField" data-bind="text: streetName" data-type="streetName"></span>
        </li>

        <li class="orderCol_addrs_fld_i" data-bind="visible: buildingName().length > 0 " style="display: none">
            <span class="orderCol_addrs_fld_n">Дом</span><span class="orderCol_addrs_fld_val jsSmartAddressEditField" data-bind="text: buildingName" data-type="buildingName"></span>
        </li>

        <li class="orderCol_addrs_fld_i" data-bind="visible: apartmentName().length > 0 " style="display: none">
            <span class="orderCol_addrs_fld_n">Квартира</span><span class="orderCol_addrs_fld_val jsSmartAddressEditField" data-bind="text: apartmentName" data-type="apartmentName"></span>
        </li>

        <li class="orderCol_addrs_fld_i orderCol_addrs_fld_i-edit ui-front">
            <span id="addressInputPrefix" class="addrsAutocmpltLbl" data-bind="text: inputPrefix, visible: apartmentName() == ''"></span><input class="jsSmartAddressInput" name="address" type="text" data-bind="visible: inputFocus() && apartmentName() == '', uniqueName: true" />
        </li>
    </ul>

<? }; ?>
 