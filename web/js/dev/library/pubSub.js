//Copyright (c) 2010 Morgan Roderick http://roderick.dk
var PubSub = {};
(function(p){
	"use strict";
	p.version = "1.0.1";
	var messages = {};
	var lastUid = -1;
	var publish = function( message, data, sync ){
		if ( !messages.hasOwnProperty( message ) ){
			return false;
		}
		
		var deliverMessage = function(){
			var subscribers = messages[message];
			var throwException = function(e){
				return function(){
					throw e;
				};
			}; 
			for ( var i = 0, j = subscribers.length; i < j; i++ ){
				try {
					subscribers[i].func( message, data );
				} catch( e ){
					setTimeout( throwException(e), 0);
				}
			}
		};
		
		if ( sync === true ){
			deliverMessage();
		} else {
			setTimeout( deliverMessage, 0 );
		}
		return true;
	};
	p.publish = function( message, data ){
		return publish( message, data, false );
	};    
	p.publishSync = function( message, data ){
		return publish( message, data, true );
	};
	p.subscribe = function( message, func ){
		if ( !messages.hasOwnProperty( message ) ){
			messages[message] = [];
		}
		var token = (++lastUid).toString();
		messages[message].push( { token : token, func : func } );
		return token;
	};
	p.unsubscribe = function( token ){
		for ( var m in messages ){
			if ( messages.hasOwnProperty( m ) ){
				for ( var i = 0, j = messages[m].length; i < j; i++ ){
					if ( messages[m][i].token === token ){
						messages[m].splice( i, 1 );
						return token;
					}
				}
			}
		}
		return false;
	};
}(PubSub));