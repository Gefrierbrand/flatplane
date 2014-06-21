<?php

/*
 * Copyright (C) 2014 Nikolai Neff <admin@flatplane.de>.
 *
 * This file is part of Flatplane.
 *
 * Flatplane is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
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

use de\flatplane\documentElements\Document;
use de\flatplane\documentElements\ElementFactory;
use de\flatplane\interfaces\documentElements\DocumentInterface;
use de\flatplane\iterators\ContentTypeFilterIterator;
use de\flatplane\iterators\RecursiveContentIterator;
use de\flatplane\model\FormulaFilesGenerator;
use de\flatplane\utilities\OSPaths;
use de\flatplane\utilities\PDF;
use de\flatplane\view\PageLayout;
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
    protected static $cacheDir = '.';
    protected static $verboseOutput = true;
    protected static $firstmessage = true;

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
        $this->stopTimer('generateDocument', true);
    }

    public static function getPhantomJsPath()
    {
        return OSPaths::getPhantomJsPath();
    }

    public static function log($msg, $extraNewLine = false)
    {
        if (self::$firstmessage) {
            if (php_sapi_name() != 'cli') {
                header('Content-Type: text/html; charset=UTF-8');
            }
            self::$firstmessage = false;
        }
        if (self::$verboseOutput) {
            $msg = trim($msg).PHP_EOL;
            if ($extraNewLine) {
                $msg .= PHP_EOL;
            }
            if (php_sapi_name() != 'cli') {
                $msg = nl2br($msg);
            }
            echo $msg;
        }
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

    public static function setCacheDir($cacheDir)
    {
        self::$cacheDir = $cacheDir;

        if (!is_dir(self::$cacheDir)
            || !is_readable(self::$cacheDir)
            || !is_writable(self::$cacheDir)
        ) {
            throw new RuntimeException(
                'Path '.self::$cacheDir .' is invalid: '
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

    public static function getCacheDir()
    {
        return self::$cacheDir ;
    }

    public static function getVerboseOutput()
    {
        return self::$verboseOutput;
    }

    /**
     * @param array $settings
     * @return Document
     */
    public function createDocument(array $settings = [], PDF $pdf = null)
    {
        $this->startTimer('AnalyzingInputs');
        $factory = new ElementFactory();
        $this->document = $factory->createDocument($settings, $pdf);
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
        $this->stopTimer('AnalyzingInputs');
        if (!extension_loaded('imagick')) {
            self::log(
                'Imagick extension is not available, assuming 72 dpi for'
                .' all images if not otherwise specified.'
            );
        }

        if (self::$verboseOutput && !empty($settings['showDocumentTree'])) {
            $this->showDocumentTree();
        }

        // validate document
        if (empty($this->document) || !($this->document instanceof DocumentInterface)) {
            throw new \RuntimeException(
                'No document or invalid document supplied for PDF generation'
            );
        }

        // include and parse input: TBD

        // generate structure

        $this->startTimer('generateLists');
        $this->generateListStructures();
        $this->stopTimer('generateLists');

        // validate / update cache: TBD

        if (!empty($settings['clearFormulaCache'])) {
            $this->startTimer('clearFormulaCache');
            $this->clearFormulaCache();
            $this->stopTimer('clearFormulaCache');
        }

        // generate dynamic content
        $this->startTimer('generateFormulas');
        $this->generateFormulas();
        $this->stopTimer('generateFormulas');

        // layout pages
        $this->startTimer('layoutPages');
        $pages = $this->layoutElements();
        $this->stopTimer('layoutPages');

        // generating Pages
        $this->startTimer('generatingPages');
        $this->generatePageContentOutput($pages);
        $this->stopTimer('generatingPages');

        // generatingPDF
        $this->startTimer('generatingPDFOutput');
        $this->generatePDFOutput();
        $this->stopTimer('generatingPDFOutput');
    }

    protected function clearFormulaCache()
    {
        FormulaFilesGenerator::cleanUp();
    }

    protected function generatePDFOutput()
    {
        //todo: filename, outputoptions (PDF/A, font subsetting, ovetwrite usw?)
        $this->getDocument()->getPdf()->Output(
            self::$outputDir.DIRECTORY_SEPARATOR.'output.pdf',
            'F'
        );
    }

    protected function generatePageContentOutput($pages)
    {
        //todo: implement
    }

    protected function layoutElements()
    {
        //$content = $this->getAllContent();
        $pages = new PageLayout($this->getDocument());
        return $pages;
    }

    protected function generateFormulas()
    {
        $formulas = $this->getAllContentOfType('formula');
        $formulaGenerator = new FormulaFilesGenerator($formulas, false);
        $formulaGenerator->generateFiles();
    }

    protected function generateListStructures()
    {
        $lists = $this->getAllContentOfType('list');
        foreach ($lists as $element) {
            $element->generateStructure($this->document->getContent());
        }
    }

    /**
     *
     * @return \RecursiveIteratorIterator
     */
    protected function getAllContent()
    {
        $recItIt = new RecursiveIteratorIterator(
            new RecursiveContentIterator($this->getDocument()->getContent()),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($recItIt as $pagelements) {
            $content[] = $pagelements;
        }
        return $content;
    }

    /**
     * @param string $type
     * @return ContentElementInterface
     *  todo: type validation etc
     */
    protected function getAllContentOfType($type)
    {
        $recItIt = new RecursiveIteratorIterator(
            new RecursiveContentIterator($this->getDocument()->getContent()),
            RecursiveIteratorIterator::SELF_FIRST
        );

        $filterIterator = new ContentTypeFilterIterator($recItIt, [$type]);

        $return = array();
        foreach ($filterIterator as $element) {
            $return[] = $element;
        }
        return $return;
    }

    protected function showDocumentTree()
    {
        echo 'Document Tree:'.PHP_EOL;
        if (!class_exists('RecursiveTreeIterator')) {
            trigger_error(
                'Showing the Document Tree is currently not supported by HHVM',
                E_USER_NOTICE
            );
            return;
        }
        $RecItIt = new RecursiveTreeIterator(
            new RecursiveContentIterator($this->getDocument()->getContent()),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($RecItIt as $value) {
            self::log($value);
        }
        self::log('');
    }

    public function startTimer($name)
    {
        self::log("Starting $name");
        $this->getStopwatch()->start($name);
    }

    public function stopTimer($name, $showMem = false)
    {
        $event = $this->getStopwatch()->stop($name);

        if (self::$verboseOutput) {
            $duration = number_format($event->getDuration()/1000, 3, '.', '').' s';

            if ($showMem) {
                $memory = '; Peak memory usage: ';
                $memory .= number_format($event->getMemory()/1024/1024, 3, '.', '');
                $memory .= ' MiB';
            } else {
                $memory = '';
            }

            self::log("Finished $name: {$duration}$memory", true);
        }
        return $event;
    }

    protected function getDocument() //todo: make this public?
    {
        return $this->document;
    }

    protected function getStopwatch()
    {
        return $this->stopwatch;
    }
}
