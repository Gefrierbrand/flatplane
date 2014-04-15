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


use de\flatplane\iterators\RecursiveContentIterator;
use de\flatplane\pageelements\Formula;
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
    'keywords' => 'super, toll, top, gigantisch, superlative!',
    'startIndex' => 1
);

$document = new Document($settings);
$vorwort = new Section('Vorwort');
$vorwort->setEnumerate(false);

$document->addContent($vorwort);

$inhalt = new ListOfContents('Inhaltsverzeichnis', 'section');
$inhalt->setEnumerate(false);
$document->addContent($inhalt);


$kapitel1 = $document->addContent(new Section('kapitel 1'));
//$document->getCounter('section')->setIncrement(2); //erhöhen des increments für NÄCHSTE section  - useCases?
$kapitel1->addContent(new Section('Subkapitel1'));
$kapitel1->addContent(new Section('Subkapitel2', 'alternativtext'));
$subkap3 = $kapitel1->addContent(new Section('Subkapitel3'));

$subkap3->addContent(new Formula('\frac{1}{2}'));
$subkap3->addContent(new Formula('\frac{1}{2}'));
$subkap3->addContent(new Formula('\frac{1}{2}'));

$formelverz = $document->addContent(new ListOfContents('Formelverzeichnis', 'formula', -1, false));
//$document->getCounter('section')->setIncrement(1); //rücksetzen des inkrements
$kap2 = $document->addContent(new Section('titel', '', false));
$kap2->addContent(new Formula('asd'));



echo 'GESAMTES DOKUMENT'.PHP_EOL;
$RecItIt = new RecursiveTreeIterator(
    new RecursiveContentIterator($document->getContent()),
    RecursiveIteratorIterator::SELF_FIRST
);
foreach ($RecItIt as $value) {
    echo $value.PHP_EOL;
}

echo PHP_EOL.PHP_EOL.PHP_EOL;
echo 'Alle nicht ausgeblendeten Kapitel:'.PHP_EOL.PHP_EOL;
$inhalt->generateStructure();

echo PHP_EOL.PHP_EOL.PHP_EOL;
echo 'Alle nicht ausgeblendeten Formeln:'.PHP_EOL.PHP_EOL;
$formelverz->generateStructure();
