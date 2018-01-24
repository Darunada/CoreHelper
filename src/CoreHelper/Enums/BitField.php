<?php

namespace CoreHelper\Enums;

/**
 * Class BitField
 * @package CoreHelper\Enums
 */
abstract class BitField extends StaticEnum
{
    /**
     * @param $field
     * @param $flag
     */
    static public function set(&$field, $flag)
    {
        $field |= $flag;
    }

    /**
     * @param $field
     * @param $flag
     */
    static public function clear(&$field, $flag)
    {
        $field &= ~$flag;
    }

    /**
     * @param $field
     * @param $flag
     * @return bool
     */
    static public function is_set(&$field, $flag)
    {
        return ($field & $flag) == $flag;
    }

    /**
     * @param $field
     * @param callable|null $formatter
     * @return array
     * @throws \ReflectionException
     */
    static public function to_array($field, Callable $formatter = null)
    {
        $to_return = array();
        foreach (self::get_all() as $name => $value) {
            if (self::is_set($field, $value)) {
                $str = $name;
                if ($formatter != null) {
                    $str = $formatter($name, $value);
                }
                $to_return[] = $str;
            }
        }

        return $to_return;
    }
}
