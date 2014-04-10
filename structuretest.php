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


//benötigte Klassen automatisch laden
function autoload($className)
{
    $className = ltrim($className, '\\');
    $fileName = '';
    $namespace = '';
    if ($lastNsPos = strrpos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName = 'classes' . DIRECTORY_SEPARATOR .
            str_replace('\\', DIRECTORY_SEPARATOR, $namespace) .
            DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
    //echo "Autoloading! $fileName \n";

    require $fileName;
}
spl_autoload_register('autoload');

//lange, volldefinierte Klassennamen aus Namespaces laden


use de\flatplane\iterators\RecursiveSectionIterator;
use de\flatplane\iterators\TocElementFilterIterator;
use de\flatplane\pageelements\Formula;
use de\flatplane\structure\Document;
use de\flatplane\utilities\Counter;

/*
 * BEGIN DOKUMENTDEFINITION
 */

$document = new Document();
$document->getSettings()->setAuthor('Max Mustermann');
$document->getSettings()->setTitle('Ganz wichtiges Dokument');


//in zweierschritten zählen. because we can.
$document->getCounter('section')->setIncrement(2);

$kapitel1 = $document->addSection('TITEL 1');
//alternative Titel fürs TOC
$kapitel2 = $document->addSection('TITEL 2', 'Hier steht was ganz anderes');
$subKapitel = $kapitel2->addSubSection('Sub 1');
$subKapitel->getCounter('section')->setIncrement(-1); //negative Zählrichtung ist möglich
$subsubKap1 = $subKapitel->addSubSection('SubSub 1');
$subsubKap2 = $subKapitel->addSubSection('SubSub 2', '', false, true); //ShowInTOC = false, enumerate = true;
$subsubKap3 = $subKapitel->addSubSection('SubSub 3', '', true, false); //ShowInTOC = true, enumerate = false;
//das handle kann überschrieben werden, ohne dass der Inhalt des alten Elements beeinflusst wird.
//das sollte man aber eigentlich nicht tun. Die Meisten IDEs warnen auch entsprechend.
$subsubKap4 = $subKapitel->addSubSection('SubSub 4');
$subsubKap4 = $subKapitel->addSubSection('SubSub 5');

//inhalte zu kapitel hinzufügen
$formela = $subsubKap4->addContent(new Formula('\frac{1}{a}'));
$formelb = $subsubKap4->addContent(new Formula('\frac{1}{b}'));
$formelc = $subsubKap4->addContent(new Formula('\frac{1}{c}'));


$subsubKap6 = $subKapitel->addSubSection('SubSub 6');
//alternative methode um optionen zu setzen, ohne alle anderen parameter
//direkt beim aufruf angeben zu müssen
$subsubKap6->setEnumerate(false);

// die Zuweisung der Unterkapitel in eine neue Variable ist nur nötig, wenn später
// noch (einfach) auf das Element zugegriffen werden soll.
$subsubKap4->addSubSection('noch eins tiefer');
$subsubKap4->addSubSection('wer a sagt muss auch b sagen');

//will man dann dennoch subsections hinzufügen, ist dies wiefolgt möglich:
$subsubKap4->getChildren()[0]->addSubSection('ganzweitunten');
$subsubKap4->getChildren()[0]->addSubSection('ganzweitunten2');

$kapitel3 = $document->addSection('TITEL 3');
$kapitel3->getCounter('section')->setValue(-2); //subsectionzähler auf -2 setzen
$subKapitel2 = $kapitel3->addSubSection('ich bin garnicht da', '', false); //zählstand -1, wird aber nicht angezeigt
$subKapitel3 = $kapitel3->addSubSection('zählertest'); //zähler 'beginnt' bei null

//subsections können auch später angegeben werden, ohne struktur oder zähler zu stören
$kapitel1->addSubSection('test');
$kapitel1->addSubSection('test2');

//inhalt hinzufügen
$kapitel1->getCounter('formula')->setValue(-2);
$formel1 = $kapitel1->addContent(new Formula('\frac{1}{2}', 'Asana-Math', 'TeX')); //formel(code, schriftart, format)
$formel2 = $kapitel1->addContent(new Formula('\frac{1}{3}', 'Asana-Math', 'TeX'));
$formel3 = $kapitel1->addContent(new Formula('\frac{1}{4}', 'Asana-Math', 'TeX'));

//kapitel etc werden jedoch immer in der reihenfolge der deklarationen angelegt
//ein Vertauschen währe jedoch prinzipiell möglich


/*
 * ENDE DOKUMENTDEFINITION
 */



//funktionen zum anzeigen
//todo: in klassen kapseln und als PDF anstatt als text ausgeben

$RecTreeIt = new RecursiveTreeIterator(
    new RecursiveSectionIterator($document->getSections())
);

echo "kompletter Baum unabhängig von den Elementeigenschaften".PHP_EOL;
foreach ($RecTreeIt as $line) {
    echo $line . PHP_EOL;
}

//newlines
echo PHP_EOL, PHP_EOL, PHP_EOL;

$RecItIt = new RecursiveIteratorIterator(
    new RecursiveSectionIterator($document->getSections()),
    RecursiveIteratorIterator::SELF_FIRST
);

// filtert Einträge heraus, deren ShowInToc-Eigenschaft auf false steht.
$FilterIt = new TocElementFilterIterator($RecItIt);

echo "Nummerierter Baum mit ausgeblendeten Einträge".PHP_EOL;
foreach ($FilterIt as $element) {
    if ($element->getEnumerate()) {
        echo implode('.', $element->getFullNumber()) . ' ' . $element . PHP_EOL;
    } else {
        echo $element . PHP_EOL;
    }
}

//newlines
echo PHP_EOL, PHP_EOL, PHP_EOL;

//eigenschaften des iterators ändern
$RecItIt->setMaxDepth(2); // nur 2 Ebenen nach der Wurzel werden angezeigt; -1 für unbegrenzt

echo "Nummerierter Baum mit ausgeblendeten Einträgen bis zur Tiefe 2".PHP_EOL;
foreach ($FilterIt as $element) {
    if ($element->getEnumerate()) {
        echo implode('.', $element->getFullNumber()) . ' ' . $element->getAltTitle() . PHP_EOL;
    } else {
        echo $element . PHP_EOL;
    }
}



//newlines
echo PHP_EOL, PHP_EOL, PHP_EOL;

$RecItIt->setMaxDepth(-1); // Ebenen zurücksetzen


echo "Baum mit idividuell nummerierten Inhalten (ohne Ausblendungen)".PHP_EOL;
//Todo: anzeigeoptionen (z.B. kapitel.zähler)
foreach ($RecItIt as $element) {
    echo implode('.', $element->getFullNumber()) . ' ' . $element->getAltTitle() . PHP_EOL;
    if (!empty($element->getContent())) {
        foreach ($element->getContent() as $content) {
            echo "({$content->getNumber()}) ".$content.PHP_EOL;
        }
    }
}


//newlines
echo PHP_EOL, PHP_EOL, PHP_EOL;

echo "Baum mit pro Dokument nummerierten Inhalten (ohne Ausblendungen)".PHP_EOL;
//TODO: do this properly:
// wrap in class, multiple types to be counted,...
$counter = new Counter;

foreach ($RecItIt as $element) {
    echo implode('.', $element->getFullNumber()) . ' ' . $element->getAltTitle() . PHP_EOL;
    if (!empty($element->getContent())) {
        foreach ($element->getContent() as $content) {
            $counter->add();
            echo "({$counter->getValue()}) ".$content.PHP_EOL;
        }
    }
}
