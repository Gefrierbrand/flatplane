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

use de\flatplane\iterators\PageElementFilterIterator;
use de\flatplane\iterators\RecursiveContentIterator;
use RecursiveIteratorIterator;

/**
 * Description of ListOfContent
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class ListOfContents extends PageElement
{
    protected $maxDepth;

    public function __construct($title, $type, $maxDepth = -1)
    {
        $this->title = $title;
        $this->type = $type;
        $this->maxDepth = $maxDepth;
    }

    public function generateStructure()
    {
        $content = $this->parent->toRoot()->getContent();

        $RecItIt = new RecursiveIteratorIterator(
            new RecursiveContentIterator($content),
            RecursiveIteratorIterator::SELF_FIRST
        );

        $RecItIt->setMaxDepth($this->maxDepth);

        // filtert Einträge heraus, deren ShowInToc-Eigenschaft auf false steht.
        // rückt entsprechend der tiefe mit leerzeichen ein
        $FilterIt = new PageElementFilterIterator($RecItIt, $this->type);

        foreach ($FilterIt as $element) {
            if ($element->getEnumerate()) {
                echo implode('.', $element->getFullNumber()) . ' ' . $element . PHP_EOL;
            } else {
                echo $element . PHP_EOL;
            }
        }
    }

    public function __toString()
    {
        return (string) $this->title;
    }
}
