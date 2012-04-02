<div class='bMobDownWrapAbs mMapPopup hidden'>
  <div class='bMobDownWrapRel'>

    <div class='bMobDown mBR5 mW2'>
      <div class='bMobDown__eWrap'>
        <div class='bMobDown__eClose close'></div>
        <div class='bMapShops__eMapWrap' id="mapPopup" >
        </div>
        <div class='bMapShops__eList' data-bind="with: productforPopup()">
          <div class='bMapShops__eListTitle'>
            <img data-bind="attr: { src: img }" alt=""/>
            <b data-bind="text: title"></b>
            <br/>(<span data-bind="text: price"></span> <span class="rubl">p</span>)
          </div>
          <h3>Выберите магазин Enter для самовывоза</h3>
          <ul data-bind="foreach: $root.popupWithShops">
            <li data-bind="click: $root.shiftAndClose">
              <div class='bMapShops__eListNum'><img data-bind="attr: { src: markerImg }" alt=""/></div>
              <div data-bind="text: title"></div>
              <span>Работаем </span><span data-bind="text: fromto"></span>
            </li>
          </ul>
        </div>
      </div>
    </div>

  </div>
</div>