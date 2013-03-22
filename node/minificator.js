var fs = require('fs'),
	compressor = require('node-minify'),
	when = require('when'),
	less = require('./node_modules/less')

var POINTS = {
	'jsdir': '../web/js/',
	'cssdir': '../web/css/',
	'js': '../web/js/combine.js',
	'less': '../web/css/global.less',
	'css': '../web/css/global.css',
	'mLess':'../web/css/mobile.less',
	'mCss':'../web/css/mobile.css'
}	

var red   = '\033[31m'
var config = {}

var typeMode = 'all',
	watchMode = 'off'

process.argv.forEach( function(val, index, array) {
	switch( val ) {
		case 'js':
			typeMode = val
			break
		case 'css':
			typeMode = val
			break	
		case 'watch':
			watchMode = 'on'
			break	
	}
})

function parseLESS() {
	var parser = new(less.Parser)({
	    paths: [ POINTS.cssdir ] // Specify search paths for @import directives
	    // filename: 'style.less' // Specify a filename, for better error messages
	})
	fs.readFile( POINTS.less , 'utf8', function(e, data ) { 
		parser.parse( data, function (err, tree) {
			console.log( '< CSS >')
		    if (err) { 
		    	console.log('</ CSS >')
		    	return console.error( red + 'Error processing less file '+err)
		    }
	    	
		    // console.log( tree.toCSS().length )
			fs.writeFile( POINTS.css , tree.toCSS(), 'utf8', function(curr, prev) {})
			console.log('OK')
			console.log('</ CSS >')
		})
	})
	// fs.readFile( POINTS.mLess , 'utf8', function(e, data ) { 
	// 	parser.parse( data, function (err, tree) {
	// 		console.log( '< CSS mobile>')
	// 	    if (err) { 
	// 	    	console.log('</ CSS mobile>')
	// 	    	return console.error( red + 'Error processing less file '+err)
	// 	    }
	    	
	// 	    // console.log( tree.toCSS().length )
	// 		fs.writeFile( POINTS.mCss , tree.toCSS(), 'utf8', function(curr, prev) {})
	// 		console.log('OK')
	// 		console.log('</ CSS mobile>')
	// 	})
	// })
}

function parseJS() {
	console.log('< JS >')
	fs.readFile( POINTS.js , 'utf8', function(e, data ) { 
		config = JSON.parse( data.replace(/^(.)*=/,'') )		
		when( procall( config ), 
			function yep(){ console.log('all files OK') }, // good
			function nope(){ console.log( red + 'error') } // wrong
		).then( function(){ 
			reconfig()
			console.log('</ JS >')
		})
	})
}

/* main() */
	var lessf = []
	// var lessmf = []
	if( typeMode !== 'js' )
		parseLESS()
	if( typeMode !== 'css' )
		parseJS()
	
	if( watchMode === 'on' ) {
		fs.watchFile( POINTS.less, function() {
			console.log('LESS CHANGED ' + POINTS.less )
			unwatchCSSbutch( lessf )
			parseLESS()
			watchAllLESS()
		})
		// fs.watchFile( POINTS.mLess, function() {
		// 	console.log('LESS CHANGED ' + POINTS.mLess )
		// 	unwatchCSSbutch( lessmf )
		// 	parseLESS()
		// 	watchAllLESS()
		// })
		watchAllLESS()

	}
/* */
function watchAllLESS() {
	fs.readFile( POINTS.less , 'utf8', function(e, data ) { 
		lessf = data.match( /@import\ \"([a-zA-Z\.\/]+)\"/g )
		for( var i in lessf ) {
			lessf[i] = lessf[i].replace( /@import\ \"([a-za-zA-Z\.\/]+)\"/g , '$1' )
		}
		watchCSSbutch( lessf )
	})
	// fs.readFile( POINTS.mLess , 'utf8', function(e, data ) { 
	// 	lessmf = data.match( /@import\ \"([a-zA-Z\.\/]+)\"/g )
	// 	for( var i in lessmf ) {
	// 		lessmf[i] = lessmf[i].replace( /@import\ \"([a-za-zA-Z\.\/]+)\"/g , '$1' )
	// 	}
	// 	watchCSSbutch( lessmf )
	// })
}
function reconfig() {
	console.log( config )
	// var chunk = 'window.filesWithVersion = ' + JSON.stringify( config )
	var chunk = 'window.filesWithVersion = {\n'
	var f = true
	for(var key in config) {
		( f ) ? f = false : chunk += ',\n'
		chunk += '"' + key + '":' + config[key] 
	}
	chunk += '\n}'
	fs.writeFile( POINTS.js , chunk, 'utf8', function(curr, prev) {})
}

function procall( list ) {
	var promises = []
	for( var k in list) {
		promises.push( procfile( k ) )
		if( watchMode === 'on' )
			watchJSfile( k )
	}
	return when.all( promises )
}

function watchCSSbutch( a ) {
	for( var i in a ) {
		watchCCSfile( a[i] )
	}
}

function unwatchCSSbutch( a ) {
	for( var i in a ) {
		// console.log( POINTS.cssdir + lessf[i] )
		fs.unwatchFile( POINTS.cssdir + a[i] )
	}
}

function watchCCSfile( filename ) {
	var path = POINTS.cssdir + filename		

	fs.watchFile( path, function() {
		console.log('LESS CHANGED ' + path )
		parseLESS()
	})
}

function watchJSfile( filename ) {
	var path = POINTS.jsdir + filename		

	fs.watchFile( path, function() {
		console.info( path + ' CHANGED')
		fs.stat( path ,function( a, b ) {
			minify( filename, b.mtime.getTime()/1000 )
		})
		reconfig()
	})
}


function procfile( filename ) {
	var deferred = when.defer()
	var path = POINTS.jsdir + filename

	fs.exists( path, function(boo) { 
		
		if( !boo ) {
			console.log( red + 'no such file ', path )
			deferred.reject(new Error( 'no such file '+ path ))
			return deferred.promise
		}
		fs.stat( path ,function( a, b ) {
			// console.log(  b.mtime.getTime() , config[''+filename] )
			if( b.mtime.getTime()/1000 != config[''+filename] ) {
				console.log( 'file changed, ', path )
				minify( filename, b.mtime.getTime()/1000 )
			}
			deferred.resolve()
		})
		
	})
	return deferred.promise
}

function minify( filename, timestamp ) {
	var path = POINTS.jsdir + filename,
		pathmin = POINTS.jsdir + filename.replace('js','min.js')

	new compressor.minify({
	    type: 'uglifyjs',
	    fileIn: path,
	    fileOut: pathmin,
	    callback: function(err) {
	        if(err) console.log( red + err)
	    }
	})
	config[''+filename] = timestamp
}