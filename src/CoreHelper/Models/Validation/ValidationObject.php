<?php

namespace CoreHelper\Models\Validation;

/**
 *
 */
interface ValidationObject
{

    /**
     * All validation objects should have a constructor that takes either an ID or an array of values
     * If an ID is passed to the constructor the object will be loaded from the database
     * If an array is passed to the constructor then those values will be used to populate the object
     * @param $params
     */
    public function __construct($params);

    /**
     * Used to set any value in the object.
     * $params should be an array with keys that corresponds to properties of the object
     * @param $params
     */
    public function set_params($params);

    /**
     * When called the object will either be saved to the database or an exception will be thrown
     * stating why the object could not be saved.
     */
    public function save();
}