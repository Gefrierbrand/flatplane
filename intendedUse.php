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

//Demo, Unfinished! DOES NOT WORK

include 'path_To_flatplane';

//hier ggf use-statements

$document = new document('title');

$document->setAutor('Max Mustermann'); //kann alternativ direkt im Konstruktor gesetzt werden
$document->setDescription('Beschreiubung');
//weitere Dokumenteigenschaften wie Schlüsselwörter usw sind möglich

$inhaltsverzeichnis = $document->addSection('Inhaltsverzeichnis');
$inhaltsverzeichnis->addContent(new TOC); // oder generisches verzeichnis: klasse als argument? // woher inhalt?

$kapitel1 = $document->addSection('EINLEITUNG');
$kapitel1->addContent(new ContentFile('zeugaufderfestplatte.php')); //oder direkt include?
$formel1 = $kapitel1->addContent(new Formula('\frac{1}{2}', 'asana', 'tex')); //formel(code, schriftart, format)
$formel1->setShowInLof(false); //oder index? TOC?
$seite = $formel1->getPage();
$nummerrierung = $formel1->getFullNumber(); // usw


generatePDF($document, 'options...');
