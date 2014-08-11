<?php

/*
 * Copyright (C) 2014 Nikolai Neff <admin@flatplane.de>.
 *
 * This file is part of Flatplane.
 *
 * Flatplane is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * Flatplane is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Flatplane.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace de\flatplane\interfaces\documentElements;

use de\flatplane\interfaces\DocumentElementInterface;

/**
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
interface ListInterface extends DocumentElementInterface
{
    /**
     * This method traverses the document-tree and filters for the desired
     * contenttypes to be displayed. It then generates an array corresponding to
     * a line in the finished list.
     * @param array $content
     *  Array containing objects implementing DocumentElementInterface
     * @return array
     *  Array with information for each line: formatted Number, absolute and
     *  relative depth, Text determined by the elements altTitle property
     */
    public function generateStructure(array $content);

    /**
     * Get the maximum iteration depth
     * @return int
     */
    public function getMaxDepth();

    /**
     * Get the element-types to be displayed in the list
     * @return array
     */
    public function getDisplayTypes();

    /**
     * Get the generated list-structure as array
     * @return array
     * @see generateStructure()
     */
    public function getData();

    /**
     * Set the maximum iteration depth
     * @param int $maxDepth
     *  use -1 for infinite
     */
    public function setMaxDepth($maxDepth);

    /**
     * Set the element-types to be displayed in the list
     * @param array $displayTypes
     *  array containing the element-types as strings
     */
    public function setDisplayTypes(array $displayTypes);

    /**
     * Set the element-indentation mode and maxlevel
     * @param array $indent
     */
    public function setIndent(array $indent);

    /**
     * @return bool
     */
    public function getShowPages();

    /**
     * Get the DrawLinesToPage option for the level specified by $key
     * @param string $key (optional)
     * @return bool
     */
    public function getDrawLinesToPage($key = null);

    /**
     * Get the pagenumbering line-style for the level defined by $key
     * @param sting $key (optional)
     * @return array
     */
    public function getLineStyle($key = null);

    /**
     * Enable/Disable the display of pagenumbers in the list
     * @param bool $showPages
     */
    public function setShowPages($showPages);

    /**
     * Enable/Disable the drawing of lines to the pagenumber for each level
     * @param array $drawLinesToPage
     */
    public function setDrawLinesToPage(array $drawLinesToPage);

    /**
     * Set the style of the line from the entry to the pagenumbers
     * @param array $lineStyle
     */
    public function setLineStyle(array $lineStyle);

    /**
     * Get the minimal distance between the elements title-text an the page
     * numbers in user-units
     * @return float
     */
    public function getMinPageNumDistance();

        /**
     * Set the minimal distance between the elements title-text an the page
     * numbers in user-units
     * @param float $minPageNumDistance
     */
    public function setMinPageNumDistance($minPageNumDistance);

    /**
     * Get the indentaion settings
     * @return array
     * @throws OutOfRangeException
     */
    public function getIndent();

    /**
     * @return float
     */
    public function getMinTitleWidthPercentage();

    /**
     * Get the horizontal space reserverd for the pagenumbers in user-units
     * @return float
     */
    public function getPageNumberWidth();

    /**
     * @param float $minTitleWidthPercentage
     */
    public function setMinTitleWidthPercentage($minTitleWidthPercentage);

    /**
     * Set the horizontal space reserverd for the pagenumbers in user-units
     * @param float $pageNumberWidth
     */
    public function setPageNumberWidth($pageNumberWidth);

    /**
     * Get the approx. space between the numbering and the text in the list (in em)
     * @return float
     */
    public function getNumberSeparationWidth();

    /**
     * Set the approx. space between the numbering and the text in the list (in em)
     * @param type $numberSeparationWidth
     */
    public function setNumberSeparationWidth($numberSeparationWidth);

    /**
     * Get the default indentaion amount (includein space for numbering) if
     * the indentation is disabled (in user-units)
     * @return float
     */
    public function getDefaultTextIndent();

    /**
     * Set the default indentaion amount (includein space for numbering) if
     * the indentation is disabled (in user-units)
     * @param float $defaultTextIndent
     */
    public function setDefaultTextIndent($defaultTextIndent);
}
