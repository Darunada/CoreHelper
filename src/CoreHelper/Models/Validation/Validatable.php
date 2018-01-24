<?php
/**
 * Created by PhpStorm.
 * User: Lea
 * Date: 10/16/2016
 * Time: 8:47 PM
 */

namespace CoreHelper\Models\Validation;

/**
 *
 * @author Lea
 */
abstract class Validatable
{
    /**
     * This was added so that a class can both
     * extend Validatable and use the Properties trait
     * @var ObjectValidator
     */
    protected $_object_validator  = NULL;
    /**
     * @var null
     */
    protected $_validation_errors = NULL;

    /**
     * @return mixed
     */
    abstract protected function validate();

    /**
     *
     * @param bool $for_display
     * @return array|string
     */
    final public function validation_errors($for_display = false)
    {
        if (isset($this->_validation_errors)) {
            if ($for_display) {
                $errors = array();
                foreach ($this->_validation_errors as $field => $error) {
                    foreach ($error as $type => $message) {
                        $errors[] = ucfirst($message);
                    }
                }

                return implode(', ', $errors);
            } else {
                return $this->_validation_errors;
            }
        } else return array();
    }

    public function set_params($params)
    {
        if (!empty($params)) {
            foreach ($params as $key => &$param) {
                if (property_exists($this, $key)) {
                    $this->$key = $param;
                }
            } unset($param);
        }
    }

    /**
     *
     * @return bool
     */
    final public function is_valid()
    {
        $this->validate();
        return $this->run_validator();
    }

    /**
     *
     * @return bool
     */
    final public function is_invalid()
    {
        return !$this->is_valid();
    }

    /**
     *
     * @param mixed $attribute
     * @param string $name
     * @param mixed $value
     * @return ObjectValidator
     */
    final protected function validates($attribute, $name = '', $value = null)
    {
        if (empty($name)) $name = $attribute;
        if ($value === null) {
            $value = $this->{$attribute};
        }
        if (!isset($this->_object_validator) || $this->_object_validator == null) {
            $this->_object_validator = new ObjectValidator();
        }

        $this->_object_validator->set_attribute($name, $value);

        return $this->_object_validator;
    }

    final public function run_validator()
    {
        $errors = array();
        $valid  = $this->_object_validator->run($errors);

        unset($this->_object_validator);
        unset($this->_validation_errors);

        if (!empty($errors)) {
            $this->_validation_errors = $errors;
        }

        return $valid;
    }
}