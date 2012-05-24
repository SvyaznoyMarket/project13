$(document).ready(function(){
	/* iPadPromo*/
	if( $('#oneClickPromo').length ) {
		$('.halfline .bOrangeButton').click( function() {
			var halfline = $(this).parent().parent()
			var ipad = {}
			ipad.token = halfline.find('.ttl').text()
			ipad.price = halfline.find('.price').text()
			ipad.image = halfline.find('img').attr('src')
			$('#ipadwrapper').html( tmpl('ipad', ipad) )
			$('#order1click-container-new').lightbox_me({})
		})
		
		$('#oneClickPromo').submit( function(e) {
			e.preventDefault()
			return false
		})
		
		if( typeof( $.mask ) !== 'undefined' ) {
			$.mask.definitions['n'] = "[()0-9\ \-]"
			$("#phonemask").mask("8nnnnnnnnnnnnnnnnn", { placeholder: " ", maxlength: 10 } )
		}
		
		function emptyValidation( node ) {
			if( node.val().replace(/\s/g,'') === '' ) {
				node.addClass('mEmpty')
				node.after( $('<span class="mEmpty">(!) Пожалуйста, верно заполните поле</span>') )
				return false
			} else {
				if( node.hasClass('mEmpty') ) {
					node.removeClass('mEmpty')
					node.parent().find('.mEmpty').remove()
				}
			}
			return true	
		}

		$('#oneClickPromo input[type=text]').change( function() {
			emptyValidation( $(this) )
		})
		
		function _f_success() { 
			$('#f_success').show()
			$('#f_init').hide()
		}
		
		function _f_error() { 
			$('#oneClickPromo input[type=text]').removeAttr('disabled') 
			$('#f_init h2').text('Произошла ошибка :( Попробуйте ещё')
			button.text('Отправить предзаказ')
		}		
		
		$('.bBigOrangeButton').click( function(e) {
			e.preventDefault()
			$('#oneClickPromo input[type=text]').trigger('change')
			if( $('.mEmpty').length )
				return
				
			var button = $(this)
			button.text('Идёт отправка...')
			var data= $('#oneClickPromo').serializeArray()
			$('#oneClickPromo input[type=text]').attr('disabled','disabled') 
			var url = $('#oneClickPromo').attr('action')
			$.ajax( {
				url: url,
				data: data,
				success: function( resp ) {
				if( !( 'success' in resp ) ) {
					_f_error()
					return false
				}
				if( resp.success !== 'ok' ) {
					_f_error()
					return false
				}
				_f_success()	
				return true
			}
			})
			
			
		})
	}
	
	/* Credits inline */
	if( $('.bCreditLine').length ) {
		document.getElementById("requirementsFullInfoHref").style.cursor="pointer";
		$('#requirementsFullInfoHref').bind('click', function() {
		  $('.bCreditLine2').toggle();
		});

		var creditOptions = $('#creditOptions').data('value');
		var bankInfo = $('#bankInfo').data('value');
		var relations = $('#relations').data('value');

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
			  programNames += "<h4>" + bankInfo[i].programs[bankRelations[i][j]].name + "</h4>\r\n<ul>";
			  for(k in bankInfo[i].programs[bankRelations[i][j]].params){
				programNames += "\t<li>" + bankInfo[i].programs[bankRelations[i][j]].params[k] + "</li>\r\n";
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

	/* Mobile apps inline */
	if( $('.bMobileApps').length ) {
		var openSelector = ''

		function hideQRpopup() {
			$(openSelector).hide()
		}
		function showQRpopup( selector ) {
			openSelector = selector
			$(selector).show()
			return false
		}

		$('body').bind('click.mob', hideQRpopup)
		$("div.bMobDown").click(function(e){
			e.stopPropagation()
		})

		$('.bMobDown__eClose').click( function() {
			hideQRpopup()
			return false
		})

		$(".android-load").click( function(){ showQRpopup( ".android-block" ); return false; } )
		$(".iphone-load").click(  function(){ showQRpopup( ".iphone-block" );  return false; } )
		$(".symbian-load").click( function(){ showQRpopup( ".symbian-block" ); return false; } )
	}
})