<?php

namespace CoreHelper\Enums;

interface Enum
{
    public static function is_valid_name($name, $strict = false);

    public static function is_valid_value($value);

    public static function generate_select(
        $name,
        $selected = array(),
        $params = array(),
        $format = '',
        $exclude = array()
    );

    public static function exists($type_id);

    public static function get_keys();

    public static function dropdown();

    public static function get_all();

    public static function get_value($name);

    public static function get_name($value);
}