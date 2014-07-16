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

namespace de\flatplane\interfaces;

use de\flatplane\documentElements\Code;
use de\flatplane\interfaces\documentElements\DocumentInterface;
use de\flatplane\interfaces\documentElements\FormulaInterface;
use de\flatplane\interfaces\documentElements\ImageInterface;
use de\flatplane\interfaces\documentElements\TableInterface;
use de\flatplane\interfaces\documentElements\TextInterface;
use de\flatplane\interfaces\documentElements\SectionInterface;
use Symfony\Component\Process\Exception\RuntimeException;
use TCPDF;

/**
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
interface DocumentElementInterface
{
    /**
     * This method is called on creating a new element.
     * @param array $config
     *  Array containing key=>value pairs wich overwrite the default properties.
     *  e.g.: $config = ['enumerate' => false] will disable numbering for the
     *  created instance
     */
    public function __construct(array $config);

    /**
     * get a string representation of the current object
     * @return string
     */
    public function __toString();

    /**
     * Adds a new Counter with the given name to the current object. If a counter
     * with that name already exists it will be overwritten with the new counter.
     * @param CounterInterface $counter
     * @param string $name
     * @return CounterInterface
     */
    public function addCounter(CounterInterface $counter, $name);

    /**
     * Checks if a counter for the content-type already exists on the current
     * level and increments its value, or creates a new one for that content-type
     * @param DocumentElementInterface $content
     * @return Counter
     *  Counter for the given content-type
     */
    public function checkLocalCounter(DocumentElementInterface $content);

    /**
     * Indicates whether the current object cointains other content objects
     * @return bool
     */
    public function hasContent();

    /**
     * This method calls itself recursively until the root node (Document)
     * is reached
     * @return DocumentInterface
     */
    public function toRoot();

    /**
     * This method travels recursively upwards in the document tree until the
     * given depth from $level is reached an returns a reference to the reached
     * object
     * @param int $level
     * @return DocumentElementInterface
     */
    public function toParentAtLevel($level);

    /**
     * This method sets the current graphic state in the PDF. This includes
     * fonts, cell-margins and -paddings and colors.
     * @param string $key (optional)
     *  name of a specific configuration directive to use if multiple styles are
     *  defined for the object. e.g. 'level1'
     */
    public function applyStyles($key = null);

    /**
     * Adds an arbitrary element of type $type to the current content
     * @param string $type
     * @param array $settings (optional)
     * @return DocumentElementInterface
     *  Content object implementing DocumentElementInterface with its parent set
     *  to the current object
     */
    public function addElement($type, array $settings = []);

    /**
     * Adds a new Section to the current element
     * @param string $title
     * @param array $settings (optional)
     * @return SectionInterface
     *  Content object implementing SectionInterface
     */
    public function addSection($title, array $settings = []);

    /**
     * Adds a new formula to the current element
     * @param string $code
     *  TeX or MathMl description of the formula
     * @param array $settings (optional)
     * @return FormulaInterface
     */
    public function addFormula($code, array $settings = []);

    /**
     * Adds a new Image to the current element
     * @param string $path
     * @param array $settings (optional)
     * @return ImageInterface
     * @throws RuntimeException
     */
    public function addImage($path, array $settings = []);

    /**
     * Adds a new Table to the current element
     * @param string $code
     *  HTML representation of the table
     * @param array $settings (optional)
     * @return TableInterface
     */
    public function addTable($code, array $settings = []);

    /**
     * Adds Text (including references or footnotes) from a file
     * @param string $path
     * @param array $settings (optional)
     * @return TextInterface
     */
    public function addTextFile($path, array $settings = []);

    /**
     * Adds Text (without references or footnotes) from a string
     * @param string $text
     * @param array $settings (optional)
     * @return TextInterface
     */
    public function addText($text, array $settings = []);

    /**
     * Adds highlighted PHP Code as text from a file
     * @param string $path
     * @param array $settings (optional)
     * @return Code
     */
    public function addCodeFile($path, array $settings = []);

    /**
     * Get the Parent of the current element
     * @return DocumentElementInterface
     */
    public function getParent();

    /**
     * Gets the level (=depth) of the current element inside the document tree
     * @return int
     */
    public function getLevel();

    /**
    * @return array
    *  Returns a multilevel array containing references to
    *  DocumentContentElement instances
    */
    public function getContent();

    /**
     * Get the elements type
     * @return string
     */
    public function getType();

    /**
     * Get the vertical dimensions and the number of pages needed to display the
     * current element.
     * @param float $startYPosition (optional)
     * @return Array
     *  keys: 'height', 'numPages', 'endYposition'
     */
    public function getSize($startYPosition = null);

    /**
     * Get the number of the page this element gets printed on. If the element
     * spans multiple pages, this number references the first occurrence.
     * @return int
     */
    public function getPage();

    /**
     * Get the linear starting page number of the current element
     * @return int
     */
    public function getLinearPage();

    /**
     * Get all numbers for the current element and its parents up to the root
     * @return array
     *  Array containing objects implementing NumberInterface
     */
    public function getNumbers();

    /**
     * Get the numbers of the current object formatted as string according to
     * the elements settings
     * @return string
     */
    public function getFormattedNumbers();

    /**
     * Returns an existing Counter for the given name or creates a new one if
     * a counter with that name is not already present. This might create
     * unwanted side effects like wrong element-numbering and therefore also
     * triggers an error in that case.
     * @param string $name
     * @return CounterInterface
     */
    public function getCounter($name);

    /**
     * Returns all defined Counters for the current level as array
     * @return array
     */
    public function getCounterArray();

    /**
     * Get the label of the current Element. (used as reference Identifier)
     * @return string
     */
    public function getLabel();

    /**
     * @return mixed
     *  Eiter returns a bool to enable or disable all subcontent or an array
     *  containing the names of allowed types
     */
    public function getAllowSubContent();

    /**
     * @return bool
     *  Indicates whether the element will recieve a number
     */
    public function getEnumerate();

    /**
     * @return bool
     *  Indicates whether the element will be shown in lists for the elements type
     */
    public function getShowInList();

    /**
     * determines whether an element can be split accross multiple pages
     * (Currently not implemented)
     * @return bool
     */
    public function getIsSplitable();

    /**
     * Get the title of the current element
     * @return type
     */
    public function getTitle();

    /**
     * Get an alternate version of the title (used in lists and the header).
     * If no alternate title is defined, the standard title is used.
     * @return string
     */
    public function getAltTitle();

    /**
     * Get the font name for the given key (like 'level1'). If the key is omitted
     * or not defined in the elements configuration, a default value is returned.
     * @param string $key (optional)
     * @return string
     */
    public function getFontType($key = null);
    /**
     * Get the fontsize (in pt) for the given key (like 'level1'). If the key is
     * omitted or not defined in the elements configuration, a default value is
     * returned.
     * @param string $key (optional)
     * @return string|int
     *  Fontsize (in pt)
     */
    public function getFontSize($key = null);

    /**
     * Get the font style (bold, italic, etc.) for the given key (like 'level1').
     * If the key is omitted or not defined in the elements configuration, a
     * default value is returned. The 'normal' font style is represented by an
     * empty string
     * @param string $key (optional)
     * @return string
     */
    public function getFontStyle($key = null);

    /**
     * Get the font color for the given key (like 'level1').
     * If the key is omitted or not defined in the elements configuration, a
     * default value is returned.
     * @param string $key (optional)
     * @return array
     *  Array containig 1, 3 or 4 numbers to represent a color in grayscale, RGB
     *  or CMYK
     * @see TCPDF::setColor()
     */
    public function getFontColor($key = null);

    /**
     * Get the draw color for the given key (like 'level1'). This color is used
     * for borders, under- or overline and other non-text elements in the PDF.
     * If the key is omitted or not defined in the elements configuration, a
     * default value is returned.
     * @param string $key (optional)
     * @return array
     *  Array containig 1, 3 or 4 numbers to represent a color in grayscale, RGB
     *  or CMYK
     * @see TCPDF::setColor()
     */
    public function getDrawColor($key = null);
    /**
     * Get the fill color for the given key (like 'level1'). This color is used
     * to fill cells in the PDF.
     * If the key is omitted or not defined in the elements configuration, a
     * default value is returned.
     * @param string $key (optional)
     * @return array
     *  Array containig 1, 3 or 4 numbers to represent a color in grayscale, RGB
     *  or CMYK
     * @see TCPDF::setColor()
     */
    public function getFillColor($key = null);

    /**
     * Get the minimum clearance (=margin) around the element for the specified
     * direction (e.g. 'top'). If no key is provides or the key does not exist,
     * a default value is returned.
     * @param string $key (optional)
     * @return mixed
     *  String or numeric margin amount
     */
    public function getMargins($key = null);

    /**
     * Get the minimum inside clearane (=paddings) of the element. (Currently not
     * used)
     *
     * @param string $key (optional)
     * @return mixed
     */
    public function getPaddings($key = null);

    /**
     * Sets the parent to an instance implementing DocumentElementInterface
     * @param DocumentElementInterface $parent
     */
    public function setParent(DocumentElementInterface $parent);

    /**
     * Set the numbers of the current element
     * @param array $numbers
     *  Array containing values of the numbers or objects implementing the
     *  NumberInterface
     */
    public function setNumbers(array $numbers);

    /**
     * set a label as identifier for references
     * @param string $label
     */
    public function setLabel($label);

    /**
     * Set the pagenumber (start) of the current element
     * @param int $page
     */
    public function setPage($page);

    /**
     * Get Hyphenation settings (on/off)
     * @return bool
     */
    public function getHyphenate();

    /**
     * Hyphenate the title and (if present) the altTitle of the element using the
     * documents hyphenation options if the hyphenate property is set to true.
     */
    public function hyphenateTitle();

    /**
     * Generates the output of the element inside the PDF
     * @return int
     *  number of pagebreaks caused by the elements display
     */
    public function generateOutput();

    /**
     * Get often needed page dimensions for the current page inside the PDF
     * @return array
     *  Keys: pageWidth, textWidth, pageHeight, textHeight
     */
    public function getPageMeasurements();

    /**
     * Set the linear (starting) page number of the current element
     * @param int $linearPage
     */
    public function setLinearPage($linearPage);

    /**
     * Creates a reference to a source
     * @param string $source
     * @param string $extras (optional)
     *  additional text (e.g. Pages 2-7) to be displayed in the text
     * @return string
     *  Formatted number and $extras to indicate the referenced source
     */
    public function cite($source, $extras = '');

    /**
     * Get the link identifier for the current element
     * @return int
     * @see TCPDF:addLink
     */
    public function getLink();

    /**
     * Set the link identifier for the current element
     * @param ressource $link
     * @see TCPDF::AddLink
     */
    public function setLink($link);

    /**
     * Get the pagegroup of the current element
     * @return string
     */
    public function getPageGroup();

    /**
     * Set the pagegroup for the current element.
     * This group is maintained by all following elements unless they specify a
     * new pagegroup
     * @param string $pagegroup
     */
    public function setPageGroup($pagegroup);
}
