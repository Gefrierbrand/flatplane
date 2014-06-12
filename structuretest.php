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
    'numberingLevel' => ['list' => -1],
    'numberingPrefix' => ['list' => '#']
);

$document = $flatplane->createDocument($settings);
$document->getPdf()->setHeaderData('', 0, date('d.m.Y H:i:s'));
$document->addSource('quelle', ['altTitle' => 'Erika Mustermann, Super Buch, DuckSpaß©-Verlag, 42. Auflage 1984']);

$sec0_1 = $document->addSection('ebene0', ['enumerate' => true]);
$sec1_1 = $sec0_1->addSection('ebene1');
$sec2_1 = $sec1_1->addSection('Ebene2: ganz langer Text, der bestimmt umbricht und viele Buchstaben enthällt Donaudampfschifffahrt Sonderzeichen: ¿öäüÖÄÜßþê¥©');
$sec2_2 = $sec1_1->addSection('ebene2;2');
$sec2_2->setNumbers([2, 1, 'q']);
$sec1_1->addSection('test');
$sec0_2 = $document->addSection('ebene0, item2');
$sec1_2 = $sec0_1->addSection('test');
$document->addSection('Inhalt3')->addImage('images/bild.png', ['caption' => 'tolles bild'.$document->cite('quelle')]);
$document->addSection('Inhalt4');
$i5 = $document->addSection('Inhalt5');
$i5->addSection('i5');
$i5->addSection('i5');
$i5->addSection('i5');
$i5->addSection('i5');
$i5->addSection('i5');
$document->addSection('Inhalt6');
$document->addSection('Inhalt7');
$document->addSection('Inhalt8');
$on = $document->addSection('Inhalt9 ohne nummer', ['enumerate' => false]);
$on->addSection('test');
$on->addSection('test');
$sub = $on->addSection('test');
$sub->addSection('title');
$sub->addSection('title');
$subsub = $sub->addSection('title');
$subsub->addSection('tief unten');
$subsub->addSection('tief unten');
$sub->addSection('title');
$sub->addSection('title');


$list = $document->addList(['section', 'image'], ['showPages' => false]);
var_dump($list->getSize());
$flatplane->generatePDF(['showDocumentTree' => true, 'clearFormulaCache' => true]);
