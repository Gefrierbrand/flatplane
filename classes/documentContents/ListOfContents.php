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

use de\flatplane\interfaces\DocumentElementInterface;
use de\flatplane\iterators\DocumentContentElementFilterIterator;
use de\flatplane\iterators\RecursiveContentIterator;
use RecursiveIteratorIterator;

/**
 * Description of ListOfContent
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class ListOfContents extends AbstractDocumentContentElement
{
    protected $maxDepth;
    protected $type='list';
    protected $displayTypes;
    protected $allowSubContent = false;
    protected $isSplitable = true;
    protected $propertiesToDisplay = ['altTitle'];

    /**
     * Generates a new list of arbitrary content elements. Used to create a
     * Table of Contents (TOC) or List of Figures (LOF) and so on.
     * @param string $title
     *  The title of the list.
     * @param array $displayTypes
     *  Array containing the content-types to be included in the list.
     *  For example ['image','table'] to list all images and tables in one index.
     * @param int $maxDepth
     *  Determines to wich level inside the documenttree the contents are
     *  displayed inside the list. Contents given on the top level are at
     *  depth 0. This value might differ from the contents 'level' property,
     *  as subtrees might also be processed by this function.
     *  Use -1 for unlimited depth.
     * @param bool $enumerate
     *  Determines wether the list will be numbered
     * @param bool $showInIndex
     *  Determines if this list is shown inside other lists (including itself if
     *  the displaytype includes section)
     */
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

    /**
     * This method traverses the documenttree and filters the desired content-
     * types to be displayed
     * @param array $content (optional)
     *  Array containing objects implementing DocumentContentStructureInterface
     */
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

        $FilterIt = new DocumentContentElementFilterIterator(
            $RecItIt,
            $this->displayTypes
        );


        //TODO: return array with level; resolve hard references
        foreach ($FilterIt as $element) {
            //echo "tiefe: ".$RecItIt->getDepth()." "; // current iteration depth
            //echo " tiefe: ".$element->getLevel()." "; // element depth regarding document
            if ($element->getEnumerate()) {
                //tiefe durch einrücken darstellen FIXME !!!! (nur sinnvoll, wenn erstes element auf level 0 liegt -> filterproblem?) tiefe aus nummerrierung ermitteln?
                //$depth = $RecItIt->getDepth();
                $depth = count($element->getNumbers())-1; //extra parameter um level abzuziehen? oder ohne einrückung?

                echo str_repeat(' ', 2*$depth);

                echo $element->getFormattedNumbers().
                ' ' .
                $this->getPropertiesAsString($element).
                PHP_EOL;
            } else {
                echo $this->getPropertiesAsString($element) . PHP_EOL;
            }
        }
    }

    protected function getPropertiesAsString(DocumentElementInterface $element)
    {
        foreach ($this->propertiesToDisplay as $prop) {
            $methodName = 'get'.ucfirst($prop);
            if (method_exists($element, $methodName)) {
                $erg[] = $element->{$methodName}();
            }
        }
        return implode(' ', $erg);
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

    public function getMaxDepth()
    {
        return $this->maxDepth;
    }

    public function getPropertiesToDisplay()
    {
        return $this->propertiesToDisplay;
    }

    public function setMaxDepth($maxDepth)
    {
        $this->maxDepth = $maxDepth;
    }

    public function setPropertiesToDisplay(array $propertiesToDisplay)
    {
        $this->propertiesToDisplay = $propertiesToDisplay;
    }

    public function getStyle()
    {
        if (empty($this->style)) {
            $this->setStyle(new ListStyle);
        }
        return $this->style;
    }
}
