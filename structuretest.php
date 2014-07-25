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

//use composer autoloading for dependencies
require 'flatplane.inc.php';

use de\flatplane\controller\Flatplane;
use de\flatplane\documentElements\Formula;
use de\flatplane\utilities\PDF;

$flatplane = new Flatplane();

$flatplane::setOutputDir('output');
$flatplane::setVerboseOutput(true);


/*
 * BEGIN DOKUMENTDEFINITION
 */
//Vom Standard abweichende dokumentweite Einstellungen setzen
$settings = array(
    'author' => 'Max Mustermann',
    'docTitle' => 'Ganz wichtiges Dokument',
    'keywords' => 'super, toll, top, gigantisch, superlative!',
    'numberingLevel' => ['list' => -1, 'formula' => -1, 'section' => -1],
    'numberingPrefix' => ['list' => '#']
);

$document = $flatplane->createDocument($settings);
$titlepage = $document->addTitlePage();
$outputCallback = function (PDF $pdf) {
    $pdf->SetFont('times', '', 64);
    $pdf->Write(0, 'TITLEPAGE 1');
};
$titlepage->setOutputCallback($outputCallback);

$titlepage2 = $document->addTitlePage();
$outputCallback2 = function (PDF $pdf) {
    $pdf->SetFont('times', '', 64);
    $pdf->Write(0, 'TITLEPAGE 2');
};
$titlepage2->setOutputCallback($outputCallback2);

$inhaltsec = $document->addSection('Inhaltsverzeichnis', ['startsNewPage' => ['level1' => false]]);
$inhaltsec->addList(['section']);
$code = <<<'EOL'
<style>
    th {
        font-weight: bold;
    }
    td {
        text-align: left;
    }
</style>
<table cellspacing="0" cellpadding="2" border="0.3">
    <tr>
        <th width="20%">Größe</th><th width="80%">Definition</th>
    </tr>
    <tr><td>Akzenthöhe</td><td>vertikaler Abstand von Versalzeichen (Großbuchstaben) mit Akzent von der Grundlinie (Versallinie)</td></tr>
    <tr><td>Versalhöhe</td><td>vertikaler Abstand von Versalzeichen von der Grundlinie</td></tr>
    <tr><td>Oberlänge</td><td>vertikaler Abstand von gemeinen Zeichen (Kleinbuchstaben) mit Oberlänge von der Grundlinie</td></tr>
    <tr><td>Mittellänge</td><td>vertikaler Abstand von gemeinen Zeichen ohne Oberlänge von der Grundlinie, auch x- oder n-Höhe genannt</td></tr>
    <tr><td>Unterlänge</td><td>vertikaler Abstand von gemeinen Zeichen mit Unterlänge zur Grundlinie</td></tr>
</table>
EOL;

$typodeftab = $inhaltsec->addTable($code);
$typodeftab->setLabel('tab:typodefs');
$typodeftab->setTitle('Begriffsdefinition nach DIN 16507-2');
$typodeftab->setMargins(['top' => 5, 'bottom' => 0, 'caption' => 0]);

$inhaltsec->addText('TEST');
$inhaltsec->addText('TEST2');
$inhaltsec->addText('TEST3');

$flatplane->generatePDF(['showDocumentTree' => true, 'clearFormulaCache' => true, 'clearTextCache' => true]);
unset($flatplane);
