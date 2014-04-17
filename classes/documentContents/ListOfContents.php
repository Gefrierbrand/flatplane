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

namespace de\flatplane\documentContents;

use de\flatplane\iterators\DocumentContentElementFilterIterator;
use de\flatplane\iterators\RecursiveContentIterator;
use RecursiveIteratorIterator;

/**
 * Description of ListOfContent
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class ListOfContents extends DocumentContentElement
{
    protected $maxDepth;
    protected $type='section'; //use section here to be able to include self
    protected $displayTypes;
    protected $allowSubContent = false;

    public function __construct(
        $title,
        $displayTypes,
        $maxDepth = -1,
        $enumerate = true,
        $showInIndex = true
    ) {
        $this->title = $title;
        if (!is_array($displayTypes)) {
            $displayTypes = [$displayTypes];
        }
        $this->displayTypes = $displayTypes;
        $this->maxDepth = $maxDepth;
        $this->enumerate = $enumerate;
        $this->showInIndex = $showInIndex;
    }

    public function generateStructure(array $content = [])
    {
        if (empty($content)) {
            $content = $this->parent->toRoot()->getContent();
        }

        $RecItIt = new RecursiveIteratorIterator(
            new RecursiveContentIterator($content),
            RecursiveIteratorIterator::SELF_FIRST
        );

        $RecItIt->setMaxDepth($this->maxDepth);

        $FilterIt = new DocumentContentElementFilterIterator($RecItIt, $this->displayTypes);

        foreach ($FilterIt as $element) {
            if ($element->getEnumerate()) {
                //todo: don't use implode?
                echo implode('.', $element->getNumbers()) .
                    ' ' . $element->getAltTitle() . PHP_EOL;
            } else {
                echo $element . PHP_EOL;
            }
        }
    }

    public function getDisplayTypes()
    {
        return $this->displayTypes;
    }

    public function setDisplayTypes($displayTypes)
    {
        if (!is_array($displayTypes)) {
            $displayTypes = [$displayTypes];
        }
        $this->displayType = $displayTypes;
    }
}
