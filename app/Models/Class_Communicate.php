<?php

namespace App\Models;

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


class Class_Communicate
{

    private $Obj_IP;
    private $Obj_paggi;
    private $Obj_BD;

    //////////////////////////////////////////////////////////////////
    // Constructor. Create exemplar.
    //////////////////////////////////////////////////////////////////
    function __construct()
    {
        $this->Obj_IP = new Class_IP();
        $this->Obj_paggi = new  Class_paggination();
        $this->Obj_BD = new Class_BD();
    }

        public function TestClass() {
        return 'Ответ от класса Communicate получен!';
    }

    ///////////////////////////////////////////////////////////
    // Функция отправки сообщений через защищенные протоколы SMTP
    //   обход проверки DKIM и SPF -> Проверка DMARC
    ///////////////////////////////////////////////////////////
    public function SendMailSMTP($email, $pwd, $host, $namesender, $letter = Array())
    {
        $mailSMTP = new SendMailSmtpClass($email, $pwd, $host, $namesender);
        if ((is_array($letter)) && (isset($letter['From'])) && (isset($letter['Theme'])) &&
            (isset($letter['Text'])) && (isset($letter['TitleLetter'])))
            return $mailSMTP->send($letter['From'], $letter['Theme'], $letter['Text'], $letter['TitleLetter']);

        return false;
    }

    /*
     *  Функция возврата getUrl
     *  param: $PHP_SELF, default value is false
     */
    protected function getUrl($PHP_SELF = false) {
        $url  = @( $_SERVER["HTTPS"] != 'on' ) ? 'http://'.$_SERVER["SERVER_NAME"] :  'https://'.$_SERVER["SERVER_NAME"];
        $url .= ( $_SERVER["SERVER_PORT"] != 80 ) ? ":".$_SERVER["SERVER_PORT"] : "";
        if ($PHP_SELF)
            $url .= $_SERVER["PHP_SELF"];
        else
            $url .= $_SERVER["REQUEST_URI"];
        return $url;
    }

    /*
     * Функция возврата корневой папочки
     *  надстройка над function getUrl
     */
    protected function getRoot()
    {
        $pos = -1;
        $directory = $this->getUrl(true);
        $pos = strrpos($directory, '/', 0);
        if ($pos > 0)
            return substr($directory, 0, $pos+1);
        else
            return '';
    }

    ////////// Функция SimpleViewIPs ///////////////////////////////////////////
    // Компонент вывода ip-адресов клиентов, смотрящих веб-страницу
    //
    // date_begin - Начало периода
    // date_end - Конец периода
    //
    //////////////////////////////////////////////////////////////////////////
    public function SimpleViewIPs($date_begin = '', $date_end = '') {
        return $this->Obj_IP->see_logs_ip($date_begin, $date_end);
    }

    ////////// Компонент ViewIPs ///////////////////////////////////////////
    // Компонент вывода ip-адресов клиентов, смотрящих веб-страницу
    //
    // current_page - текущая GET-страница
    // $amount - количество записей на странице
    // $paggi_where - адрес паггинатора
    // $where_dp - условия выбора данных
    // sort = сортировка данных
    //
    //////////////////////////////////////////////////////////////////////////
    public function ViewIPs($current_page = 1, $amount = 5,
                            $paggi_where = 'index.php?url=stran&page=',
                            $where_dp = '`del_rec` = 0', $sort = 'time_look')
    {
        $where = $where_dp;

        $Massive = Array();
        if (empty($current_page) && ($current_page)) $current_page = 1;
        $Massive['paggi'] = '';
        if (is_integer($current_page))
        {
            //if (($current_page) && is_integer($current_page)) $Limit = '';
            $Massive['amount'] = $this->Obj_BD->count_rec('clicker', 'Where ('.$where.')');
            //$this->Paggi->Set($first, $amount, $Massive['amount']);
            $this->Obj_paggi->SetfromPage($current_page, $amount, $Massive['amount']);
            $end = (int)$this->Obj_paggi->start_blog + (int)$amount;
            if (is_integer($amount)) $Limit = "".$this->Obj_paggi->start_blog.", $amount";

            $Limit_int = $this->Obj_paggi->Paggination($current_page);
            $Massive['paggi'] = $this->Obj_paggi->Component_Paggination($paggi_where, $current_page, true, true, 5);
            if (!empty($Limit_int)) $Limit = $Limit_int;
            $Massive['data'] = $this->Obj_BD->select('clicker', Array('*'), $where, $sort, false, $Limit);
        } else {
            $Massive['amount'] = $this->Obj_BD->count_rec('clicker' , $where);
            $Massive['data'] = $this->Obj_BD->select('clicker', Array('*'), $where, '', false);
        }

        $Massive['paggi_where'] = $paggi_where;
        $Massive['where'] = $where;

        return $Massive;
    }

    /////////// Компонент (isAuthorize) ////////////////////////////////////////////////////////////
    //    Компонент проверки статуса авторизации в системе
    //      Работа в СЕССИИ с параметром current_user и id_session
    ////////////////////////////////////////////////////////////////////////////////////////////////
    public function isAuthorize() {
        if (isset($_SESSION['current_user'])) {
            if (($_SESSION['current_user']) && ($_SESSION['id_session'])) {
                $where = "`id_session` = '".$_SESSION['id_session']."'";
                if ($this->BD_Obj->count_rec('visit', "Where ".$where) > 0)
                    return $this->BD_Obj->get_field('users', 'id', "`login` = '".$_SESSION['current_user']."'");
                else
                    return false;
            } else
                return false;
        } else {
            return false;
        }
    }

    /////////// Компонент (thisUser) ////////////////////////////////////////////////////////////
    //    Компонент проверки статуса пользователя в системе
    //    1. current_user - имя (логин) юзера
    ////////////////////////////////////////////////////////////////////////////////////////////////
    public function thisUser($current_user = '') {
        if ((!isset($current_user)) || (empty($current_user)))
            return false;
        else
            if ($_SESSION['current_user'] == $current_user)
                return true;
            else
                return false;
    }

    /////////// Компонент (ExitAuthorize) ////////////////////////////////////////////////////////////
    //    Компонент выхода из авторизации в системе
    //    1. redirect - страница-переадресация в случае выхода из авторизации
    ////////////////////////////////////////////////////////////////////////////////////////////////
    public function ExitAuthorize($redirect = 'index.php') {
        if (isset($_GET['url'])) {
            if (($_GET['url']) && ($_GET['url'] == 'exit'))
                if (($_SESSION['current_user']) && ($_SESSION['id_session'])) {
                    unset($_SESSION['current_user']);
                    unset($_SESSION['id_session']);
                    unset($_SESSION['Message']);
                    header('Location: '.$redirect);
                    exit;
                } else {
                    $_SESSION['Message'] = 'Сессия или id сессии не найден!';
                }
        } else {
            $_SESSION['Message'] = 'Сессия или id сессии не найден!';
        }

    }

}?>