<?php

namespace CoreHelper\Pdf;

use mikehaertl\wkhtmlto\Pdf;

/**
 * Created by PhpStorm.
 * User: Lea
 * Date: 6/12/2017
 * Time: 4:49 PM
 */
class PdfGenerator extends Pdf
{
    const LANDSCAPE = 'landscape';
    const PORTRAIT = 'portrait';

    /**
     * These settings were determined through trial and error to work for both platforms
     * @var array
     */
    private $defaultOptions = [
        'no-outline',
        'encoding' => 'UTF-8',
        // Default page options
        'enable-smart-shrinking',
        'ignoreWarnings' => true,
        'commandOptions' => array(
            'escapeArgs' => true,
            'procOptions' => array(
                // this option to true is recommended, but it's causing a crash on windows; seems fine either way on linux
                'bypass_shell' => false,
                // Try this if you get weird errors
                'suppress_errors' => true,
            ),
        )
    ];

    /**
     * Pass in global options as an array
     * Default options will be used if passing null or a string (url, html, or filename)
     * @param array|string $options global options for wkhtmltopdf or page URL, HTML or PDF/HTML filename
     */
    public function __construct($options = null)
    {
        parent::__construct($options);

        // set default options if none were passed
        if (!is_array($options)) {
            if (substr(PHP_BINDIR, 0, 1) == '/') {
                // on the live server, a special case :-(
                $this->defaultOptions['binary'] = env('WKHTMLTOPDF_BINARY', '/usr/local/bin/wkhtmltopdf');
            }

            $this->setOptions($this->defaultOptions);
        }
    }

    /**
     * Set orientation of the rendered PDF
     * Select from constants on this object
     * @param $orientation
     */
    public function setOrientation($orientation)
    {
        if ($orientation == self::LANDSCAPE || $orientation == self::PORTRAIT) {
            $this->setOptions(['orientation' => $orientation]);
        }
    }

    /**
     * enable low quality mode to save time or disk space
     */
    public function lowQuality()
    {
        $this->setOptions(['lowquality']);
    }

    /**
     * @param $css Absolute file path to css style sheet.
     */
    public function styleSheet($css)
    {
        $this->setOptions(['user-style-sheet' => $css]);
    }
}