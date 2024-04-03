/**
 * --------------------------
 * --- wpLingua GUPL File --- 
 * --------------------------
 * 
 * # Install npm dependencies
 * $ npm i
 * 
 * # Create or update the /assets/ folder
 * $ gulp
 * 
 * # Start the watcher
 * $ gulp watch
 * 
 *
 **/

'use strict';

const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const uglify = require('gulp-uglify');
const clean = require('gulp-clean');


/**
 * Task CSS
 * 
 * Minify and compil SASS in /src/css/
 * Create or update /assets/css/
 */

function css() {
    return gulp.src('src/css/**/*.{css,scss}')
        .pipe(sass({ outputStyle: 'compressed' }).on('error', sass.logError))
        .pipe(gulp.dest('assets/css/'));
};


/**
 * Task JS
 * 
 * Minify JavaScript in /src/js/
 * Create or update /assets/js/
 */

function js() {
    return gulp.src('src/js/**/*.js')
        .pipe(uglify())
        .pipe(gulp.dest('assets/js/'));
};

/**
 * Task images
 * 
 * Copy folder /src/images/
 * Create or update /assets/images/
 */

function images() {
    return gulp.src("src/images/**/*", { removeBOM: false })
        .pipe(gulp.dest("assets/images/"));
};


/**
 * Task folder-create
 * 
 * Create or update folder /wplingua/
 * A copy of the necesary WordPress plugin file
 */

function folder_create() {
    return gulp.src([
        "**",
        "!wplingua/",
        "!wplingua/**",
        "!node_modules/",
        "!node_modules/**",
        "!src/",
        "!src/**",
        "!svn/",
        "!svn/**",
        "!wp-assets/",
        "!wp-assets/**",
        "!.gitignore",
        "!gulpfile.js",
        "!package.json",
        "!package-lock.json",
        "!wplingua.zip",
        "!*.md"
    ])
        .pipe(gulp.dest("wplingua/"));
};


/**
 * Task folder-delete
 * 
 * Delete folder /wplingua/
 */

function folder_delete() {
    return gulp.src('wplingua/**/*', { allowEmpty: true, force: true })
        .pipe(clean({ force: true }));
};


/**
 * Task clear
 *
 * Delete genereted files
 */

function clear() {
    return gulp.src([
        'wplingua.zip',
        'wplingua/**/*',
        'assets/**/*'
    ], { allowEmpty: true, force: true})
        .pipe(clean({ force: true }));
};


/**
 * Watcher
 */

exports.watch = function () {
    gulp.watch('src/**/*', gulp.parallel(css, js));
};


exports.css = css;
exports.js = js;
exports.images = images;
exports.clear = clear;

exports.default = gulp.series(css, js, images);

exports.folder_create = gulp.series(css, js, images, folder_create);
exports.folder_delete = folder_delete;
