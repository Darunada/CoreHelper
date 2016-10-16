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
    } catch (Dotenv\Exception\InvalidPathException $e) {
        // do nothing
    }

    autoloadPlatform();
}

