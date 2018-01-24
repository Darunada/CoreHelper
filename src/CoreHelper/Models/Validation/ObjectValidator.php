<?php
/**
 * Created by PhpStorm.
 * User: Lea
 * Date: 10/16/2016
 * Time: 8:16 PM
 */

namespace CoreHelper\Models\Validation;

/**
 * Class ObjectValidator
 * @package CoreHelper\Models\Validation
 */
class ObjectValidator
{
    /**
     * @var null
     */
    private $name        = null;
    /**
     * @var null
     */
    private $value       = null;
    /**
     * @var array
     */
    private $errors      = array();
    /**
     * @var array
     */
    private $allow_null  = array();
    /**
     * @var array
     */
    private $allow_empty = array();

    /**
     * ObjectValidator constructor.
     */
    public function __construct()
    {

    }

    /**
     * @param $name
     * @param $value
     */
    public function set_attribute($name, $value)
    {
        $this->name  = $name;
        $this->value = $value;
    }

    /**
     * @param $errors
     * @return bool
     */
    public function run(&$errors)
    {
        foreach ($this->allow_null as $field => $value) {
            if ($value === null && array_key_exists($field, $this->allow_null)) {
                unset($this->errors[$field]);
            }
        }

        foreach ($this->allow_empty as $field => $value) {
            if ($value === null && array_key_exists($field, $this->allow_null)) {
                // nothing to do here, it's already allowed to be null
            } else if ($value === "") {
                // no more errors, "" is allowed
                unset($this->errors[$field]);
            }
        }
        if ($this->value === null && !$this->allow_null) {
            $this->errors = array(); // reset the errors, none of the validations apply if the value is null
            $this->log_error('null', "$this->name must not be null.");
        } else if ($this->value === "" && !$this->allow_empty) {
            $this->errors = array(); // reset the errors, none of the validations apply if the value is empty
            $this->log_error('empty', "$this->name must not be empty.");
        }

        $errors = $this->errors;
        return empty($errors);
    }

    /**
     *
     * @param bool $opt
     * @return \Object_Validator
     */
    public function allow_empty($opt = true)
    {
        if ($opt == true) {
            $this->allow_empty[$this->name] = $this->value;
        }
        return $this;
    }

    /**
     *
     * @param bool $opt
     * @return \Object_Validator
     */
    public function allow_null($opt = true)
    {
        if ($opt == true) {
            $this->allow_null[$this->name] = $this->value;
        }
        return $this;
    }

    /**
     *
     * @param int $length
     * @return \Object_Validator
     */
    public function min_length($length)
    {
        if (strlen($this->value) < abs($length)) {
            $this->log_error('length', "$this->name must be at least $length characters long.");
        }
        return $this;
    }

    /**
     *
     * @param int $length
     * @return \Object_Validator
     */
    public function max_length($length)
    {
        if (strlen($this->value) > abs($length)) {
            $this->log_error('length', "$this->name must be fewer than $length characters long.");
        }
        return $this;
    }

    /**
     *
     * @param string $pattern
     * @return \Object_Validator
     */
    public function matches($pattern)
    {
        $match = @preg_match($pattern, $this->value);

        if ($match === false) {
            // pattern is not a regex, so check string equality
            if ($this->value != $pattern) {
                $this->log_error('match', "$this->name is of invalid format.");
            }
        } else {
            // preg_match worked
            if ($match == 0) { // no match
                $this->log_error('match', "$this->name is of invalid format.");
            }
        }
        return $this;
    }

    /**
     *
     * @param mixed $pattern
     * @return \Object_Validator
     */
    public function strictly_matches($pattern)
    {
        if ($this->value === $pattern) {
            $this->log_error('match', "$this->name is of invalid format.");
        }
        return $this;
    }

    /**
     *
     * @param string $pattern
     * @return \Object_Validator
     */
    public function matches_regex($pattern)
    {
        $match = @preg_match($pattern, $this->value);
        if ($match == 0) { // no match
            $this->log_error('match', "$this->name is of invalid format.");
        }
        return $this;
    }

