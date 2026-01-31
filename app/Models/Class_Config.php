<?php

namespace App\Models;

/*
 * конфигуратор класс
 *
 * ipBD - ip-адрес базы данных
 * Login - логин базы данных
 * PwdBD - пароль базы данных
 * NameBD - имя базы данных
 */

Class Class_Config {
    public $ipBD = 'localhost';
    public $LoginBD = 'root';
    public $PwdBD = '';
    public $NameBD = 'NewSolarWind';
    public $Name_Theme = 'default';
    public $localServer = true;
    public $Default_Theme = true;
    public $pref = 'standart';
}

?>