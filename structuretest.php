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
use de\flatplane\documentElements\Formula;
use de\flatplane\utilities\PDF;

$flatplane = new Flatplane();

$flatplane::setOutputDir('output');
$flatplane::setVerboseOutput(false);


/*
 * BEGIN DOKUMENTDEFINITION
 */
//Vom Standard abweichende dokumentweite Einstellungen setzen
$settings = array(
    'author' => 'Max Mustermann',
    'title' => 'Ganz wichtiges Dokument',
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


//$kapitel1 = $document->addSection('KAPITEL 1');
//$kapitel1->setStartsNewPage(['level1' => false]);
//$kapitel1->addTextFile('input/testKapitelMitRef.php');
//
//$kapitel2 = $document->addSection('KAPITEL 2');
//$kapitel2->addTextFile('input/testKapitelMitRef.php');
//
//$sub = $kapitel2->addSection('Subsection');
//$sub->addTextFile('input/testKapitelMitRef.php');

$document->addSource('img:nn', ['sourceAuthor' => 'Nikolai Neff']);
$document->setPageNumberStyle(['PG1' => 'roman']);
$document->setPageNumberStyle(['PG2' => 'alpha']);

$inhaltSec = $document->addSection('Inhaltsverzeichnis', ['enumerate' => false]);
$inhaltSec->setShowInList(true);
$inhaltSec->setStartsNewPage(['level1' => false]);
$inhaltList = $inhaltSec->addList(['section'], ['showInList' => true]);
$abbildungSec = $document->addSection(
    'Abbildungsverzeichnis',
    ['enumerate' => false, 'showInList' => true]
);
$abbildingList = $abbildungSec->addList(['image'], ['showInList' => false]);

$einleitungSec = $document->addSection('Einleitung');
$einleitungSec->addSection('Vorwort');
$einleitungSec->addSection('Danksagungen');
$hauptteilSec = $document->addSection('Hauptteil');
$hauptteilSec->setPageGroup('PG1');
$problem = $hauptteilSec->addSection('Problemstellung');
$problem->setLabel('sec:problem');
$text1 = $problem->addTextFile('input/testKapitelMitRef_LP.php');

$versuch = $hauptteilSec->addSection('Versuchsaufbau');

$tex[] = '\mathcal{F}(f)(t) = \frac{1}{\left(2\pi\right)^{\frac{n}{2}}}~ \int\limits_{\mathbb{R}^n} f(x)\,e^{-\mathrm{i} t \cdot x} \,\mathrm{d} x';
$tex[] = '\int_a^b(f(x)+c)\,\mathrm dx=\int_a^b f(x)\,\mathrm dx+(b-a)\cdot c';
$tex[] = 'Z = \sum_{i=1}^{n} a_i~;~~~a_i = k_i \cdot b^i~;~~~b=2~;~~~k_i \in \{0,1\}~;~~~i\in \mathbb{N}';
$tex[] = '\overline{\overline{\left(A\, \wedge\, B\right)}\, \wedge\, C} \neq\overline{ A\, \wedge\, \overline{\left(B\, \wedge\,C \right)}}';
$tex[] = '\LaTeX ~ 2 \cdot 2 \\ 2\mathbin{\cdot}2 \\ 2 \times 2 \\ 2\mathbin{\times}2​';
$tex[] = '(\pi + \varpi) \cdot \sum_{1}^{2}{3}';
$tex[] = 'e = 2+\cfrac{1}{1+\cfrac{1}{2+\cfrac{1}{1+\cfrac{1}{1+\cfrac{1}{4+\cfrac{1}{1+\cfrac{1}{1+\cfrac{1}{6+\dotsb}}}}}}}}';

foreach (Formula::getAvailableFonts() as $key => $formulafont) {
    $versuch->addFormula($tex[$key])->setFormulaFont($formulafont);
}

foreach (Formula::getAvailableFonts() as $key => $formulafont) {
    $versuch->addFormula($tex[0])->setFormulaFont($formulafont);
}


$hauptteilSec->addSection('Versuchsdruchführung mit langen Informationen zum Umbrechen Langeswort Überschallflugzeug');
$analyse = $hauptteilSec->addSection('Datenanalyse');
$analyse->addSection('Programm A');
$programmB = $analyse->addSection('Programm B');
$bild = $programmB->addImage('images/bild.png');
$bild->setCaption('TolleSpirale '.$bild->cite('img:nn'));
$bild->setFontColor(['title' => [255, 0, 0]]);
$bild->setTitle('Roter Titel!');

$schlussSec = $document->addSection('Schluss');
$fazit = $schlussSec->addSection('Fazit');
for ($i=0; $i<10; $i++) {
    $fazit->addSection('RND'.$i.': '.mt_rand());
}
$schlussSec->addSection('ICH BIN NUR IM INHALTSVERZEICHNIS ABER NICHT IM DOKUMENT', ['showInDocument' => false]);

$schlussSec->addSection('Ausblick');
$qvz = $document->addSection('Quellenverzeichnis', ['enumerate' => false]);
$qvz->addList(['source']);

$anhangSec = $document->addSection('Anhang', ['enumerate' => false]);
$anhangSec->setPageGroup('PG2');
$document->addSection('Grundstücks­verkehrs­genehmigungs­zuständigkeits­übertragungs­verordnung (GrundVZÜV)', ['altTitle' => 'GrundVZÜV']);
$text = $anhangSec->addTextFile('input/testKapitelohneRef.php');

$document->addCodeFile('classes/documentElements/AbstractDocumentContentElement.php', ['splitInParagraphs' => true]);

$document->addSection('Formelverzeichnis', ['enumerate' => false]);
$document->addList(['formula']);

$code = '<style> td,th{border: 1px solid #000000}</style><table><tr><th>Spalte 1</th><th>Spalte 2</th></tr><tr><td>INHALT</td><td>ASDF</td></tr></table>';

$document->addTable($code);

$flatplane->generatePDF(['showDocumentTree' => false, 'clearFormulaCache' => false, 'clearTextCache' => false]);
unset($flatplane);
