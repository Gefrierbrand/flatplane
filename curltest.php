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

use Symfony\Component\Process\Process;

include 'flatplane.inc.php';

$tex[] = '\displaystyle{\mathcal{F}(f)(t) = \frac{1}{\left(2\pi\right)^{\frac{n}{2}}}~ \int\limits_{\mathbb{R}^n} f(x)\,e^{-\mathrm{i} t \cdot x} \,\mathrm{d} x}';
$tex[] = '\int_a^b(f(x)+c)\,\mathrm dx=\int_a^b f(x)\,\mathrm dx+(b-a)\cdot c';
$tex[] = 'Z = \sum_{i=1}^{n} a_i~;~~~a_i = k_i \cdot b^i~;~~~b=2~;~~~k_i \in \{0,1\}~;~~~i\in \mathbb{N}';
$tex[] = '\overline{\overline{\left(A\, \wedge\, B\right)}\, \wedge\, C} \neq\overline{ A\, \wedge\, \overline{\left(B\, \wedge\,C \right)}}';
$tex[] = '\LaTeX ~ 2 \cdot 2 \\ 2\mathbin{\cdot}2 \\ 2 \times 2 \\ 2\mathbin{\times}2​';

$process = new Process('phantomjs svgtex'.DIRECTORY_SEPARATOR.'main.js -p 16000');
$process->setTimeout(20);
$process->setIdleTimeout(20);
$process->start();

$t->now('starting svgTex process, please wait');
while ($process->isRunning()) {
    $process->checkTimeout();
    $out = $process->getOutput();
    if (!empty($out)) {
        //echo $out.PHP_EOL;
    }
    if (strpos($out, 'Server started')!==false) {
        //exit loop
        break;
    } elseif (strpos($out, 'error')!==false) {
        echo 'Error:', $process->getOutput();
        $process->clearOutput();
    } else {
        $process->clearOutput();
    }
    //wait 1/8 sec: this value is exactly representable as float while .1 is not
    sleep(0.125);
}
$t->now('SvgTex Ready');

$i=0;
foreach ($tex as $texContent) {
    $i++;
    $fields = 'type=tex&q='.$texContent;

    $ch = curl_init();
    // set URL and other appropriate options
    curl_setopt($ch, CURLOPT_URL, "http://localhost:16000/");
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    // grab URL and pass it to the browser
    //$t->now('executing curl');
    $svg = curl_exec($ch);
    // close cURL resource, and free up system resources
    curl_close($ch);
    file_put_contents('output/post'.$i.'.svg', $svg);


}
$t->now('after all POST');

function cb_urlencode(&$value, $key)
{
    $value = 'http://localhost:16000/?type=tex&q='.urlencode($value);
}
array_walk($tex, 'cb_urlencode');

$masterCurlHandle = curl_multi_init();
$i = 0;
foreach ($tex as $texContent) {
    $fields = 'type=tex&q='.$texContent;
    $curlHandles[$i] = curl_init();
    curl_setopt($curlHandles[$i], CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curlHandles[$i], CURLOPT_URL, "http://localhost:16000/");
    curl_setopt($curlHandles[$i], CURLOPT_HEADER, false);
    curl_setopt($curlHandles[$i], CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curlHandles[$i], CURLOPT_POST, true);
    curl_setopt($curlHandles[$i], CURLOPT_POSTFIELDS, $fields);
    curl_multi_add_handle($masterCurlHandle, $curlHandles[$i]);
    $i++;
}

do {
    curl_multi_exec($masterCurlHandle, $running);
} while ($running == CURLM_CALL_MULTI_PERFORM);

for ($i = 0; $i < count($tex); $i++) {
    $results = curl_multi_getcontent($curlHandles[$i]);
    file_put_contents('output/multiple'.($i).'.svg', $results);
    //echo( $i . "\n" . $results . "\n");
}
$t->now('after all multiCurl');
