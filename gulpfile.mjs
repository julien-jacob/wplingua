/**
 **                 _     _                         
 ** __      ___ __ | |   (_)_ __   __ _ _   _  __ _ 
 ** \ \ /\ / / '_ \| |   | | '_ \ / _` | | | |/ _` |
 **  \ V  V /| |_) | |___| | | | | (_| | |_| | (_| |
 **   \_/\_/ | .__/|_____|_|_| |_|\__, |\__,_|\__,_|
 **          |_|                  |___/             
 **
 **        -- wpLingua | WordPress plugin --
 **   Translate and make your website multilingual
 **
 **     https://github.com/julien-jacob/wplingua
 **      https://wordpress.org/plugins/wplingua/
 **              https://wplingua.com/
 **
 ** 
 ** # Install npm dependencies
 ** $ npm i
 ** 
 ** # Create or update the /assets/ folder
 ** $ gulp
 ** 
 ** # Start the watcher
 ** $ gulp watch
 ** 
 ** # Create or update the archive
 ** $ gulp archive
 ** 
 **/

'use strict';


/**
 * Import
 */

// Import Gulp
import gulp from 'gulp';
const { series, parallel, src, dest, task } = gulp;

// Import SASS
import * as dartSass from 'sass';
import gulpSass from 'gulp-sass';
const sass = gulpSass(dartSass);

// Import other library
import uglify from 'gulp-uglify';
import zip from 'gulp-zip';
import autoprefixer from 'gulp-autoprefixer';
import { deleteSync } from 'del';


/**
 * Task CSS
 * 
 * Minify and compil SASS in /src/css/
 * Create or update /assets/css/
 */

function css(cb) {
    gulp.src('src/css/**/*.{css,scss}', { sourcemaps: true })
        .pipe(sass({ outputStyle: 'compressed' }).on('error', sass.logError))
        .pipe(autoprefixer())
        .pipe(gulp.dest('assets/css/', { sourcemaps: '.' }))
        .on('end', function () { cb() });
};


/**
 * Task JS
 * 
 * Minify JavaScript in /src/js/
 * Create or update /assets/js/
 */

function js(cb) {
    gulp.src('src/js/**/*.js', { sourcemaps: true })
        .pipe(uglify({ output: { comments: /^!/ } }))
        .pipe(gulp.dest('assets/js/', { sourcemaps: '.' }))
        .on('end', function () { cb() });
};

gulp.task(js);


/**
 * Task images
 * 
 * Copy folder /src/images/
 * Create or update /assets/images/
 */

function images(cb) {
    gulp.src("src/images/**/*", { removeBOM: false })
        .pipe(gulp.dest("assets/images/"))
        .on('end', function () { cb() });
};

gulp.task(images);

/**
 * Function folder-zip
 * 
 * Create or update a zip archive 
 * with /wplingua/ folder
 */

function folder_zip(cb) {
    gulp.src([
        "**/wplingua/**/*"
    ], { removeBOM: false })
        .pipe(zip('wplingua.zip'))
        .pipe(gulp.dest("."))
        .on('end', function () { cb() });
};


/**
 * Function folder-create
 * 
 * Create or update folder /wplingua/
 * A copy of the necesary WordPress plugin file
 */

function folder_create(cb) {
    gulp.src([
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
        "!gulpfile.mjs",
        "!package.json",
        "!package-lock.json",
        "!wplingua.zip",
        "!*.md"
    ], { removeBOM: false })
        .pipe(gulp.dest("wplingua/"))
        .on('end', function () { cb() });
};


/**
 * function folder-delete
 * 
 * Delete folder /wplingua/
 */

function folder_delete(cb) {
    deleteSync([
        'wplingua'
    ]);
    cb();
};


/**
 * Task clear
 *
 * Delete genereted files
 */

function clear(cb) {
    deleteSync([
        'assets',
        'wplingua',
        "*.log"
    ]);
    cb();
};

gulp.task(clear);


/**
 * Task archive
 *
 * Create a plugin ZIP archive
 */

gulp.task("archive", gulp.series(
    clear,
    gulp.parallel(
        css,
        js,
        images
    ),
    folder_create,
    folder_zip,
    folder_delete,
    function (cb) {
        cb();
    }
));


/**
 * Task watch
 * 
 * Start the watcher
 * Tasks : css, js, plugins, images
 */

gulp.task("watch", () => {
    gulp.watch("src/**/*", gulp.parallel(
        css,
        js
    ));
});


/**
 * Task default
 */

export default gulp.series(
    clear,
    gulp.parallel(
        css,
        js,
        images
    )
);
