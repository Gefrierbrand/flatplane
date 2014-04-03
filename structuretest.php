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


use de\flatplane\iterators\RecursiveSectionIterator;
use de\flatplane\iterators\TocElementFilterIterator;
use de\flatplane\structure\Document;

$document = new Document();
//$document->getCounter()->setIncrement('2s');

$kapitel1 = $document->addSection('TITEL 1');
$kapitel2 = $document->addSection('TITEL 2');
$subKapitel = $kapitel2->addSubSection('Sub 1');
$subsubKap1 = $subKapitel->addSubSection('SubSub 1');
$subsubKap2 = $subKapitel->addSubSection('SubSub 2', false, true); //ShowInTOC = false, enumerate = true;
$subsubKap3 = $subKapitel->addSubSection('SubSub 3', true, false); //ShowInTOC = true, enumerate = false;
$subsubKap4 = $subKapitel->addSubSection('SubSub 4');

// die Zuweisung der Unterkapitel in eine neue Variable ist nur nötig, wenn später noch (einfach) auf das Element zugegriffen werden soll.
$subsubKap4->addSubSection('noch eins tiefer');
$subsubKap4->addSubSection('wer a sagt muss auch b sagen');

//will man dann dennoch subsections hinzufügen, ist dies wiefolgt möglich: //benötigt (in dieder form) php 5.4 oder neuer.
$subsubKap4->getChildren()[0]->addSubSection('ganzweitunten');
$subsubKap4->getChildren()[0]->addSubSection('ganzweitunten2');

$kapitel3 = $document->addSection('TITEL 3');
$subKapitel2 = $kapitel3->addSubSection('ich bin garnicht da', false);

$RecTreeIt = new RecursiveTreeIterator(
    new RecursiveSectionIterator($document->getSections())
);

echo "kompletter Baum unabhängig von den Elementeigenschaften".\PHP_EOL;
foreach ($RecTreeIt as $line) {
    echo $line . \PHP_EOL;
}

echo PHP_EOL, PHP_EOL, PHP_EOL;

$RecItIt = new RecursiveIteratorIterator(
    new RecursiveSectionIterator($document->getSections()),
    RecursiveIteratorIterator::SELF_FIRST
);

$FilterIt = new TocElementFilterIterator($RecItIt); // filtert einträge heraus, deren ShowInToc-Eigenschaft auf false steht.

echo "Nummerierter Baum mit ausgeblendeten Einträge".\PHP_EOL;
foreach ($FilterIt as $element) {
    if ($element->getEnumerate()) {
        echo implode('.', $element->getFullNumber()) . ' ' . $element . \PHP_EOL;
    } else {
        echo $element . \PHP_EOL;
    }
}

echo PHP_EOL, PHP_EOL, PHP_EOL;

$RecItIt->setMaxDepth(2); // nur 2 Ebenen nach der Wurzel werden angezeigt; -1 für unbegrenzt

echo "Nummerierter Baum mit ausgeblendeten Einträgen bis zur Tiefe 2".\PHP_EOL;
foreach ($FilterIt as $element) {
    if ($element->getEnumerate()) {
        echo implode('.', $element->getFullNumber()) . ' ' . $element . \PHP_EOL;
    } else {
        echo $element . \PHP_EOL;
    }
}
