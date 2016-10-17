<?php
namespace CoreHelper\Enums;

/**
 * Class Dynamic_Enum
 * @package CoreHelper\Enums
 */
abstract class Dynamic_Enum implements Enum
{
    private static $singletons = array();
    private $enum_values       = array();
    private $model             = array();
    private $param             = null;

    public static function singleton($param = null)
    {
        $class = get_called_class();

        if (!is_array(self::$singletons)) self::$singletons = array();
        if (!array_key_exists($class, self::$singletons)) {
            self::$singletons[$class] = array();
            if (!array_key_exists($param, self::$singletons[$class])) {
                self::$singletons[$class][$param] = new $class($param);
            }
        }
        return self::$singletons[$class][$param];
    }

    protected function __construct()
    {
        $this->enum_values = array();
    }

    protected function set_values($values)
    {
        if (!is_array($values)) {
            $values = array($values);
        }
        $this->enum_values = $values;
    }

    protected function set_model($model)
    {
        $this->model[get_called_class()] = $model;
    }

    function __get($name)
    {
        return $this->enum_values[$name]; //or throw Exception?
    }

    public function get_constants()
    {
        return $this->enum_values;
    }

    public function is_valid_name($name, $strict = false)
    {
        $constants = $this->get_constants();

        if ($strict) {
            return array_key_exists($name, $constants);
        }

        $keys = array_map('strtolower', array_keys($constants));
        return in_array(strtolower($name), $keys);
    }

    public function is_valid_value($value)
    {
        $values = array_values($this->get_constants());
        return in_array($value, $values, $strict = true);
    }

    public function generate_select($name, $selected = array(), $params = array(), $format
    = '', $exclude = array())
    {
        if (!is_array($selected)) {
            if (empty($selected)) $selected = array();
            else $selected = array($selected);
        }

        $constants = $this->get_constants();
        echo "<select name=\"$name\"";
        foreach ($params as $key => $val) {
            echo " $key=\"$val\"";
        }
        echo ">";

        if (empty($selected)) {
            echo "<option></option>";
        }

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

    public function add($name, $default = false, $company_id = null, $user_id = null)
    {
        if ($this->model[get_called_class()] != null) {
            if (!$this->is_valid_name($name)) {
                $entry = array(
                    'name' => $name,
                );

                if ($default) $entry['default']    = $default;
                if ($company_id) $entry['company_id'] = $company_id;
                if ($user_id) $entry['user_id']    = $user_id;
                $this->model[get_called_class()]->insert($entry);
            }
        }
    }

    public function get_name($value)
    {
        return array_search($value, $this->get_constants());
    }
}