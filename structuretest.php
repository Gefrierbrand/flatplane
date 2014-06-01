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

//use composer autoloading for dependencies
require 'flatplane.inc.php';

use de\flatplane\controller\Flatplane;

$flatplane = new Flatplane();

$flatplane::setOutputDir('output');
$flatplane::setVerboseOutput(true);


/*
 * BEGIN DOKUMENTDEFINITION
 */

//Vom Standard abweichende dokumentweite Einstellungen setzen
$settings = array(
    'author' => 'Max Mustermann',
    'title' => 'Ganz wichtiges Dokument',
    'keywords' => 'super, toll, top, gigantisch, superlative!',
    'startIndex' => ['section' => 0],
//    'numberingPrefix' => ['formula' => '['],
//    'numberingPostfix' => ['formula' => ']'],
//    'numberingSeparator' => ['formula' => '.'],
    'numberingLevel' => ['formula' => 0, 'list' => 0]
);

$document = $flatplane->createDocument($settings);


$vorwort = $document->addSection('vorwort', ['enumerate' => false]);
$inhalt = $document->addSection('inhaltsverzeichnis');
$list = $inhalt->addList(['section', 'formula']);
$einleitung = $document->addSection('einleitung', ['label' => 'sec:einleitung']);
$text = $einleitung->addText('input/testKapitelMitRef.php', ['fontSize' => ['default' => 16]]);
$hauptteil = $document->addSection('hauptteil');
$sub = $hauptteil->addSection('subkapitel');
$sub->addSection('subsub', ['label' => 'sec:subsub']);
$listoflists = $document->addList(['list']);

$testSec = $document->addSection('test');
$testSec2 = $testSec->addSection('test');

$tex[] = '\displaystyle{\mathcal{F}(f)(t) = \frac{1}{\left(2\pi\right)^{\frac{n}{2}}}~ \int\limits_{\mathbb{R}^n} f(x)\,e^{-\mathrm{i} t \cdot x} \,\mathrm{d} x}';
$tex[] = '\int_a^b(f(x)+c)\,\mathrm dx=\int_a^b f(x)\,\mathrm dx+(b-a)\cdot c';
$tex[] = 'Z = \sum_{i=1}^{n} a_i~;~~~a_i = k_i \cdot b^i~;~~~b=2~;~~~k_i \in \{0,1\}~;~~~i\in \mathbb{N}';
$tex[] = '\overline{\overline{\left(A\, \wedge\, B\right)}\, \wedge\, C} \neq\overline{ A\, \wedge\, \overline{\left(B\, \wedge\,C \right)}}';
$tex[] = '\LaTeX ~ 2 \cdot 2 \\ 2\mathbin{\cdot}2 \\ 2 \times 2 \\ 2\mathbin{\times}2​';
$tex[] = 'e = \lim_{n\to\infty} \left(1+\frac{1}{n}\right)^n';
$tex[] = 'e = \sum_{k=0}^{\infty}{\frac{1}{k!}} = \frac{1}{0!} + \frac{1}{1!} + \frac{1}{2!} + \frac{1}{3!} + \frac{1}{4!} + \cdots = 1 + 1 + \frac{1}{2} + \frac{1}{6} + \frac{1}{24} + \cdots';
$tex[] = '\begin{align}e &= [2; 1, 2, 1, 1, 4, 1, 1, 6, 1, 1, 8, 1, 1, 10, 1,\dots] \\ &= 2+\cfrac{1}{1+\cfrac{1}{2+\cfrac{1}{1+\cfrac{1}{1+\cfrac{1}{4+\cfrac{1}{1+\cfrac{1}{1+\cfrac{1}{6+\dotsb}}}}}}}}\end{align}';

$testFormula = new \de\flatplane\documentContents\Formula([]);

foreach ($testFormula->getAvailableFonts() as $key => $font) {
    $sec[$key] = $document->addSection('Formulas: Font: '.$font);
    foreach ($tex as $content) {
        $sec[$key]->addFormula($content, ['formulaFont' => $font]);
    }
}

$document->addText('input/testKapitelMitRef.php');
$document->addList(['text']);

$image = $document->addImage('images/bild.png', ['fitOnPage' => true]);

$flatplane->generatePDF(['showDocumentTree' => false]);
var_dump($image->getSize());
