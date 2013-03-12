// // product terminal
define('product',
	['jquery', 'library', 'termAPI'], function ($, library, termAPI) {
    $(document).ready(function() {

    	library.myConsole('product.js loaded')

		// product id
		var productId = $('.bGoodItem').data('productid')
		library.myConsole('productid '+productId)

		// check compare this product
		termAPI.checkCompare(productId)

		//
		// slider
		//
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

		//
		// toggle subItems
		//
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
				}
				else if ( $(this).hasClass('jsSimilar') ){
					$('#accessoriseSlider').hide()
					$('#similarSlider').show()
				}
				else{
					return false
				}
			})
		}			

		//
		// scroll to full Description
		//
		$('.bGoodDescBlock_eMore').bind('click', function(){
			library.scrollTo($('.bGoodItemFullDesc'), 100, 300)
		})

		//
		// helpers
		//
		if ( $('.bQuestionIco').length ){

			var popUped = false
			var popUp = $('#bHintPopup')
			$('.bQuestionIco').bind('click', function(e){
				var hint = $(this).find('.jsHint').html()
				var title = $(this).parent().find('.bGoodSpecification_eSpecTitle').html()
				var hintContent = popUp.find('.bHintPopup_eContent')
				hintContent.html(hint)
				hintContent.prepend('<h2>'+title+'</h2>')
				pH = popUp.height()/2
				popUp.css('top', e.pageY - pH).fadeIn(300, function(){
					popUped = true
				})
			})

			$('.bWrap').bind('click', function(event){
				// library.myConsole('tick! '+popUped)
				if (popUped){
					event.preventDefault()
					popUp.fadeOut(300, function(){
						popUped = false
					})
				}
			})
		}

	// end of DOM-ready
	})
})