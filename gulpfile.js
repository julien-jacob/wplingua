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
 * # Create or update the archive
 * $ gulp archive
 *
 **/

var gulp = require("gulp");
var sass = require('gulp-sass')(require('sass'));
var autoprefixer = require("gulp-autoprefixer");
var cleanCSS = require("gulp-clean-css");
var jsmin = require("gulp-jsmin");
var zip = require('gulp-zip');
var del = require('del');


/**
 * Task JS
 * 
 * Minify JavaScript in /src/js/
 * Create or update /assets/js/
 */
gulp.task("js", () => {
    return gulp.src("src/js/**/*.js")
        .pipe(jsmin())
        .pipe(gulp.dest("assets/js/."));
});


/**
 * Task CSS
 * 
 * Minify and compil SASS in /src/css/
 * Create or update /assets/css/
 */
gulp.task("css", () => {
    return gulp.src("src/css/**/*.{css,scss}")
        .pipe(sass().on("error", sass.logError))
        .pipe(autoprefixer(
            "last 2 version",
            "> 1%",
            "safari 5",
            "ie 8",
            "ie 9",
            "opera 12.1",
            "ios 6",
            "android 4"
        ))
        .pipe(cleanCSS({
            compatibility: "ie9",
            processImport: false
        }))
        .pipe(gulp.dest("assets/css/."));
});


/**
 * Task plugins
 * 
 * Copy folder /src/plugins/
 * Create or update /assets/plugins/
 */
gulp.task("plugins", () => {
    return gulp.src("src/plugins/**/*")
        .pipe(gulp.dest("assets/plugins/"));
});


/**
 * Task images
 * 
 * Copy folder /src/images/
 * Create or update /assets/images/
 */
gulp.task("images", () => {
    return gulp.src("src/images/**/*")
        .pipe(gulp.dest("assets/images/"));
});


/**
 * Task folder-zip
 * 
 * Create or update a zip archive 
 * with /wplingua/ folder
 */
gulp.task("folder-zip", () => {
    return gulp.src([
        "**/wplingua/**/*"
    ])
        .pipe(zip('wplingua.zip'))
        .pipe(gulp.dest("."));
});


/**
 * Task folder-create
 * 
 * Create or update folder /wplingua/
 * A copy of the necesary WordPress plugin file
 */
gulp.task("folder-create", () => {
    return gulp.src([
        "**",
        "!wplingua/",
        "!src/",
        "!src/**",
        "!node_modules/",
        "!node_modules/**",
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
});


/**
 * Task folder-delete
 * 
 * Delete folder /wplingua/
 */
gulp.task('folder-delete', function () {
    return del([
        "wplingua/"
    ]);
});


/**
 * Task folder-delete
 * 
 * Process files & Create archive 
 */
gulp.task("archive", gulp.series(
    "css",
    "js",
    "plugins",
    "images",
    "folder-create",
    "folder-zip",
    "folder-delete"
));


/**
 * Task clear
 * 
 * Clear genereted files
 * Assets files, wplingua.zip and log files
 */
gulp.task('clear', function () {
    return del([
        "assets/css/**/*",
        "assets/js/**/*",
        "assets/plugins/**/*",
        "wplingua.zip",
        "*.log"
    ]);
});


/**
 * Task watch
 * 
 * Start the watcher
 * Tasks : css, js, plugins, images
 */
gulp.task("watch", () => {
    gulp.watch("src/**/*", gulp.series(
        "css",
        "js",
        "plugins",
        "images"
    ));
});


/**
 * Task default
 * 
 * Tasks : css, js, plugins, images
 */
gulp.task("default", gulp.series(
    "css",
    "js",
    "plugins",
    "images"
));