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
use de\flatplane\iterators\ContentTypeFilterIterator;
use de\flatplane\iterators\RecursiveContentIterator;
use de\flatplane\model\FormulaFilesGenerator;
use RecursiveIteratorIterator;
use RecursiveTreeIterator;
use RuntimeException;
use Symfony\Component\Stopwatch\Stopwatch;

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
     * @var Document
     */
    protected $document;

    /**
     * @var Stopwatch
     */
    protected $stopwatch;

    public function __construct()
    {
        $this->stopwatch = new Stopwatch();
        $this->startTimer('generateDocument');
    }

    public function __destruct()
    {
        $event = $this->stopTimer('generateDocument');
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
        $this->document = $factory->createDocument($settings);
        return $this->document;
    }

    /**
     * steps needed:
     * validate document
     * include and parse input
     * generate structure
     * validate / update cache
     * generate dynamic content (formulas etc)
     * estimate sizes
     * layout pages
     * generate pages (including references)
     *
     * @param array $settings
     * @throws \RuntimeException
     */
    public function generatePDF(array $settings = [])
    {
        if (isset($settings['showDocumentTree']) && $settings['showDocumentTree']) {
            $this->showDocumentTree();
        }

        // validate document
        if (empty($this->document) || !($this->document instanceof Document)) {
            throw new \RuntimeException(
                'No document or invalid document supplied for PDF generation'
            );
        }

        // include and parse input: TBD

        // generate structure

        $this->startTimer('generatingLists');
        $this->generatePreliminaryLists();
        $this->stopTimer('generatingLists');

        // validate / update cache: TBD

        // generate dynamic content
        $this->startTimer('generatingFormulas');
        $this->generateFormulas();
        $this->stopTimer('generatingFormulas');
    }

    protected function generateFormulas()
    {
        $formulas = $this->getAllContentOfType('formula');
        $formulaGenerator = new FormulaFilesGenerator($formulas);
        $formulaGenerator->generateFiles();
    }

    protected function generatePreliminaryLists()
    {
        $lists = $this->getAllContentOfType('list');
        foreach ($lists as $list) {
            $list->generateStructure($this->document->getContent());
        }
    }

    /**
     * @param string $type
     * @return ContentElementInterface
     * todo: type validation etc
     */
    protected function getAllContentOfType($type)
    {
        $RecItIt = new RecursiveIteratorIterator(
            new RecursiveContentIterator($this->document->getContent()),
            RecursiveIteratorIterator::SELF_FIRST
        );

        $filterIterator = new ContentTypeFilterIterator($RecItIt, [$type]);

        $return = array();
        foreach ($filterIterator as $element) {
            $return[] = $element;
        }
        return $return;
    }

    protected function showDocumentTree()
    {
        echo 'Document Tree:'.PHP_EOL;
        $RecItIt = new RecursiveTreeIterator(
            new RecursiveContentIterator($this->document->getContent()),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($RecItIt as $value) {
            echo $value.PHP_EOL;
        }
        echo PHP_EOL;
    }

    private function startTimer($name)
    {
        echo "Starting ($name):".PHP_EOL;
        $this->stopwatch->start($name);
    }

    private function stopTimer($name)
    {
        echo PHP_EOL."Finished ($name): ";
        $event = $this->stopwatch->stop($name);
        $duration = number_format($event->getDuration()/1000, 3, '.', '').' s';
        $memory = number_format($event->getMemory()/1024/1024, 3, '.', '').' MiB';
        echo " $duration; ($memory)".PHP_EOL;
    }
}
