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

use de\flatplane\interfaces\documentElements\ListInterface;
use de\flatplane\iterators\ShowInListFilterIterator;
use de\flatplane\iterators\RecursiveContentIterator;
use RecursiveIteratorIterator;

/**
 * Description of ListOfContent
 * todo: generate lists for individual sections, not the whole document
 * @author Nikolai Neff <admin@flatplane.de>
 */
class ListOfContents extends AbstractDocumentContentElement implements ListInterface
{
    /**
     * @var int
     *  type of the element
     */
    protected $type = 'list';

    protected $title = 'list';
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
     * todo: implement/ fix
     * @var array
     */
    protected $verticalFill = ['default' => ['type' => 'dots', 'spacing' => 1, 'size' => 'inherit']];

    /**
     * Array containig the lists raw data for outputting
     * @var array
     */
    protected $data = [];

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
     * todo: order by: level, structure, content-type
     * This method traverses the document-tree and filters for the desired
     * contenttypes to be displayed. It then generates an array corresponding to
     * a line in the finished list.
     * @param array $content
     *  Array containing objects implementing DocumentElementInterface
     * @return array
     *  Array with information for each line: formatted Number, absolute and
     *  relative depth, Text determined by the elements __toString() method.
     */
    public function generateStructure(array $content)
    {
        //todo: validate content type and parent
        if (empty($content)) {
            //$content = $this->getParent()->toRoot()->getContent();
            trigger_error('no content to generate structure for', E_USER_NOTICE);
        }

        $RecItIt = new RecursiveIteratorIterator(
            new RecursiveContentIterator($content),
            RecursiveIteratorIterator::SELF_FIRST
        );

        $RecItIt->setMaxDepth($this->getMaxDepth());

        $FilterIt = new ShowInListFilterIterator(
            $RecItIt,
            $this->getDisplayTypes()
        );

        foreach ($FilterIt as $key => $element) {
            // current iteration depth
            $this->data[$key]['iteratorDepth'] = $RecItIt->getDepth();
            // element depth regarding document structure
            $this->data[$key]['level'] = $element->getLevel();
            if ($element->getEnumerate()) {
                $this->data[$key]['numbers'] = $element->getFormattedNumbers();
            } else {
                $this->data[$key]['numbers'] = null;
            }
            $this->data[$key]['text'] = $element->__toString();
            $this->data[$key]['page'] = $element->getPage();
        }

        //fixme: return?
        return $this->data;
    }

    public function getDisplayTypes()
    {
        return $this->displayTypes;
    }

    public function getMaxDepth()
    {
        return $this->maxDepth;
    }

    protected function setMaxDepth($maxDepth)
    {
        //cast to int as all settings from ini files are returned as strings
        $maxDepth = (int) $maxDepth;
        if ($maxDepth < -1) {
            trigger_error('Invalid Max depth, defaulting to -1', E_USER_NOTICE);
            $maxDepth = -1;
        }
        $this->maxDepth = $maxDepth;
    }

    public function setDisplayTypes(array $displayTypes)
    {
        $this->displayTypes = $displayTypes;
    }

    public function setIndent(array $indent)
    {
        $this->indent = $indent;
    }

    public function getSize()
    {
        //todo: implement me
    }
}
