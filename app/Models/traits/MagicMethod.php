<?php
namespace App\Models\traits;

trait MagicMethod {

    /*
     * магические методы __call для взаимных вызовов статических и динамических методов
     *   $name - имя метода
     *   $arguments - аргументы метода
     */
    public function __call($name, $arguments)
    {
        // Вызов динамического метода через статический
        if (method_exists($this, 'dynamic_' . $name)) {
            return call_user_func_array([$this, 'dynamic_' . $name], $arguments);
        }

        // Вызов статического метода через динамический
        if (method_exists(self::class, 'static_' . $name)) {
            return call_user_func_array([self::class, 'static_' . $name], $arguments);
        }

        throw new Exception("Метод $name не найден");
    }

    /*
     * Магические методы статические
     *
     *   name - имя метода
     *   arguments - аргументы метода
     */
    public static function __callStatic($name, $arguments)
    {
        // Вызов статического метода через динамический
        if (method_exists(self::class, 'dynamic_' . $name)) {
            return call_user_func_array([self::class, 'dynamic_' . $name], $arguments);
        }

        // Вызов динамического метода через статический
        $instance = new self();
        if (method_exists($instance, 'dynamic_' . $name)) {
            return call_user_func_array([$instance, 'dynamic_' . $name], $arguments);
        }

        throw new Exception("Метод $name не найден");
    }
}