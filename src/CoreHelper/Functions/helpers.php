<?php

use Stringy\StaticStringy as Str;

if (!function_exists('env')) {
    /**
     * Gets the value of an environment variable. Supports boolean, empty and null.
     *
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    function env($key, $default = null)
    {
        $value = getenv($key);

        if ($value === false) {
            return value($default);
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;

            case 'false':
            case '(false)':
                return false;

            case 'empty':
            case '(empty)':
                return '';

            case 'null':
            case '(null)':
                return;
        }

        if (Str::startsWith($value, '"') && Str::endsWith($value, '"')) {
            return substr($value, 1, -1);
        }

        return $value;
    }
}

if(defined('FCPATH') && !function_exists('autoloadPlatform')) {
    /**
     * Instantiates spl autoloaders for all libraries in platform modules
     *
     * @param string $platform The name of a platform folder in sites/
     * @param array|null $autoloadModules an array of module names in sites/$platform/
     */
    function autoloadPlatform($platform='local', $autoloadModules=null) {

        if(!file_exists(FCPATH."sites/$platform")) {
            return;
        }

        $modules = scandir(FCPATH."sites/$platform");

        foreach($modules AS $module) {
            if($autoloadModules == null || in_array($module, $autoloadModules)) {

                $autoload = function ($class_name) use ($module) {
                    // import libs
                    if (file_exists(FCPATH . 'sites/local/' . $module . '/libraries/' . $class_name . '.php')) {
                        require(FCPATH . 'sites/local/import/libraries/' . $class_name . '.php');
                    }

                    // enums
                    if (file_exists(FCPATH . 'sites/local/import/libraries/enums/' . $class_name . '.php')) {
                        require(FCPATH . 'sites/local/import/libraries/enums/' . $class_name . '.php');
                    }
                };

                spl_autoload_register($autoload);
            }

        }
    }
}