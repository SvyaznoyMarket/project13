$(document).ready(function(){
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