const gulp = require('gulp');
const watch = require('gulp-watch');
const minify = require( 'gulp-minify' );
const sourcemaps = require('gulp-sourcemaps');
const babel = require('gulp-babel');
const concat = require('gulp-concat');
var uglifycss = require('gulp-uglifycss');



const adminJSFiles = [
	'./assets/js/admin/status.js',
	'./assets/js/admin/sha1.js',
	'./assets/js/admin/message.js',
];

gulp.task( 'adminJs', function () {
	gulp.src(adminJSFiles)
		.pipe(sourcemaps.init())
		.pipe(babel({
			presets: ['env']
		}))
		.pipe(concat('./assets/js/admin.js'))
		.pipe(sourcemaps.write('.'))
		.pipe(minify({
			ext:'.min.js',
			noSource: false,
			mangle: true,
			compress: true
		}))
		.pipe(gulp.dest('.'))


});

gulp.task( 'adminCSS', function() {
	gulp.src('./assets/css/admin/*.css')
		.pipe(uglifycss({
			"maxLineLen": 80,
			"uglyComments": true,
			ext:'.min.js',

		}))
		.pipe(gulp.dest('./assets/css/'));
});




gulp.task('watch', function(){
    gulp.watch( adminJSFiles, ['adminJs' ]);
    gulp.watch( './assets/css/admin/*.css', ['adminCSS' ]);
});

gulp.task('default', ['adminJs', 'adminCSS' ]);