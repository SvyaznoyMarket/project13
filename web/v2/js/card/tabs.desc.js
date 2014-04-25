/**
 * Окно смены региона
 *
 */
$(function(){

		var body = $('body'),

			tabWrap = $('.js-tab-wrap'),
			tabWrapWidth = $('.js-tab-selector').width(),

			tabList = tabWrap.find('.js-tab'),

			tab = tabList.find('.js-cont'),
			tabCount = tab.length,
			
			tabLink = $('.js-tabs-link'),

			posLeft = 0;
		// end of vars	
	
		var 
			tabsToggle = function tabsToggle() {
				console.log('tabsToggle');

				tabWrapWidth = $('.js-tab-selector').width();
				tabWrap.css({'height' : tab.first().height() })

				tabWrap.css({'width' : tabWrapWidth})
				tab.css({'width': tabWrapWidth});	
				tabList.css({'width' : tabWrapWidth * tabCount});
				
				tabLink.removeClass('productDescTab_link__active');
				tabLink.first().addClass('productDescTab_link__active');
				tabList.stop(true, true).animate({'left' : 0});
				tabWrap.stop(true, true).animate({'height' : tab.first().height() })

				console.log(tabWrapWidth);
			},

			tabsSlide = function tabsSlide( event, inx ) {
					
				event.preventDefault();
					
				var $self = $(this),
					tabLinkId = $self.data('tab'),
					tabId = tab.filter('[data-desc="js-cont-'+tabLinkId+'"]');

				if ( tabLinkId == 0) {
					posLeft = 0;
				}
				else {
					posLeft = tabWrapWidth * tabLinkId;
				}
				
				$('html,body').animate({
					scrollTop: $self.offset().top - $('.header').outerHeight()}, 400, 
					function(){
                		$('html,body').clearQueue();
            		}
            	); 

				tabLink.removeClass('productDescTab_link__active');
				$self.addClass('productDescTab_link__active');
				tabList.stop(true, true).animate({'left' : -posLeft});
				tabWrap.stop(true, true).animate({'height' : tabId.height() });

				console.log(tabLinkId);
			};
		//end of function
		
	$(window).on('load resize', tabsToggle);
	tabLink.on('click', tabsSlide);
});