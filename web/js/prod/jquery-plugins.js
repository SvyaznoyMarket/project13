/*
 * Copyright (C) 1999-2009 Jive Software. All rights reserved.
 *
 * This software is the proprietary information of Jive Software. Use is subject to license terms.
 */

/*
* $ lightbox_me
* By: Buck Wilson
* Version : 2.2 + fix by ivn
*
* Licensed under the Apache License, Version 2.0 (the "License");
* you may not use this file except in compliance with the License.
* You may obtain a copy of the License at
*
*     http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS,
* WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and
* limitations under the License.
*/


(function($) {

    $.fn.lightbox_me = function(options) {

        return this.each(function() {

            var
                opts = $.extend({}, $.fn.lightbox_me.defaults, options),
                $overlay = $('div.' + opts.classPrefix + '_overlay'),
                $self = $(this),
                $iframe = $('iframe#lb_iframe'),
                ie6 = ($.browser.msie && $.browser.version < 7);

            if (($overlay.length > 0) && opts.removeOtherOnCreate) {
                $overlay[0].removeModal(); // if the overlay exists, then a modal probably exists. Ditch it!
            } else {
                $overlay =  $('<div class="' + opts.classPrefix + '_overlay" style="display:none;"/>'); // otherwise just create an all new overlay.
            }

            $iframe = ($iframe.length > 0) ? $iframe : $iframe = $('<iframe id="lb_iframe" style="z-index: ' + (opts.zIndex + 1) + '; display: none; border: none; margin: 0; padding: 0; position: absolute; width: 100%; height: 100%; top: 0; left: 0;"/>');

            /*----------------------------------------------------
               DOM Building
            ---------------------------------------------------- */
            if (ie6) {
                var src = /^https/i.test(window.location.href || '') ? 'javascript:false' : 'about:blank';
                $iframe.attr('src', src);
                $('body').append($iframe);
            } // iframe shim for ie6, to hide select elements
            if( opts.reallyBig ) {
            	$('body').append( $('<div>').addClass('bPopupWrap').append($self) ).append($overlay)
            } else {
            	$('body').append($self).append($overlay)
            }

            /*----------------------------------------------------
               CSS stuffs
            ---------------------------------------------------- */
			function setHeight( jnode ) {
				var jHeight = ($(window).height() - 80);
				if( jHeight < 500 ) 
					jHeight = 500
				jnode.css('height', jHeight)
			}
            // set css of the modal'd window
            
			if( opts.reallyBig ) { // fix for mediaLibrary by IVN
				$self.css({marginLeft: '50px', marginRight:  '50px' });
				setWrapPosition( $self.parent() );
				setHeight( $self );
			} else {
				$self.css({position: 'fixed'}) //IVN fixed on repeated opening
            	setSelfPosition();
            	$self.css({left: '50%', marginLeft: ($self.outerWidth() / 2) * -1,  zIndex: (opts.zIndex + 3) });
			}
            // set css of the overlay

            setOverlayHeight(); // pulled this into a function because it is called on window resize.
            //IVN $overlay.css({ position: 'absolute', width: '100%', top: 0, left: 0, right: 0, bottom: 0, zIndex: (opts.zIndex + 2) })
            $overlay.css({ position: 'fixed', width: '100%', height:'100%', top: 0, left: 0, zIndex: (opts.zIndex + 2) })
                    .css(opts.overlayCSS);

            /*----------------------------------------------------
               Animate it in.
            ---------------------------------------------------- */
            if( opts.autofocus ) {
                var cusLoad = opts.onLoad
                opts.onLoad = function() {
                    cusLoad()
                    $self.find('input:text').first().focus()
                }
            }
            if ($overlay.is(":hidden")) {
                $overlay.fadeIn(opts.overlaySpeed, function() {
                    $self[opts.appearEffect](opts.lightboxSpeed, function() { setOverlayHeight(); opts.onLoad()});
                });
            } else {
                $self[opts.appearEffect](opts.lightboxSpeed, function() { setOverlayHeight(); opts.onLoad()});
            }

            /*----------------------------------------------------
               Bind Events
            ---------------------------------------------------- */
            $(window).resize(setOverlayHeight)
                     .resize( function(){ ( opts.reallyBig ) ? setWrapPosition( $self.parent() ) : setSelfPosition() })//IVN
                     .resize( function(){ if( opts.reallyBig ) setHeight( $self ) } )
                     .scroll( function(){ if ( !opts.reallyBig ) setSelfPosition() })//IVN
                     .keydown(observeEscapePress);
			$('body').addClass('showModal');
            $self.find(opts.closeSelector).click(function() { removeModal(true); return false; });
            $overlay.click( function(e) { 
            	e.preventDefault();
            	return false;
            });
            function overlayclick () {
            	$overlay.click( function() { 
            		if(opts.closeClick){ removeModal(true); return false;} 
            	})
            }
            setTimeout( overlayclick,500 );


            $self.bind('close.lme', function() { removeModal(true) });
            $self.bind('resize.lme', function(){ ( opts.reallyBig ) ? setWrapPosition( $self.parent() ) : setSelfPosition() });//IVN
            $overlay[0].removeModal = removeModal;

            /*----------------------------------------------------------------------------------------------------------------------------------------
              ---------------------------------------------------------------------------------------------------------------------------------------- */

            /*----------------------------------------------------
               Private Functions
            ---------------------------------------------------- */


            function removeModal(removeO) {
                // fades & removes modal, then unbinds events
                $self[opts.disappearEffect](opts.lightboxDisappearSpeed, function() {

                    if (removeO) {
                      removeOverlay();
                    }

                    opts.destroyOnClose ? $self.remove() : $self.hide()
                    if( opts.reallyBig ) { $('.bPopupWrap').remove() }

                    $self.find(opts.closeSelector).unbind('click');
                    $self.unbind('.lme');//IVN
                    //$self.unbind('close');
                    //$self.unbind('resize');
                    $(window).unbind('scroll', setSelfPosition);
                    $(window).unbind('resize', setSelfPosition);

					$('body').removeClass('showModal');
                });
            }


            function removeOverlay() {
                // fades & removes overlay, then unbinds events
                $overlay.fadeOut(opts.overlayDisappearSpeed, function() {
                    $(window).unbind('resize', setOverlayHeight);

                    $overlay.remove();
                    $overlay.unbind('click');


                    opts.onClose();

                })
            }



            /* Function to bind to the window to observe the escape key press */
            function observeEscapePress(e) {
                if((e.keyCode == 27 || (e.DOM_VK_ESCAPE == 27 && e.which==0)) && opts.closeEsc) removeModal(true);
            }

            /* Set the height of the overlay
                    : if the document height is taller than the window, then set the overlay height to the document height.
                    : otherwise, just set overlay height: 100%
            */
            function setOverlayHeight() {
                if ($(window).height() < $(document).height()) {
                    $overlay.css({height: $(document).height() + 'px'});
                } else {
                    $overlay.css({height: '100%'});
                    if (ie6) {$('html,body').css('height','100%'); } // ie6 hack for height: 100%; TODO: handle this in IE7
                }
            }

            /* Set the position of the modal'd window ($self)
                    : if $self is taller than the window, then make it absolutely positioned
                    : otherwise fixed
            */
            function setWrapPosition( $node ) {//IVN
            	var s = $node[0].style;
            	//var topOffset = $(document).scrollTop() + 40;
            	var topOffset = window.pageYOffset;
            	if (! topOffset ) 
            		topOffset = document.documentElement.scrollTop ;
				$node.css({position: 'absolute', top: (topOffset + 40)*1 + 'px', marginTop: 0})
				if (ie6) {
					s.removeExpression('top');
				}
            }

            function setSelfPosition() {
                var s = $self[0].style;

                if (($self.height() + 80  >= $(window).height() || !opts.sticky) && ($self.css('position') != 'absolute' || ie6)) {
                    var topOffset = $(document).scrollTop() + 40;
                    $self.css({position: 'absolute', top: topOffset + 'px', marginTop: 0})
                    if (ie6) {
                        s.removeExpression('top');
                    }
                } else if ($self.height()+ 80  < $(window).height() && opts.sticky) {
                    if (ie6) {
                        s.position = 'absolute';
                        if (opts.centered) {
                            s.setExpression('top', '(document.documentElement.clientHeight || document.body.clientHeight) / 2 - (this.offsetHeight / 2) + (blah = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop) + "px"')
                            s.marginTop = 0;
                        } else {
                            var top = (opts.modalCSS && opts.modalCSS.top) ? parseInt(opts.modalCSS.top) : 0;
                            s.setExpression('top', '((blah = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop) + '+top+') + "px"')
                        }
                    } else {
                        if (opts.centered) {
                            $self.css({ position: 'fixed', top: '50%', marginTop: ($self.outerHeight() / 2) * -1})
                        } else {
                            $self.css({ position: 'fixed'}).css(opts.modalCSS);
                        }
                    }
                }
            }
        });
    };


    $.fn.lightbox_me.defaults = {

        // animation when appears
        appearEffect: "fadeIn",
        overlaySpeed: 300,
        lightboxSpeed: "fast",

        // animation when dissapears
        disappearEffect: "fadeOut",
        overlayDisappearSpeed: 300,
        lightboxDisappearSpeed: "fast",

        // close
        closeSelector: ".close",
        closeClick: true,
        closeEsc: true,

        // behavior
        destroyOnClose: false,

        // callbacks
        onLoad: function() {},
        onClose: function() {},
        removeOtherOnCreate: true, // удалять другие окна при создании
        autofocus: false, 

        // style
        classPrefix: 'lb',
        zIndex: 999,
        centered: false,
		sticky: true,
        modalCSS: {top: '40px'},
        overlayCSS: {background: 'black', opacity: .4}
    }


})(jQuery);
/**
 * Copyright (c) 2007-2013 Ariel Flesler - aflesler<a>gmail<d>com | http://flesler.blogspot.com
 * Dual licensed under MIT and GPL.
 * @author Ariel Flesler
 * @version 1.4.6
 */
