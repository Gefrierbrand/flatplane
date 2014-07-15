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

use de\flatplane\documentElements\ElementFactory;
use de\flatplane\documentElements\Source;
use de\flatplane\documentElements\TitlePage;
use de\flatplane\interfaces\DocumentElementInterface;
use de\flatplane\utilities\PDF;
use Symfony\Component\Process\Exception\RuntimeException;
use TCPDF;
use TCPDF_STATIC;

/**
 * todo: doc
 * @author Nikolai Neff <admin@flatplane.de>
 */
interface DocumentInterface extends DocumentElementInterface
{
    /**
     * Add an instance of DocumentElementInterface to the internal lable list
     * @param DocumentElementInterface $instance
     */
    public function addLabel(DocumentElementInterface $label);

    /**
     * Add a soucre to the documents sources list
     * @param string $label
     *  reference identifier
     * @param array $settings
     * @return Source
     */
    public function addSource($label, array $settings);

    /**
     * Add sources to the documents sources list from a BibTex file
     * @param string $BibTexFile
     *  path to the BibTeX file
     * @throws RuntimeException
     */
    public function addBibTexSources($BibTexFile);

    /**
     * Get the numbering level for the given element type
     * @param string $type (optional)
     * @return int
     */
    public function getNumberingLevel($type = '');

    /**
     * Get the numbering format for the given element type
     * @param string $type (optional)
     * @return string
     */
    public function getNumberingFormat($type = '');

    /**
     * Get the starting index for the given element type
     * @param string $type (optional)
     * @return int
     */
    public function getStartIndex($type = '');

    /**
     * Get the enumeration prefix for the given element type
     * @param string $type (optional)
     * @return string
     */
    public function getNumberingPrefix($type = '');

    /**
     * Get the enumeration postfix for the given element type
     * @param string $type (optional)
     * @return string
     */
    public function getNumberingPostfix($type = '');

    /**
     * Get the numbering separator for the given element type
     * @param string $type (optional)
     * @return string
     */
    public function getNumberingSeparator($type = '');

    /**
     * Set the numbering level for the given element types
     * @param array $numberingLevel
     *  Array containig elementtype=>numberinglevel pairs for each element-type
     */
    public function setNumberingLevel(array $numberingLevel);

    /**
     * Set the numbering format for the given element types
     * @param array $numberingFormat
     *  Array containig elementtype=>numberingformat pairs for each element-type
     */
    public function setNumberingFormat(array $numberingFormat);

    /**
     * Set the start index for the given element types
     * @param array $startIndex
     *  Array containig elementtype=>startindex pairs for each element-type
     */
    public function setStartIndex(array $startIndex);

    /**
     * Set the numbering prefix for the given element types
     * @param string $numberingPrefix
     *  Array containig elementtype=>numberingprefix pairs for each element-type
     */
    public function setNumberingPrefix(array $numberingPrefix);

    /**
     * Set the numbering postfix for the given element types
     * @param array $numberingPostfix
     *  Array containig elementtype=>numberingpostfix pairs for each element-type
     */
    public function setNumberingPostfix(array $numberingPostfix);

    /**
     * Set the numbering separator for the given element types
     * @param array $numberingSeparator
     *  Array containig elementtype=>numberingseparator pairs for each element-type
     */
    public function setNumberingSeparator(array $numberingSeparator);

    /**
     * Get all defined labels
     * @return array
     *  Array containing references to implementations of DocumentElementInterface
     */
    public function getLabels();

    /**
     * Get a reference to another element.
     * @param string $label
     *  element identifier as defined by setLabel() for the element in question
     * @param string $type (optional)
     *  Type of reference to use. Valid keys are: 'number', 'title', 'page'
     * @return string
     *  Returns the requested property of the referenced element or a default
     *  string with approximately the correct width if the reference is not found
     */
    public function getReference($label, $type = 'number');

    /**
     * Get the documents author
     * @return string
     */
    public function getAuthor();

    /**
     * Get the documents title
     * @return string
     */
    public function getDocTitle();

    /**
     * Get the documents subject or short summary
     * @return string
     */
    public function getSubject();

    /**
     * Get a comma-separated string of document keywords
     * @return string
     */
    public function getKeywords();

    /**
     * Get the user-defined unit of measurement for the document
     * @return string
     */
    public function getUnit();

    /**
     * Get the default page size in user-units
     * @return array
     *  keys: 'width', 'height'
     */
    public function getPageSize();

    /**
     * Get the default page orientation
     * @return string
     */
    public function getOrientation();

    /**
     * Get the default page margins in user-units for the specified direction.
     * If the direction is omitted or not found, a default value is returned.
     * @param string $dir (optional)
     *  page margin direction, usual keys: 'top', 'bottom', 'left', 'right'
     * @return float
     */
    public function getPageMargins($dir = '');

    /**
     * Set the documents author. Use a comma-separated string for multiple authos
     * @param string $author
     */
    public function setAuthor($author);

