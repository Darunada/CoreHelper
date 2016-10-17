<?php

/**
 * Autoload local libraries and enums globally/site-wide
 *
 * This only applies if loading through the CodeIgniter
 * environment, so check for the existence of FCPATH
 *
 * @author Lea Fairbanks
 */
if(defined('FCPATH')) {
    try {
        $dotenv = new Dotenv\Dotenv(FCPATH);
        $dotenv->load();

        $env = getenv('ENVIRONMENT', 'production');
        $env_file = ".env.$env";
        if(file_exists(FCPATH.$env_file)) {
            $dotenv->overload($env_file);
        }

    } catch (Dotenv\Exception\InvalidPathException $e) {
        // do nothing
    }

    autoloadPlatform();
}

