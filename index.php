<?php
session_start();

// Создатель CMS/Framework SolarWind: Ларионов Андрей Николаевич
// По поводу приобретения платной версии системы обращаться:
// Телефон для связи: 8-913-134-09-42
// Email: IntegralAL@mail.ru
// Автозагрузка классов

spl_autoload_register(function ($class) {
    // Преобразование пространства имен в путь к файлу
    $file = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use App\Models;
use App\Models\Class_Config;
use App\Models\Class_BD;
use App\Controller\IndexController;

$head = 'head.php';
$body = 'body.php';
$footer = 'footer.php';
$theme = '';

$conf = new Class_Config;
$BDConnect = new Class_BD;
//$Index = new IndexController();
$theme = 'default';

if ($conf->Default_Theme == true) {
    $where = 'id = 1';
    $Current_page  = $BDConnect->select('pages', ['*'], $where);

    if (file_exists('app/Views/Public/Themes/default/'.$head))
        include_once('app/Views/Public/Themes/default/'.$head);
    if (file_exists('app/Views/Public/Themes/default/'.$body))
        include_once('app/Views/Public/Themes/default/'.$body);
    if (file_exists('app/Views/Public/Themes/default/'.$footer))
        include_once('app/Views/Public/Themes/default/'.$footer);
} else {
    if (file_exists('app/Views/Public/Themes/'.$theme.'/'.$head))
        include_once('app/Views/Public/Themes/'.$theme.'/'.$head);

    if (isset($_GET['url']) && !empty($_GET['url'])) {
        if (file_exists('app/Views/Public/Themes/'.$theme.'/'.$_GET["url"].'.php'))
            include_once('app/Views/Public/Themes/'.$theme.'/'.$_GET["url"].'.php');
    } else {
        if (file_exists('app/Views/Public/Themes/'.$theme.'/main.php'))
            include_once('app/Views/Public/Themes/'.$theme.'/main.php');
    }

    if (file_exists('app/Views/Public/Themes/'.$theme.'/'.$footer))
        include_once('app/Views/Public/Themes/'.$theme.'/'.$footer);

}


include_once('epilog.php');
