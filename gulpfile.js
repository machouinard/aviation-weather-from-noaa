const { src, dest, series, parallel, watch } = require( 'gulp' );
const minify = require('gulp-minify');

const del = require( 'del' );
const babel = require( 'gulp-babel');
const source = 'js/*.js';
const destination = 'js';
const files = 'js/*-min.js';

async function clean() {
	await del(files);
}

function js(cb) {
	src(source)
		.pipe(minify())
		.pipe(dest(destination));
	cb();
}

exports.default = series( clean, js );
