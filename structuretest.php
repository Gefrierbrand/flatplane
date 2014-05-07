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
require 'vendor'.DIRECTORY_SEPARATOR.'autoload.php';

//lange, volldefinierte Klassennamen aus Namespaces laden


use de\flatplane\documentContents\Document;
use de\flatplane\documentContents\ElementFactory;
use de\flatplane\utilities\Config;
use de\flatplane\utilities\Timer;

$t = new Timer;
$factory = new ElementFactory;

/*
 * BEGIN DOKUMENTDEFINITION
 */

//Vom Standard abweichende dokumentweite Einstellungen setzen
$settings = array(
    'author' => 'Max Mustermann',
    'title' => 'Ganz wichtiges Dokument',
    'keywords' => 'super, toll, top, gigantisch, superlative!',
    'startIndex' => ['section' => 0],
    'numberingPrefix' => ['formula' => '['],
    'numberingPostfix' => ['formula' => ']'],
    'numberingSeparator' => ['formula' => '#'],
    'numberingLevel' => ['formula' => -1]
);

$document = $factory->createDocument($settings);

$vorwort = $factory->createSection(['title' => 'Vorwort']);
$einleitung = $factory->createSection(['title' => 'Einleitung', 'label' => 'sec:einleitung']);

//$inhaltsverzeichnis = $factory->createElement('list', []);

//$vorwort->setEnumerate(false);

$document->addContent($vorwort);
$document->addContent($einleitung);

var_dump($document);
