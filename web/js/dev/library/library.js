function brwsr () {
	var userag      = navigator.userAgent.toLowerCase();
	this.isAndroid  = userag.indexOf("android") > -1;
	this.isOSX      = ( userag.indexOf('ipad') > -1 ||  userag.indexOf('iphone') > -1 );
	this.isOSX4     = this.isOSX && userag.indexOf('os 5') === -1;
	this.isOpera    = userag.indexOf("opera") > -1;
	
	this.isTouch    = this.isOSX || this.isAndroid;
}

function mediaLib( jn ) {
	if ( ! jn.length ) {
		return;
	}
	var self = this;
	var popup = jn;
	var gii = null;
	var running360 = false;
	var vis = false;
	
	this.show = function( ntype, url ) {
		if (! vis ) {
			var currentfunction = function(){};
			switch ( ntype ) {
				case 'image':
					currentfunction = self.openEnormous;
					break;
				case '360':
					currentfunction = self.open360;
					break;
			}
			
			$(popup).lightbox_me({
				centered: true, 
				onLoad: function() {
						currentfunction( url );
					},
				onClose: function() {
						self.close();
						vis = false;
					},
				reallyBig: true	
			});
			vis = true;
		}
		else { // toggle
			self.close();
			switch ( ntype ) {
				case 'image':
					$('<img>').attr('src', url ).attr('id','gii').appendTo($('.photobox', popup));
					gii = new gigaimage( $('#gii'), 2,  $('.scale', popup));
					gii.addZoom();
					break;
				case '360':
					if( ! running360 ){					
						if( typeof(lkmv.start)!=='undefined' ) {
							lkmv.start();
						}
						running360 = true;
					}
					else{
						if( typeof(lkmv.show)!=='undefined' ) {
							lkmv.show();
						}
					}
					break;
			}
		}
		
		return false;
	};
	
	this.close = function() {
		if ( gii ) {
			gii.destroy();
			gii = null;		
			$('#gii').remove();
		}
		if ( running360 && lkmv ) {	
			if( typeof(lkmv.hide)!=='undefined' ) {
				lkmv.hide();
			}
		}
	};
	
	this.openEnormous = function( url ) {				
		$('<img>').attr('src', url ).attr('id','gii').appendTo($('.photobox', popup));
		gii = new gigaimage( $('#gii'), 2,  $('.scale', popup));
		gii.addZoom();
	};
	
	this.open360 = function() {	
		if( ! running360 ){					
			if( typeof(lkmv.start)!=='undefined' ) {
				lkmv.start();
			}
			running360 = true;
		} else
			if( typeof(lkmv.show)!=='undefined' ) {
				lkmv.show();
			}
	};
	
} // mediaLib object

/* Credit Brokers */
var DirectCredit = {

	basketPull : [],

	output : null,
	input  : null,

	init : function( input, output ) {
		if( !input || !output ) {
			return 'incorrect input data';
		}
		this.input  = input;
		this.output = output;
		for( var i=0, l=input.length; i < l; i++ ) {
			var tmp = {
				id : input[i].id,
				price : input[i].price,
				count : input[i].quantity,
				type : input[i].type
			};
			
			this.basketPull.push( tmp );
		}
		this.sendCredit();
	},

	change : function( message, data ) {
		var self = DirectCredit;
		if( data.q > 0 ) {
			var item = self.findProduct( self.basketPull, data.id );
			if( item < 0 ) {
				PubSub.publish( 'bankAnswered', null ); // hack
				return;
			}
			item.count = data.q;
		} else {
			var key = self.findProductKey( self.basketPull, data.id );
			if( key < 0 ) {
				PubSub.publish( 'bankAnswered', null ); // hack
				return;
			}
			self.basketPull.splice( key, 1 );
		}
		self.sendCredit();
	},

	findProduct : function( array, id) {
		for( var key=0, lk=array.length; key < lk; key++ ) {
			if( array[key].id == id ) {
				return array[key];
			}
		}
		return -1;
	},

	findProductKey : function( array, id) {
		for( var key=0, lk=array.length; key < lk; key++ ) {
			if( array[key].id == id ) {
				return key;
			}
		}
		return -1;
	},
	
	sendCredit : function(  ) {
		var self = this;
		dc_getCreditForTheProduct(
			'4427',
			'none',
			'getPayment', 
			{ products : self.basketPull },
			function(result){                       
				//var creditPrice = 0
				// for( var i=0, l=self.basketPull.length; i < l; i++ ) {
				//  var item = self.findProduct( self.basketPull, result.products[i].id )
				//  if( item ) {
				//      var itemPrice = item.price
				//      creditPrice += result.products[i].initial_instalment * itemPrice/100 * item.count
				//  }
					
				// }               
				self.output.text( printPrice( Math.ceil( result.payment ) ) );
				PubSub.publish( 'bankAnswered', null );
			}
		);
	}
}; // DirectCredit singleton


/* Date object upgrade */
if ( !Date.prototype.toISOString ) {
	
	( function() {
	
		function pad(number) {
			var r = String(number);
			if ( r.length === 1 ) {
				r = '0' + r;
			}
			return r;
		}
 
		Date.prototype.toISOString = function() {
			return this.getUTCFullYear() +
				'-' + pad( this.getUTCMonth() + 1 ) +
				'-' + pad( this.getUTCDate() ) +
				'T' + pad( this.getUTCHours() ) +
				':' + pad( this.getUTCMinutes() ) +
				':' + pad( this.getUTCSeconds() ) +
				'.' + String( (this.getUTCMilliseconds()/1000).toFixed(3) ).slice( 2, 5 ) +
				'Z';
		};
  
	}() );
}

function parseISO8601(dateStringInRange) {
	var isoExp = /^\s*(\d{4})-(\d\d)-(\d\d)\s*$/,
		date = new Date(NaN), month,
		parts = isoExp.exec(dateStringInRange);

	if (parts) {
		month = +parts[2];
		date.setFullYear(parts[1], month - 1, parts[3]);
		if (month != date.getMonth() + 1) {
			date.setTime(NaN);
		}
	}
	return date.getTime();
};
