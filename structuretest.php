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

$pdf =  new \de\flatplane\utilities\PDF();
$pdf->setHeaderData('', 0, date('d.m.Y H:i:s'));

$document = $flatplane->createDocument($settings, $pdf);
$sec0_1 = $document->addSection('ebene0', ['enumerate' => true]);
$sec1_1 = $sec0_1->addSection('ebene1');
$sec2_1 = $sec1_1->addSection('ebene2 ganz langer text der bestimmt umbricht und viele buchstaben enthällt donaudampfschifffahrt');
$sec2_2 = $sec1_1->addSection('ebene2;2');
$sec0_2 = $document->addSection('ebene0, item2');
$sec1_2 = $sec0_1->addSection('test');
$document->addSection('Inhalt3');
$document->addSection('Inhalt4');
$document->addSection('Inhalt5');
$document->addSection('Inhalt6');
$document->addSection('Inhalt7');
$document->addSection('Inhalt8');
$document->addSection('Inhalt9 ohne nummer', ['enumerate' => false]);
$document->addSection('Inhalt10');
$document->addSection('Inhalt11');
$document->addSection('Inhalt12');
$document->addSection('Inhalt13');

$list = $document->addList(['section']);
$list->generateStructure($document->getContent());
$list->getSize();
$flatplane->generatePDF(['showDocumentTree' => true, 'clearFormulaCache' => true]);
