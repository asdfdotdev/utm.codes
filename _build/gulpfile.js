'use strict';

var node,
	fs = require('fs-extra'),
	gulp = require('gulp'),
	autoprefixer = require('gulp-autoprefixer'),
	cssnano = require('gulp-cssnano'),
	sass = require('gulp-sass'),
	sourcemaps = require('gulp-sourcemaps'),
	concat = require('gulp-concat'),
	uglify = require('gulp-uglify');


gulp.task('scripts', function() {
	return gulp.src('./javascript/**/**/*.js')
		.pipe(concat('utmdotcodes.min.js'))
		.pipe(uglify())
		.pipe(gulp.dest('../js/'));
});


gulp.task('styles', function () {
	gulp.src('./sass/**/*.scss')
		.pipe(sourcemaps.init())
		.pipe(sass().on('error', sass.logError))
		.pipe(autoprefixer({
			browsers: ['last 2 versions'],
			cascade: false
		}))
		.pipe(cssnano())
		.pipe(concat('utmdotcodes.css'))
		.pipe(sourcemaps.write('./'))
		.pipe(gulp.dest('../css/'))
});


gulp.task('listen', function () {

	// rebuild CSS when source files change
	gulp.watch('./sass/**/*.scss', ['styles']);

	// rebuild JS when source files change
	gulp.watch('./javascript/**/*.js', ['scripts']);

});


gulp.task('update', function () {
	var update = require('gulp-update')();

	gulp.watch('./package.json').on('change', function (file) {
		update.write(file);
	});
});


gulp.task('default', ['update', 'styles', 'scripts', 'listen']);
