<div id="order1click-container" style="display:none; width:300px; height:200px">
	<div class="fl">
		<div data-bind="text: title">TEST</div>
		<img alt="" data-bind="attr: {src: icon}" />
		<div data-bind="text: price">TEST</div>
		
	</div>
	<div class="fr">
		<select data-bind="event: { change: changeDlvr }">
			<option value="4">Самовывоз</option>
			<option value="2">Доставка</option>			
		</select>	
		<select>
		<!-- ko foreach : dates -->
			<option value="1" data-bind="text: text"></option>
		<!-- /ko -->
		</select>		
	</div>

</div>