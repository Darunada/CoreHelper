<?php

namespace CoreHelper\Enums;

interface Enum
{
    /**
     * @param $name
     * @param bool $strict
     * @return mixed
     */
    public static function is_valid_name($name, $strict = false);

    /**
     * @param $value
     * @return mixed
     */
    public static function is_valid_value($value);

    /**
     * @param $name
     * @param array $selected
     * @param array $params
     * @param string $format
     * @param array $exclude
     * @return mixed
     *
     */
    public static function generate_select(
        $name,
        $selected = array(),
        $params = array(),
        $format = '',
        $exclude = array()
    );

    /**
     * @param $type_id
     * @return mixed
     */
    public static function exists($type_id);

    /**
     * @return mixed
     */
    public static function get_keys();

    /**
     * @return mixed
     */
    public static function dropdown();

    /**
     * @return mixed
     */
    public static function get_all();

    /**
     * @param $name
     * @return mixed
     */
    public static function get_value($name);

    /**
     * @param $value
     * @return mixed
     */
    public static function get_name($value);
}
