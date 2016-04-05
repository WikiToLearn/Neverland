var config       = require('../config');
if(!config.tasks.css) return;

var gulp         = require('gulp');
var sass         = require('gulp-sass');
var handleErrors = require('../lib/handleErrors');
var autoprefixer = require('gulp-autoprefixer');
var path         = require('path');
var rename       = require('gulp-rename');
var requireDir   = require('require-dir');
var minimist     = require('minimist'); 

var paths = {
  src: path.join(config.root.src, config.tasks.css.src, '/main.{' + config.tasks.css.extensions + '}'),
  dest: config.tasks.css.dest
};

var taskConfig = config.tasks.css.sass;
if (minimist(process.argv.slice(2)).minify !== undefined) {
  taskConfig.outputStyle = "compressed"
}

var cssTask = function () {
  return gulp.src(paths.src)
    .pipe(sass(taskConfig))
    .on('error', handleErrors)
    .pipe(autoprefixer(config.tasks.css.autoprefixer))
    .pipe(gulp.dest(paths.dest))
};

gulp.task('css', cssTask);
module.exports = cssTask;
