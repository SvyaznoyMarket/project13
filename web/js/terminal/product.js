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

		// toggle subItems
		if ($('.bGoodSubItems_eTitle').length){
			if ( !$('.jsAccessorise').length ){
				$('.jsSimilar').addClass('active')
				$('#similarSlider').show()
			}
			$('.bGoodSubItems_eTitle').bind('click', function(){
				$('.bGoodSubItems_eTitle').removeClass('active')
				$(this).addClass('active')
				if ( $(this).hasClass('jsAccessorise') ){
					$('#similarSlider').hide()
					$('#accessoriseSlider').show()
					console.log('1')
				}
				else if ( $(this).hasClass('jsSimilar') ){
					$('#accessoriseSlider').hide()
					$('#similarSlider').show()
					console.log('2')
				}
				else{
					return false
				}
			})
		}

	// end of DOM-ready
	})
})