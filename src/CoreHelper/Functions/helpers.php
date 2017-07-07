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

            if(preg_match('/\.\.?/', $module)) {
                continue;
            }

            if($autoloadModules == null || in_array($module, $autoloadModules)) {

                $autoload = function ($class_name) use ($platform, $module) {
                    // import libs
                    if (file_exists(FCPATH . "sites/$platform/$module/libraries/$class_name.php")) {
                        require(FCPATH . "sites/$platform/$module/libraries/$class_name.php");
                    }

                    // enums
                    if (file_exists(FCPATH . "sites/$platform/$module/libraries/enums/$class_name.php")) {
                        require(FCPATH . "sites/$platform/$module/libraries/enums/$class_name.php");
                    }
                };

                spl_autoload_register($autoload);
            }

        }
    }
}

if(defined('FCPATH') && !function_exists('autoloadFolder')) {
    /**
     * Instantiates spl autoloaders for all libraries in platform modules
     *
     * @param string $folder the folder name (no slash before or after)
     * @param string $platform The name of a platform folder in sites/
     * @param string $module The module name
     */
    function autoloadFolder($folder, $platform='local', $module='main') {

        $paths = [
            FCPATH."sites/$platform",
            FCPATH."sites/$platform/$module",
            FCPATH."sites/$platform/$module/$folder"
        ];

        foreach($paths as $path) {
            if (!file_exists($path)) {
                return;
            }
        }


        $autoload = function ($class_name) use($platform, $module, $folder) {
            // autoload folder
            if (file_exists(FCPATH . "sites/$platform/$module/$folder/$class_name.php")) {
                require(FCPATH . "sites/$platform/$module/$folder/$class_name.php");
            }
        };

        spl_autoload_register($autoload);
    }
}

if(!function_exists('remove_underscores')) {
    /**
     * replace all underscores in $word with $sub
     *
     * @param $word
     * @param string $sub
     * @return mixed
     */
    function remove_underscores($word, $sub = ' ')
    {
        return str_replace('_', $sub, $word);
    }
}

if(!function_exists('is_unsigned_integer')) {
    /**
     * Checks if the $value passed is both an integer and is greater than or equal to zero.
     * Created as a helper function when the Validatable class was ported to MM's core 2.
     * @author Alan <alan@modernizedmedia.com>
     * @param int $value TRUE will be returned if (is_int($value) && $value >= 0)
     * @return bool (is_int($value) && $value >= 0)
     */
    function is_unsigned_integer($value)
    {
        return (is_int($value) && $value >= 0);
    }
}


if ( ! function_exists('value'))
{
    /**
     * Return the default value of the given value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}