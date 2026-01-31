<?php

namespace App\Controller;

/////////////////////////////////////////////////////////////////////
//
// Создатель CMS/Framework SolarWind: Ларионов Андрей Николаевич
// По поводу приобретения платной версии системы обращаться:
// Телефон для связи: 8-913-134-09-42
// Email: IntegralAL@mail.ru
// Класс работы с базой данных CMS SolarWind
// Разработчик: Ларионов Андрей.
// Дата новой разработки: От Январь 2026 года.
//
// Работа выполнена исключительно на энтузиазме разработчика Ларионова Андрея без должного финансирования
// и финансовой подоплеки. Потому все претензии оставляйте у себя, платная поддержка подразумевает значительно более
// высокий уровень обеспечения и кодирования
//
// php от 5.2 и даже до 8.1, не содержит синтаксиса 8.2, 8.3, 8.4
//////////////////////////////////////////////////////////////

use App\Models\Class_BD;
use App\Models\Class_Communicate;
use App\Models\Class_Config;
use App\Models\Class_IP;
use App\Models\Class_paggination;
use App\Models\traits\MagicMethod;
use Exception;

class IndexController
{
    use MagicMethod;

    private $Obj_comm;
    private $Obj_BD;
    private $Obj_conn;

    //////////////////////////////////////////////////////////////////
    // Constructor.
    //////////////////////////////////////////////////////////////////
    function __construct()
    {
        $this->Obj_comm = new Class_Communicate();
        $this->Obj_conn = new  Class_Config();
        $this->Obj_BD = new Class_BD();
    }

    /*
    * Пролог итераций в index файле
    */
    public static function PrologIndex() {
        // Создаем экземпляр класса для доступа к нестатическим свойствам
        $instance = new self();

        if ($instance->Obj_comm->isAuthorize() == false) {
            if (isset($_GET['project'])) {
                $theme = $_GET['project'];
                $data = $instance->Obj_comm->isAuthorize() ? "`connected_user`=".$instance->Obj_comm->isAuthorize() : "`id`=1";
                $_SESSION['update'] = $instance->Obj_BD->update('config',
                    ['theme_site'],
                    [$theme],
                    $data
                );
            } else {
                return $instance->Obj_conn->Name_Theme;
            }
        } else {
            if (isset($_GET['project'])) {
                $data = "`connected_user`=".$instance->Obj_comm->isAuthorize();
                $theme = $_GET['project'];
                $_SESSION['update'] = $instance->Obj_BD->update('config',
                    ['theme_site'],
                    [$theme],
                    $data
                );
            } else {
                return $instance->Obj_conn->Name_Theme;
            }

            $data_theme = $instance->Obj_BD->select('config', ['*'], "`connected_user`=".$instance->Obj_comm->isAuthorize());
            return $data_theme[0]['theme_site'] ?? $instance->Obj_conn->Name_Theme;
        }
    }
}?>