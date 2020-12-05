<?php
if (!class_exists('System')) exit; // Запрет прямого доступа
require(DR.'/modules/turbo/cfg.php');




if($act=='index'){
    echo'<div class="header"><h1>Источники для турбо страниц Яндекса</h1></div>
    <div class="menu_page">
        <a href="index.php">&#8592; Вернуться назад</a>
    </div>
    <div class="content">';
        if($turboConfig->turbo){
            echo'<p class="row">
            Ниже приведен список RSS источников, которые можно использовать для турбо страниц Яндекса. По мере заполнения, будет происходить разбивка: максимум 1000 записей для одного источника.
            </p>
            <p class="row"><a class="button" href="module.php?module='.$MODULE.'&amp;act=turbo0">Отключить источники для турбо страниц</a> <a class="button" href="module.php?module='.$MODULE.'&amp;act=turbo_cfg">Настройки турбо страниц</a></p>
            
            <p>Подробнее про турбостраницы читайте на сайте <a href="https://yandex.ru/dev/turbo/" target="_blank">Турбо-страницы Яндекса</a>.</p>';
        
            echo'<table class="tables">
            <tr>
                <td class="tables_head" colspan="2">RSS источники</td>
                <td class="tables_head" style="text-align: right;">&nbsp;</td>
            </tr>';	
            $listTurbo = System::listPages();
            // Исключения
            $exceptions = explode(',', str_replace(' ', '', $turboConfig->exceptions));
            $listTurbo = array_diff($listTurbo, $exceptions);
            
                $nom = count($listTurbo);
                $countPage = ceil($nom / $turboConfig->turboItems);
                for($i = 1; $i <= $countPage; ++$i){
                    echo'<tr>
                    <td class="img"><img src="include/link.svg" alt=""></td>
                    <td><a href="//'.SERVER.'/'.$turboConfig->turboId.'/'.$i.'.xml" target="_blank">'.SERVER.'/'.$turboConfig->turboId.'/'.$i.'.xml</a></td>
                    <td></td>
                    </tr>';

                }

            
            echo'
            <tr>
            <td colspan="3" style="background-color: #eee; text-align:left;">Записей: '.$nom.';&nbsp; Источников: '.$countPage.';</td>
            </tr>
            </table>
            </div>';
        }else{
            echo'<p class="row"><a class="button" href="module.php?module='.$MODULE.'&amp;act=turbo1">Включить источники для турбо страниц</a> <a class="button" href="module.php?module='.$MODULE.'&amp;act=turbo_cfg">Настройки турбо страниц</a></p>
                <p>Подробнее про турбостраницы читайте на сайте <a href="https://yandex.ru/dev/turbo/" target="_blank">Турбо-страницы Яндекса</a>.</p>';
        }


    
}

if($act=='turbo1'){
    $turboConfig->turbo = 1;
    if($turboStorage->set('turboConfig', json_encode($turboConfig))){
        echo'<div class="msg">Настройки успешно сохранены</div>';
        System::notification('Включен вывод источников для турбо страниц Яндекса');
    }else{
        echo'<div class="msg">Произошла ошибка записи настроек</div>';
        System::notification('Произошла ошибка при сохранении параметров модуля новостей', 'r');
    }
    ?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>&act=index\';', 3000);
</script>
<?php
}

if($act=='turbo0'){
    $turboConfig->turbo = 0;
    if($turboStorage->set('turboConfig', json_encode($turboConfig))){
        echo'<div class="msg">Настройки успешно сохранены</div>';
        System::notification('Отключен вывод источников для турбо страниц Яндекса');
    }else{
        echo'<div class="msg">Произошла ошибка записи настроек</div>';
        System::notification('Произошла ошибка при сохранении параметров модуля новостей', 'r');
    }
    ?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>&act=index\';', 3000);
</script>
<?php
}


