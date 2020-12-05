<?php
// Фоны для капчи 150X50
$bg[] = 'bg1.png';
$bg[] = 'bg2.png';
$bg[] = 'bg3.png';
$bg[] = 'bg4.png';

Error_Reporting(0);

require('../../system/global.dat');// Подключаем глобальный файл системы дла получения настроек

// создаем картинку размером 150X50
$img = imagecreatefrompng($bg[mt_rand(0,count($bg)-1)]) or die('Cannot create image');

// Формируем символы на картинке
$str = str_split('abdfhksxyz23456789');//Переменная для формировки цифр на картинке
$rezult = '';
$x=10;//Начальная координата оси X для текста
for($i = 0; $i < 5; ++$i){//выводим одну цифру за один проход цикла (всего 5 цифр)
	$size = mt_rand(25,30);  // размер шрифта в пикселях
	$angle = mt_rand(-15,15); // угол поворота текста
	$y = 35+mt_rand(0,5); // координата y, соответствующие левому нижнему
	$color = imagecolorallocate($img, mt_rand(0,70), mt_rand(0,70), mt_rand(0,70));  // цвет шрифта
	$fontfile = dirname(__FILE__).'/font1.ttf'; // имя файла со шрифтом
	$rnd = $str[mt_rand(0,count($str)-1)]; // Случайный символ

	imagettftext($img, $size, $angle, $x, $y, $color, $fontfile, $rnd);
	//imagechar($img, 5, $x, $y, $rnd, $color);

	$x += 26;//увеличили отступ для следующего символа
	$rezult.= $rnd;// Собираем в одну строку все символы на картинке
}

// Формируем шум (точки)
for($i = 0; $i < 100; ++$i){
	imagesetpixel($img, mt_rand(0, 150),mt_rand(0, 50), 
	imagecolorallocate($img, mt_rand(150, 255), mt_rand(150, 255), mt_rand(150, 255)));
	
}

// Сохраняем куку
setcookie('captcha',md5($rezult.$Config->ticketSalt),0,'/');

//Тип содержимого – картинка формата PNG 
header('Content-type: image/png');
imagepng($img);// выводим готовую картинку в формате PNG
imagedestroy($img);// освобождаем память, выделенную для картинки
?>