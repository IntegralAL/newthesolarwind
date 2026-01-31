<?php
namespace App\Models\Components;

use App\Models;
use App\Models\Class_BD;

class Combobox extends AbstractComponent
{
    private $GeneratorNumber;
    private $BD_Conn;
    private $ID;
    private $Name;
    private $Class_Component;
    private $Style_Component;
    private $TableName;
    private $Field_Combobox;
    private $Checked_item;
    private $Sorted;
    private $Ordered;
    public $Count_items;
    public $WhereCombobox;
    public $Distinct = false;
    public $Null_current;
    public $ArrayItem = Array();
    public $Debug = false;
    public $Limit = "";
    public $Param;

    function __construct() {
        $this->BD_Conn = new Class_BD;
        $this->WhereCombobox = "";
        $this->Sorted = "";
        $this->Ordered = "";
    }

    //////////////////////////////////////////////////////////
    // Метод отклика класса - метод TestClass
    //////////////////////////////////////////////////////////
    public function TestClass() {
        return 'Ответ от класса Компоненты получен!';
    }

    //////////////////////////////////////////////////////////
    // Установление значения - метод SetValue
    //    Name_Field - имя поля
    //    Value - значение поля
    //////////////////////////////////////////////////////////
    public function setValue($Name_Field, $Value) {
        if (($Name_Field) && (!empty($Name_Field )) &&
            ($Value) && (!empty($Value))) {

            switch ($Name_Field) {
                case 'ID': $this->ID = $Value; break;
                case 'Name': $this->Name = $Value; break;
                case 'Class_Component': $this->Class_Component = $Value; break;
                case 'Style_Component': $this->Style_Component = $Value; break;
                case 'TableName': $this->TableName = $Value; break;
                case 'Field_Combobox': $this->Field_Combobox = $Value; break;
                case 'Checked_item': $this->Checked_item = $Value; break;
                case 'Sorted': $this->Sorted = $Value; break;
                case 'Ordered': $this->Ordered = $Value; break;
            }
            return true;

        } else
            return false;
    }

    //////////////////////////////////////////////////////////
    // Возвращение значения - метод GetValue
    //////////////////////////////////////////////////////////
    public function getValue($Field) {
        switch ($Field) {
            case 'ID':
                return $this->ID;
                break;
            case 'Name':
                return $this->Name;
                break;
            case 'Class_Component':
                return $this->Class_Component;
                break;
            case 'Style_Component':
                return $this->Style_Component;
                break;
            case 'TableName':
                return $this->TableName;
                break;
            case 'Field_Combobox':
                return $this->Field_Combobox;
                break;
            case 'Checked_item':
                return $this->Checked_item;
                break;
            case 'Sorted':
                return $this->Sorted;
                break;
            case 'Ordered':
                return $this->Ordered;
                break;
        }
    }

    //////////////////////////////////////////////////////////
    // Вывод данных компонента в виде массива - метод GetDataCombobox
    //////////////////////////////////////////////////////////
    public function GetDataCombobox() {
        $this->ArrayItem = Array();
        $ReturnArray = Array();

        if ($this->Distinct == true)
            $All_rec = $this->BD_Conn->DistinctSelect($this->TableName,
                $this->Field_Combobox,
                $this->WhereCombobox,
                $this->Ordered,
                $this->Sorted,
                $this->Limit,
                $this->Debug);
        else
            $All_rec = $this->BD_Conn->select($this->TableName,
                Array($this->Field_Combobox),
                $this->WhereCombobox,
                $this->Ordered,
                $this->Sorted,
                $this->Limit,
                $this->Debug);
        $Arr = Array();
        if (is_Array($All_rec)) {
            foreach ($All_rec as $k => $Record)
                foreach ($Record as $key => $Fields) {
                    $id = $this->BD_Conn->get_field($this->TableName, 'id', "(".$this->Field_Combobox." = '".$Fields."')");
                    $Arr[$id] = $Fields;
                }
        } else return $All_rec;

        return $Arr;
    }

    //////////////////////////////////////////////////////////
    // Вывод компонента в виде списка - метод PrintComponent
    // prefix - префикс компонента
    // Count_items -  количество видимых пунктов
    // Multiple - множественный выбор пунктов списка
    //////////////////////////////////////////////////////////
    public function PrintComponent($prefix = 'Combobox_',
                                   $Count_items = 1,
                                   $Multiple = false) {
        $this->GeneratorNumber = new Class_String;
        $Coder = $this->GeneratorNumber->Random_text(5, false);
        $print_str = '';

        if (empty($this->ID))
            $this->ID = $Coder;
        $print_str = "<select id='".$prefix.$this->ID."' ";

        if (!empty($this->Param))
            $print_str .= " param='".$this->Param."' ";

        if (!empty($this->Name))
            $print_str .= " name='".$prefix.$this->Name."' ";

        if (!empty($this->Class_Component))
            $print_str .= " class='".$prefix.$this->Class_Component."'";

        if (!empty($this->Style_Component))
            $print_str .= " style='".$this->Style_Component."'";
        if ($Count_items > 1) $print_str .= " size=".$Count_items;
        if ($Multiple) $print_str .= " multiple ";
        $print_str.= ">";
        $All_rec = $this->TableName;

        if ($this->Null_current == true) {
            $print_str .= "<option value=''></option>";
        }

        if (is_Array($this->TableName)) {


            foreach ($All_rec as $Keys => $Fields) {
                if (is_string($Keys))
                    $print_str .= "<option value='".$Keys."'";
                else
                    $print_str .= "<option value='".$Fields."'";

                if ($Fields == $this->Checked_item) $print_str .= " selected ";

                $print_str .= ">".$Fields."</option>";
            }

        } else {

            if ($this->Distinct == true)
                $All_rec = $this->BD_Conn->DistinctSelect($this->TableName,
                    $this->Field_Combobox,
                    $this->WhereCombobox,
                    $this->Ordered,
                    $this->Sorted,
                    $this->Limit,
                    $this->Debug);
            else
                $All_rec = $this->BD_Conn->select($this->TableName,
                    Array($this->Field_Combobox),
                    $this->WhereCombobox,
                    $this->Ordered,
                    $this->Sorted,
                    $this->Limit,
                    $this->Debug);

            if (is_Array($All_rec)) {
                foreach ($All_rec as $Record)
                    foreach ($Record as $key => $Fields) {

                        if ($Fields == $this->Checked_item)
                            $checked_it = " selected ";
                        else
                            $checked_it = '';
                        $print_str .="<option $checked_it value='".$Fields."'>".$Fields."</option>";
                    }
            } else return $All_rec;
        }

        $print_str .="</select>";
        return $print_str;
    }
}
?>