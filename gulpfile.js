/**
 *
 * GUPL File
 *
 **/

var gulp         = require("gulp");
var sass         = require('gulp-sass')(require('sass'));
var autoprefixer = require("gulp-autoprefixer");
var cleanCSS     = require("gulp-clean-css");
var jsmin        = require("gulp-jsmin");
var zip          = require('gulp-zip');
var del          = require('del');

/* JS script */
gulp.task("js", () => {
    return gulp.src("src/js/**/*.js")
        .pipe(jsmin())
        .pipe(gulp.dest("assets/js/."));
});

/* CSS / SASS */
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


/* Plugins */
gulp.task("plugins", () => {
    return gulp.src("src/plugins/**/*")
        .pipe(gulp.dest("assets/plugins/"));
});

/* Images */
gulp.task("images", () => {
    return gulp.src("src/images/**/*")
        .pipe(gulp.dest("assets/images/"));
});


gulp.task("watch", () => {
    gulp.watch("src/**/*", gulp.series(
        "css",
        "js",
        "plugins",
        "images"
    ));
});

gulp.task("default", gulp.series(
    "css",
    "js",
    "plugins",
    "images"
));

/* Create archive */
gulp.task("folder-zip",() => {
    return gulp.src([
        "**/wplingua/**/*"
    ])
        .pipe(zip('wplingua.zip'))
        .pipe(gulp.dest("."));
});

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

gulp.task('folder-delete', function () {
    return del([
        "wplingua/"
    ]);
});


/* Process files & Create archive */
gulp.task("archive", gulp.series(
    "css",
    "js",
    "plugins",
    "images",
    "folder-create",
    "folder-zip",
    "folder-delete"
));

gulp.task('clear', function () {
    return del([
        "assets/css/**/*",
        "assets/js/**/*",
        "assets/plugins/**/*",
        "wplingua.zip",
        "*.log"
    ]);
});
