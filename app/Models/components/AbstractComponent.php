<?php
namespace App\Models\Components;

abstract class AbstractComponent
{
    abstract protected function setValue($Name_Field, $Value);
    abstract protected function getValue($Field);
    abstract protected function PrintComponent($prefix);
    abstract protected function TestClass();

    public function printOut() {
        print $this->getValue() . "\n";
    }
}