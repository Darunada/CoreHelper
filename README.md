Core Helper
============

Provides Helper functions and things to help me out.  This is mostly for use with CodeIgniter and MM's core, but it should be useful elsewhere. 

Installation with Composer
--------------------------

```shell
curl -s http://getcomposer.org/installer | php
php composer.phar require darunada/corehelper
```

```json
"require": {
  "darunada/corehelper":"dev-master"
}
```

###Installed Libraries
The following libraries are autoloaded for use anywhere in the system.

+ [phpdotenv](https://www.github.com/vlucas/phpdotenv "vlucas/phpdotenv") .env Environment loading
+ [Carbon](https://www.github.com/briannesbitt/Carbon "briannesbitt/Carbon") Date functions
+ [Cron-expression](https://www.github.com/mtdowling/cron-expression "mtdowling/cron-expression") Cron expression parsing
+ [Monolog](https://www.github.com/monolog/monolog "monolog/monolog") Logging
+ [Inflector](https://www.github.com/doctrine/inflector "doctrine/inflector") String pluralization
+ [Stringy](https://www.github.com/danielstjules/stringy "danielstjules/stringy") String handling functions
+ [Html2Text](https://www.github.com/soundasleep/html2text "soundasleep/html2text") HTML stripping
+ [Geo-location](https:://www.github.com/anthonymartin/geo-location "anthonymartin/geo-location") Geolocation bounding box utilities
+ [phpwkhtmltopdf](https://www.github.com/mikehaertl/phpwkhtmltopdf "mikehaertl/phpwkhtmltopdf") PDF generator

###Usage
Use any of the included libraries per their documentation.  Additional functionality and wrappers will be provided.
