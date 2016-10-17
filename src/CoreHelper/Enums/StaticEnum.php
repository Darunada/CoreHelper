<?php

namespace CoreHelper\Enums;

abstract class StaticEnum implements Enum
{
    private static $constCache = NULL;

    private static function get_constants()
    {
        $class = get_called_class();
        if (self::$constCache === NULL) {
            self::$constCache = array();
        }
        if (!isset(self::$constCache[$class])) {
            self::$constCache[$class] = array();
            $reflect                  = new \ReflectionClass(get_called_class());
            self::$constCache[$class] = $reflect->getConstants();
        }
        return self::$constCache[$class];
    }

    public static function is_valid_name($name, $strict = false)
    {
        $constants = self::get_constants();

        if ($strict) {
            return array_key_exists($name, $constants);
        }

        $keys = array_map('strtolower', array_keys($constants));
        return in_array(strtolower($name), $keys);
    }

    public static function is_valid_value($value)
    {
        $values = array_values(self::get_constants());
        return in_array($value, $values, $strict = true);
    }

    public static function generate_select($name, $selected = array(), $params = array(), $format
    = '', $exclude = array())
    {
        if (!is_array($selected)) {
            $selected = array($selected);
        }

        $constants = self::get_constants();
        echo "<select name=\"$name\"";
        foreach ($params as $key => $val) {
            echo " $key=\"$val\"";
        }
        echo ">";

        $functions = explode('|', $format);
        foreach ($constants as $name => $val) {
            if ($name !== '__DEFAULT') {
                if (in_array($val, $exclude)) continue;
                echo "<option value=\"$val\" ";
                if (in_array($val, $selected)) echo "selected=\"selected\"";
                echo ">";

                foreach ($functions as $function) {
                    // has param?
                    $matches = array();
                    $param   = null;
                    preg_match('/\[(.*?)\]/i', $function, $matches);
                    if (isset($matches[1])) {
                        $param = $matches[1];
                    }
                    $function = preg_replace('/\[.*?\]/i', '', $function);
                    if (is_callable($function)) {
                        if ($param) {
                            $name = $function($name, $param);
                        } else {
                            $name = $function($name);
                        }
                    }
                }

                echo $name;

                echo "</option>";
            }
        }

        echo '</select>';
    }

    public static function exists($type_id)
    {
        $const = self::get_all();

        foreach ($const as $k => $v) {
            if ($type_id == $v) {
                return true;
            }
        }

        return false;
    }

    public static function get_keys()
    {
        return arrya_keys(self::get_all());
    }

    public static function dropdown()
    {
        $class  = get_called_class();
        $ref    = new ReflectionClass($class);
        $consts = $ref->getConstants();
        $array  = array();
        foreach ($consts as $const) {
            $name          = strtolower($class).'.'.$const;
            $lang          = lang($name);
            $array[$const] = $name == $lang ? self::get_name($const) : $lang;
        }
        return $array;
    }

    public static function get_all()
    {
        return self::get_constants();
    }

    public static function get_value($name)
    {
        $const = self::get_all();
        return isset($const[$name]) ? $const[$name] : NULL;
    }

    public static function get_name($value)
    {
        $const = array_flip(self::get_all());
        return isset($const[$value]) ? $const[$value] : NULL;
    }
}
if (!function_exists('remove_underscores')) {

    function remove_underscores($str)
    {
        return str_replace('_', ' ', $str);
    }
}