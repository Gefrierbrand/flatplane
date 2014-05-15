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

namespace de\flatplane\view;

use de\flatplane\documentContents\Formula;
use de\flatplane\iterators\ContentTypeFilterIterator;
use de\flatplane\iterators\RecursiveContentIterator;
use RuntimeException;
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
        $filterIterator = new ContentTypeFilterIterator(
            new RecursiveContentIterator($content),
            ['formula']
        );

        foreach ($filterIterator as $formula) {
            if (!($formula instanceof Formula)) {
                throw new RuntimeException(
                    'Invalid object supplied to GenerateFormulas: '.
                    gettype($formula)
                );
            }
            if ($formula->getUseCache() && !$this->checkCache($formula)) {
                $this->formulas[] = $formula;
            }
        }
    }

    public function generateFiles()
    {
        $this->startSVGTEX();
        $this->curlRequest();
        $this->stopSVGTEX();
    }

    protected function checkCache(Formula $formula)
    {
        return ;
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
        echo "SVGTeX is running";
    }

    protected function curlRequest()
    {
        $masterCurlHandle = curl_multi_init();
        $i = 0;
        foreach ($this->formulas as $formula) {
            /* Set the request in URL-format. Using an array with key=>value pairs
             * would set the Content-Type header to multipart/form-data, which
             * would break svgTeX' request handling which expects the POST
             * Content-Type to be application/x-www-form-urlencoded
             * @see http://www.php.net/manual/en/function.curl-setopt.php
             */
            $request = 'type='.$formula->getCodeType().'&q='.$formula->getCode();
            $curlHandles[$i] = curl_init();
            curl_setopt($curlHandles[$i], CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curlHandles[$i], CURLOPT_URL, "http://localhost:16000/");
            curl_setopt($curlHandles[$i], CURLOPT_HEADER, false);
            curl_setopt($curlHandles[$i], CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curlHandles[$i], CURLOPT_POST, true);
            curl_setopt($curlHandles[$i], CURLOPT_POSTFIELDS, $request);
            curl_multi_add_handle($masterCurlHandle, $curlHandles[$i]);
            $i++;
        }

        do {
            curl_multi_exec($masterCurlHandle, $running);
        } while ($running == CURLM_CALL_MULTI_PERFORM);

        $n = 0;
        foreach ($this->formulas as $formula) {
            $results = curl_multi_getcontent($curlHandles[$n]);
            $path = FLATPLANE_IMAGE_PATH.$formula->getHash().'.svg';
            file_put_contents($path, $results);
            $formula->setPath($path);
            $n++;
        }
    }

    protected function stopSVGTEX()
    {
        $this->process->stop();
    }
}
