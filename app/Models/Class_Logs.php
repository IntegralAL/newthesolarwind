<?php
namespace App\Models;

class Class_Logs
{
    /*  clicker
     *  -----------------------------------------------------
     * | id               |   +                             |
     * ------------------------------------------------------
     * | ipaddress         |   string                      |
     * ------------------------------------------------------
     * |  macaddress       |   string                       |
     * ------------------------------------------------------
     * | page_look       |   text                           |
     * ------------------------------------------------------
     * |
     *
     */

    //////////////////////////////////////////////////////////////////
    // Constructor.
    //////////////////////////////////////////////////////////////////
    function __construct()
    {
        $this->Obj_IP = new Class_IP();
        $this->Obj_paggi = new  Class_paggination();
        $this->Obj_BD = new Class_BD();
    }

    public function TestClass() {
        return 'Ответ от класса Logs!';
    }

    //////////////////////////////////////////////////////////
    // Функция добавления записи в лог add_logs_ip
    //   Параметры:
    //    $geolocation - геолокационное определение клиента по ip-адресу
    //////////////////////////////////////////////////////////
    public function add_logs_ip($geolocation = false) {
        if ($geolocation) {

        } else {
            $ip_user = $this->Obj_IP->get_ip();
            $page_look = $this->Obj_IP->getUrl();
            $macaddress = '000-0000-000-000';
            $fields = Array('ipaddress', 'macaddress', 'page_look');
            $values = Array($ip_user, $macaddress, $page_look);
            $Answer = $this->Obj_BD->insert('clicker', $fields, $values, false);
            return $Answer;
        }
    }

    ////////////////////////////////////////////////////////
    // Функция see_logs_ip вывода лога адресов в интервале времени
    //  Параметры:
    //   $time_begin - начало временного интервала (работать только с оригиналом)
    //   $time_end - конец временного интервала
    //
    ////////////////////////////////////////////////////////
    public function see_logs_ip($time_begin, $time_end) {
        if ($time_begin > $time_end) {
            $tmp = &$time_begin;
            $time_begin = $time_end;
            $time_end = $tmp;
        }

        $where = '';
        if (!empty($time_begin)) $where .= "(`time_look` >".$time_begin.")";
        if (!empty($time_end)) {
            if (!empty($where)) $where .= "&&";
            $where .= "(`time_look` <".$time_end.")";
        }

        $Answer = $this->BD_Obj->select('clicker', Array('*'), $where, '', true);
        return $Answer;
    }

    ////////////////////////////////////////////////////////
    // Функция see_logs_ip_param вывода лога адресов в интервале времени с параметрами
    //  Параметры:
    //   $time_begin - начало временного интервала (работать только с оригиналом)
    //   $time_end - конец временного интервала
    //
    ////////////////////////////////////////////////////////
    public function see_logs_ip_param($time_begin, $time_end,
                                      $current_page, $amount,
                                      $paggi_where, $ip_address) {

        if ($time_begin > $time_end) {
            $tmp = &$time_begin;
            $time_begin = $time_end;
            $time_end = $tmp;
        }

        $where = '';
        if (!empty($time_begin)) $where .= "(`time_look` >".$time_begin.")";
        if (!empty($time_end)) {
            if (!empty($where)) $where .= "&&";
            $where .= "(`time_look` <".$time_end.")";
        }

        $Answer = $this->BD_Obj->select('clicker', Array('*'), $where, '', true);
        return $Answer;
    }
}