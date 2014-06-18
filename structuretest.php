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
$pdf = $document->getPdf();
$pdf->setHeaderData('', 0, date('d.m.Y H:i:s'));

$pdf->AddPage();
$pdf->Rect(
    $document->getPageMargins('left'),
    $document->getPageMargins('right'),
    $document->getPageMeasurements()['textwidth'],
    $document->getPageMeasurements()['textheight'],
    '',
    ['all' => ['color' => [255, 0, 0]]]
);


$inhaltSec = $document->addSection('Inhaltsverzeichnis mit langen Informationen zum Umbrechen Langeswort', ['enumerate' => true]);

var_dump($inhaltSec->getSize());

$inhaltList = $inhaltSec->addList(['section', 'list'], ['showInList' => false]);
$inhaltSec->addList(['image']);
$inhaltSec->addList(['table']);
$inhaltSec->addList(['formula']);

$einleitungSec = $document->addSection('Einleitung');
$einleitungSec->addSection('Vorwort');
$einleitungSec->addSection('Danksagungen');
$hauptteilSec = $document->addSection('Hauptteil');
$hauptteilSec->addSection('Problemstellung');
$hauptteilSec->addSection('Versuchsaufbau');
$hauptteilSec->addSection('Versuchsdruchführung');
$hauptteilSec->addSection('Datenanalyse');

$schlussSec = $document->addSection('Schluss');
$schlussSec->addSection('Fazit');
$schlussSec->addSection('Ausblick');
$anahngSec = $document->addSection('Anhang', ['enumerate' => false]);

$inhaltList->getSize();

$flatplane->generatePDF(['showDocumentTree' => true, 'clearFormulaCache' => true]);
