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

namespace de\flatplane\model;

use de\flatplane\documentContents\Formula;
use de\flatplane\iterators\RecursiveContentIterator;
use de\flatplane\iterators\ContentTypeFilterIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Process\Process;

/**
 * todo: doc, error-checking
 * Description of GenerateFormulas
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class GenerateFormulas
{
    protected $formulas = [];
    protected $process;

    public function __construct(array $content = [])
    {
        $RecItIt = new RecursiveIteratorIterator(
            new RecursiveContentIterator($content),
            RecursiveIteratorIterator::SELF_FIRST
        );

        $filterIterator = new ContentTypeFilterIterator($RecItIt, ['formula']);

        $num = 0;
        foreach ($filterIterator as $formula) {
            if (!($formula instanceof Formula)) {
                throw new RuntimeException(
                    'Invalid object supplied to GenerateFormulas: '.
                    gettype($formula)
                );
            }
            if ($formula->getUseCache() == false || $this->isCached($formula) == false) {
                $this->formulas[] = $formula;
            }
            $num ++;
        }
        echo $num. ' Formulas total, '.
            count($this->formulas). ' need rendering'.PHP_EOL;
    }

    public function generateFiles()
    {
        if (!empty($this->formulas)) {
            $this->startSVGTEX();
            $this->curlRequest();
            $this->stopSVGTEX();
        } else {
            trigger_error('nothing to render', E_USER_NOTICE);
        }
    }

    protected function isCached(Formula $formula)
    {
        $filename = FLATPLANE_IMAGE_PATH.
            DIRECTORY_SEPARATOR.$formula->getHash().'.svg';
        if (file_exists($filename) && is_readable($filename)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * todo: error checking, outputhandling/logging?
     * todo: doc
     */
    protected function startSVGTEX()
    {
        //todo: path, os, port?
        $this->process = new Process(
            'phantomjs svgtex'.DIRECTORY_SEPARATOR.'main.js -p 16000'
        );
        $this->process->setTimeout(20);
        $this->process->setIdleTimeout(20);
        $this->process->start();

        echo "Starting SVGTeX, please wait.".PHP_EOL;
        while ($this->process->isRunning()) {
            $this->process->checkTimeout();
            $out = $this->process->getOutput();
            if (!empty($out)) {
                //echo $out.PHP_EOL;
            }
            if (strpos($out, 'Server started')!==false) {
                //exit loop
                break;
            } elseif (strpos($out, 'error')!==false) {
                echo 'Error:', $this->process->getOutput();
                $this->process->clearOutput();
            } else {
                $this->process->clearOutput();
            }
            //wait 1/8 sec: this value is exactly representable as float
            sleep(0.125);
        }
        echo "SVGTeX is running".PHP_EOL;
    }

    protected function curlRequest()
    {
        if (!$this->process->isRunning()) {
            trigger_error('The SVGTeX process is not runnig', E_USER_WARNING);
        }
        $masterCurlHandle = curl_multi_init();
        $curlHandles = array();
        foreach ($this->formulas as $key => $formula) {
            $format = strtolower($formula->getCodeFormat());
            $url = 'http://localhost:16000/';
            $request = '?type='.$format.'&q='.urlencode($formula->getCode());

            $curlHandles[$key] = curl_init($url.$request);
            curl_setopt($curlHandles[$key], CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curlHandles[$key], CURLOPT_HEADER, false);
            curl_multi_add_handle($masterCurlHandle, $curlHandles[$key]);

//            $curlHandles[$key] = curl_init();
//            curl_setopt($curlHandles[$key], CURLOPT_RETURNTRANSFER, true);
//            curl_setopt($curlHandles[$key], CURLOPT_URL, "http://localhost:16000/");
//            curl_setopt($curlHandles[$key], CURLOPT_HEADER, false);
//            curl_setopt($curlHandles[$key], CURLOPT_RETURNTRANSFER, true);
//            curl_setopt($curlHandles[$key], CURLOPT_POST, true);
//            curl_setopt($curlHandles[$key], CURLOPT_POSTFIELDS, $request);
//            curl_multi_add_handle($masterCurlHandle, $curlHandles[$key]);
        }
        do {
            curl_multi_exec($masterCurlHandle, $running);
        } while ($running > 0);

        foreach ($this->formulas as $key => $formula) {
            $result = curl_multi_getcontent($curlHandles[$key]);

            //todo: propper error handling
            if (empty($result)) {
                trigger_error('EMPTY');
            }

            $filename = FLATPLANE_IMAGE_PATH.
                DIRECTORY_SEPARATOR.$formula->getHash().'.svg';
            file_put_contents($filename, $result);
            $formula->setPath($filename);
        }
    }

    protected function stopSVGTEX()
    {
        $this->process->stop();
        echo "SVGTeX stopped".PHP_EOL;
    }
}