;(function($){var h=$.scrollTo=function(a,b,c){$(window).scrollTo(a,b,c)};h.defaults={axis:'xy',duration:parseFloat($.fn.jquery)>=1.3?0:1,limit:true};h.window=function(a){return $(window)._scrollable()};$.fn._scrollable=function(){return this.map(function(){var a=this,isWin=!a.nodeName||$.inArray(a.nodeName.toLowerCase(),['iframe','#document','html','body'])!=-1;if(!isWin)return a;var b=(a.contentWindow||a).document||a.ownerDocument||a;return/webkit/i.test(navigator.userAgent)||b.compatMode=='BackCompat'?b.body:b.documentElement})};$.fn.scrollTo=function(e,f,g){if(typeof f=='object'){g=f;f=0}if(typeof g=='function')g={onAfter:g};if(e=='max')e=9e9;g=$.extend({},h.defaults,g);f=f||g.duration;g.queue=g.queue&&g.axis.length>1;if(g.queue)f/=2;g.offset=both(g.offset);g.over=both(g.over);return this._scrollable().each(function(){if(e==null)return;var d=this,$elem=$(d),targ=e,toff,attr={},win=$elem.is('html,body');switch(typeof targ){case'number':case'string':if(/^([+-]=?)?\d+(\.\d+)?(px|%)?$/.test(targ)){targ=both(targ);break}targ=$(targ,this);if(!targ.length)return;case'object':if(targ.is||targ.style)toff=(targ=$(targ)).offset()}$.each(g.axis.split(''),function(i,a){var b=a=='x'?'Left':'Top',pos=b.toLowerCase(),key='scroll'+b,old=d[key],max=h.max(d,a);if(toff){attr[key]=toff[pos]+(win?0:old-$elem.offset()[pos]);if(g.margin){attr[key]-=parseInt(targ.css('margin'+b))||0;attr[key]-=parseInt(targ.css('border'+b+'Width'))||0}attr[key]+=g.offset[pos]||0;if(g.over[pos])attr[key]+=targ[a=='x'?'width':'height']()*g.over[pos]}else{var c=targ[pos];attr[key]=c.slice&&c.slice(-1)=='%'?parseFloat(c)/100*max:c}if(g.limit&&/^\d+$/.test(attr[key]))attr[key]=attr[key]<=0?0:Math.min(attr[key],max);if(!i&&g.queue){if(old!=attr[key])animate(g.onAfterFirst);delete attr[key]}});animate(g.onAfter);function animate(a){$elem.animate(attr,f,g.easing,a&&function(){a.call(this,targ,g)})}}).end()};h.max=function(a,b){var c=b=='x'?'Width':'Height',scroll='scroll'+c;if(!$(a).is('html,body'))return a[scroll]-$(a)[c.toLowerCase()]();var d='client'+c,html=a.ownerDocument.documentElement,body=a.ownerDocument.body;return Math.max(html[scroll],body[scroll])-Math.min(html[d],body[d])};function both(a){return typeof a=='object'?a:{top:a,left:a}}})(jQuery);
/*
* Placeholder plugin for jQuery
* ---
* Copyright 2010, Daniel Stocks (http://webcloud.se)
* Released under the MIT, BSD, and GPL Licenses.
*/
(function($) {
    function Placeholder(input) {
        this.input = input;
        if (input.attr('type') == 'password') {
            this.handlePassword();
        }
        // Prevent placeholder values from submitting
        $(input[0].form).submit(function() {
            if (input.hasClass('placeholder') && input[0].value == input.attr('placeholder')) {
                input[0].value = '';
            }
        });
    }
    Placeholder.prototype = {
        show : function(loading) {
            // FF and IE saves values when you refresh the page. If the user refreshes the page with
            // the placeholders showing they will be the default values and the input fields won't be empty.
            if (this.input[0].value === '' || (loading && this.valueIsPlaceholder())) {
                if (this.isPassword) {
                    try {
                        this.input[0].setAttribute('type', 'text');
                    } catch (e) {
                        this.input.before(this.fakePassword.show()).hide();
                    }
                }
                this.input.addClass('placeholder');
                this.input[0].value = this.input.attr('placeholder');
            }
        },
        hide : function() {
            if (this.valueIsPlaceholder() && this.input.hasClass('placeholder')) {
                this.input.removeClass('placeholder');
                this.input[0].value = '';
                if (this.isPassword) {
                    try {
                        this.input[0].setAttribute('type', 'password');
                    } catch (e) { }
                    // Restore focus for Opera and IE
                    this.input.show();
                    this.input[0].focus();
                }
            }
        },
        valueIsPlaceholder : function() {
            return this.input[0].value == this.input.attr('placeholder');
        },
        handlePassword: function() {
            var input = this.input;
            input.attr('realType', 'password');
            this.isPassword = true;
            // IE < 9 doesn't allow changing the type of password inputs
            if ($.browser.msie && input[0].outerHTML) {
                var fakeHTML = $(input[0].outerHTML.replace(/type=(['"])?password\1/gi, 'type=$1text$1'));
                this.fakePassword = fakeHTML.val(input.attr('placeholder')).addClass('placeholder').focus(function() {
                    input.trigger('focus');
                    $(this).hide();
                });
                $(input[0].form).submit(function() {
                    fakeHTML.remove();
                    input.show()
                });
            }
        }
    };
    var NATIVE_SUPPORT = !!("placeholder" in document.createElement( "input" ));
    $.fn.placeholder = function() {
        return NATIVE_SUPPORT ? this : this.each(function() {
            var input = $(this);
            var placeholder = new Placeholder(input);
            placeholder.show(true);
            input.focus(function() {
                placeholder.hide();
            });
            input.blur(function() {
                placeholder.show(false);
            });

            // On page refresh, IE doesn't re-populate user input
            // until the window.onload event is fired.
            if ($.browser.msie) {
                $(window).load(function() {
                    if(input.val()) {
                        input.removeClass("placeholder");
                    }
                    placeholder.show(true);
                });
                // What's even worse, the text cursor disappears
                // when tabbing between text inputs, here's a fix
                input.focus(function() {
                    if(this.value == "") {
                        var range = this.createTextRange();
                        range.collapse(true);
                        range.moveStart('character', 0);
                        range.select();
                    }
                });
            }
        });
    }
})(jQuery);
// JavaScript Document
/*Caruosel ------------------------------------------------------------------------------------------------------*/
$.fn.infiniteCarousel = function () {

    function repeat(str, num) {
        return new Array( num + 1 ).join( str );
    }
  
    return this.each(function () {
        var $wrapper = $('> div', this).css('overflow', 'hidden'),
            $slider = $wrapper.find('> ul'),
            $items = $slider.find('> li'),
            $single = $items.filter(':first'),
            
            singleWidth = $single.outerWidth(), 
            visible = Math.ceil($wrapper.innerWidth() / singleWidth), // note: doesn't include padding or border
            currentPage = 1,
            pages = Math.ceil($items.length / visible);            


        // 1. Pad so that 'visible' number will always be seen, otherwise create empty items
        if (($items.length % visible) != 0) {
            $slider.append(repeat('<li class="empty" />', visible - ($items.length % visible)));
            $items = $slider.find('> li');
        }

        // 2. Top and tail the list with 'visible' number of items, top has the last section, and tail has the first
        $items.filter(':first').before($items.slice(- visible).clone().addClass('cloned'));
        $items.filter(':last').after($items.slice(0, visible).clone().addClass('cloned'));
        $items = $slider.find('> li'); // reselect
        
        // 3. Set the left position to the first 'real' item
        $wrapper.scrollLeft(singleWidth * visible);
        
        // 4. paging function
        function gotoPage(page) {
            var dir = page < currentPage ? -1 : 1,
                n = Math.abs(currentPage - page),
                left = singleWidth * dir * visible * n;
            
            $wrapper.filter(':not(:animated)').animate({
                scrollLeft : '+=' + left
            }, 500, function () {
                if (page == 0) {
                    $wrapper.scrollLeft(singleWidth * visible * pages);
                    page = pages;
                } else if (page > pages) {
                    $wrapper.scrollLeft(singleWidth * visible);
                    // reset back to start position
                    page = 1;
                } 

                currentPage = page;
            });                
            
            return false;
        }
        
        $wrapper.after('<a class="arrow back" title="Назад">&lt;</a><a class="arrow forward" title="Вперед">&gt;</a>');
        
        // 5. Bind to the forward and back buttons
        $('a.back', this).click(function () {
            return gotoPage(currentPage - 1);                
        });
        
        $('a.forward', this).click(function () {
            return gotoPage(currentPage + 1);
        });
        
        // create a public interface to move to a specific page
        $(this).bind('goto', function (event, page) {
            gotoPage(page);
        });
    });  
};
/* /Carousel ----------------------------------------------------------------------------------------------------------*/

(function($){

    /**
     * Copyright 2012, Digital Fusion
     * Licensed under the MIT license.
     * http://teamdf.com/jquery-plugins/license/
     *
     * @author Sam Sehnert
     * @desc A small plugin that checks whether elements are within
     *       the user visible viewport of a web browser.
     *       only accounts for vertical position, not horizontal.
     * @url https://github.com/teamdf/jquery-visible
     */
    var $w = $(window);
    $.fn.visible = function(partial,hidden,direction){

        if (this.length < 1)
            return;

        var $t        = this.length > 1 ? this.eq(0) : this,
            t         = $t.get(0),
            vpWidth   = $w.width(),
            vpHeight  = $w.height(),
            direction = (direction) ? direction : 'both',
            clientSize = hidden === true ? t.offsetWidth * t.offsetHeight : true;

        if (typeof t.getBoundingClientRect === 'function'){

            // Use this native browser method, if available.
            var rec = t.getBoundingClientRect(),
                tViz = rec.top    >= 0 && rec.top    <  vpHeight,
                bViz = rec.bottom >  0 && rec.bottom <= vpHeight,
                lViz = rec.left   >= 0 && rec.left   <  vpWidth,
                rViz = rec.right  >  0 && rec.right  <= vpWidth,
                vVisible   = partial ? tViz || bViz : tViz && bViz,
                hVisible   = partial ? lViz || lViz : lViz && rViz;

            if(direction === 'both')
                return clientSize && vVisible && hVisible;
            else if(direction === 'vertical')
                return clientSize && vVisible;
            else if(direction === 'horizontal')
                return clientSize && hVisible;
        } else {

            var viewTop         = $w.scrollTop(),
                viewBottom      = viewTop + vpHeight,
                viewLeft        = $w.scrollLeft(),
                viewRight       = viewLeft + vpWidth,
                offset          = $t.offset(),
                _top            = offset.top,
                _bottom         = _top + $t.height(),
                _left           = offset.left,
                _right          = _left + $t.width(),
                compareTop      = partial === true ? _bottom : _top,
                compareBottom   = partial === true ? _top : _bottom,
                compareLeft     = partial === true ? _right : _left,
                compareRight    = partial === true ? _left : _right;

            if(direction === 'both')
                return !!clientSize && ((compareBottom <= viewBottom) && (compareTop >= viewTop)) && ((compareRight <= viewRight) && (compareLeft >= viewLeft));
            else if(direction === 'vertical')
                return !!clientSize && ((compareBottom <= viewBottom) && (compareTop >= viewTop));
            else if(direction === 'horizontal')
                return !!clientSize && ((compareRight <= viewRight) && (compareLeft >= viewLeft));
        }
    };

})(jQuery);
/*
* jQuery.fn.typewriter( speed, callback );
*
* Typewriter, writes your text in a flow
*
* USAGE:
* $('.element').typewriter( speed, callback );
*
*
* Version 1.0.1
* www.labs.skengdon.com/typewriter/
* www.labs.skengdon.com/typewriter/js/typewriter.min.js
*/
;(function($){
	$.fn.typewriter = function( speed, callback ) {
		if ( typeof callback !== 'function' ) callback = function(){};
		var write = function( e, text, time ) {
			var next = $(e).text().length + 1;
			if ( next <= text.length ) {
				$(e).text( text.substr( 0, next ) );
				setTimeout( function( ) {
					write( e, text, time );
				}, time);
			} else {
				e.callback();
			}
		};
		return this.each(function() {
			this.callback = callback;
			var text = $(this).text();
			var time = speed/text.length;
			
			$(this).text('');
			
			write( this, text, time )
		});
	}
}(jQuery));

/*
 jQuery Masked Input Plugin
 Copyright (c) 2007 - 2014 Josh Bush (digitalbush.com)
 Licensed under the MIT license (http://digitalbush.com/projects/masked-input-plugin/#license)
 Version: 1.4.0
 */
!function(factory) {
	"function" == typeof define && define.amd ? define([ "jquery" ], factory) : factory("object" == typeof exports ? require("jquery") : jQuery);
}(function($) {
	var caretTimeoutId, ua = navigator.userAgent, iPhone = /iphone/i.test(ua), chrome = /chrome/i.test(ua), android = /android/i.test(ua);
	$.mask = {
		definitions: {
			"9": "[0-9]",
			a: "[A-Za-z]",
			"*": "[A-Za-z0-9]"
		},
		autoclear: !0,
		dataName: "rawMaskFn",
		placeholder: "_"
	}, $.fn.extend({
		caret: function(begin, end) {
			var range;
			if (0 !== this.length && !this.is(":hidden")) return "number" == typeof begin ? (end = "number" == typeof end ? end : begin,
				this.each(function() {
					this.setSelectionRange ? this.setSelectionRange(begin, end) : this.createTextRange && (range = this.createTextRange(),
						range.collapse(!0), range.moveEnd("character", end), range.moveStart("character", begin),
						range.select());
				})) : (this[0].setSelectionRange ? (begin = this[0].selectionStart, end = this[0].selectionEnd) : document.selection && document.selection.createRange && (range = document.selection.createRange(),
				begin = 0 - range.duplicate().moveStart("character", -1e5), end = begin + range.text.length),
			{
				begin: begin,
				end: end
			});
		},
		unmask: function() {
			return this.trigger("unmask");
		},
		mask: function(mask, settings) {
			var input, defs, tests, partialPosition, firstNonMaskPos, lastRequiredNonMaskPos, len, oldVal;
			if (!mask && this.length > 0) {
				input = $(this[0]);
				var fn = input.data($.mask.dataName);
				return fn ? fn() : void 0;
			}
			return settings = $.extend({
				autoclear: $.mask.autoclear,
				placeholder: $.mask.placeholder,
				completed: null
			}, settings), defs = $.mask.definitions, tests = [], partialPosition = len = mask.length,
				firstNonMaskPos = null, $.each(mask.split(""), function(i, c) {
				"?" == c ? (len--, partialPosition = i) : defs[c] ? (tests.push(new RegExp(defs[c])),
				null === firstNonMaskPos && (firstNonMaskPos = tests.length - 1), partialPosition > i && (lastRequiredNonMaskPos = tests.length - 1)) : tests.push(null);
			}), this.trigger("unmask").each(function() {
				function tryFireCompleted() {
					if (settings.completed) {
						for (var i = firstNonMaskPos; lastRequiredNonMaskPos >= i; i++) if (tests[i] && buffer[i] === getPlaceholder(i)) return;
						settings.completed.call(input);
					}
				}
				function getPlaceholder(i) {
					return settings.placeholder.charAt(i < settings.placeholder.length ? i : 0);
				}
				function seekNext(pos) {
					for (;++pos < len && !tests[pos]; ) ;
					return pos;
				}
				function seekPrev(pos) {
					for (;--pos >= 0 && !tests[pos]; ) ;
					return pos;
				}
				function shiftL(begin, end) {
					var i, j;
					if (!(0 > begin)) {
						for (i = begin, j = seekNext(end); len > i; i++) if (tests[i]) {
							if (!(len > j && tests[i].test(buffer[j]))) break;
							buffer[i] = buffer[j], buffer[j] = getPlaceholder(j), j = seekNext(j);
						}
						writeBuffer(), input.caret(Math.max(firstNonMaskPos, begin));
					}
				}
				function shiftR(pos) {
					var i, c, j, t;
					for (i = pos, c = getPlaceholder(pos); len > i; i++) if (tests[i]) {
						if (j = seekNext(i), t = buffer[i], buffer[i] = c, !(len > j && tests[j].test(t))) break;
						c = t;
					}
				}
				function androidInputEvent() {
					var curVal = input.val(), pos = input.caret();
					if (curVal.length < oldVal.length) {
						for (checkVal(!0); pos.begin > 0 && !tests[pos.begin - 1]; ) pos.begin--;
						if (0 === pos.begin) for (;pos.begin < firstNonMaskPos && !tests[pos.begin]; ) pos.begin++;
						input.caret(pos.begin, pos.begin);
					} else {
						for (checkVal(!0); pos.begin < len && !tests[pos.begin]; ) pos.begin++;
						input.caret(pos.begin, pos.begin);
					}
					tryFireCompleted();
				}
				function blurEvent() {
					checkVal(), input.val() != focusText && input.change();
				}
				function keydownEvent(e) {
					if (!input.prop("readonly")) {
						var pos, begin, end, k = e.which || e.keyCode;
						oldVal = input.val(), 8 === k || 46 === k || iPhone && 127 === k ? (pos = input.caret(),
							begin = pos.begin, end = pos.end, end - begin === 0 && (begin = 46 !== k ? seekPrev(begin) : end = seekNext(begin - 1),
							end = 46 === k ? seekNext(end) : end), clearBuffer(begin, end), shiftL(begin, end - 1),
							e.preventDefault()) : 13 === k ? blurEvent.call(this, e) : 27 === k && (input.val(focusText),
							input.caret(0, checkVal()), e.preventDefault());
					}
				}
				function keypressEvent(e) {
					if (!input.prop("readonly")) {
						var p, c, next, k = e.which || e.keyCode, pos = input.caret();
						if (!(e.ctrlKey || e.altKey || e.metaKey || 32 > k) && k && 13 !== k) {
							if (pos.end - pos.begin !== 0 && (clearBuffer(pos.begin, pos.end), shiftL(pos.begin, pos.end - 1)),
									p = seekNext(pos.begin - 1), len > p && (c = String.fromCharCode(k), tests[p].test(c))) {
								if (shiftR(p), buffer[p] = c, writeBuffer(), next = seekNext(p), android) {
									var proxy = function() {
										$.proxy($.fn.caret, input, next)();
									};
									setTimeout(proxy, 0);
								} else input.caret(next);
								pos.begin <= lastRequiredNonMaskPos && tryFireCompleted();
							}
							e.preventDefault();
						}
					}
				}
				function clearBuffer(start, end) {
					var i;
					for (i = start; end > i && len > i; i++) tests[i] && (buffer[i] = getPlaceholder(i));
				}
				function writeBuffer() {
					input.val(buffer.join(""));
				}
				function checkVal(allow) {
					var i, c, pos, test = input.val(), lastMatch = -1;
					for (i = 0, pos = 0; len > i; i++) if (tests[i]) {
						for (buffer[i] = getPlaceholder(i); pos++ < test.length; ) if (c = test.charAt(pos - 1),
								tests[i].test(c)) {
							buffer[i] = c, lastMatch = i;
							break;
						}
						if (pos > test.length) {
							clearBuffer(i + 1, len);
							break;
						}
					} else buffer[i] === test.charAt(pos) && pos++, partialPosition > i && (lastMatch = i);
					return allow ? writeBuffer() : partialPosition > lastMatch + 1 ? settings.autoclear || buffer.join("") === defaultBuffer ? (input.val() && input.val(""),
						clearBuffer(0, len)) : writeBuffer() : (writeBuffer(), input.val(input.val().substring(0, lastMatch + 1))),
						partialPosition ? i : firstNonMaskPos;
				}
				var input = $(this), buffer = $.map(mask.split(""), function(c, i) {
					return "?" != c ? defs[c] ? getPlaceholder(i) : c : void 0;
				}), defaultBuffer = buffer.join(""), focusText = input.val();
				input.data($.mask.dataName, function() {
					return $.map(buffer, function(c, i) {
						return tests[i] && c != getPlaceholder(i) ? c : null;
					}).join("");
				}), input.one("unmask", function() {
					input.off(".mask").removeData($.mask.dataName);
				}).on("focus.mask", function() {
					if (!input.prop("readonly")) {
						clearTimeout(caretTimeoutId);
						var pos;
						focusText = input.val(), pos = checkVal(), caretTimeoutId = setTimeout(function() {
							writeBuffer(), pos == mask.replace("?", "").length ? input.caret(0, pos) : input.caret(pos);
						}, 10);
					}
				}).on("blur.mask", blurEvent).on("keydown.mask", keydownEvent).on("keypress.mask", keypressEvent).on("input.mask paste.mask", function() {
					input.prop("readonly") || setTimeout(function() {
						var pos = checkVal(!0);
						input.caret(pos), tryFireCompleted();
					}, 0);
				}), chrome && android && input.off("input.mask").on("input.mask", androidInputEvent),
					checkVal();
			});
		}
	});
});
// jQuery plugin: PutCursorAtEnd 1.0
// http://plugins.jquery.com/project/PutCursorAtEnd
// by teedyay
//
// Puts the cursor at the end of a textbox/ textarea

// codesnippet: 691e18b1-f4f9-41b4-8fe8-bc8ee51b48d4
(function($)
{
    jQuery.fn.putCursorAtEnd = function()
    {
    return this.each(function()
    {
        // $(this).focus()

        // If this function exists...
        if (this.setSelectionRange)
        {
        // ... then use it
        // (Doesn't work in IE)

        // Double the length because Opera is inconsistent about whether a carriage return is one character or two. Sigh.
        var len = $(this).val().length * 2;
        this.setSelectionRange(len, len);
        }
        else
        {
        // ... otherwise replace the contents with itself
        // (Doesn't work in Google Chrome)
        $(this).val($(this).val());
        }

        // Scroll to the bottom, in case we're in a tall textarea
        // (Necessary for Firefox and Google Chrome)
        this.scrollTop = 999999;
    });
    };
})(jQuery);
;(function($) {
	/**
	 * jQuery плагин спиннера количества товаров
     * Установка обработчиков $(elems).goodsCounter(options)
     * Удаление обработчиков $(elems).goodsCounter('destroy')
	 *
	 * @author		Zaytsev Alexandr
	 * @requires	jQuery
	 * @param		{Object}	plusBtn					Элемент кнопки увеличения
	 * @param		{Object}	minusBtn				Элемент кнопки уменьшения
	 * @param		{Object}	input					Поле ввода
	 * @param		{String}	counterGroupName		Имя группы спиннеров, к которой принадлежит данный спиннер
	 * @param		{Object}	counterGroup			Все спиннеры к группе которой принадлежит данный спиннер
	 * @param		{Number}	timeout_id				Идентификатор таймаута
	 * @return		{jQuery}
	 */
	$.fn.goodsCounter = function(params) {

        if (params === 'destroy') {
            return this.each(function(){
                $(this).find('*').off('.goodsCounter');
            })
        }

		return this.each(function() {
			var options = $.extend(
							{},
							$.fn.goodsCounter.defaults,
							params),
				$self = $(this),

				plusBtn = $self.find(options.plusSelector),
				minusBtn = $self.find(options.minusSelector),
				input = $self.find(options.inputSelector),

				counterGroupName = $self.attr('data-spinner-for'),
				counterGroup = $('[data-spinner-for="'+counterGroupName+'"]'),

				timeout_id = 0;
			// end of vars


				/**
				 * Срабатывание функции обратного вызова onChange
				 * 
				 * @param	{Number}	count	Текущее значение в поле ввода
				 */
			var changeHandler = function changeHandler( count ) {
					clearTimeout(timeout_id);
					
					timeout_id = setTimeout(function() {
						$self.find('input').val( count );
						options.onChange.apply( $self, [count] );
					}, 400);
				},

				/**
				 * Обработчик увеличения количества в поле ввода
				 * 
				 * @param	{Event}	e	Данные события
				 * @return	{Boolean}
				 */
				plusHandler = function plusHandler( e ) {
					var nowCount = input.val();

					e.stopPropagation();

					if ( $self.hasClass('mDisabled') ) {
						return false;
					}

					if ( (nowCount * 1) + 1 > options.maxVal ) {
						return false;
					}

					nowCount++;
					input.val( nowCount );
					changeHandler( nowCount );

					return false;
				},

				/**
				 * Обработчик уменьшения количества в поле ввода
				 * 
				 * @param	{Event}	e	Данные события
				 * @return	{Boolean}
				 */
				minusHandler = function minusHandler( e ) {
					var nowCount = input.val();

					e.stopPropagation();

					if ( $self.hasClass('mDisabled') ) {
						return false;
					}

					if ( (nowCount * 1) - 1 < 1 ) {
						return false;
					}

					nowCount--;
					input.val( nowCount );
					changeHandler( nowCount );

					return false;
				},

				/**
				 * Обработчик отпускания клавиши клавиатуры
				 * 
				 * @param	{Event}	e	Данные события
				 * @return	{Boolean}
				 */
				keyupHandler = function keyupHandler( e ) {
					var nowCount = input.val();

					e.stopPropagation();

					if ( $self.hasClass('mDisabled') ) {
						return false;
					}

					nowCount = input.val();

					if ( (nowCount * 1) < 1 ) {
						nowCount = 1;
					}

					if ( (nowCount * 1) > options.maxVal ) {
						nowCount = options.maxVal;
					}

					input.val( nowCount );
					changeHandler( nowCount );

					return false;
				},

				/**
				 * Обработчик нажатия клавиши клавиатуры
				 * 
				 * @param	{Event}	e	Данные события
				 * @return	{Boolean}
				 */
				keydownHandler = function keydownHandler( e ) {
					e.stopPropagation();

					if ( e.which === 38 ) { // up arrow
						plusBtn.trigger('click');
						return false;
					}
					else if ( e.which === 40 ) { // down arrow
						minusBtn.trigger('click');
						return false;
					}
					else if ( !(( (e.which >= 48) && (e.which <= 57) ) ||  //num keys
								( (e.which >= 96) && (e.which <= 105) ) || //numpad keys
								(e.which === 8) ||
								(e.which === 46)) 
							) {
						return false;
					}
				};
			//end of functions

			plusBtn.on('click.goodsCounter', plusHandler);
			minusBtn.on('click.goodsCounter',minusHandler);
			input.on('keydown.goodsCounter', keydownHandler);
			input.on('keyup.goodsCounter', keyupHandler);
		});
	};

	$.fn.goodsCounter.defaults = {
		// callbacks
		plusSelector:'.bCountSection__eP',
		minusSelector:'.bCountSection__eM',
		inputSelector:'.bCountSection__eNum',

		maxVal: 99,

		onChange: function(){}
	};

})(jQuery);
/*
 *	jQuery elevateZoom 2.5.6
 *	+ fix by Alexandr Zaytcev:
 * 				- add disableZoom option
 * 	+ fix by Mihail Haritonov:
 *              - add imageContainer option
 *              - remove gallery option (because it works with errors, see SITE-4463 for details)
 *              - remove galleryActiveClass option
 *              - remove onImageSwap option
 *              - remove onImageSwapComplete option
 *
 *	Demo's and documentation:
 *	www.elevateweb.co.uk/image-zoom
 *
 *	Copyright (c) 2012 Andrew Eades
 *	www.elevateweb.co.uk
 *
 *
 *	Dual licensed under the GPL and MIT licenses.
 *	http://en.wikipedia.org/wiki/MIT_License
 *	http://en.wikipedia.org/wiki/GNU_General_Public_License
 */


if ( typeof Object.create !== 'function' ) {
	Object.create = function( obj ) {
		function F() {};
		F.prototype = obj;
		return new F();
	};
}

(function( $, window, document, undefined ) {
	var ElevateZoom = {
		init: function( options, elem ) {
			var self = this;

			self.elem = elem;
			self.$elem = $( elem );

			self.imageSrc = self.$elem.data("zoom-image") ? self.$elem.data("zoom-image") : self.$elem.attr("src");

			self.options = $.extend( {}, $.fn.elevateZoom.options, options );

			//TINT OVERRIDE SETTINGS
			if(self.options.tint) {
				self.options.lensColour = "none", //colour of the lens background
				self.options.lensOpacity =  "1" //opacity of the lens
			}
			//INNER OVERRIDE SETTINGS
			if(self.options.zoomType == "inner") {self.options.showLens = false;}


			//Remove alt on hover

			self.$elem.parent().removeAttr('title').removeAttr('alt');

			self.zoomImage = self.imageSrc;

			//get dimensions of the non zoomed image
			self.nzWidth = (self.options.$imageContainer || self.$elem).width();
			self.nzHeight = (self.options.$imageContainer || self.$elem).height();

			self.imageWidth = self.$elem.width();
			self.imageHeight = self.$elem.height();

			//get offset of the non zoomed image
			self.nzOffset = (self.options.$imageContainer || self.$elem).offset();

			self.loadLargeImageAndStartZoom();
		},

		loadLargeImageAndStartZoom: function() {
			var self = this;
			setTimeout(function() {
				//get the image
				var newImg = new Image();
				newImg.onload = function() {
					//set the large image dimensions - used to calculte ratio's
					self.largeImageWidth = newImg.width;
					self.largeImageHeight = newImg.height;
					self.largeContainerWidth = newImg.width + (self.nzWidth - self.imageWidth) * (newImg.width / self.imageWidth);
					self.largeContainerHeight = newImg.height + (self.nzHeight - self.imageHeight) * (newImg.height / self.imageHeight);
					//once image is loaded start the calls
					self.startZoom();
					//let caller know image has been loaded
					self.options.onZoomedImageLoaded(self.$elem);
				};
				newImg.src = self.imageSrc; // this must be done AFTER setting onload
			}, 1);
		},

		startZoom: function( ) {
			var self = this;

			//calculate the width ratio of the large/small image
			self.widthRatio = (self.largeContainerWidth/self.options.zoomLevel) / self.nzWidth;
			self.heightRatio = (self.largeContainerHeight/self.options.zoomLevel) / self.nzHeight;

			//if window zoom
			if(self.options.zoomType == "window") {
				self.zoomWindowStyle = "overflow: hidden;"
					+ "background-position: 0px 0px;text-align:center;"
					+ "background-color: " + String(self.options.zoomWindowBgColour)
					+ ";width: " + String(self.options.zoomWindowWidth) + "px;"
					+ "height: " + String(self.options.zoomWindowHeight)
					+ "px;float: left;"
					+ "background-size: "+ self.largeImageWidth/self.options.zoomLevel+ "px " +self.largeImageHeight/self.options.zoomLevel + "px;"
					+ "display: none;z-index:100"
					+ "px;border: " + String(self.options.borderSize)
					+ "px solid " + self.options.borderColour
					+ ";background-repeat: no-repeat;"
					+ "position: absolute;";
			}
			//if inner  zoom
			if(self.options.zoomType == "inner") {
				self.zoomWindowStyle = "overflow: hidden;"
					+ "background-position: 0px 0px;"
					+ "width: " + String(self.nzWidth) + "px;"
					+ "height: " + String(self.nzHeight)
					+ "px;float: left;"
					+ "display: none;"
					+ "cursor:"+(self.options.cursor)+";"
					+ "px solid " + self.options.borderColour
					+ ";background-repeat: no-repeat;"
					+ "position: absolute;";
			}



			//lens style for window zoom
			if(self.options.zoomType == "window") {


				// adjust images less than the window height

				if(self.nzHeight < self.options.zoomWindowWidth/self.widthRatio){
					lensHeight = self.nzHeight;
				}
				else{
					lensHeight = String((self.options.zoomWindowHeight/self.heightRatio))
				}
				if(self.largeContainerWidth < self.options.zoomWindowWidth){
					lensWidth = self.nzWidth;
				}
				else{
					lensWidth =  (self.options.zoomWindowWidth/self.widthRatio);
				}


				self.lensStyle = "background-position: 0px 0px;width: " + String((self.options.zoomWindowWidth)/self.widthRatio) + "px;height: " + String((self.options.zoomWindowHeight)/self.heightRatio)
				+ "px;float: right;display: none;"
				+ "overflow: hidden;"
				+ "z-index: 999;"
				+ "-webkit-transform: translateZ(0);"
				+ "opacity:"+(self.options.lensOpacity)+";filter: alpha(opacity = "+(self.options.lensOpacity*100)+"); zoom:1;"
				+ "width:"+lensWidth+"px;"
				+ "height:"+lensHeight+"px;"
				+ "background-color:"+(self.options.lensColour)+";"
				+ "cursor:"+(self.options.cursor)+";"
				+ "border: "+(self.options.lensBorderSize)+"px" +
				" solid "+(self.options.lensBorderColour)+";background-repeat: no-repeat;position: absolute;";
			}


			//tint style
			self.tintStyle = "display: block;"
				+ "position: absolute;"
				+ "background-color: "+self.options.tintColour+";"
				+ "filter:alpha(opacity=0);"
				+ "opacity: 0;"
				+ "width: " + self.nzWidth + "px;"
				+ "height: " + self.nzHeight + "px;"

				;

			//lens style for lens zoom with optional round for modern browsers
			self.lensRound = '';
			if(self.options.zoomType == "lens") {
				self.lensStyle = "background-position: 0px 0px;"
					+ "float: left;display: none;"
					+ "border: " + String(self.options.borderSize) + "px solid " + self.options.borderColour+";"
					+ "width:"+ String(self.options.lensSize) +"px;"
					+ "height:"+ String(self.options.lensSize)+"px;"
					+ "background-repeat: no-repeat;position: absolute;";


			}


			//does not round in all browsers
			if(self.options.lensShape == "round") {
				self.lensRound = "border-top-left-radius: " + String(self.options.lensSize / 2 + self.options.borderSize) + "px;"
				+ "border-top-right-radius: " + String(self.options.lensSize / 2 + self.options.borderSize) + "px;"
				+ "border-bottom-left-radius: " + String(self.options.lensSize / 2 + self.options.borderSize) + "px;"
				+ "border-bottom-right-radius: " + String(self.options.lensSize / 2 + self.options.borderSize) + "px;";

			}

			//create the div's                                                + ""
			//self.zoomContainer = $('<div/>').addClass('zoomContainer').css({"position":"relative", "height":self.nzHeight, "width":self.nzWidth});

			if ( !self.options.disableZoom ) {
				self.zoomContainer = $('<div class="zoomContainer" style="z-index: 19;-webkit-transform: translateZ(0);position:absolute;left:'+self.nzOffset.left+'px;top:'+self.nzOffset.top+'px;height:'+self.nzHeight+'px;width:'+self.nzWidth+'px;"></div>');
				$('body').append(self.zoomContainer);


				//this will add overflow hidden and contrain the lens on lens mode
				if ( self.options.containLensZoom && self.options.zoomType == "lens" ) {
					self.zoomContainer.css("overflow", "hidden");
				}
				if ( self.options.zoomType != "inner" ) {
					self.zoomLens = $("<div class='zoomLens' style='" + self.lensStyle + self.lensRound +"'>&nbsp;</div>")
					.appendTo(self.zoomContainer)
					.click(function () {
						self.$elem.trigger('click');
					});
				}



				if ( self.options.tint ) {
					self.tintContainer = $('<div/>').addClass('tintContainer');
					self.zoomTint = $("<div class='zoomTint' style='"+self.tintStyle+"'></div>");


					self.zoomLens.wrap(self.tintContainer);


					self.zoomTintcss = self.zoomLens.after(self.zoomTint);

					//if tint enabled - set an image to show over the tint

					self.zoomTintImage = $('<img style="position: absolute; left: 0px; top: 0px; max-width: none; width: '+self.nzWidth+'px; height: '+self.nzHeight+'px;" src="'+self.imageSrc+'">')
					.appendTo(self.zoomLens)
					.click(function () {

						self.$elem.trigger('click');
					});

				}
			}

			//create zoom window
			if(isNaN(self.options.zoomWindowPosition)){
				self.zoomWindow = $("<div style='z-index:999;left:"+(self.windowOffsetLeft)+"px;top:"+(self.windowOffsetTop)+"px;" + self.zoomWindowStyle + "' class='zoomWindow'>&nbsp;</div>")
				.appendTo('body')
				.click(function () {
					self.$elem.trigger('click');
				});
			}else{
				self.zoomWindow = $("<div style='z-index:999;left:"+(self.windowOffsetLeft)+"px;top:"+(self.windowOffsetTop)+"px;" + self.zoomWindowStyle + "' class='zoomWindow'>&nbsp;</div>")
				.appendTo(self.zoomContainer)
				.click(function () {
					self.$elem.trigger('click');
				});
			}
			self.zoomWindowContainer = $('<div/>').addClass('zoomWindowContainer').css("width",self.options.zoomWindowWidth);
			self.zoomWindow.wrap(self.zoomWindowContainer);


			//  self.captionStyle = "text-align: left;background-color: black;color: white;font-weight: bold;padding: 10px;font-family: sans-serif;font-size: 11px";
			// self.zoomCaption = $('<div class="elevatezoom-caption" style="'+self.captionStyle+'display: block; width: 280px;">INSERT ALT TAG</div>').appendTo(self.zoomWindow.parent());

			if(self.options.zoomType == "lens") {
				self.zoomLens.css({ backgroundImage: "url('" + self.imageSrc + "')" });
			}
			if(self.options.zoomType == "window") {
				self.zoomWindow.css({ backgroundImage: "url('" + self.imageSrc + "')" });
			}
			if(self.options.zoomType == "inner") {
				self.zoomWindow.css({ backgroundImage: "url('" + self.imageSrc + "')" });
			}
			/*-------------------END THE ZOOM WINDOW AND LENS----------------------------------*/
			//touch events

			if ( self.options.disableZoom ) {
				console.log('disableZoom');
				return false;
			}

			(self.options.$imageContainer || self.$elem).bind('touchmove', function( e ) {
				e.preventDefault();
				var touch = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
				self.setPosition(touch);
			});

			self.zoomContainer.bind('touchmove', function( e ) {
				if(self.options.zoomType == "inner") {
					if(self.options.zoomWindowFadeIn){
						self.zoomWindow.stop(true, true).fadeIn(self.options.zoomWindowFadeIn);
					}
					else{self.zoomWindow.show();}

				}
				e.preventDefault();
				var touch = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
				self.setPosition(touch);

			});

			self.zoomContainer.bind('touchend', function( e ) {
				self.zoomWindow.hide();
				if(self.options.showLens) {self.zoomLens.hide();}
				if(self.options.tint) {self.zoomTint.hide();}
			});

			(self.options.$imageContainer || self.$elem).bind('touchend', function( e ) {
				self.zoomWindow.hide();
				if(self.options.showLens) {self.zoomLens.hide();}
				if(self.options.tint) {self.zoomTint.hide();}
			});

			if(self.options.showLens) {
				self.zoomLens.bind('touchmove', function( e ) {
					e.preventDefault();
					var touch = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
					self.setPosition(touch);
				});


				self.zoomLens.bind('touchend', function( e ) {
					self.zoomWindow.hide();
					if(self.options.showLens) {self.zoomLens.hide();}
					if(self.options.tint) {self.zoomTint.hide();}
				});
			}
			//Needed to work in IE
			(self.options.$imageContainer || self.$elem).bind('mousemove', function( e ) {
				//make sure on orientation change the setposition is not fired
				if(self.lastX !== e.clientX || self.lastY !== e.clientY){
					self.setPosition(e);
				}
				self.lastX = e.clientX;
				self.lastY = e.clientY;

			});

			self.zoomContainer.bind('mousemove', function( e ) {
				//make sure on orientation change the setposition is not fired
				if(self.lastX !== e.clientX || self.lastY !== e.clientY){
					self.setPosition(e);
				}
				self.lastX = e.clientX;
				self.lastY = e.clientY;
			});

			if(self.options.zoomType != "inner") {
				self.zoomLens.bind('mousemove', function(e){
					//make sure on orientation change the setposition is not fired
					if(self.lastX !== e.clientX || self.lastY !== e.clientY){
						self.setPosition(e);
					}
					self.lastX = e.clientX;
					self.lastY = e.clientY;
				});
			}

			if(self.options.tint) {
				self.zoomTint.bind('mousemove', function( e ) {
					//make sure on orientation change the setposition is not fired
					if(self.lastX !== e.clientX || self.lastY !== e.clientY){
						self.setPosition(e);
					}
					self.lastX = e.clientX;
					self.lastY = e.clientY;
				});

			}

			if(self.options.zoomType == "inner") {
				self.zoomWindow.bind('mousemove', function( e ) {
					//make sure on orientation change the setposition is not fired
					if(self.lastX !== e.clientX || self.lastY !== e.clientY){
						self.setPosition(e);
					}
					self.lastX = e.clientX;
					self.lastY = e.clientY;
				});

			}


			//  lensFadeOut: 500,  zoomTintFadeIn
			self.zoomContainer.mouseenter(function() {
				if(self.options.zoomType == "inner") {
					if(self.options.zoomWindowFadeIn){
						self.zoomWindow.stop(true, true).fadeIn(self.options.zoomWindowFadeIn);
					}
					else{self.zoomWindow.show();}

				}
				if(self.options.zoomType == "window") {

					if(self.options.zoomWindowFadeIn){
						self.zoomWindow.stop(true, true).fadeIn(self.options.zoomWindowFadeIn);
					}
					else{self.zoomWindow.show();}

				}
				if(self.options.showLens) {

					if(self.options.lensFadeIn){
						self.zoomLens.stop(true, true).fadeIn(self.options.lensFadeIn);
					}
					else{self.zoomLens.show();}

				}
				if(self.options.tint) {

					if(self.options.zoomTintFadeIn){
						self.zoomTint.stop(true, true).fadeIn(self.options.zoomTintFadeIn);
					}
					else{self.zoomTint.show();}

					//  self.zoomTint.show();


				}
			}).mouseleave(function() {
				self.zoomWindow.hide();
				if(self.options.showLens) {self.zoomLens.hide();}

				if(self.options.tint) {
					self.zoomTint.hide();
				}
			});
			//end ove image

			(self.options.$imageContainer || self.$elem).mouseenter(function(){
				if(self.options.zoomType == "inner") {
					if(self.options.zoomWindowFadeIn){
						self.zoomWindow.stop(true, true).fadeIn(self.options.zoomWindowFadeIn);
					}
					else{self.zoomWindow.show();}

				}
				if(self.options.zoomType == "window") {

					if(self.options.zoomWindowFadeIn){
						self.zoomWindow.stop(true, true).fadeIn(self.options.zoomWindowFadeIn);
					}
					else{self.zoomWindow.show();}

				}
				if(self.options.showLens) {

					if(self.options.lensFadeIn){
						self.zoomLens.stop(true, true).fadeIn(self.options.lensFadeIn);
					}
					else{self.zoomLens.show();}

				}
				if(self.options.tint) {

					if(self.options.zoomTintFadeIn){
						self.zoomTint.stop(true, true).fadeIn(self.options.zoomTintFadeIn);
					}
					else{self.zoomTint.show();}

					//  self.zoomTint.show();


				}
			}).mouseleave(function() {
				self.zoomWindow.hide();
				if(self.options.showLens) {self.zoomLens.hide();}

				if(self.options.tint) {
					self.zoomTint.hide();
				}
			});
			//end ove image

			if(self.options.zoomType != "inner") {

				self.zoomLens.mouseenter(function(){
					if(self.options.zoomType == "inner") {
						if(self.options.zoomWindowFadeIn){

							self.zoomWindow.stop(true, true).fadeIn(self.options.zoomWindowFadeIn);
						}
						else{

							self.zoomWindow.show();
						}
					}
					if(self.options.zoomType == "window") {self.zoomWindow.show();}
					if(self.options.showLens) {self.zoomLens.show();}
					if(self.options.tint) {self.zoomTint.show(); }
				}).mouseleave(function(){


					if(self.options.zoomWindowFadeOut){
						self.zoomWindow.stop(true, true).fadeOut(self.options.zoomWindowFadeOut);
					}
					else{self.zoomWindow.hide();}


					if(self.options.zoomType != "inner") {
						self.zoomLens.hide();
					}
					if(self.options.tint) {self.zoomTint.hide(); }
				});
			}


			if(self.options.tint) {
				self.zoomTint.mouseenter(function(){
					if(self.options.zoomType == "inner") {self.zoomWindow.show();}
					if(self.options.zoomType == "window") {self.zoomWindow.show();}
					if(self.options.showLens) {self.zoomLens.show();}
					self.zoomTint.show();

				}).mouseleave(function(){

					self.zoomWindow.hide();
					if(self.options.zoomType != "inner") {
						self.zoomLens.hide();
					}
					self.zoomTint.hide();

				});
			}

			if(self.options.zoomType == "inner") {
				self.zoomWindow.mouseenter(function(){
					if(self.options.zoomType == "inner") {self.zoomWindow.show();}
					if(self.options.zoomType == "window") {self.zoomWindow.show();}
					if(self.options.showLens) {self.zoomLens.show();}


				}).mouseleave(function(){

					if(self.options.zoomWindowFadeOut){
						self.zoomWindow.stop(true, true).fadeOut(self.options.zoomWindowFadeOut);
					}
					else{self.zoomWindow.hide();}
					if(self.options.zoomType != "inner") {
						self.zoomLens.hide();
					}


				});
			}
		},

		setPosition: function(e) {

			var self = this;


			//recaclc offset each time in case the image moves
			//this can be caused by other on page elements
			self.nzHeight = (self.options.$imageContainer || self.$elem).height();
			self.nzWidth = (self.options.$imageContainer || self.$elem).width();
			self.nzOffset = (self.options.$imageContainer || self.$elem).offset();

			self.imageWidth = self.$elem.width();
			self.imageHeight = self.$elem.height();

			if(self.options.tint) {
				self.zoomTint.css({ top: 0});
				self.zoomTint.css({ left: 0});
			}
			//set responsive
			//will checking if the image needs changing before running this code work faster?
			if(self.options.responsive){
				if(self.nzHeight < self.options.zoomWindowWidth/self.widthRatio){
					lensHeight = self.nzHeight;
				}
				else{
					lensHeight = String((self.options.zoomWindowHeight/self.heightRatio))
				}
				if(self.largeContainerWidth < self.options.zoomWindowWidth){
					lensWidth = self.nzHWidth;
				}
				else{
					lensWidth =  (self.options.zoomWindowWidth/self.widthRatio);
				}
				self.widthRatio = self.largeContainerWidth / self.nzWidth;
				self.heightRatio = self.largeContainerHeight / self.nzHeight;
				self.zoomLens.css({ width: String((self.options.zoomWindowWidth)/self.widthRatio) + 'px', height: String((self.options.zoomWindowHeight)/self.heightRatio) + 'px' })
				//end responsive image change
			}

			//container fix
			self.zoomContainer.css({ top: self.nzOffset.top});
			self.zoomContainer.css({ left: self.nzOffset.left});
			self.mouseLeft = parseInt(e.pageX - self.nzOffset.left);
			self.mouseTop = parseInt(e.pageY - self.nzOffset.top);
			//calculate the Location of the Lens

			//calculate the bound regions - but only if zoom window
			if(self.options.zoomType == "window") {
				self.Etoppos = (self.mouseTop < (self.zoomLens.height()/2));
				self.Eboppos = (self.mouseTop > self.nzHeight - (self.zoomLens.height()/2)-(self.options.lensBorderSize*2));
				self.Eloppos = (self.mouseLeft < 0+((self.zoomLens.width()/2)));
				self.Eroppos = (self.mouseLeft > (self.nzWidth - (self.zoomLens.width()/2)-(self.options.lensBorderSize*2)));
			}

			//calculate the bound regions - but only for inner zoom
			if(self.options.zoomType == "inner"){
				self.Etoppos = (self.mouseTop < (self.nzHeight/2)/self.heightRatio );
				self.Eboppos = (self.mouseTop > self.nzHeight - ((self.nzHeight/2)/self.heightRatio));
				self.Eloppos = (self.mouseLeft < 0+((self.nzWidth/2)/self.widthRatio));
				self.Eroppos = (self.mouseLeft > (self.nzWidth - (self.nzWidth/2)/self.widthRatio-(self.options.lensBorderSize*2)));
			}

			// if the mouse position of the slider is one of the outerbounds, then hide  window and lens
			if (self.mouseLeft < 0 || self.mouseTop <= 0 || self.mouseLeft > self.nzWidth || self.mouseTop > self.nzHeight ) {
				self.zoomWindow.hide();
				if(self.options.showLens) {self.zoomLens.hide();}
				if(self.options.tint) {self.zoomTint.hide();}
				return;
			}
			//else continue with operations
			else {

				//should already be visible - but make sure
				if(self.options.zoomType == "window") {self.zoomWindow.show();}
				if(self.options.tint) {self.zoomTint.show();}

				//lens options
				if(self.options.showLens) {
					self.zoomLens.show();
					//set background position of lens
					self.lensLeftPos = String(self.mouseLeft - self.zoomLens.width() / 2);
					self.lensTopPos = String(self.mouseTop - self.zoomLens.height() / 2);
				}
				//adjust the background position if the mouse is in one of the outer regions

				//Top region
				if(self.Etoppos){
					self.lensTopPos = 0;
				}
				//Left Region
				if(self.Eloppos){
					self.windowLeftPos = 0;
					self.lensLeftPos = 0;
					self.tintpos=0;
				}
				//Set bottom and right region for window mode
				if(self.options.zoomType == "window") {
					if(self.Eboppos){
						self.lensTopPos = Math.max( (self.nzHeight)-self.zoomLens.height()-(self.options.lensBorderSize*2), 0 );
					}
					if(self.Eroppos){
						self.lensLeftPos = (self.nzWidth-(self.zoomLens.width())-(self.options.lensBorderSize*2));
					}
				}
				//Set bottom and right region for inner mode
				if(self.options.zoomType == "inner") {
					if(self.Eboppos){
						self.lensTopPos = Math.max( (self.nzHeight)-(self.options.lensBorderSize*2), 0 );
					}
					if(self.Eroppos){
						self.lensLeftPos = (self.nzWidth-(self.nzWidth)-(self.options.lensBorderSize*2));
					}
				}
				//if lens zoom
				if(self.options.zoomType == "lens") {
					self.windowLeftPos = String(((e.pageX - self.nzOffset.left) * self.widthRatio - self.zoomLens.width() / 2) * (-1));
					self.windowTopPos = String(((e.pageY - self.nzOffset.top) * self.heightRatio - self.zoomLens.height() / 2) * (-1));
					self.zoomLens.css({ backgroundPosition: self.windowLeftPos + 'px ' + self.windowTopPos + 'px' });
					self.setWindowPostition(e);
				}
				//if tint zoom
				if(self.options.tint) {
					self.setTintPosition(e);

				}
				//set the css background position
				if(self.options.zoomType == "window") {
					self.setWindowPostition(e);
				}
				if(self.options.zoomType == "inner") {
					self.setWindowPostition(e);
				}
				if(self.options.showLens) {
					self.zoomLens.css({ left: self.lensLeftPos + 'px', top: self.lensTopPos + 'px' })
				}

			} //end else



		},
		setLensPostition: function( e ) {


		},
		setWindowPostition: function( e ) {
			//return obj.slice( 0, count );
			var self = this;

			if(!isNaN(self.options.zoomWindowPosition)){

				switch (self.options.zoomWindowPosition) {
				case 1: //done
					self.windowOffsetTop = (self.options.zoomWindowOffety);//DONE - 1
					self.windowOffsetLeft =(+self.nzWidth); //DONE 1, 2, 3, 4, 16
					break;
				case 2:
					if(self.options.zoomWindowHeight > self.nzHeight){ //positive margin

						self.windowOffsetTop = ((self.options.zoomWindowHeight/2)-(self.nzHeight/2))*(-1);
						self.windowOffsetLeft =(self.nzWidth); //DONE 1, 2, 3, 4, 16
					}
					else{ //negative margin

					}
					break;
				case 3: //done
					self.windowOffsetTop = (self.nzHeight - self.zoomWindow.height() - (self.options.borderSize*2)); //DONE 3,9
					self.windowOffsetLeft =(self.nzWidth); //DONE 1, 2, 3, 4, 16
					break;
				case 4: //done
					self.windowOffsetTop = (self.nzHeight); //DONE - 4,5,6,7,8
					self.windowOffsetLeft =(self.nzWidth); //DONE 1, 2, 3, 4, 16
					break;
				case 5: //done
					self.windowOffsetTop = (self.nzHeight); //DONE - 4,5,6,7,8
					self.windowOffsetLeft =(self.nzWidth-self.zoomWindow.width()-(self.options.borderSize*2)); //DONE - 5,15
					break;
				case 6:
					if(self.options.zoomWindowHeight > self.nzHeight){ //positive margin
						self.windowOffsetTop = (self.nzHeight);  //DONE - 4,5,6,7,8

						self.windowOffsetLeft =((self.options.zoomWindowWidth/2)-(self.nzWidth/2)+(self.options.borderSize*2))*(-1);
					}
					else{ //negative margin

					}


					break;
				case 7: //done
					self.windowOffsetTop = (self.nzHeight);  //DONE - 4,5,6,7,8
					self.windowOffsetLeft = 0; //DONE 7, 13
					break;
				case 8: //done
					self.windowOffsetTop = (self.nzHeight); //DONE - 4,5,6,7,8
					self.windowOffsetLeft =(self.zoomWindow.width()+(self.options.borderSize*2) )* (-1);  //DONE 8,9,10,11,12
					break;
				case 9:  //done
					self.windowOffsetTop = (self.nzHeight - self.zoomWindow.height() - (self.options.borderSize*2)); //DONE 3,9
					self.windowOffsetLeft =(self.zoomWindow.width()+(self.options.borderSize*2) )* (-1);  //DONE 8,9,10,11,12
					break;
				case 10:
					if(self.options.zoomWindowHeight > self.nzHeight){ //positive margin

						self.windowOffsetTop = ((self.options.zoomWindowHeight/2)-(self.nzHeight/2))*(-1);
						self.windowOffsetLeft =(self.zoomWindow.width()+(self.options.borderSize*2) )* (-1);  //DONE 8,9,10,11,12
					}
					else{ //negative margin

					}
					break;
				case 11:
					self.windowOffsetTop = (self.options.zoomWindowOffety);
					self.windowOffsetLeft =(self.zoomWindow.width()+(self.options.borderSize*2) )* (-1);  //DONE 8,9,10,11,12
					break;
				case 12: //done
					self.windowOffsetTop = (self.zoomWindow.height()+(self.options.borderSize*2))*(-1); //DONE 12,13,14,15,16
					self.windowOffsetLeft =(self.zoomWindow.width()+(self.options.borderSize*2) )* (-1);  //DONE 8,9,10,11,12
					break;
				case 13: //done
					self.windowOffsetTop = (self.zoomWindow.height()+(self.options.borderSize*2))*(-1); //DONE 12,13,14,15,16
					self.windowOffsetLeft =(0); //DONE 7, 13
					break;
				case 14:
					if(self.options.zoomWindowHeight > self.nzHeight){ //positive margin
						self.windowOffsetTop = (self.zoomWindow.height()+(self.options.borderSize*2))*(-1); //DONE 12,13,14,15,16

						self.windowOffsetLeft =((self.options.zoomWindowWidth/2)-(self.nzWidth/2)+(self.options.borderSize*2))*(-1);
					}
					else{ //negative margin

					}

					break;
				case 15://done
					self.windowOffsetTop = (self.zoomWindow.height()+(self.options.borderSize*2))*(-1); //DONE 12,13,14,15,16
					self.windowOffsetLeft =(self.nzWidth-self.zoomWindow.width()-(self.options.borderSize*2)); //DONE - 5,15
					break;
				case 16:  //done
					self.windowOffsetTop = (self.zoomWindow.height()+(self.options.borderSize*2))*(-1); //DONE 12,13,14,15,16
					self.windowOffsetLeft =(self.nzWidth); //DONE 1, 2, 3, 4, 16
					break;
				default: //done
					self.windowOffsetTop = (self.options.zoomWindowOffety);//DONE - 1
				self.windowOffsetLeft =(self.nzWidth); //DONE 1, 2, 3, 4, 16
				}
			} //end isNAN
			else{
				//WE CAN POSITION IN A CLASS - ASSUME THAT ANY STRING PASSED IS
				self.externalContainer = $('#'+self.options.zoomWindowPosition);
				self.externalContainerWidth = self.externalContainer.width();
				self.externalContainerHeight = self.externalContainer.height();
				self.externalContainerOffset = self.externalContainer.offset();

				self.windowOffsetTop = self.externalContainerOffset.top;//DONE - 1
				self.windowOffsetLeft =self.externalContainerOffset.left; //DONE 1, 2, 3, 4, 16

			}
			self.windowOffsetTop = self.windowOffsetTop + self.options.zoomWindowOffety;
			self.windowOffsetLeft = self.windowOffsetLeft + self.options.zoomWindowOffetx;

			self.zoomWindow.css({ top: self.windowOffsetTop});
			self.zoomWindow.css({ left: self.windowOffsetLeft});

			if(self.options.zoomType == "inner") {
				self.zoomWindow.css({ top: 0});
				self.zoomWindow.css({ left: 0});

			}


			self.windowLeftPos = String(((e.pageX - self.nzOffset.left) * self.widthRatio - self.zoomWindow.width() / 2) * (-1));
			self.windowTopPos = String(((e.pageY - self.nzOffset.top) * self.heightRatio - self.zoomWindow.height() / 2) * (-1));
			if(self.Etoppos){self.windowTopPos = 0;}
			if(self.Eloppos){self.windowLeftPos = 0;}
			if(self.Eboppos){self.windowTopPos = (self.largeContainerHeight/self.options.zoomLevel-self.zoomWindow.height())*(-1);}
			if(self.Eroppos){self.windowLeftPos = ((self.largeContainerWidth/self.options.zoomLevel-self.zoomWindow.width())*(-1));}

			//set the css background position


			if(self.options.zoomType == "window" || self.options.zoomType == "inner") {

				//overrides for images not zoomable
				if(self.widthRatio <= 1){
					self.windowLeftPos = 0;
				}

				if(self.heightRatio <= 1){
					self.windowTopPos = 0;
				}

				// adjust images less than the window height

				if(self.largeContainerHeight < self.options.zoomWindowHeight){
					self.windowTopPos = 0;
				}
				if(self.largeContainerWidth < self.options.zoomWindowWidth){
					self.windowLeftPos = 0;
				}

				//set the zoomwindow background position
				if (self.options.easing){
					//set the pos to 0 if not set
					if(!self.xp){self.xp = 0;}
					if(!self.yp){self.yp = 0;}
					//if loop not already started, then run it
					if (!self.loop){
						self.loop = setInterval(function(){
							//using zeno's paradox
							self.xp += (self.windowLeftPos - self.xp) / self.options.easingAmount;
							self.yp += (self.windowTopPos - self.yp) / self.options.easingAmount;
							self.zoomWindow.css('backgroundPosition', (Math.floor(parseFloat(self.xp) + (self.largeContainerWidth - self.largeImageWidth) / 2) + 'px') + ' ' + (Math.floor(parseFloat(self.yp) + (self.largeContainerHeight - self.largeImageHeight) / 2) + 'px'));
						}, 16);
					}
				}
				else{
					self.zoomWindow.css('backgroundPosition', (Math.floor(parseFloat(self.windowLeftPos) + (self.largeContainerWidth - self.largeImageWidth) / 2) + 'px') + ' ' + (Math.floor(parseFloat(self.windowTopPos) + (self.largeContainerHeight - self.largeImageHeight) / 2) + 'px'));
				}
			}
		},
		setTintPosition: function(e){
			var self = this;
			self.nzOffset = (self.options.$imageContainer || self.$elem).offset();
			self.tintpos = String(((e.pageX - self.nzOffset.left)-(self.zoomLens.width() / 2)) * (-1));
			self.tintposy = String(((e.pageY - self.nzOffset.top) - self.zoomLens.height() / 2) * (-1));
			if(self.Etoppos){
				self.tintposy = 0;
			}
			if(self.Eloppos){
				self.tintpos=0;
			}
			if(self.Eboppos){
				self.tintposy = (self.nzHeight-self.zoomLens.height()-(self.options.lensBorderSize*2))*(-1);
			}
			if(self.Eroppos){
				self.tintpos = ((self.nzWidth-self.zoomLens.width()-(self.options.lensBorderSize*2))*(-1));
			}
			if(self.options.tint) {
				self.zoomTint.css({opacity:self.options.tintOpacity}).animate().fadeIn("slow");
				self.zoomTintImage.css({'left': self.tintpos-self.options.lensBorderSize+'px'});
				self.zoomTintImage.css({'top': self.tintposy-self.options.lensBorderSize+'px'});
			}
		},
		doneCallback: function(){

			var self = this;

			if ( self.options.tint ) {
				self.zoomTintImage.attr("src",largeimage);
				//self.zoomTintImage.attr("width",elem.data("image"));
				self.zoomTintImage.attr("height",self.$elem.height());
				//self.zoomTintImage.attr('src') = elem.data("image");
				self.zoomTintImage.css({ height: self.$elem.height()});
				self.zoomTint.css({ height: self.$elem.height()});

			}
			self.nzOffset = (self.options.$imageContainer || self.$elem).offset();
			self.nzWidth = (self.options.$imageContainer || self.$elem).width();
			self.nzHeight = (self.options.$imageContainer || self.$elem).height();

			self.imageWidth = self.$elem.width();
			self.imageHeight = self.$elem.height();

			//   alert("THIS");
			//ratio of the large to small image
			self.widthRatio = self.largeContainerWidth / self.nzWidth;
			self.heightRatio = self.largeContainerHeight / self.nzHeight;

			//NEED TO ADD THE LENS SIZE FOR ROUND
			// adjust images less than the window height
			if(self.options.zoomType == "window") {
				if(self.nzHeight < self.options.zoomWindowWidth/self.widthRatio){
					lensHeight = self.nzHeight;
				}
				else{
					lensHeight = String((self.options.zoomWindowHeight/self.heightRatio))
				}
				if(self.largeContainerWidth < self.options.zoomWindowWidth){
					lensWidth = self.nzHWidth;
				}
				else{
					lensWidth =  (self.options.zoomWindowWidth/self.widthRatio);
				}

				if(self.zoomLens){
					self.zoomLens.css('width', lensWidth);
					self.zoomLens.css('height', lensHeight);
				}
			}
		},
		getCurrentImage: function(){
			var self = this;
			return self.zoomImage;
		},
		changeZoomLevel: function(value){
			var self = this;
			self.widthRatio = (self.largeContainerWidth/value) / self.nzWidth;
			self.heightRatio = (self.largeContainerHeight/value) / self.nzHeight;
			self.zoomWindow.css({ "background-size": self.largeImageWidth/value + 'px ' + self.largeImageHeight/value + 'px' });
			self.zoomLens.css({ width: String((self.options.zoomWindowWidth)/self.widthRatio) + 'px', height: String((self.options.zoomWindowHeight)/self.heightRatio) + 'px' })
			//sets the boundry change, called in setWindowPos
			self.options.zoomLevel = value;

		},
		closeAll: function(){
			if(self.zoomWindow){self.zoomWindow.hide();}
			if(self.zoomLens){self.zoomLens.hide();}
			if(self.zoomTint){self.zoomTint.hide();}
		},
		destroy: function(){
			var self = this;

			if (self.zoomContainer) {
				self.zoomContainer.remove();
			}

			if (self.zoomWindow) {
				self.zoomWindow.remove();
			}

			self.$elem.removeData('elevateZoom');
		}
	};

	$.fn.elevateZoom = function( options ) {
		return this.each(function() {
			var elevate = Object.create( ElevateZoom );

			elevate.init( options, this );

			$.data( this, 'elevateZoom', elevate );

		});
	};

	$.fn.elevateZoom.options = {
		imageContainer: null,
		disableZoom: false,
		zoomLevel: 1,
		easing: false,
		easingAmount: 12,
		lensSize: 200,
		zoomWindowWidth: 400,
		zoomWindowHeight: 400,
		zoomWindowOffetx: 0,
		zoomWindowOffety: 0,
		zoomWindowPosition: 1,
		zoomWindowBgColour: "#fff",
		lensFadeIn: false,
		lensFadeOut: false,
		debug: false,
		zoomWindowFadeIn: false,
		zoomWindowFadeOut: false,
		zoomWindowAlwaysShow: false,
		zoomTintFadeIn: false,
		zoomTintFadeOut: false,
		borderSize: 4,
		showLens: true,
		borderColour: "#888",
		lensBorderSize: 1,
		lensBorderColour: "#000",
		lensShape: "square", //can be "round"
		zoomType: "window", //window is default,  also "lens" available -
		containLensZoom: false,
		lensColour: "white", //colour of the lens background
		lensOpacity: 0.4, //opacity of the lens
		lenszoom: false,
		tint: false, //enable the tinting
		tintColour: "#333", //default tint color, can be anything, red, #ccc, rgb(0,0,0)
		tintOpacity: 0.4, //opacity of the tint
		cursor:"default", // user should set to what they want the cursor as, if they have set a click function
		responsive:false,
		onComplete: $.noop,
		onZoomedImageLoaded: function() {}
	};

})( jQuery, window, document );
/**!
 * @preserve Shadow animation 1.11
 * http://www.bitstorm.org/jquery/shadow-animation/
 * Copyright 2011, 2013 Edwin Martin <edwin@bitstorm.org>
 * Contributors: Mark Carver, Xavier Lepretre and Jason Redding
 * Released under the MIT and GPL licenses.
 */

jQuery(function($, undefined) {
	/**
	 * Check whether the browser supports RGBA color mode.
	 *
	 * Author Mehdi Kabab <http://pioupioum.fr>
	 * @return {boolean} True if the browser support RGBA. False otherwise.
	 */
	function isRGBACapable() {
		var $script = $('script:first'),
		color = $script.css('color'),
		result = false;
		if (/^rgba/.test(color)) {
			result = true;
		} else {
			try {
				result = (color !== $script.css('color', 'rgba(0, 0, 0, 0.5)').css('color'));
				$script.css('color', color);
			} catch (e) {
			}
		}
		$script.removeAttr('style');

		return result;
	}

	$.extend(true, $, {
		support: {
			'rgba': isRGBACapable()
		}
	});

	/*************************************/

	// First define which property to use
	var styles = $('html').prop('style');
	var boxShadowProperty;
	$.each(['boxShadow', 'MozBoxShadow', 'WebkitBoxShadow'], function(i, property) {
		var val = styles[property];
		if (typeof val !== 'undefined') {
			boxShadowProperty = property;
			return false;
		}
	});

	// Extend the animate-function
	if (boxShadowProperty) {
		$['Tween']['propHooks']['boxShadow'] = {
			get: function(tween) {
				return $(tween.elem).css(boxShadowProperty);
			},
			set: function(tween) {
				var style = tween.elem.style;
				var p_begin = parseShadows($(tween.elem)[0].style[boxShadowProperty] || $(tween.elem).css(boxShadowProperty));
				var p_end = parseShadows(tween.end);
				var maxShadowCount = Math.max(p_begin.length, p_end.length);
				var i;
				for(i = 0; i < maxShadowCount; i++) {
					p_end[i] = $.extend({}, p_begin[i], p_end[i]);
					if (p_begin[i]) {
						if (!('color' in p_begin[i]) || $.isArray(p_begin[i].color) === false) {
							p_begin[i].color = p_end[i].color || [0, 0, 0, 0];
						}
					} else {
						p_begin[i] = parseShadows('0 0 0 0 rgba(0,0,0,0)')[0];
					}
				}
				tween['run'] = function(progress) {
					var rs = calculateShadows(p_begin, p_end, progress);
					style[boxShadowProperty] = rs;
				};
			}
		};
	}

	// Calculate an in-between shadow.
	function calculateShadows(beginList, endList, pos) {
		var shadows = [];
		$.each(beginList, function(i) {
			var parts = [], begin = beginList[i], end = endList[i];

			if (begin.inset) {
				parts.push('inset');
			}
			if (typeof end.left !== 'undefined') {
				parts.push(parseFloat(begin.left + pos * (end.left - begin.left)) + 'px '
				+ parseFloat(begin.top + pos * (end.top - begin.top)) + 'px');
			}
			if (typeof end.blur !== 'undefined') {
				parts.push(parseFloat(begin.blur + pos * (end.blur - begin.blur)) + 'px');
			}
			if (typeof end.spread !== 'undefined') {
				parts.push(parseFloat(begin.spread + pos * (end.spread - begin.spread)) + 'px');
			}
			if (typeof end.color !== 'undefined') {
				var color = 'rgb' + ($.support['rgba'] ? 'a' : '') + '('
				+ parseInt((begin.color[0] + pos * (end.color[0] - begin.color[0])), 10) + ','
				+ parseInt((begin.color[1] + pos * (end.color[1] - begin.color[1])), 10) + ','
				+ parseInt((begin.color[2] + pos * (end.color[2] - begin.color[2])), 10);
				if ($.support['rgba']) {
					color += ',' + parseFloat(begin.color[3] + pos * (end.color[3] - begin.color[3]));
				}
				color += ')';
				parts.push(color);
			}
			shadows.push(parts.join(' '));
		});
		return shadows.join(', ');
	}

	// Parse the shadow value and extract the values.
	function parseShadows(shadow) {
		var parsedShadows = [];
		var parsePosition = 0;
		var parseLength = shadow.length;

		function findInset() {
			var m = /^inset\b/.exec(shadow.substring(parsePosition));
			if (m !== null && m.length > 0) {
				parsedShadow.inset = true;
				parsePosition += m[0].length;
				return true;
			}
			return false;
		}
		function findOffsets() {
			var m = /^(-?[0-9\.]+)(?:px)?\s+(-?[0-9\.]+)(?:px)?(?:\s+(-?[0-9\.]+)(?:px)?)?(?:\s+(-?[0-9\.]+)(?:px)?)?/.exec(shadow.substring(parsePosition));
			if (m !== null && m.length > 0) {
				parsedShadow.left = parseInt(m[1], 10);
				parsedShadow.top = parseInt(m[2], 10);
				parsedShadow.blur = (m[3] ? parseInt(m[3], 10) : 0);
				parsedShadow.spread = (m[4] ? parseInt(m[4], 10) : 0);
				parsePosition += m[0].length;
				return true;
			}
			return false;
		}
		function findColor() {
			var m = /^#([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})/.exec(shadow.substring(parsePosition));
			if (m !== null && m.length > 0) {
				parsedShadow.color = [parseInt(m[1], 16), parseInt(m[2], 16), parseInt(m[3], 16), 1];
				parsePosition += m[0].length;
				return true;
			}
			m = /^#([0-9a-fA-F])([0-9a-fA-F])([0-9a-fA-F])/.exec(shadow.substring(parsePosition));
			if (m !== null && m.length > 0) {
				parsedShadow.color = [parseInt(m[1], 16) * 17, parseInt(m[2], 16) * 17, parseInt(m[3], 16) * 17, 1];
				parsePosition += m[0].length;
				return true;
			}
			m = /^rgb\(\s*([0-9\.]+)\s*,\s*([0-9\.]+)\s*,\s*([0-9\.]+)\s*\)/.exec(shadow.substring(parsePosition));
			if (m !== null && m.length > 0) {
				parsedShadow.color = [parseInt(m[1], 10), parseInt(m[2], 10), parseInt(m[3], 10), 1];
				parsePosition += m[0].length;
				return true;
			}
			m = /^rgba\(\s*([0-9\.]+)\s*,\s*([0-9\.]+)\s*,\s*([0-9\.]+)\s*,\s*([0-9\.]+)\s*\)/.exec(shadow.substring(parsePosition));
			if (m !== null && m.length > 0) {
				parsedShadow.color = [parseInt(m[1], 10), parseInt(m[2], 10), parseInt(m[3], 10), parseFloat(m[4])];
				parsePosition += m[0].length;
				return true;
			}
			return false;
		}
		function findWhiteSpace() {
			var m = /^\s+/.exec(shadow.substring(parsePosition));
			if (m !== null && m.length > 0) {
				parsePosition += m[0].length;
				return true;
			}
			return false;
		}
		function findComma() {
			var m = /^\s*,\s*/.exec(shadow.substring(parsePosition));
			if (m !== null && m.length > 0) {
				parsePosition += m[0].length;
				return true;
			}
			return false;
		}
		function normalizeShadow(shadow) {
			if ($.isPlainObject(shadow)) {
				var i, sColor, cLength = 0, color = [];
				if ($.isArray(shadow.color)) {
					sColor = shadow.color;
					cLength = sColor.length;
				}
				for(i = 0; i < 4; i++) {
					if (i < cLength) {
						color.push(sColor[i]);
					} else if (i === 3) {
						color.push(1);
					} else {
						color.push(0);
					}
				}
			}
			return $.extend({
				'left': 0,
				'top': 0,
				'blur': 0,
				'spread': 0
			}, shadow);
		}
		var parsedShadow = normalizeShadow();

		while (parsePosition < parseLength) {
			if (findInset()) {
				findWhiteSpace();
			} else if (findOffsets()) {
				findWhiteSpace();
			} else if (findColor()) {
				findWhiteSpace();
			} else if (findComma()) {
				parsedShadows.push(normalizeShadow(parsedShadow));
				parsedShadow = {};
			} else {
				break;
			}
		}
		parsedShadows.push(normalizeShadow(parsedShadow));
		return parsedShadows;
	}
});
;(function($){
	/**
	 * Плагин кастомных элементов select для карточки товара
	 *
	 * @author		Zaytsev Alexandr
	 * @requires	jQuery
	 * @param		{Object}	params
	 */
	$.fn.customDropDown = function( params ) {
		return this.each(function() {
			var options = $.extend(
							{},
							$.fn.customDropDown.defaults,
							params),
				$self = $(this),

				select = $self.find(options.selectSelector),
				value = $self.find(options.valueSelector);
			// end of vars

			var selectChangeHandler = function selectChangeHandler() {
				var selectedOption = select.find('option:selected');

				value.html( selectedOption.val() );
				options.changeHandler( selectedOption );
			};

			select.on('change', selectChangeHandler);
		});
	};
			
	$.fn.customDropDown.defaults = {
		valueSelector: '.bDescSelectItem__eValue',
		selectSelector: '.bDescSelectItem__eSelect',
		changeHandler: function(){}
	};

})(jQuery);
/**
 * Слайдер товаров
 *
 * @author		Zaytsev Alexandr
 * @requires	jQuery
 */
;(function( $ ) {
	$.fn.goodsSlider = function( params ) {
		params = params || {};

		var
			slidersWithUrl = 0,
			slidersRecommendation = 0,
			body = $('body'),
			reqArray = [],
            urlData = {senders: []},
			recommendArray = [];
		// end of vars

		var
			/**
			 * Слайдер для рекомендаций
			 *
			 * @param type Тип слайдера
			 * @return bool
			 */
			isRecommendation = function isRecommendation( type ) {
				return -1 != $.inArray(type, ['alsoBought', 'similar', 'alsoViewed', 'main', 'search'/*, 'viewed'*/]);
			};
		// end of functions

		this.each(function() {
			var $self = $(this),
				sliderParams = $self.data('slider');
			// end of vars

			if ( sliderParams.url !== null ) {
				slidersWithUrl++;
			}

			if ( sliderParams.type !== null && isRecommendation(sliderParams.type) ) {
				slidersRecommendation++;
			}

			if (sliderParams.url && sliderParams.sender) {
				sliderParams.sender.type = sliderParams.type;
				urlData.senders.push(sliderParams.sender);
			}

			if (sliderParams.sender2) {
				urlData.sender2 = sliderParams.sender2;
			}

            if (sliderParams.rrviewed) {
                //urlData.rrviewed = sliderParams.rrviewed;
            }
		});

		var getSlidersData = function getSlidersData( url, type, callback ) {
			if ( isRecommendation(type) ) {
				recommendArray.push({
					type: type,
					callback: callback
				});

				if ( recommendArray.length === slidersRecommendation ) {
					$.ajax({
						type: 'GET',
						url: url,
                        data: urlData,
						success: function( res ) {
							var
								i, type, callbF, data;

							try{
								for ( i in recommendArray ) {
									type = recommendArray[i].type;
									callbF = recommendArray[i].callback;

									if ( 'undefined' !== typeof(callbF) ) {
										if ( 'undefined' !== typeof(type) && 'undefined' !== typeof(res.recommend) && 'undefined' !== typeof(res.recommend[type]) ) {
											callbF(res.recommend[type]);

											data = res.recommend[type].data;
											if ( data ) {
												console.log('Показ товарных рекомендаций от Retailrocket для блока ' + type);
												try {
													rrApi.recomTrack(data.method, data.id, data.recommendations);
												} catch( e ) {
													console.warn('Retailrocket error');
													console.log(e.message);
												}
											}
										}
										else {
											callbF(res);
										}
									}
								}
							}
							catch(e) {
								console.warn('Error in RR recomendations');
								console.log(e);
								callback({'success': false});
							}
						},
						error: function(e) {
							console.warn('Error in RR ajax response');
							console.log(e);
							callback({'success': false});
						}
					});
				}
			}
			else {
				reqArray.push({
					type: 'GET',
					url: url,
					callback: callback
				});

				if ( reqArray.length === (slidersWithUrl - slidersRecommendation) ) {
					window.ENTER.utils.packageReq(reqArray);
				}
			}
		};

		/**
		 * Обработка для каждого элемента попавшего в набор
		 */
		var SliderControl = function( mainNode ) {
			/**
			 * Обработка для каждого элемента попавшего в набор
			 *
			 * @var		{Object}	options			Расширение стандартных значений слайдера пользовательскими настройками
			 * @var	{Object}	$self			Ссылка на текущий элемент из набора
			 * @var	{Object}	sliderParams	Параметры текущего слайдера
			 * @var	{Boolean}	hasCategory		Имеет ли слайдер категории
			 *
			 * @var	{Object}	leftBtn			Ссылка на левую стрелку
			 * @var	{Object}	rightBtn		Ссылка на правую стрелку
			 * @var	{Object}	wrap			Ссылка на обертку слайдера
			 * @var	{Object}	slider			Ссылка на контейнер с товарами
			 * @var	{Object}	item			Ссылка на карточки товаров в слайдере
			 * @var	{Object}	catItem			Ссылка на категории в слайдере
			 * @var	{Object}	pageTitle   	Ссылка на заголовоклисталки в слайдере
			 *
			 * @var	{Number}	itemW			Ширина одной карточки товара в слайдере
			 * @var	{Number}	elementOnSlide	Количество помещающихся карточек на один слайд
			 *
			 * @var	{Number}	nowLeft			Текущий отступ слева
			 */
			var
				options = $.extend(
							{},
							$.fn.goodsSlider.defaults,
							params ),
				$self = mainNode,
				sliderParams = $self.data('slider'),
				hasCategory = $self.hasClass('mWithCategory'),

				leftBtn = $self.find(options.leftArrowSelector),
				rightBtn = $self.find(options.rightArrowSelector),
				wrap = $self.find(options.sliderWrapperSelector),
				slider = $self.find(options.sliderSelector),
				item = $self.find(options.itemSelector),
				catItem = $self.find(options.categoryItemSelector),
                pageTitle = $self.find(options.pageTitleSelector),

				nowLeft = 0;
			// end of vars

			var
				calculateItemWidth = function() {
					return item.width() + parseInt(item.css('marginLeft'),10) + parseInt(item.css('marginRight'),10);
				},
				calculateElementOnSlideCount = function(itemW) {
					return parseInt(wrap.width()/itemW, 10);
				},
				/**
				 * Переключение на следующий слайд. Проверка состояния кнопок.
				 */
				nextSlide = function nextSlide(e) {
					if ( $(this).hasClass('mDisabled') ) {
						return false;
					}

					var
						itemW = calculateItemWidth(),
						elementOnSlide = calculateElementOnSlideCount(itemW);

					leftBtn.removeClass('mDisabled');

					if ( nowLeft + elementOnSlide * itemW >= slider.width()-elementOnSlide * itemW ) {
						nowLeft = slider.width() - elementOnSlide * itemW;
						rightBtn.addClass('mDisabled');
					}
					else {
						nowLeft = nowLeft + elementOnSlide * itemW;
						rightBtn.removeClass('mDisabled');
					}

					console.info(itemW);
					console.log(elementOnSlide);
					console.log(nowLeft);
					console.log(wrap.width());

					slider.animate({'left': -nowLeft });

                    updatePageTitle(wrap.width(), nowLeft);

                    e.preventDefault();
                    //return false;
				},

				/**
				 * Переключение на предыдущий слайд. Проверка состояния кнопок.
				 */
				prevSlide = function prevSlide(e) {
					if ( $(this).hasClass('mDisabled') ) {
						return false;
					}

					var
						itemW = calculateItemWidth(),
						elementOnSlide = calculateElementOnSlideCount(itemW);

					rightBtn.removeClass('mDisabled');

					if ( nowLeft - elementOnSlide * itemW <= 0 ) {
						nowLeft = 0;
						leftBtn.addClass('mDisabled');
					}
					else {
						nowLeft = nowLeft - elementOnSlide * itemW;
						leftBtn.removeClass('mDisabled');
					}

					slider.animate({'left': -nowLeft });

                    updatePageTitle(wrap.width(), nowLeft);

                    e.preventDefault();
					//return false;
				},

                updatePageTitle = function updatePageTitle(width, left) {
                    var
						pageNum = Math.floor(left / width) + 1,
						itemW = calculateItemWidth(),
						elementOnSlide = calculateElementOnSlideCount(itemW);

                    if (!sliderParams.count || !elementOnSlide || !pageNum) return;

                    //pageTitle.text('Страница ' + pageNum +  ' goodsSliderиз ' + Math.ceil(sliderParams.count / elementOnSlide));
                },

				/**
				 * Вычисление ширины слайдера
				 * 
				 * @param	{Object}	nowItems	Текущие элементы слайдера
				 */
				reWidthSlider = function reWidthSlider( nowItems ) {
					var
						itemW = calculateItemWidth(),
						elementOnSlide = calculateElementOnSlideCount(itemW);

					leftBtn.addClass('mDisabled');
					rightBtn.addClass('mDisabled');

					if ( nowItems.length > elementOnSlide ) {
						rightBtn.removeClass('mDisabled');
					}

					slider.width(nowItems.length * itemW);
					nowLeft = 0;
					leftBtn.addClass('mDisabled');
					slider.css({'left':nowLeft});
					wrap.removeClass('mLoader');
					nowItems.show();
				},

				/**
				 * Показ товаров определенной категории
				 */
				showCategoryGoods = function showCategoryGoods() {
					var nowCategoryId = catItem.filter('.mActive').attr('id'),
						showAll = ( catItem.filter('.mActive').data('product') === 'all' ),
						nowShowItem = ( showAll ) ? item : item.filter('[data-category="'+nowCategoryId+'"]');
					//end of vars
					
					item.hide();
					reWidthSlider( nowShowItem );
				},

				/**
				 * Хандлер выбора категории
				 */
				selectCategory = function selectCategory() {
					catItem.removeClass('mActive');
					$(this).addClass('mActive');
					showCategoryGoods();
				},

				/**
				 * Обработка ответа от сервера
				 * 
				 * @param	{Object}	res	Ответ от сервера
				 */
				authFromServer = function authFromServer( res ) {
					var newSlider;

					if ( !res.success ){
						$self.remove();
						
						return false;
					}

					newSlider = $(res.content)[0];
					$self.before(newSlider);
					$self.remove();
					$(newSlider).goodsSlider();

					if (params.onLoad) {
						params.onLoad(newSlider);
					}

                    body.trigger('TLT_logCustomEvent', ['recommendation_loaded', $(newSlider).data('position')]);
				},

				/**
				 * Неудача при получении данных с сервера
				 */
				errorStatusCode = function errorStatusCode() {
					console.warn('Слайдер товаров: Неудача при получении данных с сервера');
					
					$self.remove();
				};
			// end of function

// SITE-4612
//            if (sliderParams.count) {
//				var
//					itemW = calculateItemWidth(),
//					elementOnSlide = calculateElementOnSlideCount(itemW);
//
//                pageTitle.text('Страница ' + '1' +  ' из ' + Math.ceil(sliderParams.count / elementOnSlide));
//            }

			if ( sliderParams.url !== null ) {
				if ( typeof window.ENTER.utils.packageReq === 'function' ) {
                    try {
                        if ('viewed' == sliderParams.type) {
                            sliderParams.url += ((-1 != sliderParams.url.indexOf('?')) ? '&' : '?') + 'rrviewed=' + sliderParams.rrviewed + '&' + $.param({senders: [sliderParams.sender]}) + (sliderParams.sender2 ? '&' + $.param({sender2: sliderParams.sender2}) : '');

                            getSlidersData(sliderParams.url, sliderParams.type, function(res) {
                                res.recommend && res.recommend.viewed && authFromServer(res.recommend.viewed);
                            });
                        } else {
                            getSlidersData(sliderParams.url, sliderParams.type, authFromServer);
                        }
                    } catch (e) { console.error(e); }
				}
				else {
					$.ajax({
						type: 'GET',
						url: sliderParams.url,
						success: authFromServer,
						statusCode: {
							500: errorStatusCode,
							503: errorStatusCode,
							504: errorStatusCode
						}
					});
				}
			}
			else {
				if ( hasCategory ) {
					showCategoryGoods();
				}
				else {
					reWidthSlider( item );
				}
			}

			rightBtn.on('click', nextSlide);
			leftBtn.on('click', prevSlide);
			catItem.on('click', selectCategory);
		};


		return this.each(function() {
			var $self = $(this);

			new SliderControl($self);
		});
	};

	$.fn.goodsSlider.defaults = {
		leftArrowSelector: '.slideItem_btn-prv',
		rightArrowSelector: '.slideItem_btn-nxt',
		sliderWrapperSelector: '.slideItem_inn',
		sliderSelector: '.slideItem_lst',
		itemSelector: '.slideItem_i',
		categoryItemSelector: '.bGoodsSlider__eCatItem',
        pageTitleSelector: '.slideItem_cntr'
	};

})(jQuery);
/*! jQuery UI - v1.10.3 - 2013-07-09
* http://jqueryui.com
* Includes: jquery.ui.core.js, jquery.ui.widget.js, jquery.ui.mouse.js, jquery.ui.position.js, jquery.ui.autocomplete.js, jquery.ui.menu.js, jquery.ui.slider.js
* Copyright 2013 jQuery Foundation and other contributors Licensed MIT */

(function( $, undefined ) {

var uuid = 0,
	runiqueId = /^ui-id-\d+$/;

// $.ui might exist from components with no dependencies, e.g., $.ui.position
$.ui = $.ui || {};

$.extend( $.ui, {
	version: "1.10.3",

	keyCode: {
		BACKSPACE: 8,
		COMMA: 188,
		DELETE: 46,
		DOWN: 40,
		END: 35,
		ENTER: 13,
		ESCAPE: 27,
		HOME: 36,
		LEFT: 37,
		NUMPAD_ADD: 107,
		NUMPAD_DECIMAL: 110,
		NUMPAD_DIVIDE: 111,
		NUMPAD_ENTER: 108,
		NUMPAD_MULTIPLY: 106,
		NUMPAD_SUBTRACT: 109,
		PAGE_DOWN: 34,
		PAGE_UP: 33,
		PERIOD: 190,
		RIGHT: 39,
		SPACE: 32,
		TAB: 9,
		UP: 38
	}
});

// plugins
$.fn.extend({
	focus: (function( orig ) {
		return function( delay, fn ) {
			return typeof delay === "number" ?
				this.each(function() {
					var elem = this;
					setTimeout(function() {
						$( elem ).focus();
						if ( fn ) {
							fn.call( elem );
						}
					}, delay );
				}) :
				orig.apply( this, arguments );
		};
	})( $.fn.focus ),

	scrollParent: function() {
		var scrollParent;
		if (($.ui.ie && (/(static|relative)/).test(this.css("position"))) || (/absolute/).test(this.css("position"))) {
			scrollParent = this.parents().filter(function() {
				return (/(relative|absolute|fixed)/).test($.css(this,"position")) && (/(auto|scroll)/).test($.css(this,"overflow")+$.css(this,"overflow-y")+$.css(this,"overflow-x"));
			}).eq(0);
		} else {
			scrollParent = this.parents().filter(function() {
				return (/(auto|scroll)/).test($.css(this,"overflow")+$.css(this,"overflow-y")+$.css(this,"overflow-x"));
			}).eq(0);
		}

		return (/fixed/).test(this.css("position")) || !scrollParent.length ? $(document) : scrollParent;
	},

	zIndex: function( zIndex ) {
		if ( zIndex !== undefined ) {
			return this.css( "zIndex", zIndex );
		}

		if ( this.length ) {
			var elem = $( this[ 0 ] ), position, value;
			while ( elem.length && elem[ 0 ] !== document ) {
				// Ignore z-index if position is set to a value where z-index is ignored by the browser
				// This makes behavior of this function consistent across browsers
				// WebKit always returns auto if the element is positioned
				position = elem.css( "position" );
				if ( position === "absolute" || position === "relative" || position === "fixed" ) {
					// IE returns 0 when zIndex is not specified
					// other browsers return a string
					// we ignore the case of nested elements with an explicit value of 0
					// <div style="z-index: -10;"><div style="z-index: 0;"></div></div>
					value = parseInt( elem.css( "zIndex" ), 10 );
					if ( !isNaN( value ) && value !== 0 ) {
						return value;
					}
				}
				elem = elem.parent();
			}
		}

		return 0;
	},

	uniqueId: function() {
		return this.each(function() {
			if ( !this.id ) {
				this.id = "ui-id-" + (++uuid);
			}
		});
	},

	removeUniqueId: function() {
		return this.each(function() {
			if ( runiqueId.test( this.id ) ) {
				$( this ).removeAttr( "id" );
			}
		});
	}
});

// selectors
function focusable( element, isTabIndexNotNaN ) {
	var map, mapName, img,
		nodeName = element.nodeName.toLowerCase();
	if ( "area" === nodeName ) {
		map = element.parentNode;
		mapName = map.name;
		if ( !element.href || !mapName || map.nodeName.toLowerCase() !== "map" ) {
			return false;
		}
		img = $( "img[usemap=#" + mapName + "]" )[0];
		return !!img && visible( img );
	}
	return ( /input|select|textarea|button|object/.test( nodeName ) ?
		!element.disabled :
		"a" === nodeName ?
			element.href || isTabIndexNotNaN :
			isTabIndexNotNaN) &&
		// the element and all of its ancestors must be visible
		visible( element );
}

function visible( element ) {
	return $.expr.filters.visible( element ) &&
		!$( element ).parents().addBack().filter(function() {
			return $.css( this, "visibility" ) === "hidden";
		}).length;
}

$.extend( $.expr[ ":" ], {
	data: $.expr.createPseudo ?
		$.expr.createPseudo(function( dataName ) {
			return function( elem ) {
				return !!$.data( elem, dataName );
			};
		}) :
		// support: jQuery <1.8
		function( elem, i, match ) {
			return !!$.data( elem, match[ 3 ] );
		},

	focusable: function( element ) {
		return focusable( element, !isNaN( $.attr( element, "tabindex" ) ) );
	},

	tabbable: function( element ) {
		var tabIndex = $.attr( element, "tabindex" ),
			isTabIndexNaN = isNaN( tabIndex );
		return ( isTabIndexNaN || tabIndex >= 0 ) && focusable( element, !isTabIndexNaN );
	}
});

// support: jQuery <1.8
if ( !$( "<a>" ).outerWidth( 1 ).jquery ) {
	$.each( [ "Width", "Height" ], function( i, name ) {
		var side = name === "Width" ? [ "Left", "Right" ] : [ "Top", "Bottom" ],
			type = name.toLowerCase(),
			orig = {
				innerWidth: $.fn.innerWidth,
				innerHeight: $.fn.innerHeight,
				outerWidth: $.fn.outerWidth,
				outerHeight: $.fn.outerHeight
			};

		function reduce( elem, size, border, margin ) {
			$.each( side, function() {
				size -= parseFloat( $.css( elem, "padding" + this ) ) || 0;
				if ( border ) {
					size -= parseFloat( $.css( elem, "border" + this + "Width" ) ) || 0;
				}
				if ( margin ) {
					size -= parseFloat( $.css( elem, "margin" + this ) ) || 0;
				}
			});
			return size;
		}

		$.fn[ "inner" + name ] = function( size ) {
			if ( size === undefined ) {
				return orig[ "inner" + name ].call( this );
			}

			return this.each(function() {
				$( this ).css( type, reduce( this, size ) + "px" );
			});
		};

		$.fn[ "outer" + name] = function( size, margin ) {
			if ( typeof size !== "number" ) {
				return orig[ "outer" + name ].call( this, size );
			}

			return this.each(function() {
				$( this).css( type, reduce( this, size, true, margin ) + "px" );
			});
		};
	});
}

// support: jQuery <1.8
if ( !$.fn.addBack ) {
	$.fn.addBack = function( selector ) {
		return this.add( selector == null ?
			this.prevObject : this.prevObject.filter( selector )
		);
	};
}

// support: jQuery 1.6.1, 1.6.2 (http://bugs.jquery.com/ticket/9413)
if ( $( "<a>" ).data( "a-b", "a" ).removeData( "a-b" ).data( "a-b" ) ) {
	$.fn.removeData = (function( removeData ) {
		return function( key ) {
			if ( arguments.length ) {
				return removeData.call( this, $.camelCase( key ) );
			} else {
				return removeData.call( this );
			}
		};
	})( $.fn.removeData );
}





// deprecated
$.ui.ie = !!/msie [\w.]+/.exec( navigator.userAgent.toLowerCase() );

$.support.selectstart = "onselectstart" in document.createElement( "div" );
$.fn.extend({
	disableSelection: function() {
		return this.bind( ( $.support.selectstart ? "selectstart" : "mousedown" ) +
			".ui-disableSelection", function( event ) {
				event.preventDefault();
			});
	},

	enableSelection: function() {
		return this.unbind( ".ui-disableSelection" );
	}
});

$.extend( $.ui, {
	// $.ui.plugin is deprecated. Use $.widget() extensions instead.
	plugin: {
		add: function( module, option, set ) {
			var i,
				proto = $.ui[ module ].prototype;
			for ( i in set ) {
				proto.plugins[ i ] = proto.plugins[ i ] || [];
				proto.plugins[ i ].push( [ option, set[ i ] ] );
			}
		},
		call: function( instance, name, args ) {
			var i,
				set = instance.plugins[ name ];
			if ( !set || !instance.element[ 0 ].parentNode || instance.element[ 0 ].parentNode.nodeType === 11 ) {
				return;
			}

			for ( i = 0; i < set.length; i++ ) {
				if ( instance.options[ set[ i ][ 0 ] ] ) {
					set[ i ][ 1 ].apply( instance.element, args );
				}
			}
		}
	},

	// only used by resizable
	hasScroll: function( el, a ) {

		//If overflow is hidden, the element might have extra content, but the user wants to hide it
		if ( $( el ).css( "overflow" ) === "hidden") {
			return false;
		}

		var scroll = ( a && a === "left" ) ? "scrollLeft" : "scrollTop",
			has = false;

		if ( el[ scroll ] > 0 ) {
			return true;
		}

		// TODO: determine which cases actually cause this to happen
		// if the element doesn't have the scroll set, see if it's possible to
		// set the scroll
		el[ scroll ] = 1;
		has = ( el[ scroll ] > 0 );
		el[ scroll ] = 0;
		return has;
	}
});

})( jQuery );
(function( $, undefined ) {

var uuid = 0,
	slice = Array.prototype.slice,
	_cleanData = $.cleanData;
$.cleanData = function( elems ) {
	for ( var i = 0, elem; (elem = elems[i]) != null; i++ ) {
		try {
			$( elem ).triggerHandler( "remove" );
		// http://bugs.jquery.com/ticket/8235
		} catch( e ) {}
	}
	_cleanData( elems );
};

$.widget = function( name, base, prototype ) {
	var fullName, existingConstructor, constructor, basePrototype,
		// proxiedPrototype allows the provided prototype to remain unmodified
		// so that it can be used as a mixin for multiple widgets (#8876)
		proxiedPrototype = {},
		namespace = name.split( "." )[ 0 ];

	name = name.split( "." )[ 1 ];
	fullName = namespace + "-" + name;

	if ( !prototype ) {
		prototype = base;
		base = $.Widget;
	}

	// create selector for plugin
	$.expr[ ":" ][ fullName.toLowerCase() ] = function( elem ) {
		return !!$.data( elem, fullName );
	};

	$[ namespace ] = $[ namespace ] || {};
	existingConstructor = $[ namespace ][ name ];
	constructor = $[ namespace ][ name ] = function( options, element ) {
		// allow instantiation without "new" keyword
		if ( !this._createWidget ) {
			return new constructor( options, element );
		}

		// allow instantiation without initializing for simple inheritance
		// must use "new" keyword (the code above always passes args)
		if ( arguments.length ) {
			this._createWidget( options, element );
		}
	};
	// extend with the existing constructor to carry over any static properties
	$.extend( constructor, existingConstructor, {
		version: prototype.version,
		// copy the object used to create the prototype in case we need to
		// redefine the widget later
		_proto: $.extend( {}, prototype ),
		// track widgets that inherit from this widget in case this widget is
		// redefined after a widget inherits from it
		_childConstructors: []
	});

	basePrototype = new base();
	// we need to make the options hash a property directly on the new instance
	// otherwise we'll modify the options hash on the prototype that we're
	// inheriting from
	basePrototype.options = $.widget.extend( {}, basePrototype.options );
	$.each( prototype, function( prop, value ) {
		if ( !$.isFunction( value ) ) {
			proxiedPrototype[ prop ] = value;
			return;
		}
		proxiedPrototype[ prop ] = (function() {
			var _super = function() {
					return base.prototype[ prop ].apply( this, arguments );
				},
				_superApply = function( args ) {
					return base.prototype[ prop ].apply( this, args );
				};
			return function() {
				var __super = this._super,
					__superApply = this._superApply,
					returnValue;

				this._super = _super;
				this._superApply = _superApply;

				returnValue = value.apply( this, arguments );

				this._super = __super;
				this._superApply = __superApply;

				return returnValue;
			};
		})();
	});
	constructor.prototype = $.widget.extend( basePrototype, {
		// TODO: remove support for widgetEventPrefix
		// always use the name + a colon as the prefix, e.g., draggable:start
		// don't prefix for widgets that aren't DOM-based
		widgetEventPrefix: existingConstructor ? basePrototype.widgetEventPrefix : name
	}, proxiedPrototype, {
		constructor: constructor,
		namespace: namespace,
		widgetName: name,
		widgetFullName: fullName
	});

	// If this widget is being redefined then we need to find all widgets that
	// are inheriting from it and redefine all of them so that they inherit from
	// the new version of this widget. We're essentially trying to replace one
	// level in the prototype chain.
	if ( existingConstructor ) {
		$.each( existingConstructor._childConstructors, function( i, child ) {
			var childPrototype = child.prototype;

			// redefine the child widget using the same prototype that was
			// originally used, but inherit from the new version of the base
			$.widget( childPrototype.namespace + "." + childPrototype.widgetName, constructor, child._proto );
		});
		// remove the list of existing child constructors from the old constructor
		// so the old child constructors can be garbage collected
		delete existingConstructor._childConstructors;
	} else {
		base._childConstructors.push( constructor );
	}

	$.widget.bridge( name, constructor );
};

$.widget.extend = function( target ) {
	var input = slice.call( arguments, 1 ),
		inputIndex = 0,
		inputLength = input.length,
		key,
		value;
	for ( ; inputIndex < inputLength; inputIndex++ ) {
		for ( key in input[ inputIndex ] ) {
			value = input[ inputIndex ][ key ];
			if ( input[ inputIndex ].hasOwnProperty( key ) && value !== undefined ) {
				// Clone objects
				if ( $.isPlainObject( value ) ) {
					target[ key ] = $.isPlainObject( target[ key ] ) ?
						$.widget.extend( {}, target[ key ], value ) :
						// Don't extend strings, arrays, etc. with objects
						$.widget.extend( {}, value );
				// Copy everything else by reference
				} else {
					target[ key ] = value;
				}
			}
		}
	}
	return target;
};

$.widget.bridge = function( name, object ) {
	var fullName = object.prototype.widgetFullName || name;
	$.fn[ name ] = function( options ) {
		var isMethodCall = typeof options === "string",
			args = slice.call( arguments, 1 ),
			returnValue = this;

		// allow multiple hashes to be passed on init
		options = !isMethodCall && args.length ?
			$.widget.extend.apply( null, [ options ].concat(args) ) :
			options;

		if ( isMethodCall ) {
			this.each(function() {
				var methodValue,
					instance = $.data( this, fullName );
				if ( !instance ) {
					return $.error( "cannot call methods on " + name + " prior to initialization; " +
						"attempted to call method '" + options + "'" );
				}
				if ( !$.isFunction( instance[options] ) || options.charAt( 0 ) === "_" ) {
					return $.error( "no such method '" + options + "' for " + name + " widget instance" );
				}
				methodValue = instance[ options ].apply( instance, args );
				if ( methodValue !== instance && methodValue !== undefined ) {
					returnValue = methodValue && methodValue.jquery ?
						returnValue.pushStack( methodValue.get() ) :
						methodValue;
					return false;
				}
			});
		} else {
			this.each(function() {
				var instance = $.data( this, fullName );
				if ( instance ) {
					instance.option( options || {} )._init();
				} else {
					$.data( this, fullName, new object( options, this ) );
				}
			});
		}

		return returnValue;
	};
};

$.Widget = function( /* options, element */ ) {};
$.Widget._childConstructors = [];

$.Widget.prototype = {
	widgetName: "widget",
	widgetEventPrefix: "",
	defaultElement: "<div>",
	options: {
		disabled: false,

		// callbacks
		create: null
	},
	_createWidget: function( options, element ) {
		element = $( element || this.defaultElement || this )[ 0 ];
		this.element = $( element );
		this.uuid = uuid++;
		this.eventNamespace = "." + this.widgetName + this.uuid;
		this.options = $.widget.extend( {},
			this.options,
			this._getCreateOptions(),
			options );

		this.bindings = $();
		this.hoverable = $();
		this.focusable = $();

		if ( element !== this ) {
			$.data( element, this.widgetFullName, this );
			this._on( true, this.element, {
				remove: function( event ) {
					if ( event.target === element ) {
						this.destroy();
					}
				}
			});
			this.document = $( element.style ?
				// element within the document
				element.ownerDocument :
				// element is window or document
				element.document || element );
			this.window = $( this.document[0].defaultView || this.document[0].parentWindow );
		}

		this._create();
		this._trigger( "create", null, this._getCreateEventData() );
		this._init();
	},
	_getCreateOptions: $.noop,
	_getCreateEventData: $.noop,
	_create: $.noop,
	_init: $.noop,

	destroy: function() {
		this._destroy();
		// we can probably remove the unbind calls in 2.0
		// all event bindings should go through this._on()
		this.element
			.unbind( this.eventNamespace )
			// 1.9 BC for #7810
			// TODO remove dual storage
			.removeData( this.widgetName )
			.removeData( this.widgetFullName )
			// support: jquery <1.6.3
			// http://bugs.jquery.com/ticket/9413
			.removeData( $.camelCase( this.widgetFullName ) );
		this.widget()
			.unbind( this.eventNamespace )
			.removeAttr( "aria-disabled" )
			.removeClass(
				this.widgetFullName + "-disabled " +
				"ui-state-disabled" );

		// clean up events and states
		this.bindings.unbind( this.eventNamespace );
		this.hoverable.removeClass( "ui-state-hover" );
		this.focusable.removeClass( "ui-state-focus" );
	},
	_destroy: $.noop,

	widget: function() {
		return this.element;
	},

	option: function( key, value ) {
		var options = key,
			parts,
			curOption,
			i;

		if ( arguments.length === 0 ) {
			// don't return a reference to the internal hash
			return $.widget.extend( {}, this.options );
		}

		if ( typeof key === "string" ) {
			// handle nested keys, e.g., "foo.bar" => { foo: { bar: ___ } }
			options = {};
			parts = key.split( "." );
			key = parts.shift();
			if ( parts.length ) {
				curOption = options[ key ] = $.widget.extend( {}, this.options[ key ] );
				for ( i = 0; i < parts.length - 1; i++ ) {
					curOption[ parts[ i ] ] = curOption[ parts[ i ] ] || {};
					curOption = curOption[ parts[ i ] ];
				}
				key = parts.pop();
				if ( value === undefined ) {
					return curOption[ key ] === undefined ? null : curOption[ key ];
				}
				curOption[ key ] = value;
			} else {
				if ( value === undefined ) {
					return this.options[ key ] === undefined ? null : this.options[ key ];
				}
				options[ key ] = value;
			}
		}

		this._setOptions( options );

		return this;
	},
	_setOptions: function( options ) {
		var key;

		for ( key in options ) {
			this._setOption( key, options[ key ] );
		}

		return this;
	},
	_setOption: function( key, value ) {
		this.options[ key ] = value;

		if ( key === "disabled" ) {
			this.widget()
				.toggleClass( this.widgetFullName + "-disabled ui-state-disabled", !!value )
				.attr( "aria-disabled", value );
			this.hoverable.removeClass( "ui-state-hover" );
			this.focusable.removeClass( "ui-state-focus" );
		}

		return this;
	},

	enable: function() {
		return this._setOption( "disabled", false );
	},
	disable: function() {
		return this._setOption( "disabled", true );
	},

	_on: function( suppressDisabledCheck, element, handlers ) {
		var delegateElement,
			instance = this;

		// no suppressDisabledCheck flag, shuffle arguments
		if ( typeof suppressDisabledCheck !== "boolean" ) {
			handlers = element;
			element = suppressDisabledCheck;
			suppressDisabledCheck = false;
		}

		// no element argument, shuffle and use this.element
		if ( !handlers ) {
			handlers = element;
			element = this.element;
			delegateElement = this.widget();
		} else {
			// accept selectors, DOM elements
			element = delegateElement = $( element );
			this.bindings = this.bindings.add( element );
		}

		$.each( handlers, function( event, handler ) {
			function handlerProxy() {
				// allow widgets to customize the disabled handling
				// - disabled as an array instead of boolean
				// - disabled class as method for disabling individual parts
				if ( !suppressDisabledCheck &&
						( instance.options.disabled === true ||
							$( this ).hasClass( "ui-state-disabled" ) ) ) {
					return;
				}
				return ( typeof handler === "string" ? instance[ handler ] : handler )
					.apply( instance, arguments );
			}

			// copy the guid so direct unbinding works
			if ( typeof handler !== "string" ) {
				handlerProxy.guid = handler.guid =
					handler.guid || handlerProxy.guid || $.guid++;
			}

			var match = event.match( /^(\w+)\s*(.*)$/ ),
				eventName = match[1] + instance.eventNamespace,
				selector = match[2];
			if ( selector ) {
				delegateElement.delegate( selector, eventName, handlerProxy );
			} else {
				element.bind( eventName, handlerProxy );
			}
		});
	},

	_off: function( element, eventName ) {
		eventName = (eventName || "").split( " " ).join( this.eventNamespace + " " ) + this.eventNamespace;
		element.unbind( eventName ).undelegate( eventName );
	},

	_delay: function( handler, delay ) {
		function handlerProxy() {
			return ( typeof handler === "string" ? instance[ handler ] : handler )
				.apply( instance, arguments );
		}
		var instance = this;
		return setTimeout( handlerProxy, delay || 0 );
	},

	_hoverable: function( element ) {
		this.hoverable = this.hoverable.add( element );
		this._on( element, {
			mouseenter: function( event ) {
				$( event.currentTarget ).addClass( "ui-state-hover" );
			},
			mouseleave: function( event ) {
				$( event.currentTarget ).removeClass( "ui-state-hover" );
			}
		});
	},

	_focusable: function( element ) {
		this.focusable = this.focusable.add( element );
		this._on( element, {
			focusin: function( event ) {
				$( event.currentTarget ).addClass( "ui-state-focus" );
			},
			focusout: function( event ) {
				$( event.currentTarget ).removeClass( "ui-state-focus" );
			}
		});
	},

	_trigger: function( type, event, data ) {
		var prop, orig,
			callback = this.options[ type ];

		data = data || {};
		event = $.Event( event );
		event.type = ( type === this.widgetEventPrefix ?
			type :
			this.widgetEventPrefix + type ).toLowerCase();
		// the original event may come from any element
		// so we need to reset the target on the new event
		event.target = this.element[ 0 ];

		// copy original event properties over to the new event
		orig = event.originalEvent;
		if ( orig ) {
			for ( prop in orig ) {
				if ( !( prop in event ) ) {
					event[ prop ] = orig[ prop ];
				}
			}
		}

		this.element.trigger( event, data );
		return !( $.isFunction( callback ) &&
			callback.apply( this.element[0], [ event ].concat( data ) ) === false ||
			event.isDefaultPrevented() );
	}
};

$.each( { show: "fadeIn", hide: "fadeOut" }, function( method, defaultEffect ) {
	$.Widget.prototype[ "_" + method ] = function( element, options, callback ) {
		if ( typeof options === "string" ) {
			options = { effect: options };
		}
		var hasOptions,
			effectName = !options ?
				method :
				options === true || typeof options === "number" ?
					defaultEffect :
					options.effect || defaultEffect;
		options = options || {};
		if ( typeof options === "number" ) {
			options = { duration: options };
		}
		hasOptions = !$.isEmptyObject( options );
		options.complete = callback;
		if ( options.delay ) {
			element.delay( options.delay );
		}
		if ( hasOptions && $.effects && $.effects.effect[ effectName ] ) {
			element[ method ]( options );
		} else if ( effectName !== method && element[ effectName ] ) {
			element[ effectName ]( options.duration, options.easing, callback );
		} else {
			element.queue(function( next ) {
				$( this )[ method ]();
				if ( callback ) {
					callback.call( element[ 0 ] );
				}
				next();
			});
		}
	};
});

})( jQuery );
(function( $, undefined ) {

var mouseHandled = false;
$( document ).mouseup( function() {
	mouseHandled = false;
});

$.widget("ui.mouse", {
	version: "1.10.3",
	options: {
		cancel: "input,textarea,button,select,option",
		distance: 1,
		delay: 0
	},
	_mouseInit: function() {
		var that = this;

		this.element
			.bind("mousedown."+this.widgetName, function(event) {
				return that._mouseDown(event);
			})
			.bind("click."+this.widgetName, function(event) {
				if (true === $.data(event.target, that.widgetName + ".preventClickEvent")) {
					$.removeData(event.target, that.widgetName + ".preventClickEvent");
					event.stopImmediatePropagation();
					return false;
				}
			});

		this.started = false;
	},

	// TODO: make sure destroying one instance of mouse doesn't mess with
	// other instances of mouse
	_mouseDestroy: function() {
		this.element.unbind("."+this.widgetName);
		if ( this._mouseMoveDelegate ) {
			$(document)
				.unbind("mousemove."+this.widgetName, this._mouseMoveDelegate)
				.unbind("mouseup."+this.widgetName, this._mouseUpDelegate);
		}
	},

	_mouseDown: function(event) {
		// don't let more than one widget handle mouseStart
		if( mouseHandled ) { return; }

		// we may have missed mouseup (out of window)
		(this._mouseStarted && this._mouseUp(event));

		this._mouseDownEvent = event;

		var that = this,
			btnIsLeft = (event.which === 1),
			// event.target.nodeName works around a bug in IE 8 with
			// disabled inputs (#7620)
			elIsCancel = (typeof this.options.cancel === "string" && event.target.nodeName ? $(event.target).closest(this.options.cancel).length : false);
		if (!btnIsLeft || elIsCancel || !this._mouseCapture(event)) {
			return true;
		}

		this.mouseDelayMet = !this.options.delay;
		if (!this.mouseDelayMet) {
			this._mouseDelayTimer = setTimeout(function() {
				that.mouseDelayMet = true;
			}, this.options.delay);
		}

		if (this._mouseDistanceMet(event) && this._mouseDelayMet(event)) {
			this._mouseStarted = (this._mouseStart(event) !== false);
			if (!this._mouseStarted) {
				event.preventDefault();
				return true;
			}
		}

		// Click event may never have fired (Gecko & Opera)
		if (true === $.data(event.target, this.widgetName + ".preventClickEvent")) {
			$.removeData(event.target, this.widgetName + ".preventClickEvent");
		}

		// these delegates are required to keep context
		this._mouseMoveDelegate = function(event) {
			return that._mouseMove(event);
		};
		this._mouseUpDelegate = function(event) {
			return that._mouseUp(event);
		};
		$(document)
			.bind("mousemove."+this.widgetName, this._mouseMoveDelegate)
			.bind("mouseup."+this.widgetName, this._mouseUpDelegate);

		event.preventDefault();

		mouseHandled = true;
		return true;
	},

	_mouseMove: function(event) {
		// IE mouseup check - mouseup happened when mouse was out of window
		if ($.ui.ie && ( !document.documentMode || document.documentMode < 9 ) && !event.button) {
			return this._mouseUp(event);
		}

		if (this._mouseStarted) {
			this._mouseDrag(event);
			return event.preventDefault();
		}

		if (this._mouseDistanceMet(event) && this._mouseDelayMet(event)) {
			this._mouseStarted =
				(this._mouseStart(this._mouseDownEvent, event) !== false);
			(this._mouseStarted ? this._mouseDrag(event) : this._mouseUp(event));
		}

		return !this._mouseStarted;
	},

	_mouseUp: function(event) {
		$(document)
			.unbind("mousemove."+this.widgetName, this._mouseMoveDelegate)
			.unbind("mouseup."+this.widgetName, this._mouseUpDelegate);

		if (this._mouseStarted) {
			this._mouseStarted = false;

			if (event.target === this._mouseDownEvent.target) {
				$.data(event.target, this.widgetName + ".preventClickEvent", true);
			}

			this._mouseStop(event);
		}

		return false;
	},

	_mouseDistanceMet: function(event) {
		return (Math.max(
				Math.abs(this._mouseDownEvent.pageX - event.pageX),
				Math.abs(this._mouseDownEvent.pageY - event.pageY)
			) >= this.options.distance
		);
	},

	_mouseDelayMet: function(/* event */) {
		return this.mouseDelayMet;
	},

	// These are placeholder methods, to be overriden by extending plugin
	_mouseStart: function(/* event */) {},
	_mouseDrag: function(/* event */) {},
	_mouseStop: function(/* event */) {},
	_mouseCapture: function(/* event */) { return true; }
});

})(jQuery);
(function( $, undefined ) {

$.ui = $.ui || {};

var cachedScrollbarWidth,
	max = Math.max,
	abs = Math.abs,
	round = Math.round,
	rhorizontal = /left|center|right/,
	rvertical = /top|center|bottom/,
	roffset = /[\+\-]\d+(\.[\d]+)?%?/,
	rposition = /^\w+/,
	rpercent = /%$/,
	_position = $.fn.position;

function getOffsets( offsets, width, height ) {
	return [
		parseFloat( offsets[ 0 ] ) * ( rpercent.test( offsets[ 0 ] ) ? width / 100 : 1 ),
		parseFloat( offsets[ 1 ] ) * ( rpercent.test( offsets[ 1 ] ) ? height / 100 : 1 )
	];
}

function parseCss( element, property ) {
	return parseInt( $.css( element, property ), 10 ) || 0;
}

function getDimensions( elem ) {
	var raw = elem[0];
	if ( raw.nodeType === 9 ) {
		return {
			width: elem.width(),
			height: elem.height(),
			offset: { top: 0, left: 0 }
		};
	}
	if ( $.isWindow( raw ) ) {
		return {
			width: elem.width(),
			height: elem.height(),
			offset: { top: elem.scrollTop(), left: elem.scrollLeft() }
		};
	}
	if ( raw.preventDefault ) {
		return {
			width: 0,
			height: 0,
			offset: { top: raw.pageY, left: raw.pageX }
		};
	}
	return {
		width: elem.outerWidth(),
		height: elem.outerHeight(),
		offset: elem.offset()
	};
}

$.position = {
	scrollbarWidth: function() {
		if ( cachedScrollbarWidth !== undefined ) {
			return cachedScrollbarWidth;
		}
		var w1, w2,
			div = $( "<div style='display:block;width:50px;height:50px;overflow:hidden;'><div style='height:100px;width:auto;'></div></div>" ),
			innerDiv = div.children()[0];

		$( "body" ).append( div );
		w1 = innerDiv.offsetWidth;
		div.css( "overflow", "scroll" );

		w2 = innerDiv.offsetWidth;

		if ( w1 === w2 ) {
			w2 = div[0].clientWidth;
		}

		div.remove();

		return (cachedScrollbarWidth = w1 - w2);
	},
	getScrollInfo: function( within ) {
		var overflowX = within.isWindow ? "" : within.element.css( "overflow-x" ),
			overflowY = within.isWindow ? "" : within.element.css( "overflow-y" ),
			hasOverflowX = overflowX === "scroll" ||
				( overflowX === "auto" && within.width < within.element[0].scrollWidth ),
			hasOverflowY = overflowY === "scroll" ||
				( overflowY === "auto" && within.height < within.element[0].scrollHeight );
		return {
			width: hasOverflowY ? $.position.scrollbarWidth() : 0,
			height: hasOverflowX ? $.position.scrollbarWidth() : 0
		};
	},
	getWithinInfo: function( element ) {
		var withinElement = $( element || window ),
			isWindow = $.isWindow( withinElement[0] );
		return {
			element: withinElement,
			isWindow: isWindow,
			offset: withinElement.offset() || { left: 0, top: 0 },
			scrollLeft: withinElement.scrollLeft(),
			scrollTop: withinElement.scrollTop(),
			width: isWindow ? withinElement.width() : withinElement.outerWidth(),
			height: isWindow ? withinElement.height() : withinElement.outerHeight()
		};
	}
};

$.fn.position = function( options ) {
	if ( !options || !options.of ) {
		return _position.apply( this, arguments );
	}

	// make a copy, we don't want to modify arguments
	options = $.extend( {}, options );

	var atOffset, targetWidth, targetHeight, targetOffset, basePosition, dimensions,
		target = $( options.of ),
		within = $.position.getWithinInfo( options.within ),
		scrollInfo = $.position.getScrollInfo( within ),
		collision = ( options.collision || "flip" ).split( " " ),
		offsets = {};

	dimensions = getDimensions( target );
	if ( target[0].preventDefault ) {
		// force left top to allow flipping
		options.at = "left top";
	}
	targetWidth = dimensions.width;
	targetHeight = dimensions.height;
	targetOffset = dimensions.offset;
	// clone to reuse original targetOffset later
	basePosition = $.extend( {}, targetOffset );

	// force my and at to have valid horizontal and vertical positions
	// if a value is missing or invalid, it will be converted to center
	$.each( [ "my", "at" ], function() {
		var pos = ( options[ this ] || "" ).split( " " ),
			horizontalOffset,
			verticalOffset;

		if ( pos.length === 1) {
			pos = rhorizontal.test( pos[ 0 ] ) ?
				pos.concat( [ "center" ] ) :
				rvertical.test( pos[ 0 ] ) ?
					[ "center" ].concat( pos ) :
					[ "center", "center" ];
		}
		pos[ 0 ] = rhorizontal.test( pos[ 0 ] ) ? pos[ 0 ] : "center";
		pos[ 1 ] = rvertical.test( pos[ 1 ] ) ? pos[ 1 ] : "center";

		// calculate offsets
		horizontalOffset = roffset.exec( pos[ 0 ] );
		verticalOffset = roffset.exec( pos[ 1 ] );
		offsets[ this ] = [
			horizontalOffset ? horizontalOffset[ 0 ] : 0,
			verticalOffset ? verticalOffset[ 0 ] : 0
		];

		// reduce to just the positions without the offsets
		options[ this ] = [
			rposition.exec( pos[ 0 ] )[ 0 ],
			rposition.exec( pos[ 1 ] )[ 0 ]
		];
	});

	// normalize collision option
	if ( collision.length === 1 ) {
		collision[ 1 ] = collision[ 0 ];
	}

	if ( options.at[ 0 ] === "right" ) {
		basePosition.left += targetWidth;
	} else if ( options.at[ 0 ] === "center" ) {
		basePosition.left += targetWidth / 2;
	}

	if ( options.at[ 1 ] === "bottom" ) {
		basePosition.top += targetHeight;
	} else if ( options.at[ 1 ] === "center" ) {
		basePosition.top += targetHeight / 2;
	}

	atOffset = getOffsets( offsets.at, targetWidth, targetHeight );
	basePosition.left += atOffset[ 0 ];
	basePosition.top += atOffset[ 1 ];

	return this.each(function() {
		var collisionPosition, using,
			elem = $( this ),
			elemWidth = elem.outerWidth(),
			elemHeight = elem.outerHeight(),
			marginLeft = parseCss( this, "marginLeft" ),
			marginTop = parseCss( this, "marginTop" ),
			collisionWidth = elemWidth + marginLeft + parseCss( this, "marginRight" ) + scrollInfo.width,
			collisionHeight = elemHeight + marginTop + parseCss( this, "marginBottom" ) + scrollInfo.height,
			position = $.extend( {}, basePosition ),
			myOffset = getOffsets( offsets.my, elem.outerWidth(), elem.outerHeight() );

		if ( options.my[ 0 ] === "right" ) {
			position.left -= elemWidth;
		} else if ( options.my[ 0 ] === "center" ) {
			position.left -= elemWidth / 2;
		}

		if ( options.my[ 1 ] === "bottom" ) {
			position.top -= elemHeight;
		} else if ( options.my[ 1 ] === "center" ) {
			position.top -= elemHeight / 2;
		}

		position.left += myOffset[ 0 ];
		position.top += myOffset[ 1 ];

		// if the browser doesn't support fractions, then round for consistent results
		if ( !$.support.offsetFractions ) {
			position.left = round( position.left );
			position.top = round( position.top );
		}

		collisionPosition = {
			marginLeft: marginLeft,
			marginTop: marginTop
		};

		$.each( [ "left", "top" ], function( i, dir ) {
			if ( $.ui.position[ collision[ i ] ] ) {
				$.ui.position[ collision[ i ] ][ dir ]( position, {
					targetWidth: targetWidth,
					targetHeight: targetHeight,
					elemWidth: elemWidth,
					elemHeight: elemHeight,
					collisionPosition: collisionPosition,
					collisionWidth: collisionWidth,
					collisionHeight: collisionHeight,
					offset: [ atOffset[ 0 ] + myOffset[ 0 ], atOffset [ 1 ] + myOffset[ 1 ] ],
					my: options.my,
					at: options.at,
					within: within,
					elem : elem
				});
			}
		});

		if ( options.using ) {
			// adds feedback as second argument to using callback, if present
			using = function( props ) {
				var left = targetOffset.left - position.left,
					right = left + targetWidth - elemWidth,
					top = targetOffset.top - position.top,
					bottom = top + targetHeight - elemHeight,
					feedback = {
						target: {
							element: target,
							left: targetOffset.left,
							top: targetOffset.top,
							width: targetWidth,
							height: targetHeight
						},
						element: {
							element: elem,
							left: position.left,
							top: position.top,
							width: elemWidth,
							height: elemHeight
						},
						horizontal: right < 0 ? "left" : left > 0 ? "right" : "center",
						vertical: bottom < 0 ? "top" : top > 0 ? "bottom" : "middle"
					};
				if ( targetWidth < elemWidth && abs( left + right ) < targetWidth ) {
					feedback.horizontal = "center";
				}
				if ( targetHeight < elemHeight && abs( top + bottom ) < targetHeight ) {
					feedback.vertical = "middle";
				}
				if ( max( abs( left ), abs( right ) ) > max( abs( top ), abs( bottom ) ) ) {
					feedback.important = "horizontal";
				} else {
					feedback.important = "vertical";
				}
				options.using.call( this, props, feedback );
			};
		}

		elem.offset( $.extend( position, { using: using } ) );
	});
};

$.ui.position = {
	fit: {
		left: function( position, data ) {
			var within = data.within,
				withinOffset = within.isWindow ? within.scrollLeft : within.offset.left,
				outerWidth = within.width,
				collisionPosLeft = position.left - data.collisionPosition.marginLeft,
				overLeft = withinOffset - collisionPosLeft,
				overRight = collisionPosLeft + data.collisionWidth - outerWidth - withinOffset,
				newOverRight;

			// element is wider than within
			if ( data.collisionWidth > outerWidth ) {
				// element is initially over the left side of within
				if ( overLeft > 0 && overRight <= 0 ) {
					newOverRight = position.left + overLeft + data.collisionWidth - outerWidth - withinOffset;
					position.left += overLeft - newOverRight;
				// element is initially over right side of within
				} else if ( overRight > 0 && overLeft <= 0 ) {
					position.left = withinOffset;
				// element is initially over both left and right sides of within
				} else {
					if ( overLeft > overRight ) {
						position.left = withinOffset + outerWidth - data.collisionWidth;
					} else {
						position.left = withinOffset;
					}
				}
			// too far left -> align with left edge
			} else if ( overLeft > 0 ) {
				position.left += overLeft;
			// too far right -> align with right edge
			} else if ( overRight > 0 ) {
				position.left -= overRight;
			// adjust based on position and margin
			} else {
				position.left = max( position.left - collisionPosLeft, position.left );
			}
		},
		top: function( position, data ) {
			var within = data.within,
				withinOffset = within.isWindow ? within.scrollTop : within.offset.top,
				outerHeight = data.within.height,
				collisionPosTop = position.top - data.collisionPosition.marginTop,
				overTop = withinOffset - collisionPosTop,
				overBottom = collisionPosTop + data.collisionHeight - outerHeight - withinOffset,
				newOverBottom;

			// element is taller than within
			if ( data.collisionHeight > outerHeight ) {
				// element is initially over the top of within
				if ( overTop > 0 && overBottom <= 0 ) {
					newOverBottom = position.top + overTop + data.collisionHeight - outerHeight - withinOffset;
					position.top += overTop - newOverBottom;
				// element is initially over bottom of within
				} else if ( overBottom > 0 && overTop <= 0 ) {
					position.top = withinOffset;
				// element is initially over both top and bottom of within
				} else {
					if ( overTop > overBottom ) {
						position.top = withinOffset + outerHeight - data.collisionHeight;
					} else {
						position.top = withinOffset;
					}
				}
			// too far up -> align with top
			} else if ( overTop > 0 ) {
				position.top += overTop;
			// too far down -> align with bottom edge
			} else if ( overBottom > 0 ) {
				position.top -= overBottom;
			// adjust based on position and margin
			} else {
				position.top = max( position.top - collisionPosTop, position.top );
			}
		}
	},
	flip: {
		left: function( position, data ) {
			var within = data.within,
				withinOffset = within.offset.left + within.scrollLeft,
				outerWidth = within.width,
				offsetLeft = within.isWindow ? within.scrollLeft : within.offset.left,
				collisionPosLeft = position.left - data.collisionPosition.marginLeft,
				overLeft = collisionPosLeft - offsetLeft,
				overRight = collisionPosLeft + data.collisionWidth - outerWidth - offsetLeft,
				myOffset = data.my[ 0 ] === "left" ?
					-data.elemWidth :
					data.my[ 0 ] === "right" ?
						data.elemWidth :
						0,
				atOffset = data.at[ 0 ] === "left" ?
					data.targetWidth :
					data.at[ 0 ] === "right" ?
						-data.targetWidth :
						0,
				offset = -2 * data.offset[ 0 ],
				newOverRight,
				newOverLeft;

			if ( overLeft < 0 ) {
				newOverRight = position.left + myOffset + atOffset + offset + data.collisionWidth - outerWidth - withinOffset;
				if ( newOverRight < 0 || newOverRight < abs( overLeft ) ) {
					position.left += myOffset + atOffset + offset;
				}
			}
			else if ( overRight > 0 ) {
				newOverLeft = position.left - data.collisionPosition.marginLeft + myOffset + atOffset + offset - offsetLeft;
				if ( newOverLeft > 0 || abs( newOverLeft ) < overRight ) {
					position.left += myOffset + atOffset + offset;
				}
			}
		},
		top: function( position, data ) {
			var within = data.within,
				withinOffset = within.offset.top + within.scrollTop,
				outerHeight = within.height,
				offsetTop = within.isWindow ? within.scrollTop : within.offset.top,
				collisionPosTop = position.top - data.collisionPosition.marginTop,
				overTop = collisionPosTop - offsetTop,
				overBottom = collisionPosTop + data.collisionHeight - outerHeight - offsetTop,
				top = data.my[ 1 ] === "top",
				myOffset = top ?
					-data.elemHeight :
					data.my[ 1 ] === "bottom" ?
						data.elemHeight :
						0,
				atOffset = data.at[ 1 ] === "top" ?
					data.targetHeight :
					data.at[ 1 ] === "bottom" ?
						-data.targetHeight :
						0,
				offset = -2 * data.offset[ 1 ],
				newOverTop,
				newOverBottom;
			if ( overTop < 0 ) {
				newOverBottom = position.top + myOffset + atOffset + offset + data.collisionHeight - outerHeight - withinOffset;
				if ( ( position.top + myOffset + atOffset + offset) > overTop && ( newOverBottom < 0 || newOverBottom < abs( overTop ) ) ) {
					position.top += myOffset + atOffset + offset;
				}
			}
			else if ( overBottom > 0 ) {
				newOverTop = position.top -  data.collisionPosition.marginTop + myOffset + atOffset + offset - offsetTop;
				if ( ( position.top + myOffset + atOffset + offset) > overBottom && ( newOverTop > 0 || abs( newOverTop ) < overBottom ) ) {
					position.top += myOffset + atOffset + offset;
				}
			}
		}
	},
	flipfit: {
		left: function() {
			$.ui.position.flip.left.apply( this, arguments );
			$.ui.position.fit.left.apply( this, arguments );
		},
		top: function() {
			$.ui.position.flip.top.apply( this, arguments );
			$.ui.position.fit.top.apply( this, arguments );
		}
	}
};

// fraction support test
(function () {
	var testElement, testElementParent, testElementStyle, offsetLeft, i,
		body = document.getElementsByTagName( "body" )[ 0 ],
		div = document.createElement( "div" );

	//Create a "fake body" for testing based on method used in jQuery.support
	testElement = document.createElement( body ? "div" : "body" );
	testElementStyle = {
		visibility: "hidden",
		width: 0,
		height: 0,
		border: 0,
		margin: 0,
		background: "none"
	};
	if ( body ) {
		$.extend( testElementStyle, {
			position: "absolute",
			left: "-1000px",
			top: "-1000px"
		});
	}
	for ( i in testElementStyle ) {
		testElement.style[ i ] = testElementStyle[ i ];
	}
	testElement.appendChild( div );
	testElementParent = body || document.documentElement;
	testElementParent.insertBefore( testElement, testElementParent.firstChild );

	div.style.cssText = "position: absolute; left: 10.7432222px;";

	offsetLeft = $( div ).offset().left;
	$.support.offsetFractions = offsetLeft > 10 && offsetLeft < 11;

	testElement.innerHTML = "";
	testElementParent.removeChild( testElement );
})();

}( jQuery ) );
(function( $, undefined ) {

// used to prevent race conditions with remote data sources
var requestIndex = 0;

$.widget( "ui.autocomplete", {
	version: "1.10.3",
	defaultElement: "<input>",
	options: {
		appendTo: null,
		autoFocus: false,
		delay: 300,
		minLength: 1,
		position: {
			my: "left top",
			at: "left bottom",
			collision: "none"
		},
		source: null,

		// callbacks
		change: null,
		close: null,
		focus: null,
		open: null,
		response: null,
		search: null,
		select: null
	},

	pending: 0,

	_create: function() {
		// Some browsers only repeat keydown events, not keypress events,
		// so we use the suppressKeyPress flag to determine if we've already
		// handled the keydown event. #7269
		// Unfortunately the code for & in keypress is the same as the up arrow,
		// so we use the suppressKeyPressRepeat flag to avoid handling keypress
		// events when we know the keydown event was used to modify the
		// search term. #7799
		var suppressKeyPress, suppressKeyPressRepeat, suppressInput,
			nodeName = this.element[0].nodeName.toLowerCase(),
			isTextarea = nodeName === "textarea",
			isInput = nodeName === "input";

		this.isMultiLine =
			// Textareas are always multi-line
			isTextarea ? true :
			// Inputs are always single-line, even if inside a contentEditable element
			// IE also treats inputs as contentEditable
			isInput ? false :
			// All other element types are determined by whether or not they're contentEditable
			this.element.prop( "isContentEditable" );

		this.valueMethod = this.element[ isTextarea || isInput ? "val" : "text" ];
		this.isNewMenu = true;

		this.element
			.addClass( "ui-autocomplete-input" )
			.attr( "autocomplete", "off" );

		this._on( this.element, {
			keydown: function( event ) {
				/*jshint maxcomplexity:15*/
				if ( this.element.prop( "readOnly" ) ) {
					suppressKeyPress = true;
					suppressInput = true;
					suppressKeyPressRepeat = true;
					return;
				}

				suppressKeyPress = false;
				suppressInput = false;
				suppressKeyPressRepeat = false;
				var keyCode = $.ui.keyCode;
				switch( event.keyCode ) {
				case keyCode.PAGE_UP:
					suppressKeyPress = true;
					this._move( "previousPage", event );
					break;
				case keyCode.PAGE_DOWN:
					suppressKeyPress = true;
					this._move( "nextPage", event );
					break;
				case keyCode.UP:
					suppressKeyPress = true;
					this._keyEvent( "previous", event );
					break;
				case keyCode.DOWN:
					suppressKeyPress = true;
					this._keyEvent( "next", event );
					break;
				case keyCode.ENTER:
				case keyCode.NUMPAD_ENTER:
					// when menu is open and has focus
					if ( this.menu.active ) {
						// #6055 - Opera still allows the keypress to occur
						// which causes forms to submit
						suppressKeyPress = true;
						event.preventDefault();
						this.menu.select( event );
					}
					break;
				case keyCode.TAB:
					if ( this.menu.active ) {
						this.menu.select( event );
					}
					break;
				case keyCode.ESCAPE:
					if ( this.menu.element.is( ":visible" ) ) {
						this._value( this.term );
						this.close( event );
						// Different browsers have different default behavior for escape
						// Single press can mean undo or clear
						// Double press in IE means clear the whole form
						event.preventDefault();
					}
					break;
				default:
					suppressKeyPressRepeat = true;
					// search timeout should be triggered before the input value is changed
					this._searchTimeout( event );
					break;
				}
			},
			keypress: function( event ) {
				if ( suppressKeyPress ) {
					suppressKeyPress = false;
					if ( !this.isMultiLine || this.menu.element.is( ":visible" ) ) {
						event.preventDefault();
					}
					return;
				}
				if ( suppressKeyPressRepeat ) {
					return;
				}

				// replicate some key handlers to allow them to repeat in Firefox and Opera
				var keyCode = $.ui.keyCode;
				switch( event.keyCode ) {
				case keyCode.PAGE_UP:
					this._move( "previousPage", event );
					break;
				case keyCode.PAGE_DOWN:
					this._move( "nextPage", event );
					break;
				case keyCode.UP:
					this._keyEvent( "previous", event );
					break;
				case keyCode.DOWN:
					this._keyEvent( "next", event );
					break;
				}
			},
			input: function( event ) {
				if ( suppressInput ) {
					suppressInput = false;
					event.preventDefault();
					return;
				}
				this._searchTimeout( event );
			},
			focus: function() {
				this.selectedItem = null;
				this.previous = this._value();
			},
			blur: function( event ) {
				if ( this.cancelBlur ) {
					delete this.cancelBlur;
					return;
				}

				clearTimeout( this.searching );
				this.close( event );
				this._change( event );
			}
		});

		this._initSource();
		this.menu = $( "<ul>" )
			.addClass( "ui-autocomplete ui-front" )
			.appendTo( this._appendTo() )
			.menu({
				// disable ARIA support, the live region takes care of that
				role: null
			})
			.hide()
			.data( "ui-menu" );

		this._on( this.menu.element, {
			mousedown: function( event ) {
				// prevent moving focus out of the text field
				event.preventDefault();

				// IE doesn't prevent moving focus even with event.preventDefault()
				// so we set a flag to know when we should ignore the blur event
				this.cancelBlur = true;
				this._delay(function() {
					delete this.cancelBlur;
				});

				// clicking on the scrollbar causes focus to shift to the body
				// but we can't detect a mouseup or a click immediately afterward
				// so we have to track the next mousedown and close the menu if
				// the user clicks somewhere outside of the autocomplete
				var menuElement = this.menu.element[ 0 ];
				if ( !$( event.target ).closest( ".ui-menu-item" ).length ) {
					this._delay(function() {
						var that = this;
						this.document.one( "mousedown", function( event ) {
							if ( event.target !== that.element[ 0 ] &&
									event.target !== menuElement &&
									!$.contains( menuElement, event.target ) ) {
								that.close();
							}
						});
					});
				}
			},
			menufocus: function( event, ui ) {
				// support: Firefox
				// Prevent accidental activation of menu items in Firefox (#7024 #9118)
				if ( this.isNewMenu ) {
					this.isNewMenu = false;
					if ( event.originalEvent && /^mouse/.test( event.originalEvent.type ) ) {
						this.menu.blur();

						this.document.one( "mousemove", function() {
							$( event.target ).trigger( event.originalEvent );
						});

						return;
					}
				}

				var item = ui.item.data( "ui-autocomplete-item" );
				if ( false !== this._trigger( "focus", event, { item: item } ) ) {
					// use value to match what will end up in the input, if it was a key event
					if ( event.originalEvent && /^key/.test( event.originalEvent.type ) ) {
						this._value( item.value );
					}
				} else {
					// Normally the input is populated with the item's value as the
					// menu is navigated, causing screen readers to notice a change and
					// announce the item. Since the focus event was canceled, this doesn't
					// happen, so we update the live region so that screen readers can
					// still notice the change and announce it.
					this.liveRegion.text( item.value );
				}
			},
			menuselect: function( event, ui ) {
				var item = ui.item.data( "ui-autocomplete-item" ),
					previous = this.previous;

				// only trigger when focus was lost (click on menu)
				if ( this.element[0] !== this.document[0].activeElement ) {
					this.element.focus();
					this.previous = previous;
					// #6109 - IE triggers two focus events and the second
					// is asynchronous, so we need to reset the previous
					// term synchronously and asynchronously :-(
					this._delay(function() {
						this.previous = previous;
						this.selectedItem = item;
					});
				}

				if ( false !== this._trigger( "select", event, { item: item } ) ) {
					this._value( item.value );
				}
				// reset the term after the select event
				// this allows custom select handling to work properly
				this.term = this._value();

				this.close( event );
				this.selectedItem = item;
			}
		});

		this.liveRegion = $( "<span>", {
				role: "status",
				"aria-live": "polite"
			})
			.addClass( "ui-helper-hidden-accessible" )
			.insertBefore( this.element );

		// turning off autocomplete prevents the browser from remembering the
		// value when navigating through history, so we re-enable autocomplete
		// if the page is unloaded before the widget is destroyed. #7790
		this._on( this.window, {
			beforeunload: function() {
				this.element.removeAttr( "autocomplete" );
			}
		});
	},

	_destroy: function() {
		clearTimeout( this.searching );
		this.element
			.removeClass( "ui-autocomplete-input" )
			.removeAttr( "autocomplete" );
		this.menu.element.remove();
		this.liveRegion.remove();
	},

	_setOption: function( key, value ) {
		this._super( key, value );
		if ( key === "source" ) {
			this._initSource();
		}
		if ( key === "appendTo" ) {
			this.menu.element.appendTo( this._appendTo() );
		}
		if ( key === "disabled" && value && this.xhr ) {
			this.xhr.abort();
		}
	},

	_appendTo: function() {
		var element = this.options.appendTo;

		if ( element ) {
			element = element.jquery || element.nodeType ?
				$( element ) :
				this.document.find( element ).eq( 0 );
		}

		if ( !element ) {
			element = this.element.closest( ".ui-front" );
		}

		if ( !element.length ) {
			element = this.document[0].body;
		}

		return element;
	},

	_initSource: function() {
		var array, url,
			that = this;
		if ( $.isArray(this.options.source) ) {
			array = this.options.source;
			this.source = function( request, response ) {
				response( $.ui.autocomplete.filter( array, request.term ) );
			};
		} else if ( typeof this.options.source === "string" ) {
			url = this.options.source;
			this.source = function( request, response ) {
				if ( that.xhr ) {
					that.xhr.abort();
				}
				that.xhr = $.ajax({
					url: url,
					data: request,
					dataType: "json",
					success: function( data ) {
						response( data );
					},
					error: function() {
						response( [] );
					}
				});
			};
		} else {
			this.source = this.options.source;
		}
	},

	_searchTimeout: function( event ) {
		clearTimeout( this.searching );
		this.searching = this._delay(function() {
			// only search if the value has changed
			if ( this.term !== this._value() ) {
				this.selectedItem = null;
				this.search( null, event );
			}
		}, this.options.delay );
	},

	search: function( value, event ) {
		value = value != null ? value : this._value();

		// always save the actual value, not the one passed as an argument
		this.term = this._value();

		if ( value.length < this.options.minLength ) {
			return this.close( event );
		}

		if ( this._trigger( "search", event ) === false ) {
			return;
		}

		return this._search( value );
	},

	_search: function( value ) {
		this.pending++;
		this.element.addClass( "ui-autocomplete-loading" );
		this.cancelSearch = false;

		this.source( { term: value }, this._response() );
	},

	_response: function() {
		var that = this,
			index = ++requestIndex;

		return function( content ) {
			if ( index === requestIndex ) {
				that.__response( content );
			}

			that.pending--;
			if ( !that.pending ) {
				that.element.removeClass( "ui-autocomplete-loading" );
			}
		};
	},

	__response: function( content ) {
		if ( content ) {
			content = this._normalize( content );
		}
		this._trigger( "response", null, { content: content } );
		if ( !this.options.disabled && content && content.length && !this.cancelSearch ) {
			this._suggest( content );
			this._trigger( "open" );
		} else {
			// use ._close() instead of .close() so we don't cancel future searches
			this._close();
		}
	},

	close: function( event ) {
		this.cancelSearch = true;
		this._close( event );
	},

	_close: function( event ) {
		if ( this.menu.element.is( ":visible" ) ) {
			this.menu.element.hide();
			this.menu.blur();
			this.isNewMenu = true;
			this._trigger( "close", event );
		}
	},

	_change: function( event ) {
		if ( this.previous !== this._value() ) {
			this._trigger( "change", event, { item: this.selectedItem } );
		}
	},

	_normalize: function( items ) {
		// assume all items have the right format when the first item is complete
		if ( items.length && items[0].label && items[0].value ) {
			return items;
		}
		return $.map( items, function( item ) {
			if ( typeof item === "string" ) {
				return {
					label: item,
					value: item
				};
			}
			return $.extend({
				label: item.label || item.value,
				value: item.value || item.label
			}, item );
		});
	},

	_suggest: function( items ) {
		var ul = this.menu.element.empty();
		this._renderMenu( ul, items );
		this.isNewMenu = true;
		this.menu.refresh();

		// size and position menu
		ul.show();
		this._resizeMenu();
		ul.position( $.extend({
			of: this.element
		}, this.options.position ));

		if ( this.options.autoFocus ) {
			this.menu.next();
		}
	},

	_resizeMenu: function() {
		var ul = this.menu.element;
		ul.outerWidth( Math.max(
			// Firefox wraps long text (possibly a rounding bug)
			// so we add 1px to avoid the wrapping (#7513)
			ul.width( "" ).outerWidth() + 1,
			this.element.outerWidth()
		) );
	},

	_renderMenu: function( ul, items ) {
		var that = this;
		$.each( items, function( index, item ) {
			that._renderItemData( ul, item );
		});
	},

	_renderItemData: function( ul, item ) {
		return this._renderItem( ul, item ).data( "ui-autocomplete-item", item );
	},

	_renderItem: function( ul, item ) {
		return $( "<li>" )
			.append( $( "<a>" ).text( item.label ) )
			.appendTo( ul );
	},

	_move: function( direction, event ) {
		if ( !this.menu.element.is( ":visible" ) ) {
			this.search( null, event );
			return;
		}
		if ( this.menu.isFirstItem() && /^previous/.test( direction ) ||
				this.menu.isLastItem() && /^next/.test( direction ) ) {
			this._value( this.term );
			this.menu.blur();
			return;
		}
		this.menu[ direction ]( event );
	},

	widget: function() {
		return this.menu.element;
	},

	_value: function() {
		return this.valueMethod.apply( this.element, arguments );
	},

	_keyEvent: function( keyEvent, event ) {
		if ( !this.isMultiLine || this.menu.element.is( ":visible" ) ) {
			this._move( keyEvent, event );

			// prevents moving cursor to beginning/end of the text field in some browsers
			event.preventDefault();
		}
	}
});

$.extend( $.ui.autocomplete, {
	escapeRegex: function( value ) {
		return value.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, "\\$&");
	},
	filter: function(array, term) {
		var matcher = new RegExp( $.ui.autocomplete.escapeRegex(term), "i" );
		return $.grep( array, function(value) {
			return matcher.test( value.label || value.value || value );
		});
	}
});


// live region extension, adding a `messages` option
// NOTE: This is an experimental API. We are still investigating
// a full solution for string manipulation and internationalization.
$.widget( "ui.autocomplete", $.ui.autocomplete, {
	options: {
		messages: {
			noResults: "No search results.",
			results: function( amount ) {
				return amount + ( amount > 1 ? " results are" : " result is" ) +
					" available, use up and down arrow keys to navigate.";
			}
		}
	},

	__response: function( content ) {
		var message;
		this._superApply( arguments );
		if ( this.options.disabled || this.cancelSearch ) {
			return;
		}
		if ( content && content.length ) {
			message = this.options.messages.results( content.length );
		} else {
			message = this.options.messages.noResults;
		}
		this.liveRegion.text( message );
	}
});

}( jQuery ));
(function( $, undefined ) {

$.widget( "ui.menu", {
	version: "1.10.3",
	defaultElement: "<ul>",
	delay: 300,
	options: {
		icons: {
			submenu: "ui-icon-carat-1-e"
		},
		menus: "ul",
		position: {
			my: "left top",
			at: "right top"
		},
		role: "menu",

		// callbacks
		blur: null,
		focus: null,
		select: null
	},

	_create: function() {
		this.activeMenu = this.element;
		// flag used to prevent firing of the click handler
		// as the event bubbles up through nested menus
		this.mouseHandled = false;
		this.element
			.uniqueId()
			.addClass( "ui-menu ui-widget ui-widget-content ui-corner-all" )
			.toggleClass( "ui-menu-icons", !!this.element.find( ".ui-icon" ).length )
			.attr({
				role: this.options.role,
				tabIndex: 0
			})
			// need to catch all clicks on disabled menu
			// not possible through _on
			.bind( "click" + this.eventNamespace, $.proxy(function( event ) {
				if ( this.options.disabled ) {
					event.preventDefault();
				}
			}, this ));

		if ( this.options.disabled ) {
			this.element
				.addClass( "ui-state-disabled" )
				.attr( "aria-disabled", "true" );
		}

		this._on({
			// Prevent focus from sticking to links inside menu after clicking
			// them (focus should always stay on UL during navigation).
			"mousedown .ui-menu-item > a": function( event ) {
				event.preventDefault();
			},
			"click .ui-state-disabled > a": function( event ) {
				event.preventDefault();
			},
			"click .ui-menu-item:has(a)": function( event ) {
				var target = $( event.target ).closest( ".ui-menu-item" );
				if ( !this.mouseHandled && target.not( ".ui-state-disabled" ).length ) {
					this.mouseHandled = true;

					this.select( event );
					// Open submenu on click
					if ( target.has( ".ui-menu" ).length ) {
						this.expand( event );
					} else if ( !this.element.is( ":focus" ) ) {
						// Redirect focus to the menu
						this.element.trigger( "focus", [ true ] );

						// If the active item is on the top level, let it stay active.
						// Otherwise, blur the active item since it is no longer visible.
						if ( this.active && this.active.parents( ".ui-menu" ).length === 1 ) {
							clearTimeout( this.timer );
						}
					}
				}
			},
			"mouseenter .ui-menu-item": function( event ) {
				var target = $( event.currentTarget );
				// Remove ui-state-active class from siblings of the newly focused menu item
				// to avoid a jump caused by adjacent elements both having a class with a border
				target.siblings().children( ".ui-state-active" ).removeClass( "ui-state-active" );
				this.focus( event, target );
			},
			mouseleave: "collapseAll",
			"mouseleave .ui-menu": "collapseAll",
			focus: function( event, keepActiveItem ) {
				// If there's already an active item, keep it active
				// If not, activate the first item
				var item = this.active || this.element.children( ".ui-menu-item" ).eq( 0 );

				if ( !keepActiveItem ) {
					this.focus( event, item );
				}
			},
			blur: function( event ) {
				this._delay(function() {
					if ( !$.contains( this.element[0], this.document[0].activeElement ) ) {
						this.collapseAll( event );
					}
				});
			},
			keydown: "_keydown"
		});

		this.refresh();

		// Clicks outside of a menu collapse any open menus
		this._on( this.document, {
			click: function( event ) {
				if ( !$( event.target ).closest( ".ui-menu" ).length ) {
					this.collapseAll( event );
				}

				// Reset the mouseHandled flag
				this.mouseHandled = false;
			}
		});
	},

	_destroy: function() {
		// Destroy (sub)menus
		this.element
			.removeAttr( "aria-activedescendant" )
			.find( ".ui-menu" ).addBack()
				.removeClass( "ui-menu ui-widget ui-widget-content ui-corner-all ui-menu-icons" )
				.removeAttr( "role" )
				.removeAttr( "tabIndex" )
				.removeAttr( "aria-labelledby" )
				.removeAttr( "aria-expanded" )
				.removeAttr( "aria-hidden" )
				.removeAttr( "aria-disabled" )
				.removeUniqueId()
				.show();

		// Destroy menu items
		this.element.find( ".ui-menu-item" )
			.removeClass( "ui-menu-item" )
			.removeAttr( "role" )
			.removeAttr( "aria-disabled" )
			.children( "a" )
				.removeUniqueId()
				.removeClass( "ui-corner-all ui-state-hover" )
				.removeAttr( "tabIndex" )
				.removeAttr( "role" )
				.removeAttr( "aria-haspopup" )
				.children().each( function() {
					var elem = $( this );
					if ( elem.data( "ui-menu-submenu-carat" ) ) {
						elem.remove();
					}
				});

		// Destroy menu dividers
		this.element.find( ".ui-menu-divider" ).removeClass( "ui-menu-divider ui-widget-content" );
	},

	_keydown: function( event ) {
		/*jshint maxcomplexity:20*/
		var match, prev, character, skip, regex,
			preventDefault = true;

		function escape( value ) {
			return value.replace( /[\-\[\]{}()*+?.,\\\^$|#\s]/g, "\\$&" );
		}

		switch ( event.keyCode ) {
		case $.ui.keyCode.PAGE_UP:
			this.previousPage( event );
			break;
		case $.ui.keyCode.PAGE_DOWN:
			this.nextPage( event );
			break;
		case $.ui.keyCode.HOME:
			this._move( "first", "first", event );
			break;
		case $.ui.keyCode.END:
			this._move( "last", "last", event );
			break;
		case $.ui.keyCode.UP:
			this.previous( event );
			break;
		case $.ui.keyCode.DOWN:
			this.next( event );
			break;
		case $.ui.keyCode.LEFT:
			this.collapse( event );
			break;
		case $.ui.keyCode.RIGHT:
			if ( this.active && !this.active.is( ".ui-state-disabled" ) ) {
				this.expand( event );
			}
			break;
		case $.ui.keyCode.ENTER:
		case $.ui.keyCode.SPACE:
			this._activate( event );
			break;
		case $.ui.keyCode.ESCAPE:
			this.collapse( event );
			break;
		default:
			preventDefault = false;
			prev = this.previousFilter || "";
			character = String.fromCharCode( event.keyCode );
			skip = false;

			clearTimeout( this.filterTimer );

			if ( character === prev ) {
				skip = true;
			} else {
				character = prev + character;
			}

			regex = new RegExp( "^" + escape( character ), "i" );
			match = this.activeMenu.children( ".ui-menu-item" ).filter(function() {
				return regex.test( $( this ).children( "a" ).text() );
			});
			match = skip && match.index( this.active.next() ) !== -1 ?
				this.active.nextAll( ".ui-menu-item" ) :
				match;

			// If no matches on the current filter, reset to the last character pressed
			// to move down the menu to the first item that starts with that character
			if ( !match.length ) {
				character = String.fromCharCode( event.keyCode );
				regex = new RegExp( "^" + escape( character ), "i" );
				match = this.activeMenu.children( ".ui-menu-item" ).filter(function() {
					return regex.test( $( this ).children( "a" ).text() );
				});
			}

			if ( match.length ) {
				this.focus( event, match );
				if ( match.length > 1 ) {
					this.previousFilter = character;
					this.filterTimer = this._delay(function() {
						delete this.previousFilter;
					}, 1000 );
				} else {
					delete this.previousFilter;
				}
			} else {
				delete this.previousFilter;
			}
		}

		if ( preventDefault ) {
			event.preventDefault();
		}
	},

	_activate: function( event ) {
		if ( !this.active.is( ".ui-state-disabled" ) ) {
			if ( this.active.children( "a[aria-haspopup='true']" ).length ) {
				this.expand( event );
			} else {
				this.select( event );
			}
		}
	},

	refresh: function() {
		var menus,
			icon = this.options.icons.submenu,
			submenus = this.element.find( this.options.menus );

		// Initialize nested menus
		submenus.filter( ":not(.ui-menu)" )
			.addClass( "ui-menu ui-widget ui-widget-content ui-corner-all" )
			.hide()
			.attr({
				role: this.options.role,
				"aria-hidden": "true",
				"aria-expanded": "false"
			})
			.each(function() {
				var menu = $( this ),
					item = menu.prev( "a" ),
					submenuCarat = $( "<span>" )
						.addClass( "ui-menu-icon ui-icon " + icon )
						.data( "ui-menu-submenu-carat", true );

				item
					.attr( "aria-haspopup", "true" )
					.prepend( submenuCarat );
				menu.attr( "aria-labelledby", item.attr( "id" ) );
			});

		menus = submenus.add( this.element );

		// Don't refresh list items that are already adapted
		menus.children( ":not(.ui-menu-item):has(a)" )
			.addClass( "ui-menu-item" )
			.attr( "role", "presentation" )
			.children( "a" )
				.uniqueId()
				.addClass( "ui-corner-all" )
				.attr({
					tabIndex: -1,
					role: this._itemRole()
				});

		// Initialize unlinked menu-items containing spaces and/or dashes only as dividers
		menus.children( ":not(.ui-menu-item)" ).each(function() {
			var item = $( this );
			// hyphen, em dash, en dash
			if ( !/[^\-\u2014\u2013\s]/.test( item.text() ) ) {
				item.addClass( "ui-widget-content ui-menu-divider" );
			}
		});

		// Add aria-disabled attribute to any disabled menu item
		menus.children( ".ui-state-disabled" ).attr( "aria-disabled", "true" );

		// If the active item has been removed, blur the menu
		if ( this.active && !$.contains( this.element[ 0 ], this.active[ 0 ] ) ) {
			this.blur();
		}
	},

	_itemRole: function() {
		return {
			menu: "menuitem",
			listbox: "option"
		}[ this.options.role ];
	},

	_setOption: function( key, value ) {
		if ( key === "icons" ) {
			this.element.find( ".ui-menu-icon" )
				.removeClass( this.options.icons.submenu )
				.addClass( value.submenu );
		}
		this._super( key, value );
	},

	focus: function( event, item ) {
		var nested, focused;
		this.blur( event, event && event.type === "focus" );

		this._scrollIntoView( item );

		this.active = item.first();
		focused = this.active.children( "a" ).addClass( "ui-state-focus" );
		// Only update aria-activedescendant if there's a role
		// otherwise we assume focus is managed elsewhere
		if ( this.options.role ) {
			this.element.attr( "aria-activedescendant", focused.attr( "id" ) );
		}

		// Highlight active parent menu item, if any
		this.active
			.parent()
			.closest( ".ui-menu-item" )
			.children( "a:first" )
			.addClass( "ui-state-active" );

		if ( event && event.type === "keydown" ) {
			this._close();
		} else {
			this.timer = this._delay(function() {
				this._close();
			}, this.delay );
		}

		nested = item.children( ".ui-menu" );
		if ( nested.length && ( /^mouse/.test( event.type ) ) ) {
			this._startOpening(nested);
		}
		this.activeMenu = item.parent();

		this._trigger( "focus", event, { item: item } );
	},

	_scrollIntoView: function( item ) {
		var borderTop, paddingTop, offset, scroll, elementHeight, itemHeight;
		if ( this._hasScroll() ) {
			borderTop = parseFloat( $.css( this.activeMenu[0], "borderTopWidth" ) ) || 0;
			paddingTop = parseFloat( $.css( this.activeMenu[0], "paddingTop" ) ) || 0;
			offset = item.offset().top - this.activeMenu.offset().top - borderTop - paddingTop;
			scroll = this.activeMenu.scrollTop();
			elementHeight = this.activeMenu.height();
			itemHeight = item.height();

			if ( offset < 0 ) {
				this.activeMenu.scrollTop( scroll + offset );
			} else if ( offset + itemHeight > elementHeight ) {
				this.activeMenu.scrollTop( scroll + offset - elementHeight + itemHeight );
			}
		}
	},

	blur: function( event, fromFocus ) {
		if ( !fromFocus ) {
			clearTimeout( this.timer );
		}

		if ( !this.active ) {
			return;
		}

		this.active.children( "a" ).removeClass( "ui-state-focus" );
		this.active = null;

		this._trigger( "blur", event, { item: this.active } );
	},

	_startOpening: function( submenu ) {
		clearTimeout( this.timer );

		// Don't open if already open fixes a Firefox bug that caused a .5 pixel
		// shift in the submenu position when mousing over the carat icon
		if ( submenu.attr( "aria-hidden" ) !== "true" ) {
			return;
		}

		this.timer = this._delay(function() {
			this._close();
			this._open( submenu );
		}, this.delay );
	},

	_open: function( submenu ) {
		var position = $.extend({
			of: this.active
		}, this.options.position );

		clearTimeout( this.timer );
		this.element.find( ".ui-menu" ).not( submenu.parents( ".ui-menu" ) )
			.hide()
			.attr( "aria-hidden", "true" );

		submenu
			.show()
			.removeAttr( "aria-hidden" )
			.attr( "aria-expanded", "true" )
			.position( position );
	},

	collapseAll: function( event, all ) {
		clearTimeout( this.timer );
		this.timer = this._delay(function() {
			// If we were passed an event, look for the submenu that contains the event
			var currentMenu = all ? this.element :
				$( event && event.target ).closest( this.element.find( ".ui-menu" ) );

			// If we found no valid submenu ancestor, use the main menu to close all sub menus anyway
			if ( !currentMenu.length ) {
				currentMenu = this.element;
			}

			this._close( currentMenu );

			this.blur( event );
			this.activeMenu = currentMenu;
		}, this.delay );
	},

	// With no arguments, closes the currently active menu - if nothing is active
	// it closes all menus.  If passed an argument, it will search for menus BELOW
	_close: function( startMenu ) {
		if ( !startMenu ) {
			startMenu = this.active ? this.active.parent() : this.element;
		}

		startMenu
			.find( ".ui-menu" )
				.hide()
				.attr( "aria-hidden", "true" )
				.attr( "aria-expanded", "false" )
			.end()
			.find( "a.ui-state-active" )
				.removeClass( "ui-state-active" );
	},

	collapse: function( event ) {
		var newItem = this.active &&
			this.active.parent().closest( ".ui-menu-item", this.element );
		if ( newItem && newItem.length ) {
			this._close();
			this.focus( event, newItem );
		}
	},

	expand: function( event ) {
		var newItem = this.active &&
			this.active
				.children( ".ui-menu " )
				.children( ".ui-menu-item" )
				.first();

		if ( newItem && newItem.length ) {
			this._open( newItem.parent() );

			// Delay so Firefox will not hide activedescendant change in expanding submenu from AT
			this._delay(function() {
				this.focus( event, newItem );
			});
		}
	},

	next: function( event ) {
		this._move( "next", "first", event );
	},

	previous: function( event ) {
		this._move( "prev", "last", event );
	},

	isFirstItem: function() {
		return this.active && !this.active.prevAll( ".ui-menu-item" ).length;
	},

	isLastItem: function() {
		return this.active && !this.active.nextAll( ".ui-menu-item" ).length;
	},

	_move: function( direction, filter, event ) {
		var next;
		if ( this.active ) {
			if ( direction === "first" || direction === "last" ) {
				next = this.active
					[ direction === "first" ? "prevAll" : "nextAll" ]( ".ui-menu-item" )
					.eq( -1 );
			} else {
				next = this.active
					[ direction + "All" ]( ".ui-menu-item" )
					.eq( 0 );
			}
		}
		if ( !next || !next.length || !this.active ) {
			next = this.activeMenu.children( ".ui-menu-item" )[ filter ]();
		}

		this.focus( event, next );
	},

	nextPage: function( event ) {
		var item, base, height;

		if ( !this.active ) {
			this.next( event );
			return;
		}
		if ( this.isLastItem() ) {
			return;
		}
		if ( this._hasScroll() ) {
			base = this.active.offset().top;
			height = this.element.height();
			this.active.nextAll( ".ui-menu-item" ).each(function() {
				item = $( this );
				return item.offset().top - base - height < 0;
			});

			this.focus( event, item );
		} else {
			this.focus( event, this.activeMenu.children( ".ui-menu-item" )
				[ !this.active ? "first" : "last" ]() );
		}
	},

	previousPage: function( event ) {
		var item, base, height;
		if ( !this.active ) {
			this.next( event );
			return;
		}
		if ( this.isFirstItem() ) {
			return;
		}
		if ( this._hasScroll() ) {
			base = this.active.offset().top;
			height = this.element.height();
			this.active.prevAll( ".ui-menu-item" ).each(function() {
				item = $( this );
				return item.offset().top - base + height > 0;
			});

			this.focus( event, item );
		} else {
			this.focus( event, this.activeMenu.children( ".ui-menu-item" ).first() );
		}
	},

	_hasScroll: function() {
		return this.element.outerHeight() < this.element.prop( "scrollHeight" );
	},

	select: function( event ) {
		// TODO: It should never be possible to not have an active item at this
		// point, but the tests don't trigger mouseenter before click.
		this.active = this.active || $( event.target ).closest( ".ui-menu-item" );
		var ui = { item: this.active };
		if ( !this.active.has( ".ui-menu" ).length ) {
			this.collapseAll( event, true );
		}
		this._trigger( "select", event, ui );
	}
});

}( jQuery ));
(function( $, undefined ) {

// number of pages in a slider
// (how many times can you page up/down to go through the whole range)
var numPages = 5;

$.widget( "ui.slider", $.ui.mouse, {
	version: "1.10.3",
	widgetEventPrefix: "slide",

	options: {
		animate: false,
		distance: 0,
		max: 100,
		min: 0,
		orientation: "horizontal",
		range: false,
		step: 1,
		value: 0,
		values: null,

		// callbacks
		change: null,
		slide: null,
		start: null,
		stop: null
	},

	_create: function() {
		this._keySliding = false;
		this._mouseSliding = false;
		this._animateOff = true;
		this._handleIndex = null;
		this._detectOrientation();
		this._mouseInit();

		this.element
			.addClass( "ui-slider" +
				" ui-slider-" + this.orientation +
				" ui-widget" +
				" ui-widget-content" +
				" ui-corner-all");

		this._refresh();
		this._setOption( "disabled", this.options.disabled );

		this._animateOff = false;
	},

	_refresh: function() {
		this._createRange();
		this._createHandles();
		this._setupEvents();
		this._refreshValue();
	},

	_createHandles: function() {
		var i, handleCount,
			options = this.options,
			existingHandles = this.element.find( ".ui-slider-handle" ).addClass( "ui-state-default ui-corner-all" ),
			handle = "<a class='ui-slider-handle ui-state-default ui-corner-all' href='#'></a>",
			handles = [];

		handleCount = ( options.values && options.values.length ) || 1;

		if ( existingHandles.length > handleCount ) {
			existingHandles.slice( handleCount ).remove();
			existingHandles = existingHandles.slice( 0, handleCount );
		}

		for ( i = existingHandles.length; i < handleCount; i++ ) {
			handles.push( handle );
		}

		this.handles = existingHandles.add( $( handles.join( "" ) ).appendTo( this.element ) );

		this.handle = this.handles.eq( 0 );

		this.handles.each(function( i ) {
			$( this ).data( "ui-slider-handle-index", i );
		});
	},

	_createRange: function() {
		var options = this.options,
			classes = "";

		if ( options.range ) {
			if ( options.range === true ) {
				if ( !options.values ) {
					options.values = [ this._valueMin(), this._valueMin() ];
				} else if ( options.values.length && options.values.length !== 2 ) {
					options.values = [ options.values[0], options.values[0] ];
				} else if ( $.isArray( options.values ) ) {
					options.values = options.values.slice(0);
				}
			}

			if ( !this.range || !this.range.length ) {
				this.range = $( "<div></div>" )
					.appendTo( this.element );

				classes = "ui-slider-range" +
				// note: this isn't the most fittingly semantic framework class for this element,
				// but worked best visually with a variety of themes
				" ui-widget-header ui-corner-all";
			} else {
				this.range.removeClass( "ui-slider-range-min ui-slider-range-max" )
					// Handle range switching from true to min/max
					.css({
						"left": "",
						"bottom": ""
					});
			}

			this.range.addClass( classes +
				( ( options.range === "min" || options.range === "max" ) ? " ui-slider-range-" + options.range : "" ) );
		} else {
			this.range = $([]);
		}
	},

	_setupEvents: function() {
		var elements = this.handles.add( this.range ).filter( "a" );
		this._off( elements );
		this._on( elements, this._handleEvents );
		this._hoverable( elements );
		this._focusable( elements );
	},

	_destroy: function() {
		this.handles.remove();
		this.range.remove();

		this.element
			.removeClass( "ui-slider" +
				" ui-slider-horizontal" +
				" ui-slider-vertical" +
				" ui-widget" +
				" ui-widget-content" +
				" ui-corner-all" );

		this._mouseDestroy();
	},

	_mouseCapture: function( event ) {
		var position, normValue, distance, closestHandle, index, allowed, offset, mouseOverHandle,
			that = this,
			o = this.options;

		if ( o.disabled ) {
			return false;
		}

		this.elementSize = {
			width: this.element.outerWidth(),
			height: this.element.outerHeight()
		};
		this.elementOffset = this.element.offset();

		position = { x: event.pageX, y: event.pageY };
		normValue = this._normValueFromMouse( position );
		distance = this._valueMax() - this._valueMin() + 1;
		this.handles.each(function( i ) {
			var thisDistance = Math.abs( normValue - that.values(i) );
			if (( distance > thisDistance ) ||
				( distance === thisDistance &&
					(i === that._lastChangedValue || that.values(i) === o.min ))) {
				distance = thisDistance;
				closestHandle = $( this );
				index = i;
			}
		});

		allowed = this._start( event, index );
		if ( allowed === false ) {
			return false;
		}
		this._mouseSliding = true;

		this._handleIndex = index;

		closestHandle
			.addClass( "ui-state-active" )
			.focus();

		offset = closestHandle.offset();
		mouseOverHandle = !$( event.target ).parents().addBack().is( ".ui-slider-handle" );
		this._clickOffset = mouseOverHandle ? { left: 0, top: 0 } : {
			left: event.pageX - offset.left - ( closestHandle.width() / 2 ),
			top: event.pageY - offset.top -
				( closestHandle.height() / 2 ) -
				( parseInt( closestHandle.css("borderTopWidth"), 10 ) || 0 ) -
				( parseInt( closestHandle.css("borderBottomWidth"), 10 ) || 0) +
				( parseInt( closestHandle.css("marginTop"), 10 ) || 0)
		};

		if ( !this.handles.hasClass( "ui-state-hover" ) ) {
			this._slide( event, index, normValue );
		}
		this._animateOff = true;
		return true;
	},

	_mouseStart: function() {
		return true;
	},

	_mouseDrag: function( event ) {
		var position = { x: event.pageX, y: event.pageY },
			normValue = this._normValueFromMouse( position );

		this._slide( event, this._handleIndex, normValue );

		return false;
	},

	_mouseStop: function( event ) {
		this.handles.removeClass( "ui-state-active" );
		this._mouseSliding = false;

		this._stop( event, this._handleIndex );
		this._change( event, this._handleIndex );

		this._handleIndex = null;
		this._clickOffset = null;
		this._animateOff = false;

		return false;
	},

	_detectOrientation: function() {
		this.orientation = ( this.options.orientation === "vertical" ) ? "vertical" : "horizontal";
	},

	_normValueFromMouse: function( position ) {
		var pixelTotal,
			pixelMouse,
			percentMouse,
			valueTotal,
			valueMouse;

		if ( this.orientation === "horizontal" ) {
			pixelTotal = this.elementSize.width;
			pixelMouse = position.x - this.elementOffset.left - ( this._clickOffset ? this._clickOffset.left : 0 );
		} else {
			pixelTotal = this.elementSize.height;
			pixelMouse = position.y - this.elementOffset.top - ( this._clickOffset ? this._clickOffset.top : 0 );
		}

		percentMouse = ( pixelMouse / pixelTotal );
		if ( percentMouse > 1 ) {
			percentMouse = 1;
		}
		if ( percentMouse < 0 ) {
			percentMouse = 0;
		}
		if ( this.orientation === "vertical" ) {
			percentMouse = 1 - percentMouse;
		}

		valueTotal = this._valueMax() - this._valueMin();
		valueMouse = this._valueMin() + percentMouse * valueTotal;

		return this._trimAlignValue( valueMouse );
	},

	_start: function( event, index ) {
		var uiHash = {
			handle: this.handles[ index ],
			value: this.value()
		};
		if ( this.options.values && this.options.values.length ) {
			uiHash.value = this.values( index );
			uiHash.values = this.values();
		}
		return this._trigger( "start", event, uiHash );
	},

	_slide: function( event, index, newVal ) {
		var otherVal,
			newValues,
			allowed;

		if ( this.options.values && this.options.values.length ) {
			otherVal = this.values( index ? 0 : 1 );

			if ( ( this.options.values.length === 2 && this.options.range === true ) &&
					( ( index === 0 && newVal > otherVal) || ( index === 1 && newVal < otherVal ) )
				) {
				newVal = otherVal;
			}

			if ( newVal !== this.values( index ) ) {
				newValues = this.values();
				newValues[ index ] = newVal;
				// A slide can be canceled by returning false from the slide callback
				allowed = this._trigger( "slide", event, {
					handle: this.handles[ index ],
					value: newVal,
					values: newValues
				} );
				otherVal = this.values( index ? 0 : 1 );
				if ( allowed !== false ) {
					this.values( index, newVal, true );
				}
			}
		} else {
			if ( newVal !== this.value() ) {
				// A slide can be canceled by returning false from the slide callback
				allowed = this._trigger( "slide", event, {
					handle: this.handles[ index ],
					value: newVal
				} );
				if ( allowed !== false ) {
					this.value( newVal );
				}
			}
		}
	},

	_stop: function( event, index ) {
		var uiHash = {
			handle: this.handles[ index ],
			value: this.value()
		};
		if ( this.options.values && this.options.values.length ) {
			uiHash.value = this.values( index );
			uiHash.values = this.values();
		}

		this._trigger( "stop", event, uiHash );
	},

	_change: function( event, index ) {
		if ( !this._keySliding && !this._mouseSliding ) {
			var uiHash = {
				handle: this.handles[ index ],
				value: this.value()
			};
			if ( this.options.values && this.options.values.length ) {
				uiHash.value = this.values( index );
				uiHash.values = this.values();
			}

			//store the last changed value index for reference when handles overlap
			this._lastChangedValue = index;

			this._trigger( "change", event, uiHash );
		}
	},

	value: function( newValue ) {
		if ( arguments.length ) {
			this.options.value = this._trimAlignValue( newValue );
			this._refreshValue();
			this._change( null, 0 );
			return;
		}

		return this._value();
	},

	values: function( index, newValue ) {
		var vals,
			newValues,
			i;

		if ( arguments.length > 1 ) {
			this.options.values[ index ] = this._trimAlignValue( newValue );
			this._refreshValue();
			this._change( null, index );
			return;
		}

		if ( arguments.length ) {
			if ( $.isArray( arguments[ 0 ] ) ) {
				vals = this.options.values;
				newValues = arguments[ 0 ];
				for ( i = 0; i < vals.length; i += 1 ) {
					vals[ i ] = this._trimAlignValue( newValues[ i ] );
					this._change( null, i );
				}
				this._refreshValue();
			} else {
				if ( this.options.values && this.options.values.length ) {
					return this._values( index );
				} else {
					return this.value();
				}
			}
		} else {
			return this._values();
		}
	},

	_setOption: function( key, value ) {
		var i,
			valsLength = 0;

		if ( key === "range" && this.options.range === true ) {
			if ( value === "min" ) {
				this.options.value = this._values( 0 );
				this.options.values = null;
			} else if ( value === "max" ) {
				this.options.value = this._values( this.options.values.length-1 );
				this.options.values = null;
			}
		}

		if ( $.isArray( this.options.values ) ) {
			valsLength = this.options.values.length;
		}

		$.Widget.prototype._setOption.apply( this, arguments );

		switch ( key ) {
			case "orientation":
				this._detectOrientation();
				this.element
					.removeClass( "ui-slider-horizontal ui-slider-vertical" )
					.addClass( "ui-slider-" + this.orientation );
				this._refreshValue();
				break;
			case "value":
				this._animateOff = true;
				this._refreshValue();
				this._change( null, 0 );
				this._animateOff = false;
				break;
			case "values":
				this._animateOff = true;
				this._refreshValue();
				for ( i = 0; i < valsLength; i += 1 ) {
					this._change( null, i );
				}
				this._animateOff = false;
				break;
			case "min":
			case "max":
				this._animateOff = true;
				this._refreshValue();
				this._animateOff = false;
				break;
			case "range":
				this._animateOff = true;
				this._refresh();
				this._animateOff = false;
				break;
		}
	},

	//internal value getter
	// _value() returns value trimmed by min and max, aligned by step
	_value: function() {
		var val = this.options.value;
		val = this._trimAlignValue( val );

		return val;
	},

	//internal values getter
	// _values() returns array of values trimmed by min and max, aligned by step
	// _values( index ) returns single value trimmed by min and max, aligned by step
	_values: function( index ) {
		var val,
			vals,
			i;

		if ( arguments.length ) {
			val = this.options.values[ index ];
			val = this._trimAlignValue( val );

			return val;
		} else if ( this.options.values && this.options.values.length ) {
			// .slice() creates a copy of the array
			// this copy gets trimmed by min and max and then returned
			vals = this.options.values.slice();
			for ( i = 0; i < vals.length; i+= 1) {
				vals[ i ] = this._trimAlignValue( vals[ i ] );
			}

			return vals;
		} else {
			return [];
		}
	},

	// returns the step-aligned value that val is closest to, between (inclusive) min and max
	_trimAlignValue: function( val ) {
		if ( val <= this._valueMin() ) {
			return this._valueMin();
		}
		if ( val >= this._valueMax() ) {
			return this._valueMax();
		}
		var step = ( this.options.step > 0 ) ? this.options.step : 1,
			valModStep = (val - this._valueMin()) % step,
			alignValue = val - valModStep;

		if ( Math.abs(valModStep) * 2 >= step ) {
			alignValue += ( valModStep > 0 ) ? step : ( -step );
		}

		// Since JavaScript has problems with large floats, round
		// the final value to 5 digits after the decimal point (see #4124)
		return parseFloat( alignValue.toFixed(5) );
	},

	_valueMin: function() {
		return this.options.min;
	},

	_valueMax: function() {
		return this.options.max;
	},

	_refreshValue: function() {
		var lastValPercent, valPercent, value, valueMin, valueMax,
			oRange = this.options.range,
			o = this.options,
			that = this,
			animate = ( !this._animateOff ) ? o.animate : false,
			_set = {};

		if ( this.options.values && this.options.values.length ) {
			this.handles.each(function( i ) {
				valPercent = ( that.values(i) - that._valueMin() ) / ( that._valueMax() - that._valueMin() ) * 100;
				_set[ that.orientation === "horizontal" ? "left" : "bottom" ] = valPercent + "%";
				$( this ).stop( 1, 1 )[ animate ? "animate" : "css" ]( _set, o.animate );
				if ( that.options.range === true ) {
					if ( that.orientation === "horizontal" ) {
						if ( i === 0 ) {
							that.range.stop( 1, 1 )[ animate ? "animate" : "css" ]( { left: valPercent + "%" }, o.animate );
						}
						if ( i === 1 ) {
							that.range[ animate ? "animate" : "css" ]( { width: ( valPercent - lastValPercent ) + "%" }, { queue: false, duration: o.animate } );
						}
					} else {
						if ( i === 0 ) {
							that.range.stop( 1, 1 )[ animate ? "animate" : "css" ]( { bottom: ( valPercent ) + "%" }, o.animate );
						}
						if ( i === 1 ) {
							that.range[ animate ? "animate" : "css" ]( { height: ( valPercent - lastValPercent ) + "%" }, { queue: false, duration: o.animate } );
						}
					}
				}
				lastValPercent = valPercent;
			});
		} else {
			value = this.value();
			valueMin = this._valueMin();
			valueMax = this._valueMax();
			valPercent = ( valueMax !== valueMin ) ?
					( value - valueMin ) / ( valueMax - valueMin ) * 100 :
					0;
			_set[ this.orientation === "horizontal" ? "left" : "bottom" ] = valPercent + "%";
			this.handle.stop( 1, 1 )[ animate ? "animate" : "css" ]( _set, o.animate );

			if ( oRange === "min" && this.orientation === "horizontal" ) {
				this.range.stop( 1, 1 )[ animate ? "animate" : "css" ]( { width: valPercent + "%" }, o.animate );
			}
			if ( oRange === "max" && this.orientation === "horizontal" ) {
				this.range[ animate ? "animate" : "css" ]( { width: ( 100 - valPercent ) + "%" }, { queue: false, duration: o.animate } );
			}
			if ( oRange === "min" && this.orientation === "vertical" ) {
				this.range.stop( 1, 1 )[ animate ? "animate" : "css" ]( { height: valPercent + "%" }, o.animate );
			}
			if ( oRange === "max" && this.orientation === "vertical" ) {
				this.range[ animate ? "animate" : "css" ]( { height: ( 100 - valPercent ) + "%" }, { queue: false, duration: o.animate } );
			}
		}
	},

	_handleEvents: {
		keydown: function( event ) {
			/*jshint maxcomplexity:25*/
			var allowed, curVal, newVal, step,
				index = $( event.target ).data( "ui-slider-handle-index" );

			switch ( event.keyCode ) {
				case $.ui.keyCode.HOME:
				case $.ui.keyCode.END:
				case $.ui.keyCode.PAGE_UP:
				case $.ui.keyCode.PAGE_DOWN:
				case $.ui.keyCode.UP:
				case $.ui.keyCode.RIGHT:
				case $.ui.keyCode.DOWN:
				case $.ui.keyCode.LEFT:
					event.preventDefault();
					if ( !this._keySliding ) {
						this._keySliding = true;
						$( event.target ).addClass( "ui-state-active" );
						allowed = this._start( event, index );
						if ( allowed === false ) {
							return;
						}
					}
					break;
			}

			step = this.options.step;
			if ( this.options.values && this.options.values.length ) {
				curVal = newVal = this.values( index );
			} else {
				curVal = newVal = this.value();
			}

			switch ( event.keyCode ) {
				case $.ui.keyCode.HOME:
					newVal = this._valueMin();
					break;
				case $.ui.keyCode.END:
					newVal = this._valueMax();
					break;
				case $.ui.keyCode.PAGE_UP:
					newVal = this._trimAlignValue( curVal + ( (this._valueMax() - this._valueMin()) / numPages ) );
					break;
				case $.ui.keyCode.PAGE_DOWN:
					newVal = this._trimAlignValue( curVal - ( (this._valueMax() - this._valueMin()) / numPages ) );
					break;
				case $.ui.keyCode.UP:
				case $.ui.keyCode.RIGHT:
					if ( curVal === this._valueMax() ) {
						return;
					}
					newVal = this._trimAlignValue( curVal + step );
					break;
				case $.ui.keyCode.DOWN:
				case $.ui.keyCode.LEFT:
					if ( curVal === this._valueMin() ) {
						return;
					}
					newVal = this._trimAlignValue( curVal - step );
					break;
			}

			this._slide( event, index, newVal );
		},
		click: function( event ) {
			event.preventDefault();
		},
		keyup: function( event ) {
			var index = $( event.target ).data( "ui-slider-handle-index" );

			if ( this._keySliding ) {
				this._keySliding = false;
				this._stop( event, index );
				this._change( event, index );
				$( event.target ).removeClass( "ui-state-active" );
			}
		}
	}

});

}(jQuery));

(function($) {

  $.rimages = {
    $els: $('[data-rimage]'),
    breakpoints: {},

    init: function() {
      $.rimages.$body = $('body');
      $.rimages.scrollbarWidth = $.rimages.getScrollbarWidth();

      if (!$.rimages.$els[0]) {
        return;
      }
      $.rimages.pixelRatio = window.devicePixelRatio;
      $.rimages.$els.each(function() {
        var elData = $(this).data();
        for (var key in elData) {
          var isSrc = /^src/.test(key);
          var is2x = /at2x$/.test(key);
          var baseKey = key.replace('at2x', '');
          var has2x = $(this).data(key + 'at2x');
          var width = /\d+/.exec(baseKey);
          width = width ? parseInt(width[0]) : null;
          var shouldAdd = false;
          var add2x = false;
          var isNotApplicable = !isSrc || ($.rimages.pixelRatio !== 2 && is2x) || ($.rimages.pixelRatio === 2 && !is2x && has2x);

          if (!isNotApplicable) {
            $.rimages.addToBreakpoint(baseKey, width, elData[key], this);
          }
        }
      });

      $(window).on('resize', $.rimages.setPerWindowWidth);
      $.rimages.setPerWindowWidth();
    },

    getScrollbarWidth: function() {
      $.rimages.$body = $('body');
      var overflow = $.rimages.$body.css('overflow');
      $.rimages.$body.css({
        'overflow': 'hidden'
      });
      var widthWithoutScrollbar = $.rimages.$body.outerWidth();
      $.rimages.$body.css({
        'overflow': 'scroll',
        'height': '10000px'
      });
      var widthWithScrollbar = $.rimages.$body.outerWidth();
      $.rimages.$body.removeAttr('style');
      return widthWithoutScrollbar - widthWithScrollbar;
    },

    addToBreakpoint: function(breakpointId, width, src, el) {
      if (!$.rimages.breakpoints[breakpointId]) {
        $.rimages.breakpoints[breakpointId] = {
          max: /^srcmax/.test(breakpointId),
          width: width,
          els: []
        };
      }
      $.rimages.breakpoints[breakpointId].els.push({$el: $(el), src: src});
    },

    set: function(breakpointId) {
      if (typeof($.rimages.breakpoints[breakpointId]) !== "undefined") return;

      var images = $.rimages.breakpoints[breakpointId]['els'];
      for (var i = 0; i < images.length; i++) {
        var image = images[i];
        var $image = images[i].$el;
        if ($image.data('currentBreakpointId') !== breakpointId) {
          $image.attr('src', image['src']);
        }
        $image.data('currentBreakpointId', breakpointId);
      }
    },

    setPerWindowWidth: function() {
      var windowWidth = $(window).width();
      var windowHeight = $(window).height();
      var bodyHeight = $.rimages.$body.height();
      var breakpointsToApply = [];
      var maxBreakpointToApply;
      var minBreakpointToApply;
      var maxBreakpointWidth = Infinity;
      var minBreakpointWidth = -Infinity;

      if (bodyHeight > windowHeight) {
        windowWidth += $.rimages.scrollbarWidth;
      }

      for (var breakpointId in $.rimages.breakpoints) {
        var breakpoint = $.rimages.breakpoints[breakpointId];

        // check max
        if (breakpoint['max'] && breakpoint['width'] >= windowWidth && breakpoint['width'] < maxBreakpointWidth) {
          maxBreakpointToApply = breakpointId;
          maxBreakpointWidth = breakpoint['width'];
        }

        // check min
        if (!breakpoint['max'] && breakpoint['width'] >= windowWidth && breakpoint['width'] < minBreakpointWidth) {
          minBreakpointToApply = breakpointId;
          minBreakpointWidth = breakpoint['width'];
        }
      }

      if (!maxBreakpointToApply && !minBreakpointToApply) {
        $.rimages.set('src');
      }

      if (maxBreakpointToApply) {
        $.rimages.set(maxBreakpointToApply);
      }

      if (minBreakpointToApply) {
        $.rimages.set(minBreakpointToApply);
      }
    }
  };

  $.rimages.init();

})(jQuery);
(function($) {
    $.kladr = {};
    
    // Service URL
//    $.kladr.url = 'http://kladr-api.ru/api.php';
    $.kladr.url = 'http://kladr.enter.ru/api.php';

    // Enum KLADR object types
    $.kladr.type = {
        region: 'region',
        district: 'district',
        city: 'city',
        street: 'street',
        building: 'building'
    };
    
    // Send query to service
    $.kladr.api = function(query, callback) {
        var params = {};
        
        if( query.token ) params.token = query.token;
        if( query.key ) params.key = query.key;
        if( query.type ) params.contentType = query.type;
        if( query.name ) params.query = query.name;
        
        if( query.parentType && query.parentId ){
            params[query.parentType+'Id'] = query.parentId;
        }
        
        if( query.withParents ) params.withParent = 1;
        params.limit = query.limit ? query.limit : 2000;
        
        var completed = false;
        
        $.getJSON($.kladr.url + "?callback=?",
            params,
            function(data) {
                if(completed) return;
                completed = true;                
                callback && callback( data.result );
            }
        );
            
        setTimeout(function() {
            if(completed) return;
            completed = true;   
            console.error('Request error');
            callback && callback( [] );
        }, 5000);
    };
    
    // Check existence object
    $.kladr.check = function(query, callback) {
        query.withParents = false;
        query.limit = 1;
        
        $.kladr.api(query, function(objs) {
            if(objs && objs.length){
                callback && callback(objs[0]); 
            } else {
                callback && callback(false);
            }
        });
    };
})(jQuery);

(function($, undefined) {
    $.fn.kladr = function(param1, param2) {
        
        var result = undefined;        
        this.each(function() {
            var res = kladr($(this), param1, param2);
            if(result == undefined) result = res;
        });
        
        return result;
        
        function kladr(input, param1, param2) {
            var ac = null;        
            var spinner = null;

            var options = null;
            var defaultOptions = {
                token: null,
                key: null,
                type: null,
                parentType: null,
                parentId: null,
                limit: 10,
                withParents: false,
                verify: false,
                showSpinner: true,
                arrowSelect: true,
                current: null,

                open: null,
                close: null,
                send: null,
                received: null,
                select: null,
                check: null,

                source: function(query, callback) {
                    var params = {
                        token: options.token,
                        key: options.token,
                        type: options.type,
                        name: query,
                        parentType: options.parentType,
                        parentId: options.parentId,
                        withParents: options.withParents,
                        limit: options.limit
                    };

                    $.kladr.api(params, callback);
                },

                labelFormat: function(obj, query) {
                    var label = '';

                    var name = obj.name.toLowerCase();
                    query = query.toLowerCase();

                    var start = name.indexOf(query);
                    start = start > 0 ? start : 0;

                    if(obj.typeShort){
                        label += obj.typeShort + '. ';
                    }

                    if(query.length < obj.name.length){
                        label += obj.name.substr(0, start);
                        label += '<strong>' + obj.name.substr(start, query.length) + '</strong>';
                        label += obj.name.substr(start+query.length, obj.name.length-query.length-start);
                    } else {
                        label += '<strong>' + obj.name + '</strong>';
                    }

                    return label;
                },

                valueFormat: function(obj, query) {
                    return obj.name;
                }
            };

            var keys = {
                up:    38,
                down:  40,
                esc:   27,
                enter: 13
            };

            var spinnerInterval = null;
            
            return init(param1, param2, function() {
                var isActive = false;

                create(); 
                position();

                input.keyup(open);
                input.keydown(keyselect);
                input.change(function(){
                    if(!isActive) change();
                });
                input.blur(function(){
                    if(!isActive) close();
                });

                ac.on('click', 'li, a', mouseselect);
                ac.on('mouseenter', 'li', function(){ 
                    var $this = $(this);
                    
                    ac.find('li.active').removeClass('active');
                    $this.addClass('active');
                    
                    var obj = $this.find('a').data('kladr-object');
                    trigger('preselect', obj);
                    
                    isActive = true;
                });
                ac.on('mouseleave', 'li', function(){
                    $(this).removeClass('active'); 
                    isActive = false;
                });

                $(window).resize(position);
            });

            function init( param1, param2, callback ) {
                options = input.data('kladr-options');

                if(param2 !== undefined){
                    options[param1] = param2;
                    input.data('kladr-options', options);
                    return input;
                }

                if($.type(param1) === 'string'){
                    if(!options) return null;
                    return options[param1];
                }

                if(options){
                    return input;
                }

                options = defaultOptions;
                if($.type(param1) === 'object'){
                    for(var i in param1){
                        options[i] = param1[i];
                    }
                }

                input.data('kladr-options', options);
                callback && callback();
                return input;
            };

            function create() {
                var container = $(document.getElementById('kladr_autocomplete'));
                var inputName = input.attr('name');

                if(!container.length){
                    container = $('<div id="kladr_autocomplete"></div>').appendTo('body');
                }

                input.attr('autocomplete', 'off');

                ac = $('<ul class="kladr_autocomplete_'+inputName+'" style="display: none;"></ul>');
                ac.appendTo(container); 

                spinner = $('<div class="spinner kladr_autocomplete_'+inputName+'_spinner" class="spinner" style="display: none;"></div>');
                spinner.appendTo(container);
            };
            
            function render(objs, query) {        
                ac.empty();  
                for(var i in objs){
                    var obj = objs[i];                
                    var value = options.valueFormat(obj, query);
                    var label = options.labelFormat(obj, query);

                    var a = $('<a data-val="'+value+'">'+label+'</a>');
                    a.data('kladr-object', obj);

                    var li = $('<li></li>').append(a);                
                    li.appendTo(ac);
                }
            };

            function position() {
                var inputOffset = input.offset();
                var inputWidth = input.outerWidth();
                var inputHeight = input.outerHeight();

                ac.css({
                   top:  inputOffset.top + inputHeight + 'px',
                   left: inputOffset.left
                });

                var differ = ac.outerWidth() - ac.width();
                ac.width(inputWidth - differ);

                var spinnerWidth = spinner.width();
                var spinnerHeight = spinner.height();

                spinner.css({
                    top:  inputOffset.top + (inputHeight - spinnerHeight)/2 - 1,
                    left: inputOffset.left + inputWidth - spinnerWidth - 2,
                });
            };

            function open(event) {
                // return on keyup control keys
                if((event.which > 8) && (event.which < 46)) return;

                if(!validate()) return;

                var query = key(input.val());
                if(!$.trim(query)){
                    close();
                    return;
                }

                spinnerShow();
                trigger('send');

                options.source(query, function(objs) {
                    spinnerHide();
                    trigger('received');

                    if(!input.is(':focus')){
                        close();
                        return;
                    }

                    if(!$.trim(input.val()) || !objs.length){
                        close();
                        return;
                    } 

                    render(objs, query);
                    position();  
                    ac.slideDown(50);
                    trigger('open');
                });
            };

            function close() {
                select();            
                ac.hide();
                trigger('close');
            };
            
            function validate() {
                switch(options.type){
                    case $.kladr.type.region:
                    case $.kladr.type.district:
                    case $.kladr.type.city:
                        if(options.parentType && !options.parentId)
                        {
                            console.error('parentType is defined and parentId in not');
                            return false;
                        }
                        break;
                    case $.kladr.type.street:
                        if(options.parentType != $.kladr.type.city){
                            console.error('For street parentType must equal "city"');
                            return false;
                        }
                        if(!options.parentId){
                            console.error('For street parentId must defined');
                            return false;
                        }
                        break;
                    case $.kladr.type.building:
                        if(options.parentType != $.kladr.type.street){
                            console.error('For building parentType must equal "street"');
                            return false;
                        }
                        if(!options.parentId){
                            console.error('For building parentId must defined');
                            return false;
                        }
                        break;
                    default:
                        console.error('type must defined and equal "region", "district", "city", "street" or "building"');
                        return false;
                }

                if(options.limit < 1){
                    console.error('limit must greater than 0');
                    return false;
                }

                return true;
            };
            
            function select() {
                var a = ac.find('.active a');
                if(!a.length) return;

                input.val(a.attr('data-val'));
                options.current = a.data('kladr-object');
                input.data('kladr-options', options);
                trigger('select', options.current);
            }; 
            
            function keyselect(event) {
                var active = ac.find('li.active');  
                switch(event.which){
                    case keys.up:
                        if(active.length) {
                            active.removeClass('active');
                            active = active.prev();
                        } else {
                            active = ac.find('li').last();
                        }
                        active.addClass('active');
                        
                        var obj = active.find('a').data('kladr-object');
                        trigger('preselect', obj);
                        
                        if(options.arrowSelect) select();
                        break;
                    case keys.down:                    
                        if(active.length) {
                            active.removeClass('active');
                            active = active.next();
                        } else {
                            active = ac.find('li').first();
                        }
                        active.addClass('active');
                        
                        var obj = active.find('a').data('kladr-object');
                        trigger('preselect', obj);
                        
                        if(options.arrowSelect) select();
                        break;
                    case keys.esc:
                        active.removeClass('active');
                        close();
                        break;
                    case keys.enter:
                        if(!options.arrowSelect) select();
                        active.removeClass('active');
                        close();
                        return false;
                }
            };
            
            function mouseselect() {
                close();
                input.focus();
                return false;
            };
            
            function change() {
                if(!options.verify) return;

                if(!validate()) return;

                var query = key(input.val());
                if(!$.trim(query)) return;

                spinnerShow();
                trigger('send');

                options.source(query, function(objs) {
                    spinnerHide();
                    trigger('received');

                    var obj = null;                
                    for(var i=0; i<objs.length; i++){
                        var queryLowerCase = query.toLowerCase();
                        var nameLowerCase = objs[i].name.toLowerCase();
                        if(queryLowerCase == nameLowerCase){
                            obj = objs[i];
                            break;
                        }
                    }

                    if(obj) input.val(options.valueFormat(obj, query));

                    options.current = obj;
                    input.data('kladr-options', options);
                    trigger('check', options.current);
                });
            };

            function key(val) {
                var en = "1234567890qazwsxedcrfvtgbyhnujmik,ol.p;[']- " +
                         "QAZWSXEDCRFVTGBYHNUJMIK<OL>P:{\"} ";

                var ru = "1234567890йфяцычувскамепинртгоьшлбщдюзжхэъ- " +
                         "ЙФЯЦЫЧУВСКАМЕПИНРТГОЬШЛБЩДЮЗЖХЭЪ ";

                var strNew = '';
                var ch;
                var index;
                for( var i=0; i<val.length; i++ ){
                    ch = val[i];                    
                    index = en.indexOf(ch);

                    if(index > -1){
                        strNew += ru[index];
                        continue;
                    }

                    strNew += ch;
                }

                return strNew;
            };

            function trigger(event, obj) {
                if(!event) return;
                input.trigger('kladr_'+event, obj);
                if(options[event]) options[event].call(input.get(0), obj);
            };

            function spinnerStart() {
                if(spinnerInterval) return;

                var top = -0.2;
                spinnerInterval = setInterval(function() {
                    if(!spinner.is(':visible')){
                        clearInterval(spinnerInterval);
                        spinnerInterval = null;
                        return;
                    }

                    spinner.css('background-position', '0% '+top+'%');

                    top += 5.555556;
                    if(top > 95) top = -0.2;
                }, 30);
            };

            function spinnerShow() {
                if(options.showSpinner) {
                    spinner.show();
                    spinnerStart();
                }
            };

            function spinnerHide() {
                spinner.hide();
            };
        };
    };
})(jQuery);
