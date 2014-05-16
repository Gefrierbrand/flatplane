<?php

/*
 * Copyright (C) 2014 Nikolai Neff <admin@flatplane.de>.
 *
 * This file is part of Flatplane.
 *
 * Flatplane is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or(at your option) any later version.
 *
 * Flatplane is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Flatplane.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace de\flatplane\controller;

use de\flatplane\documentContents\Document;
use de\flatplane\documentContents\ElementFactory;
use RuntimeException;

/**
 * Description of Flatplane
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class Flatplane
{
    const VERSION = '0.1-dev';

    protected static $inputDir = '.';
    protected static $outputDir = '.';
    protected static $workingDir = '.';
    protected static $verboseOutput = true; //todo: set this to false before shipping

    /**
     * Set basic quasi-global variables. All supplied paths have to be read- and
     * writable for php and spawned suprocesses.
     * @param string $inputDir
     *  absolute or relative path to input directory.
     * @param string $workingDir (optional)
     *  absolute or relative path to directory for work files. Will default to
     *  $inputDir if ommited.
     * @param string $outputDir (optional)
     *  absolute or relative path to diretory where the output PDF will be
     *  placed. Defaults to $workingDir (if set) or $inputDir. If the dir is
     *  non-existant, flatplane will attempt to create it.
     * @param bool $verbose
     *  Toggle verbose console output. (false: show only essential
     *  messages; true: show all messages; default: false)
     */
    public function __construct()
    {
        //todo: update doc
    }

    public static function setInputDir($inputDir)
    {
        self::$inputDir = $inputDir;

        if (!is_dir(self::$inputDir)
            || !is_readable(self::$inputDir)
            || !is_writable(self::$inputDir)
        ) {
            throw new RuntimeException(
                'Path '.self::$inputDir.' is invalid: '
                . 'Not a directory or not read/writable'
            );
        }
    }

    public static function setWorkingDir($workingDir)
    {
        self::$workingDir = $workingDir;

        if (!is_dir(self::$workingDir)
            || !is_readable(self::$workingDir)
            || !is_writable(self::$workingDir)
        ) {
            throw new RuntimeException(
                'Path '.self::$workingDir.' is invalid: '
                . 'Not a directory or not read/writable'
            );
        }
    }

    public static function setOutputDir($outputDir)
    {
        self::$outputDir = $outputDir;

        if (!is_dir(self::$outputDir)) {
            if (!mkdir(self::$outputDir, 0777, true)) {
                throw new RuntimeException('could not create output dir');
            }
        }
        if (!is_readable(self::$outputDir) || !is_writable(self::$outputDir)) {
            throw new RuntimeException(
                'Path '.self::$outputDir.' is invalid: '
                . 'Not a directory or not read/writable'
            );
        }
    }

    public static function setVerboseOutput($verbose)
    {
        self::$verboseOutput = (bool) $verbose;
    }

    public static function getInputDir()
    {
        return self::$inputDir;
    }

    public static function getOutputDir()
    {
        return self::$outputDir;
    }

    public static function getWorkingDir()
    {
        return self::$workingDir;
    }

    public static function getVerboseOutput()
    {
        return self::$verboseOutput;
    }

    /**
     * @param array $settings
     * @return Document
     */
    public function createDocument(array $settings = [])
    {
        $factory = new ElementFactory();
        return $factory->createDocument($settings);
    }

    public function generatePDF(array $settings)
    {
        //steps needed:
        // - include and parse input
        // - generate structure
        // - validate / update cache
        // - generate dynamic content (formulas etc)
        // - estiamte sizes
        // - layout pages
        // - generate pages (including references)
    }
}
