<?php return function(

) { ?>

    <ul class="order-delivery-info-address__field it jsAddressRootNode" style="height: inherit">

        <li class="order-delivery-info-address__field-item" data-bind="visible: streetName().length > 0 " style="display: none">
            <span class="order-delivery-info-address__field-name" data-bind="text: streetType"></span><span class="order-delivery-info-address__field-value jsSmartAddressEditField" data-bind="text: streetName" data-type="streetName"></span>
        </li>

        <li class="order-delivery-info-address__field-item" data-bind="visible: buildingName().length > 0 " style="display: none">
            <span class="order-delivery-info-address__field-name">Дом</span><span class="order-delivery-info-address__field-value jsSmartAddressEditField" data-bind="text: buildingName" data-type="buildingName"></span>
        </li>

        <li class="order-delivery-info-address__field-item" data-bind="visible: apartmentName().length > 0 " style="display: none">
            <span class="order-delivery-info-address__field-name">Квартира</span><span class="order-delivery-info-address__field-value jsSmartAddressEditField" data-bind="text: apartmentName" data-type="apartmentName"></span>
        </li>

        <li class="order-delivery-info-address__field-item order-delivery-info-address__field-item_edit ui-front">
            <span id="addressInputPrefix" class="addrsAutocmpltLbl" data-bind="text: inputPrefix, visible: apartmentName() == ''"></span><input class="jsSmartAddressInput" name="address" type="text" data-bind="visible: inputFocus() && apartmentName() == '', uniqueName: true" />
        </li>
    </ul>

<? }; ?>
