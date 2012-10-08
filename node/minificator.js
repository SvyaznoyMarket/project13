var fs = require('fs'),
	compressor = require('node-minify'),
	when = require('when'),
	less = require('less')


var POINTS = {
	'jsdir': '../web/js/',
	'cssdir': '../web/css/',
	'js': '../web/js/combine.js',
	'less': '../web/css/global.less',
	'css': '../web/css/global.css'
}	

var red   = '\033[31m'
var config = {}

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

parseLESS()
parseJS()

fs.watchFile( POINTS.less, function() {
	console.info('LESS CHANGED')
	parseLESS()
})

function reconfig() {
	console.log( config )
	var chunk = 'window.filesWithVersion = ' + JSON.stringify( config )
	fs.writeFile( POINTS.js , chunk, 'utf8', function(curr, prev) {})
}

function procall( list ) {
	var promises = []
	for( var k in list) {
		promises.push( procfile( k ) )
		watchJSfile( k )
	}
	return when.all( promises )
}

function watchJSfile( filename ) {
	var path = POINTS.jsdir + filename		

	fs.watchFile( path, function() {
		console.info( path + ' CHANGED')
		fs.stat( path ,function( a, b ) {
			minify( filename, b.mtime.getTime() )
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
			if( b.mtime.getTime() != config[''+filename] ) {
				console.log( 'file changed, ', path )
				minify( filename, b.mtime.getTime() )
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