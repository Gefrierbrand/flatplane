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

include ('vendor'.DIRECTORY_SEPARATOR.'autoload.php');

use de\flatplane\documentContents\Section;

$content = [new Section('test')];

$currentHeight = 0;
$maxHeight = 123;
foreach ($content as $element) {
    if ($element->getType() == 'section' && !$element->getShowInDocument()) {
        continue;
    }
    $contentHeight = $element->getSize()['height']; //maybe as param?

    if ($contentHeight > $maxHeight && !$element->getIsSplitable()) {
        throw new RuntimeException('ELEMENT IS TO BIG AND CAN\'T BE SPLIT');
    }

    //TODO: special considerations: newpage for section titles without content and so on
}
