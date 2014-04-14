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

//putenv('LANG=en_US.UTF-8');

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


use de\flatplane\pageelements\ListOfContents;
use de\flatplane\pageelements\Section;
use de\flatplane\structure\Document;

/*
 * BEGIN DOKUMENTDEFINITION
 */

//Vom Standard abweichende dokumentweite Einstellungen setzen
$settings = array(
    'author' => 'Max Mustermann',
    'title' => 'Ganz wichtiges Dokument',
    'keywords' => 'super, toll, top, gigantisch, superlative!'
);

$document = new Document($settings);

$vorwort = $document->addContent(new Section('Vorwort'));
$vorwort->setEnumerate(false);

$inhalt = $document->addContent(new ListOfContents('Inhaltsverzeichnis', 'section'));

$kapitel1 = $document->addContent(new Section('kapitel 1'));
$kapitel1->addContent(new Section('Subkapitel1'));
$kapitel1->addContent(new Section('Subkapitel2'));
$kapitel1->addContent(new Section('Subkapitel3'));

$inhalt->generateStructure();




////in zweierschritten zählen. because we can.
////$document->getCounter('section')->setIncrement(2);
//$vorwort = $document->addSection('Vorwort');
//$vorwort->setEnumerate(false);
//$vorwort->addContent(new Text('GANZ VIEL TEXT'));
//$inhaltsverzeichnis = $document->addSection('Inhaltsverzeichnis');
//$inhaltsverzeichnis->setEnumerate(false);
//$inhaltsverzeichnis->setContent(new TableOfContents);
//
//$kapitel1 = $document->addSection('TITEL 1');
////alternative Titel fürs TOC
//$kapitel2 = $document->addSection('TITEL 2', 'Hier steht was ganz anderes');
//$subKapitel = $kapitel2->addSubSection('Sub 1');
//$subKapitel->getCounter('section')->setIncrement(-1); //negative Zählrichtung ist möglich
//$subsubKap1 = $subKapitel->addSubSection('SubSub 1');
//$subsubKap2 = $subKapitel->addSubSection('SubSub 2', '', false, true); //ShowInTOC = false, enumerate = true;
//$subsubKap3 = $subKapitel->addSubSection('SubSub 3', '', true, false); //ShowInTOC = true, enumerate = false;
////das handle kann überschrieben werden, ohne dass der Inhalt des alten Elements beeinflusst wird.
////das sollte man aber eigentlich nicht tun. Die meisten IDEs warnen auch entsprechend.
//$subsubKap4 = $subKapitel->addSubSection('SubSub 4');
//$subsubKap4 = $subKapitel->addSubSection('SubSub 5');
//
//$inhaltsverzeichnis2 = $subsubKap4->addSubSection('Inhaltsverzeichnis2');
//$inhaltsverzeichnis2->setEnumerate(false);
//$inhaltsverzeichnis2->setContent(new TableOfContents);
//
////inhalte zu kapitel hinzufügen
//$formela = $subsubKap4->addContent(new Formula('\frac{1}{a}'));
//$formelb = $subsubKap4->addContent(new Formula('\frac{1}{b}'));
//$formelc = $subsubKap4->addContent(new Formula('\frac{1}{c}'));
//
//
//$subsubKap6 = $subKapitel->addSubSection('SubSub 6');
////alternative methode um optionen zu setzen, ohne alle anderen parameter
////direkt beim aufruf angeben zu müssen
//$subsubKap6->setEnumerate(false);
//
//// die Zuweisung der Unterkapitel in eine neue Variable ist nur nötig, wenn später
//// noch (einfach) auf das Element zugegriffen werden soll.
//$subsubKap4->addSubSection('noch eins tiefer');
//$subsubKap4->addSubSection('wer a sagt muss auch b sagen');
//
////will man dann dennoch subsections hinzufügen, ist dies wiefolgt möglich:
//$subsubKap4->getChildren()[1]->addSubSection('ganzweitunten');
//$subsubKap4->getChildren()[1]->addSubSection('ganzweitunten2');
//
//$kapitel3 = $document->addSection('TITEL 3');
//$kapitel3->getCounter('section')->setValue(-2); //subsectionzähler auf -2 setzen
//$subKapitel2 = $kapitel3->addSubSection('ich bin garnicht da', '', false); //zählstand -1, wird aber nicht angezeigt
//$subKapitel3 = $kapitel3->addSubSection('zählertest'); //zähler 'beginnt' bei null
//
////subsections können auch später angegeben werden, ohne struktur oder zähler zu stören
//$kapitel1->addSubSection('test');
//$kapitel1->addSubSection('test2');
//
////inhalt hinzufügen
//$kapitel1->getCounter('formula')->setValue(-2);
//$formel1 = $kapitel1->addContent(new Formula('\frac{1}{2}', 'Asana-Math', 'TeX')); //formel(code, schriftart, format)
//$formel2 = $kapitel1->addContent(new Formula('\frac{1}{3}', 'Asana-Math', 'TeX'));
//$formel3 = $kapitel1->addContent(new Formula('\frac{1}{4}', 'Asana-Math', 'TeX'));
//
////kapitel etc werden jedoch immer in der Reihenfolge der Deklarationen angelegt
////ein Vertauschen währe jedoch prinzipiell möglich
//
//$LOE = $document->addSection('Formelverzeichnis');
//$LOE->setEnumerate(false);
//$LOE->setContent(new ListOfContents('formula'));
//
//
///*
// * ENDE DOKUMENTDEFINITION
// */
//
//
//
////funktionen zum anzeigen
//
////Es können beliebig viele Inhaltsverzeichnisse an beliebigen Stellen des Dokuments
////verwendet werden. TODO: inhalt für einzelne kapitel
//$inhaltsverzeichnis->getContent()[0]->generateStructure();
//
//echo PHP_EOL.PHP_EOL.PHP_EOL;
//
//$inhaltsverzeichnis2->getContent()[0]->setMaxDepdth(2);
//$inhaltsverzeichnis2->getContent()[0]->generateStructure();
//
//echo PHP_EOL.PHP_EOL.PHP_EOL;
//
//$LOE->getContent()[0]->generateStructure();