    /**
     *
     * @return \Object_Validator
     */
    public function is_email()
    {
        if (!filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
            $this->log_error('email', "$this->name must be a valid email address.");
        }
        return $this;
    }

    /**
     *
     * @return \Object_Validator
     */
    public function is_boolean()
    {
        if (!is_bool($this->value)) {
            $this->log_error('type', "$this->name must be boolean");
        }
        return $this;
    }

    /**
     *
     * @param string $format
     * @return \Object_Validator
     */
    public function is_date($format = 'Y-m-d H:i:s')
    {
        if ($this->value != date($format, strtotime($this->value))) {
            $this->log_error('format', "$this->name must be a date in the format '$format'");
        }
        return $this;
    }

    /**
     * Checks if a value is both an integer and greater than or equal to zero.
     *
     * @return \Object_Validator
     */
    public function is_unsigned_integer()
    {
        if (is_unsigned_integer($this->value)) {
            return $this;
        } else {
            $this->log_error('type', "$this->name must be an integer greater than 0.  It's current value is $this->value");
            return $this;
        }
    }

    /**
     *
     * @return \Object_Validator
     */
    public function is_integer()
    {
        if (!is_int($this->value)) {
            $this->log_error('type', "$this->name must be an integer");
        }
        return $this;
    }

    /**
     *
     * @return \Object_Validator
     */
    public function is_number()
    {
        if (!is_numeric($this->value)) {
            $this->log_error('type', "$this->name must be numeric");
        }
        return $this;
    }

    /**
     *
     * @return \Object_Validator
     */
    public function is_required()
    {
        if (empty($this->value)) {
            $this->log_error('required', "$this->name is required");
        }
        return $this;
    }

    /**
     *
     * @param bool $param
     * @param string $message
     * @return \Object_Validator
     */
    public function param_is_true($param, $message = "")
    {
        if ($param !== TRUE) {
            if (empty($message)) {
                $message = "$this->name has a param error";
            }
            $this->log_error('param', $message);
        }
        return $this;
    }

    /**
     *
     * @param bool $param
     * @param string $message
     * @return \Object_Validator
     */
    public function param_is_false($param, $message = "")
    {
        if ($param !== FALSE) {
            if (empty($message)) {
                $message = "$this->name has a param error";
            }
            $this->log_error('param', $message);
        }
        return $this;
    }

    /**
     *
     * @param type $model
     * @param array $except
     * @param string $column
     * @return \Object_Validator
     */
    public function is_unique($model, $except = array(), $column = NULL)
    {
        if ($column == null) $column = $this->name;

        $test = $model->get_by(array($column => $this->value));
        if (!empty($test)) {
            foreach ($except as $key => $value) {
                if ($test->$key == $value) {
                    // matches an exception
                    return $this;
                }
            }

            $this->log_error('unique', "$this->name must be unique.  '$this->value' is already taken.");
        }

        return $this;
    }

    /**
     *
     * @param mixed $number
     * @param string $name
     * @return \Object_Validator
     */
    public function is_greater_than($number, $name = '')
    {
        if (!($this->value > $number)) {
            if (empty($name)) $name = $number;
            $this->log_error('greater', "$this->name must be greater than $name");
        }
        return $this;
    }

    /**
     *
     * @param mixed $number
     * @param string $name
     * @return \Object_Validator
     */
    public function is_less_than($number, $name = '')
    {
        if (!($this->value < $number)) {
            if (empty($name)) $name = $number;
            $this->log_error('less', "$this->name must be less than $name");
        }
        return $this;
    }

    private function log_error($type, $message)
    {
        if (!isset($this->errors[$this->name]))
            $this->errors[$this->name] = array();

        $this->errors[$this->name][$type] = $message;
    }

    /**
     * @deprecated This method should not be used in MM's core 2 as it is specific to
     * MM's core 3.
     * @return \Object_Validator
     */
    public function is_core_id()
    {
        if (strlen($this->value) != 36) {
            $this->log_error('id', "$this->name is not a core id");
        }
        return $this;
    }
}