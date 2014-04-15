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

namespace de\flatplane\pageelements;

use de\flatplane\iterators\RecursiveSectionIterator;
use de\flatplane\iterators\TocElementFilterIterator;
use RecursiveIteratorIterator;

/**
 * Description of Index
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class TableOfContents extends PageElement
{
    protected $maxDepdth;

    public function __construct($maxDepdth = -1)
    {
        $this->maxDepdth = $maxDepdth;
    }

    public function generateStructure()
    {
        $sections = $this->parent->toRoot()->getSections();

        $RecItIt = new RecursiveIteratorIterator(
            new RecursiveSectionIterator($sections),
            RecursiveIteratorIterator::SELF_FIRST
        );

        $RecItIt->setMaxDepth($this->maxDepdth);

        // filtert Einträge heraus, deren ShowInToc-Eigenschaft auf false steht.
        // rückt entsprechend der tiefe mit leerzeichen ein
        $FilterIt = new TocElementFilterIterator($RecItIt);

        foreach ($FilterIt as $element) {
            if ($element->getEnumerate()) {
                echo implode('.', $element->getFullNumber()) . ' ' . $element . PHP_EOL;
            } else {
                echo $element . PHP_EOL;
            }
        }
    }

    public function setLevel($level)
    {
        $this->level = $level;
    }

    public function getMaxDepdth()
    {
        return $this->maxDepdth;
    }

    public function setMaxDepdth($maxDepdth)
    {
        $this->maxDepdth = $maxDepdth;
    }
}
