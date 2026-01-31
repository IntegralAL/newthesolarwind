<?php
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

namespace App\Models;

use mysqli;

Class Class_BD
{
    private $Connect;
    public $current_date;
    public $mysqli;

    public function TestClass() {
        return 'Ответ от класса БД получен!';
    }

    //////////////////////////////////////////////////////////////////
    // Constructor. Create exemplar.
    //////////////////////////////////////////////////////////////////
    function __construct()
    {  // Инициализация класса репорта ошибок
        //print "Конструктор класса BaseClass\n";

        //$this->report_error1 = new Report_error();
        $this->Connect = new Class_Config();
        $ModeEdit = false;

        // Инициализация класса конфигуратора и подключение к бд
        $this->mysqli = new mysqli($this->Connect->ipBD,
            $this->Connect->LoginBD,
            $this->Connect->PwdBD,
            $this->Connect->NameBD);


        if ($this->mysqli->connect_errno)
        {
            printf("No connect with BD: %s\n", $this->mysqli->connect_error);
            exit();
        }
        else {
            $ModeEdit = true;
        }
        $CodeWeb = 'RU';
        $this->mysqli->query("SET lc_time_names = 'ru_RU'");
        $this->mysqli->query("SET NAMES 'utf8'");

        $this->current_date = date('d.m.y');

    }

    ////////////////////////////////////////////////////////////////
    // Private function execute sql
    //
    // parametr: $sql - query SQL
    ////////////////////////////////////////////////////////////////
    public function external_query($sql)
    {
        $result = $this->mysqli->query($sql);
        return $result;
    }

    ///////////////////////////////////////////////////////////////
    // public function - Simple table
    //
    // parametrs:   $sql - Query sql
    //              $debug - boolean value for debugs
    ///////////////////////////////////////////////////////////////
    public function simple_select($sql, $debug = false)
    {
        if ($debug == true) {
            return $sql;
        } else {
            $result_set = $this->mysqli->query($sql);

            if (!$result_set) return false;
            $i = 1;
            $data = array();
            while ($row = $result_set->fetch_assoc()) {
                $data[$i] = $row;
                $i++;
            }

            $result_set->close();
            return $data;
        }
    }

    ///////////////////////////////////////////////////////////////
    // Function Annulate specsimbol
    //	хакинк против sql-иньекций
    //
    // parametrs - $data - string of sumbols
    ///////////////////////////////////////////////////////////////
    public function _strip($data)
    {
        $lit = array("\\t", "\\n", "\\n\\r", "\\r\\n", "  ", "(", ")");
        $sp = array('', '', '', '', '', '[', ']');
        return str_replace($lit, $sp, $data);
    }

    ///////////////////////////////////////////////////////////////
    // Function _desrtip
    //	обращение символов
    //  parametr - $data - replace specsumbols
    ///////////////////////////////////////////////////////////////
    public function _destrip($data)
    {
        $lit = array('&[&', '&]&');
        $sp = array("(", ")");
        return str_replace($lit, $sp, $data);
    }

    ///////////////////////////////////////////////////////////////
    // Function xss blocking
    // Хакинг против xss-атак
    //
    //   paramtr - $data - array of values
    ///////////////////////////////////////////////////////////////
    public function xss($data)
    {
        if (is_array($data)) {
            $escaped = array();
            foreach ($data as $key => $value)
            { $escaped[$key] = $this->xss($value); }
            return $escaped;
        }
        return htmlspecialchars($data, ENT_QUOTES);
    }

    ///////////////////////////////////////////////////////////////
    // Функция вставки данных
    // Function Insert of data
    // $table_name - Name Table
    // $fields - list of field's
    // $values - list of field's
    //
    ///////////////////////////////////////////////////////////////

    public function insert($table_name, $fields, $values, $debug = false)
    {
        if ((count($fields)) == (count($values)))
        {
            // Защита от xss-атаки
            $esc_ = Array();
            foreach ($values as $key_ => $value_)
            {
                $esc_[$key_] = $this->_strip($value_);
            }
            // Защита от ошибок экранирования
            $values = $esc_;
            $values = $this->xss($values);
            $return_where = "(";
            //
            for ($i = 0; $i < count($fields); $i++)
            {
                if ((strpos($fields[$i], "(") === false) && ($fields[$i] != "*"))
                    $fields[$i] = "`".$fields[$i]."`";
                if ((strpos($values[$i], "(") === false) && ($values[$i] != "*"))
                    $values[$i] = "'".$values[$i]."'";

                $return_where .= "(`".$fields[$i]."` = '".$values[$i]."')";
                if ($i < count($fields)-1) $return_where .= " and ";
            }
            $return_where = ")";

            $fields = implode(",", $fields);
            $values = implode(",", $values);

            $table_name = $this->Connect->pref.$table_name;

            $query = "Insert into `$table_name` ($fields) Values ($values)";

            if ($debug)
                return $query;
            else
            {
                $dt = $this->mysqli->query($query);

                return true;
            }
        }
        else return false;
    }

    ///////////////////////////////////////////////////////////////
    // Функция удаления записи по id
    // Function Delete of data - deleteRecord
    // $table_name - Name Table
    // $id - number delete record
    // $debug - отладчик кода
    //
    ///////////////////////////////////////////////////////////////
    public function deleteRecord($table_name, $id = '', $debug = false)
    {
        $table_name = $this->Connect->pref.$table_name;
        if (empty($id))
            $query = "Delete from `$table_name`";
        else
            $query = "Delete from `$table_name` Where (`id` =".$id.")";
        $value = $this->mysqli->query($query);
        if (!$debug)
            return $value;
        else
            return $query;
    }

    ///////////////////////////////////////////////////////////////
    // Удаление записи с условием Where
    // Function Delete of data - deleteRecordWhere
    // $table_name - Name Table
    // $id - number delete record
    // $debug - отладчик кода
    //
    ///////////////////////////////////////////////////////////////
    public function deleteRecordWhere($table_name, $where, $debug = false) {
        $table_name = $this->Connect->pref.$table_name;

        if (empty($where))
            $query = "Delete from `$table_name`";
        else
            $query = "Delete from `$table_name` Where ".$where."";
        $value = $this->mysqli->query($query);
        if (!$debug)
            return $value;
        else
            return $query;
    }

    ///////////////////////////////////////////////////////////////
    // Функция обновления данных с отладкой 
    // Function Update of data
    // $table_name - Name Table
    // $fields - list of field's
    // $values - list of field's
    //
    ///////////////////////////////////////////////////////////////
    public function update($table_name, $fields, $values, $where, $debug = false)
    {
        if ((count($fields)) == (count($values)))
        {
            $esc_ = Array();
            foreach ($values as $key_ => $value_)
            {
                $esc_[$key_] = $this->_strip($value_);
            }
            $values = $esc_;
            $values = $this->xss($values);

            for ($i = 0; $i < count($fields); $i++)
            {
                if ((strpos($fields[$i], "(") === false) && ($fields[$i] != "*"))
                    $fields[$i] = "`".$fields[$i]."`";
                if ((strpos($values[$i], "(") === false) && ($values[$i] != "*"))
                    $values[$i] = "'".$values[$i]."'";
            }

            $table_name = $this->Connect->pref.$table_name;
            $query = "Update `$table_name` set ";

            for ($i = 0; $i < count($fields); $i++)
            {
                $query.= ''.$fields[$i].'='.$values[$i].'';
                if ($i < (count($fields)-1))
                    $query.=',';
            }

            $query.= ' Where ('.$where.');';

            //if (!$result_set) return false;
            //else return true;
            if ($debug)
                return $query;
            else {
                $this->mysqli->query($query);
                return true;
            }
        }
        else return false;
    }

    //////////////////////////////////////////////////////////////////
    // count_sql_rec функция количества записей
    //  1. sql - sql-запрос
    //  2. debug - отладчик
    //////////////////////////////////////////////////////////////////
    public function count_sql_rec($sql, $debug = false)
    {
        if (isset($debug) && ($debug)) {
            return $sql;
        }
        else
        {
            $result_set = $this->mysqli->query($sql);
            if (isset($result_set) && (!empty($result_set)))
                return $result_set->num_rows;
            else
                return false;
        }
    }

    //////////////////////////////////////////////////////////////////
    // count_rec функция количества записей
    //  table_name - название таблицы
    //  where - условия
    //////////////////////////////////////////////////////////////////

    public function count_rec($table_name, $where = '', $debug = false)
    {

        $select = 'Select * from `'.$this->Connect->pref.$table_name.'` ';
        if (!empty($where)) $select.= $where;
        $select.= ';';

        if (isset($debug) && ($debug)) {
            return $select;
        }
        else
        {
            $result_set = $this->mysqli->query($select);
            if (isset($result_set) && (!empty($result_set)))
                return $result_set->num_rows;
            else
                return "0";
        }
    }

    ///////////////////////////////////////////////////////////////
    // Function calling stored procedure
    //   name_procedure - name stored procedure.
    //
    ///////////////////////////////////////////////////////////////
    public function exec_procedure($name_procedure)	{
        if (empty($name_procedure)) return "Error";
        else {
            $pro = $this->mysqli->prepare("CALL ".$this->Connect->pref.$name_procedure);
            $data = $pro->execute();
            return $data;
        }
    }

    /////////////////////////////////////////////////////////////////
    // Function get result of field
    // Функция возврата единичного значения поля
    //
    // параметры:
    //    table_name - имя таблицы
    //    field_name - поле возврата
    //    where - условия выбора
    //
    ////////////////////////////////////////////////////////////////
    public function get_field($table_name, $field_name, $where, $debug = false) {
        if ((empty($table_name)) || (empty($field_name))) {
            exit;
            return "Error: Имя таблицы и возвращаемое значение поля пусто!";
        } else {
            $select = "Select `$field_name` from `".$this->Connect->pref.$table_name."` ";
            if (($where) && (!empty($where))) $select .= " Where ".$where;

            if ($debug) {
                return $select;
            } else {
                $result_set = $this->mysqli->query($select);
                //if (is_array($result_set)) {
                if (!empty($result_set)) {
                    //if ((!empty($result_set)) && (count($result_set) > 0)) {
                    foreach ($result_set as $datas) {
                        $value = $datas;
                    }

                    return $value[$field_name];
                } else
                    return false;

            }
        }
    }

    /////////////////////////////////////////////////////////////////
    // Function get_random_record
    // Функция возврата случайного значения из поля field_name
    //
    // параметры:
    //    table_name - имя таблицы
    //    field_name - поле возврата
    //    where - условия данных
    //
    ////////////////////////////////////////////////////////////////
    public function get_random_record($table_name, $field_name, $where) {
        if ((empty($table_name)) || (empty($field_name))) {
            exit;
            return "Error: This name table or value clear!";
        } else {
            if (is_array($field_name)) {
                $strfield = "";
                $i = 0;
                foreach ($field_name as $field) {
                    if ($i > 0) $strfield .=", ";
                    $strfield.= $field;
                    $i++;
                }
                $select = "Select $strfield from `".$this->Connect->pref.$table_name."` ";
            } else
                $select = "Select `$field_name` from `".$this->Connect->pref.$table_name."` ";
            if (($where) && (!empty($where))) $select .= " Where ".$where." ";
            $select .= " Order By Rand()";
            $select .= " Limit 1;";

            $result_set = $this->mysqli->query($select);
            foreach ($result_set as $datas)
                return $datas;
        }
    }

    ///////////////////////////////////////////////////////////////
    // Function DistinctSelect
    //
    // table_name - name table
    // field - field for input data
    // where - list all if of execute sql
    // order - list sort fields
    // up - sort of fields
    // limit - amount look record's
    //
    ///////////////////////////////////////////////////////////////
    public function DistinctSelect($table_name, $field, $where = "",
                                   $order = "", $up = true, $limit = "",
                                   $debug = false)
    {
        if (($table_name) && ($field) && (!empty($table_name)) && (!empty($field))) {
            $sql = "Select DISTINCT ";

            $sql .= "`".$field."` from `".$this->Connect->pref.$table_name."`";


            if (($where) && (!empty($where)))
                $sql .= " Where (".$where.")";
            if (($order) && (!empty($order))) {
                $sql .= ' ORDER BY '.$order;
                if ($up)
                    $sql .= ' ASC';
                else
                    $sql .= ' DESC';
            }

            if (($limit) && (!empty($limit))) $sql .= ' LIMIT '.$limit;
            $sql .= ';';

            if ($debug)
                return $sql;
            else {

                $result_set = $this->mysqli->query($sql);
                if ($result_set) {
                    $data = Array();
                    while ($row = $result_set->fetch_assoc()) {
                        $data[$colvo] = $this->_destrip($row);
                        $colvo++;
                    }

                    $result_set->close();
                    return $data;
                } else return false;
            }
        } else
            return false;
    }

    ///////////////////////////////////////////////////////////////
    // Open function select
    // Функция Выборки данных
    // parametr's:
    // 1. table_name - name table
    // 2. fields - list all fields
    // 3. where - list all if of execute sql
    // 4. order - list sort fields
    // 5. up - sort of fields
    // 6. limit - amount look record's
    // 7. debug - mode text-sql
    ////////////////////////////////////////////////////////////////
    public function select($table_name, $fields, $where = "",
                           $order = "", $up = true,
                           $limit = "", $debug = false)
    {
        $select = 'Select ';
        if (is_array($fields))
        {
            $colvo = 0;
            foreach ($fields as $field)
            {
                if ($colvo > 0) $select .= ',';
                if ($field == '*') $select .= $field;
                else $select .= '`'.$field.'`';
                $colvo++;
            }

            $colvo = 0;
            //if ($this->Connect->CodeLicense === base64_encode(base64_encode(php_uname())))
                $select .= ' from `'.$this->Connect->pref.$table_name.'`';
            //else
            //    $select .= ' from `'.$this->Data_Config->pref.$table_name.'`';

            if (($where) && (!empty($where))) $select .= ' WHERE ('.$where.')';
            //if (($order) && (!empty($order))) {
            if ($order) {
                $select .= ' ORDER BY '.$order;
                if ($up)
                    $select .= ' ASC';
                else
                    $select .= ' DESC';

            }
            if (($limit) && (!empty($limit))) $select .= ' LIMIT '.$limit;
            $select .= ';';

            if ($debug)
            {
                return $select;
            }
            else {
                $result_set = $this->mysqli->query($select);
                if ($result_set) {
                    $data = Array();
                    /*foreach ($result_set as $row)
                    {
                       $data[$colvo] = $row;
                       $colvo++;
                    }*/
                    while ($row = $result_set->fetch_assoc()) {
                        $data[$colvo] = $this->_destrip($row);
                        $colvo++;
                    }

                    $result_set->close();
                    return $data;
                }
                else return false;
            }
        }
    }

    ////////////////////////////////////////////////////////////////
    // Destructor. Delete all object's
    ////////////////////////////////////////////////////////////////
    public function __destruct()
    {
        unset($this->Connect);
        unset($this->current_date);
        unset($this->mysqli);
    }
}
?>