    /**
     * Set the documents title
     * @param string $docTitle
     */
    public function setDocTitle($docTitle);

    /**
     * Set the documents subject (or a shor summary)
     * @param string $subject
     */
    public function setSubject($subject);

    /**
     * Set the documents keywords. Use a comma-separated string for multiple
     * keywords
     * @param string $keywords
     */
    public function setKeywords($keywords);

    /**
     * Set the documents default unit of measurement.
     * Possible values are:
     *  <ul>
     *  <li>'pt': point</li>
     *  <li>'mm': millimeter (default)</li>
     *  <li>'cm': centimeter</li>
     *  <li>'in': inch</li>
     * </ul>
     * The 'postscript-point' is defined as 1/72 inch.
     * @param string $unit
     */
    public function setUnit($unit);

    /**
     * Set the default page size (in user-units)
     * @param array $pageSize
     *  keys: 'width', 'height'
     */
    public function setPageSize(array $pageSize);

    /**
     * Set the pages orientation
     * @param string $orientation
     *  values: 'P' for portrait mode (default) or 'L' for landscape mode
     */
    public function setOrientation($orientation);

    /**
     * Set the default page margins for each given direction in user-units
     * @param array $margins
     *  array containg direction=>margin pairs
     */
    public function setPageMargins(array $margins);

    /**
     * Get the total number of pages for the document
     * (not implemented)
     * @return int
     */
    public function getNumPages();

    /**
     * Get the documents instance of the PDF object
     * @return PDF
     */
    public function getPDF();

    /**
     * Set the documents PDF instance
     * @param PDF $pdf
     */
    public function setPDF(PDF $pdf);

    /**
     * Get the documents ElementFactory instance
     * @return ElementFactory
     */
    public function getElementFactory();

    /**
     * Set the documents ElementFactory instalce
     * @param ElementFactory $elementFactory
     */
    public function setElementFactory(ElementFactory $elementFactory);

    /**
     * Get the hyphenation patterns for the document
     * @return array
     */
    public function getHyphenationPatterns();

    /**
     * Get the documents hyphenation settings
     * @return array
     */
    public function getHyphenationOptions();

    /**
     * Get the pagenumber style for the given pagegroup
     * @param string $pageGroup
     *  pagegroup identifier. if it is unset or not found, a default will be used
     * @return string
     */
    public function getPageNumberStyle($pageGroup = 'default');

    /**
     * Get the start index for page numbers for the given pagegroup
     * @param string $pageGroup (optional)
     * @return int
     */
    public function getPageNumberStartValue($pageGroup = 'default');

    /**
     * Set the pagenumber style for the given pagegroups
     * @param array $pageNumberStyle
     *  Array containing pagegroup=>style pairs
     */
    public function setPageNumberStyle(array $pageNumberStyle);

    /**
     * Set the start index for the given pagegroups
     * @param array $pageNumberStartValue
     *  array containing pagegroup=>startindex pairs
     */
    public function setPageNumberStartValue(array $pageNumberStartValue);

    /**
     * Get the default page format as string (e.g. 'A4')
     * @return string
     */
    public function getPageFormat();

    /**
     * Set the documents default page format.
     * for available formats see the TCPDF getPageSizeFromFormat() documentation
     * @see TCPDF_STATIC::getPageSizeFromFormat()
     * @param string $format
     *  page format (e.g. 'A4')
     */
    public function setPageFormat($format);

    /**
     * Add soft-hyphens (&shy;) to the words of the provided text according to
     * the defined hyphenation patterns.
     * @param string $text
     * @return string
     *  hyphenated version of the input string. HTML tags will remain unhyphenated
     * @see TCPDF::hyphenateText()
     */
    public function hypenateText($text);

    /**
     * Set the options for the hyphenation process
     * @param array $hyphenation
     *  Arraykeys:
     *  'dictionary' (array): blacklist of words to ignore
	 *  'leftMin' (int): Minimum number of character to leave on the left of the
     *   word without applying the hyphens.
	 *  'rightMin' (int): Minimum number of character to leave on the right of
     *   the word without applying the hyphens.
	 *  'charMin' (int): Minimum word length to apply the hyphenation algoritm.
	 *  'charMax' (int): Maximum length of broken piece of word.
     * @see TCPDF::hyphenateText()
     */
    public function setHyphenationOptions(array $hyphenation);

    /**
     * Get a list of all defined sources for the document
     * @return array
     *  Array containing references to Source objects
     */
    public function getSources();

    /**
     * Get the documents citation style (not implemented)
     * @return string
     */
    public function getCitationStyle();

    /**
     * Set the documents sources
     * @param array $sources
     */
    public function setSources(array $sources);

    /**
     * Set de documents citation style (not implemented)
     * @param string $style
     */
    public function setCitationStyle($style);

    /**
     * Add a titlepage to de document
     * @param array $settings
     * @return TitlePage
     */
    public function addTitlePage(array $settings = []);
}
