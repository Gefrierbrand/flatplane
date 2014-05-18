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

use de\flatplane\controller\Flatplane;
use de\flatplane\documentContents\Formula;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;

/**
 * todo: doc, error-checking, update paths?
 * Description of GenerateFormulas
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class FormulaFilesGenerator
{
    protected $formulas = [];
    protected $process;

    public function __construct(array $content = [])
    {
        $num = 0;
        foreach ($content as $formula) {
            if (!($formula instanceof Formula)) {
                throw new RuntimeException(
                    'Invalid object supplied to GenerateFormulas: '.
                    gettype($formula)
                );
            }
            $formula->applyStyles();
            if ($formula->getUseCache() == false || $this->isCached($formula) == false) {
                $this->formulas[] = $formula;
            }
            $num ++;
        }
        Flatplane::log(
            $num.' Formulas total, '.count($this->formulas). ' need rendering'
        );
    }

    public function generateFiles()
    {
        if (!empty($this->formulas)) {
            $this->startSVGTEX();
            $this->curlRequest();
            $this->stopSVGTEX();
        }
    }

    protected function isCached(Formula $formula)
    {
        $filename = Flatplane::getWorkingDir().DIRECTORY_SEPARATOR.
            'formulas'.DIRECTORY_SEPARATOR.$formula->getHash().'.svg';
        if (file_exists($filename) && is_readable($filename)) {
            $formula->setPath($filename);
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


        Flatplane::log("Starting SVGTeX, please wait.");

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
                Flatplane::log('Error:', $this->process->getOutput());
                $this->process->clearOutput();
            } else {
                $this->process->clearOutput();
            }
            //wait 1/8 sec: this value is exactly representable as float
            sleep(0.125);
        }
        Flatplane::log("SVGTeX is running");
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
            $request = 'type='.$format.'&q='.urlencode($formula->getCode());

            $curlHandles[$key] = curl_init();
            curl_setopt($curlHandles[$key], CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curlHandles[$key], CURLOPT_URL, $url);
            curl_setopt($curlHandles[$key], CURLOPT_HEADER, false);
            curl_setopt($curlHandles[$key], CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curlHandles[$key], CURLOPT_POST, true);
            curl_setopt($curlHandles[$key], CURLOPT_POSTFIELDS, $request);
            curl_multi_add_handle($masterCurlHandle, $curlHandles[$key]);
        }
        do {
            curl_multi_exec($masterCurlHandle, $running);
        } while ($running > 0);

        foreach ($this->formulas as $key => $formula) {
            $result = curl_multi_getcontent($curlHandles[$key]);

            $this->validateResult($result);

            $dir = Flatplane::getWorkingDir().DIRECTORY_SEPARATOR.'formulas';
            if (!is_dir($dir)) {
                mkdir($dir);
            }
            $filename = $dir.DIRECTORY_SEPARATOR.$formula->getHash().'.svg';
            file_put_contents($filename, $result);
            $formula->setPath($filename);
        }
        Flatplane::log('Formulas generated');
    }

    protected function stopSVGTEX()
    {
        $this->process->stop();
        Flatplane::log("SVGTeX stopped");
    }

    protected function validateResult($result)
    {
        if (empty($result) || strpos($result, '<svg') !== 0) {
            trigger_error(
                'The SVGTeX result is not a valid SVG file. Error: '.$result,
                E_USER_WARNING
            );
        }
    }
}
