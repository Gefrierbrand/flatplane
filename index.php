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

use de\flatplane\controller\Flatplane;
use de\flatplane\utilities\PDF;

//----Include Base File----
include 'flatplane.inc.php';

$flatplane = new Flatplane();
$flatplane->setVerboseOutput(true);
Flatplane::setConfigDir('config');

//create a Document-Instance
$document = $flatplane->createDocument();

//define the pagenumbering style for the pagegroup PG1
$document->setPageNumberStyle(['PG1' => 'int']);

//define the Titlepages content. You can use all basic TCPDF Methods here
$outputCallback = function (PDF $pdf) {
    $pdf->write(0, 'Hello!');
};

//add a titlepage. This is currenly mandatory
$document->addTitlePage()->setOutputCallback($outputCallback);

//add a section as content to the page. The first element has to have its 'startsNewPage'
//property set to false for 'level1' due to a known bug. 
//It also has to be in a pagegroup for the numbering to work correctly.
//This will be fixed in the future.
$section1 = $document->addSection('FirstSection', ['startsNewPage' => ['level1' => false]]);
$section1->setPageGroup('PG1');

//add a subsection to the first section
$subsection1 = $section1->addSection('subsection');

//create a list of all sections
$list = $document->addList(['section']);


//----OUTPUT----
$flatplane->generatePDF(
    ['showDocumentTree' => true,
    'clearFormulaCache' => false,
    'clearTextCache' => false,
    'clearImageCache' => false]
);
