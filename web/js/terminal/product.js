// // product terminal
define('product',
	['jquery', 'library'], function ($) {
    $(document).ready(function() {
	//product code

		// slider
		if ($('#similarSlider').length){
			var slider = $('#similarSlider')
			slider.width( slider.find('.bGoodSubItem_eGoods').length * (slider.find('.bGoodSubItem_eGoods').width()+20) )
			slider.draggable()
		}
		if ($('#accessoriseSlider').length){
			var slider = $('#accessoriseSlider')
			slider.width( slider.find('.bGoodSubItem_eGoods').length * (slider.find('.bGoodSubItem_eGoods').width()+20) )
			slider.draggable()
		}
		
		
	// end of DOM-ready
	})
})