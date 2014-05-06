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
    protected $type='list';
    /**
     * @var array $settings
     * Array containing key=>value pairs of configuration and style options.
     * Required keys for lists are:
     * <ul>
     *  <li>enumerate (bool): determines if the list itself is enumerated</li>
     *  <li>showInLists (bool): determines if the list can be shown in other
     *   lists</li>
     *  <li>allowSubContent (mixed): determines if the list can contain other
     *   content</li>
     *  <li>isSplitable (bool): indicates whether the list can be printed across
     *   multiple pages</li>
     *  <li>maxDepth (int): Determines to wich level inside the documenttree the
     *   contents are displayed inside the list. Contents given on the top level
     *   are at depth 0. This value might differ from the contents level-property,
     *   as subtrees might also be processed by this function.
     *   Use -1 for unlimited depth.</li>
     *  <li>displayTypes (array): Array containing the content-types to be
     *   included in the list. For example use ['image', 'table'] to list all
     *   images and all tables wich have their 'showInList' setting set to true
     *   in one index.</li>
     *  <li>propDisplay (array): Array containing the 'properties' / 'settings' of
     *   the listed objects wich will be displayes as their name</li>
     *  <li>indent (array): key maxLevel: (int) defines to which depth the
     *   list entries should be indented: use 0 for off and -1 for unlimited
     *   key amount (int): defines how far a level should be indented, in
     *   character-widths</li>
     * </ul>
     */
    protected $settings = ['enumerate' => true,
                           'showInList' => true,
                           'allowSubContent' => false,
                           'isSplitable' => true,
                           'maxDepth' => -1,
                           'displayTypes' => ['section'],
                           'propDisplay' => ['altTitle'],
                           'indent' => ['maxLevel' => -1, 'amount' => 4]];

    /**
     * Generates a new list of arbitrary content elements. Used to create a
     * Table of Contents (TOC) or List of Figures (LOF) and so on.
     * @param array $config
     *  Array containing key=>value pairs of configuration and style options
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
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
            $content = $this->getParent()->toRoot()->getContent();
        }

        $RecItIt = new RecursiveIteratorIterator(
            new RecursiveContentIterator($content),
            RecursiveIteratorIterator::SELF_FIRST
        );

        $RecItIt->setMaxDepth($this->getSettings('maxDepth'));

        $FilterIt = new DocumentContentElementFilterIterator(
            $RecItIt,
            $this->getSettings('displayTypes')
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
        foreach ($this->getSettings('display') as $prop) {
            $methodName = 'get'.ucfirst($prop);
            if (method_exists($element, $methodName)) {
                $erg[] = $element->{$methodName}();
            }
        }
        return implode(' ', $erg);
    }

    public function getDisplayTypes()
    {
        return $this->getSettings('display');
    }

    public function setDisplayTypes($displayTypes)
    {
        if (!is_array($displayTypes)) {
            $displayTypes = [$displayTypes];
        }
        $this->setSettings(['display' => $displayTypes]);
    }

    public function getMaxDepth()
    {
        return $this->getSettings('maxDepth');
    }

    public function getPropertiesToDisplay()
    {
        return $this->getSettings('propDisplay');
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
