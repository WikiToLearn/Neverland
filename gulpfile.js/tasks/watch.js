var config       = require('../config');
var gulp         = require('gulp');
var path         = require('path');
var watch        = require('gulp-watch');
var minimist     = require('minimist');

var watchTask = function() {
  var watchableTasks = ['css']

  watchableTasks.forEach(function(taskName) {
    var task = config.tasks[taskName]
    if(task) {
      var glob = path.join(config.root.src, task.src, '**/*.{' + task.extensions.join(',') + '}')
      console.log("Watching \"" + glob + "\"")
      watch(glob, function() {
        require('./' + taskName)()
      })
    }
  })
}

gulp.task('watch', ['browserSync'], watchTask)
module.exports = watchTask
