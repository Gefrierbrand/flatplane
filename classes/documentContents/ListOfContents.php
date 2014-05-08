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

use de\flatplane\interfaces\documentelements\ListInterface;
use de\flatplane\iterators\DocumentContentElementFilterIterator;
use de\flatplane\iterators\RecursiveContentIterator;
use RecursiveIteratorIterator;

/**
 * Description of ListOfContent
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class ListOfContents extends AbstractDocumentContentElement implements ListInterface
{
    /**
     * @var int
     *  type of the element
     */
    protected $type = 'list';

    /**
     * @var mixed
     *  use a bool to completely allow/disallow subcontent for the element or
     *  define allowed types as array values: e.g. ['section', 'formula']
     */
    protected $allowSubContent = false;

    /**
     * @var bool
     *  indicates if the element can be split across multiple pages
     */
    protected $isSplitable = true;

    /**
     * @var int
     *  Determines to wich level inside the documenttree the
     *  contents are displayed inside the list. Contents given on the top level
     *  are at depth 0. The actual depth might differ from the contents
     *  level-property, as subtrees can also be processed by this function.
     *  Use -1 for unlimited depth.
     */
    protected $maxDepth = -1;

    /**
     * @var array
     *  Array containing the content-types to be included in the list.
     *  For example use ['image', 'table'] to list all images and all tables
     *  wich have their 'showInList' property set to true.
     */
    protected $displayTypes = ['section'];

    /**
     * @var array
     *  todo: fixme
     */
    protected $indent = ['level' => -1, 'maxAmount' => 20, 'mode' => 'relative'];

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
        //todo: validate content type and parent
        if (empty($content)) {
            $content = $this->getParent()->toRoot()->getContent();
        }

        $RecItIt = new RecursiveIteratorIterator(
            new RecursiveContentIterator($content),
            RecursiveIteratorIterator::SELF_FIRST
        );

        $RecItIt->setMaxDepth($this->getMaxDepth());

        $FilterIt = new DocumentContentElementFilterIterator(
            $RecItIt,
            $this->getDisplayTypes()
        );


        //TODO: return array with level; resolve hard references?
        foreach ($FilterIt as $element) {
            //echo "tiefe: ".$RecItIt->getDepth()." "; // current iteration depth
            //echo " tiefe: ".$element->getLevel()." "; // element depth regarding document
            if ($element->getEnumerate()) {
                //tiefe durch einrücken darstellen FIXME !!!! (nur sinnvoll, wenn erstes element auf level 0 liegt -> filterproblem?) tiefe aus nummerrierung ermitteln?
                //$depth = $RecItIt->getDepth();
                $depth = count($element->getNumbers())-1; //extra parameter um level abzuziehen? oder ohne einrückung?

                //todo: use indent pr
                echo str_repeat(' ', 2*$depth);

                echo $element->getFormattedNumbers().
                ' ' .$element.PHP_EOL;
            } else {
                echo $element.PHP_EOL;
            }
        }
    }

    public function getDisplayTypes()
    {
        return $this->displayTypes;
    }

    public function getMaxDepth()
    {
        return $this->maxDepth;
    }
}
