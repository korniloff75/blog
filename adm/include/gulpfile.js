var gulp = require('gulp'), 
    less = require('gulp-less'), 
	cleanCSS = require('gulp-clean-css'),
	rename = require('gulp-rename'); 
	
gulp.task('less', function(){ // Создаем таск "less"
    
	return gulp.src('*.less') // Берем источник
        .pipe(less()) // Преобразуем less в CSS посредством gulp-less
		.pipe(gulp.dest('.')) // Выгружаем результата в папку css
		.pipe(cleanCSS()) // Минимизируем css
		.pipe(rename({ // Переименовываем
			suffix: '.min'
		}))
		.pipe(gulp.dest('.')); // Выгружаем
});

gulp.task('watch', ['less'], function() {
	gulp.watch('*.less', ['less']);
});