var gulp = require('gulp'),
    less = require('gulp-less'),
    cleanCSS = require('gulp-clean-css'),
    rename = require('gulp-rename');

var livereload = require('gulp-livereload');

gulp.task('less', function () { // Создаем таск "less"

    return gulp.src('*.less') // Берем источник
        .pipe(less()) // Преобразуем less в CSS посредством gulp-less
        .pipe(gulp.dest('.')) // Выгружаем результата в папку css
        .pipe(cleanCSS()) // Минимизируем css
        .pipe(rename({ // Переименовываем
            suffix: '.min'
        }))
        .pipe(gulp.dest('.')) // Сохраняем в корневую папку
        .pipe(livereload()); // livereload
});

gulp.task('watch', ['less'], function () {
    livereload.listen(); // livereload
    gulp.watch('*.less', ['less']);
});