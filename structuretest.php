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
require 'vendor'.DIRECTORY_SEPARATOR.'autoload.php';

//lange, volldefinierte Klassennamen aus Namespaces laden


use de\flatplane\documentContents\ElementFactory;
use de\flatplane\utilities\Timer;

$t = new Timer();

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

$factory = new ElementFactory;
$document = $factory->createDocument($settings);

$vorwort = $document->addSection('vorwort', ['enumerate' => false]);
$inhalt = $document->addSection('inhaltsverzeichnis');
$list = $inhalt->addList(['section', 'formula']);
$einleitung = $document->addSection('einleitung', ['label' => 'sec:einleitung']);
$text = $einleitung->addText('content/testKapitelMitRef.php');
$hauptteil = $document->addSection('hauptteil');
$sub = $hauptteil->addSection('subkapitel');
$sub->addSection('subsub', ['label' => 'sec:subsub']);
$formula = $sub->addFormula('\frac{1}{2}', ['label' => 'eq:f1']);
$formula->addFormula('\text{subformula}');
$listoflists = $document->addList(['list']);

echo PHP_EOL;
$list->generateStructure($document->getContent());
echo PHP_EOL.PHP_EOL;
$listoflists->generateStructure($document->getContent());
echo PHP_EOL;

$t->now('before reading');

echo $text->getText();
$t->now('after reading');

print_r($text->getSize());