if($act=='turbo_cfg'){
    echo'<div class="header"><h1>Настройки турбо страниц</h1></div>
    <div class="menu_page">
        <a href="module.php?module='.$MODULE.'&amp;act=index">&#8592; Вернуться назад</a>
    </div>
    <div class="content">
    <form name="form_name" action="module.php?module='.$MODULE.'&amp;" method="post" style="margin:0px; padding:0px;">
    <INPUT TYPE="hidden" NAME="act" VALUE="turbo_addcfg">
    <table class="tblform">
    
    <tr>
        <td>Количество item для одного источника:</td>
        <td><input type="text" name="turboItems" value="'.$turboConfig->turboItems.'"><br>
        <span class="comment">Уменьшайте это число если получаете уведомление от яндекса о превышении веса загружаемого источника</span></td>
    </tr>

    <tr>
        <td>Идентификатор страницы источников:</td>
        <td><input type="text" name="turboId" value="'.$turboConfig->turboId.'"><br>
        <span class="comment">Этот идентификатор используется для формирования уникального адреса на источники</span></td>
    </tr>
    
    <tr>
        <td>Время кеширования сформированных источников:</td>
        <td>
            <select name="turboCacheTime">
                <option value="0"'.($turboConfig->turboCacheTime == '0'?' selected':'').'>Не кешировать (Не рекомендуется)
                <option value="900"'.($turboConfig->turboCacheTime == '900'?' selected':'').'>15 минут
                <option value="1800"'.($turboConfig->turboCacheTime == '1800'?' selected':'').'>30 минут
                <option value="3600"'.($turboConfig->turboCacheTime == '3600'?' selected':'').'>1 час (По умолчанию)
                <option value="10800"'.($turboConfig->turboCacheTime == '10800'?' selected':'').'>3 часа
                <option value="21600"'.($turboConfig->turboCacheTime == '21600'?' selected':'').'>6 часов
                <option value="43200"'.($turboConfig->turboCacheTime == '43200'?' selected':'').'>12 часов
                <option value="86400"'.($turboConfig->turboCacheTime == '86400'?' selected':'').'>1 день
                <option value="259200"'.($turboConfig->turboCacheTime == '259200'?' selected':'').'>3 дня
                <option value="604800"'.($turboConfig->turboCacheTime == '604800'?' selected':'').'>1 неделю
                <option value="1209600"'.($turboConfig->turboCacheTime == '1209600'?' selected':'').'>2 недели
                <option value="2678400"'.($turboConfig->turboCacheTime == '2678400'?' selected':'').'>1 месяц
                <option value="8035200"'.($turboConfig->turboCacheTime == '8035200'?' selected':'').'>3 месяца
                <option value="16070400"'.($turboConfig->turboCacheTime == '16070400'?' selected':'').'>6 месяцев
            </select>
        </td>
    </tr>
    
    <tr>
        <td class="top">
            Исключения для турбо страниц:
        </td>
        <td><textarea name="exceptions" style="height:150px;">'.$turboConfig->exceptions.'</textarea><br>
        <span class="comment">Введите идентификаторы страниц, которым нужно запретить отображение на турбо страницах яндекса. Страницы, которые доступны только администратору или пользователям с преференциями, в источники вообще не попадают.<br>
        Идентификаторы должны быть введены через запятую.</span>
        </td>
	</tr>
    
    <tr>
        <td>&nbsp;</td>
        <td><button type="button" onClick="submit();">Сохранить</button> &nbsp; <a href="module.php?module='.$MODULE.'">Вернуться назад</a></td>
    </tr>
    </table>
    </form>
    <p><a href="module.php?module=news&act=info">Настройки турбо страниц в модуле новостей</a></p>
    
    </div>';
}

if($act=='turbo_addcfg'){
		 
    $turboConfig->turboItems = is_numeric($_POST['turboItems'])?$_POST['turboItems']:1000;
    $turboConfig->turboId = System::validPath($_POST['turboId'])?$_POST['turboId']:'turbo';
    $turboConfig->turboCacheTime = is_numeric($_POST['turboCacheTime'])?$_POST['turboCacheTime']:3600;
    $turboConfig->exceptions = htmlspecialchars($_POST['exceptions']);

    if($turboStorage->set('turboConfig', json_encode($turboConfig))){
        echo'<div class="msg">Настройки успешно сохранены</div>';
        System::notification('Сохранены настройки модуля турбо страниц');
    }else{
        echo'<div class="msg">Произошла ошибка записи настроек</div>';
        System::notification('Произошла ошибка при сохранении модуля турбо страниц', 'r');
    }
    ?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>&act=turbo_cfg\';', 3000);
</script>
<?php
}

?>