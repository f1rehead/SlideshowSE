const { gulp, src, dest, series, parallel, watch } = require( 'gulp' );
const babel = require( 'gulp-babel' );
const uglify = require( 'gulp-uglify' );
const uglifycss = require( 'gulp-uglifycss' );
const include = require( 'gulp-include' );
const less = require( 'gulp-less' );
const rename = require( 'gulp-rename' );
const path = require( 'path' );

// Private task for building.
function clean( cb ) {
	// Remove the bundled JS, minimized JS, and generated CSS files
	cb();
}

function jsBundle( cb ) {
	// Combines all the JS files into their respective all.*.js
	// files WITHOUT minimizing. Not part of the build chain
	return src( [ 'js/all.backend.js', 'js/all.frontend.js' ] )
		.pipe( include() )
		.pipe( rename( { extname: '.inc.js' } ) )
		.pipe( dest( 'js/' ) );
}

function jsMinify( cb ) {
	return src( [ 'js/all.backend.js', 'js/all.frontend.js' ] )
		.pipe( include() )
		.pipe( babel() )
		.pipe( uglify() )
		.pipe( rename( { extname: '.min.js' } ) )
		.pipe( dest( 'js/min/' ) );
}

function cssTranspile( cb ) {
	return src( 'less/all.backend.less' )
		.pipe(
			less( {
				paths: [ path.join( __dirname, 'less', 'includes' ) ],
			} )
		)
		.pipe( dest( './css' ) );
}

function cssMinify( cb ) {
	return src( 'css/all.backend.css' )
		.pipe( uglifycss() )
		.pipe( rename( { extname: '.min.css' } ) )
		.pipe( dest( 'css/' ) );
}

exports.cssTranspile = cssTranspile;
exports.cssMinify = cssMinify;
exports.jsBundle = jsBundle;
exports.jsMinify = jsMinify;

exports.build = series( clean, cssTranspile, parallel( cssMinify, jsMinify ) );

exports.watch = function () {
	watch( [ 'js/*.js', '' ], { events: 'change' }, jsMinify );
	watch( 'less/**/*.less', series( cssTranspile, cssMinify ) );
};
