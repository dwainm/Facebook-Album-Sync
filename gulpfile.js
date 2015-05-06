var gulp = require('gulp');
var react = require('gulp-react');

gulp.task('default', function() {
    // place code for your default task here
});

gulp.task('default', function () {
    return gulp.src('js/views/*.jsx')
        .pipe(react())
        .pipe(gulp.dest('js/views/'));
});