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

namespace de\flatplane\documentElements;

use de\flatplane\BibtexParser\BibtexParser;
use de\flatplane\interfaces\DocumentElementInterface;
use de\flatplane\interfaces\documentElements\DocumentInterface;
use de\flatplane\utilities\Number;
use de\flatplane\utilities\PDF;
use de\flatplane\utilities\StaticPDF;
use Symfony\Component\Process\Exception\RuntimeException;
use TCPDF;
use TCPDF_STATIC;

//todo: count unresolved references
/**
 * This class represents the base document.
 * @author Nikolai Neff <admin@flatplane.de>
 */
class Document extends AbstractDocumentContentElement implements DocumentInterface
{
    //todo: maybe without traits?
    use \de\flatplane\documentElements\traits\DocumentReferences;

    protected $type='document';

    /**
     *
     * @var string[]
     */
    protected $labels = [];
    protected $sources = [];
    protected $isSplitable = true;

    protected $author =  '';
    protected $docTitle = '';
    protected $subject = '';
    protected $keywords = '';
    protected $unit = 'mm';
    protected $pageFormat = 'A4';
    protected $pageSize;
    protected $orientation = 'P';

    protected $numberingFormat = ['default' => 'int'];
    protected $numberingLevel = ['default' => -1];
    protected $numberingPrefix = ['default' => ''];
    protected $numberingPostfix = ['default' => ''];
    protected $numberingSeparator = ['default' => '.'];
    protected $startIndex = ['default' => 1];

    protected $pageMargins = ['default' => 20];

    protected $citationStyle = ['prefix' => '[', 'postfix' => ']', 'separator' => ','];
    protected $hyphenate = true;
    protected $hyphenationOptions = ['file' => '',
                             'dictionary' => [],
                             'leftMin' => 2,
                             'rightMin' => 2,
                             'charMin' => 1,
                             'charMax' => 8];
    protected $hyphenationPatterns;


    /**
     * @var PDF
     */
    protected $pdf;

    /**
     * @var ElementFactory
     */
    protected $elementFactory;

    /**
     * @var int
     *  Number of pages;
     */
    protected $numPages;

    /**
     * @var array
     *  number style for the current pagegroup. For possible values:
     * @see Number::getFormattedValue()
     */
    protected $pageNumberStyle = ['default' => 'int'];

    /**
     * todo: doc
     * @var array
     */
    protected $pageNumberStartValue = ['default' => 1];

    /**
     * Return a string representation of the document
     * @return type
     */
    public function __toString()
    {
        return (string) $this->getDocTitle();
    }
    
    /**
     * @return Document
     */
    public function toRoot()
    {
        return $this;
    }

    /**
     * Add an instance of DocumentElementInterface to the internal lable list
     * @param DocumentElementInterface $instance
     */
    public function addLabel(DocumentElementInterface $instance)
    {
        $this->labels[$instance->getLabel()] = $instance;
    }

    /**
     * Throws an exeption as the Document is already the root object and
     * therefore can't have a parent
     * @param DocumentElementInterface $parent
     * @throws RuntimeException
     */
    public function setParent(DocumentElementInterface $parent)
    {
        throw new RuntimeException('You can\'t set a parent for the document');
    }

    /**
    * @return Document
    */
    public function getParent()
    {
        return null;
    }

    /**
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get all defined labels
     * @return array
     *  Array containing references to implementations of DocumentElementInterface
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * Get the documents author
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Get the documents title
     * @return string
     */
    public function getDocTitle()
    {
        return $this->docTitle;
    }

    /**
     * Get the documents subject or short summary
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Get a comma-separated string of document keywords
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Get the user-defined unit of measurement for the document
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * Get the default page size in user-units
     * @return array
     *  keys: 'width', 'height'
     */
    public function getPageSize()
    {
        if (empty($this->pageSize)) {
            $this->pageSize = $this->getPageSizeFromFormat($this->getPageFormat());
        }
        return $this->pageSize;
    }

    /**
     * Returns the PageSize as array in user-units
     * @param string $format
     * @return array
     */
    protected function getPageSizeFromFormat($format)
    {
        //get the pagesize from predefined formats in points
        $val = StaticPDF::getPageSizeFromFormat($format);
        //convert points to user-units
        $width = $this->getPDF()->getHTMLUnitToUnits($val[0], 1, 'pt');
        $height = $this->getPDF()->getHTMLUnitToUnits($val[1], 1, 'pt');
        return ['width' => $width, 'height' => $height];
    }

    /**
     * Get the default page orientation
     * @return string
     */
    public function getOrientation()
    {
        return $this->orientation;
    }

    /**
     * Get the total number of pages for the document
     * (not implemented)
     * @return int
     */
    public function getNumPages()
    {
        //todo: implement total number of pages
        return $this->numPages;
    }

    /**
     * Set the documents author. Use a comma-separated string for multiple authos
     * @param string $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * Set the documents subject (or a shor summary)
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * Set the documents keywords. Use a comma-separated string for multiple
     * keywords
     * @param string $keywords
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * Get the numbering format for the given element type
     * @param string $type (optional)
     * @return string
     */
    public function getNumberingFormat($type = '')
    {
        if (empty($type) || !array_key_exists($type, $this->numberingFormat)) {
            return $this->numberingFormat['default'];
        } else {
            return $this->numberingFormat[$type];
        }
    }

    /**
     * Get the numbering level for the given element type
     * @param string $type (optional)
     * @return int
     */
    public function getNumberingLevel($type = '')
    {
        if (empty($type) || !array_key_exists($type, $this->numberingLevel)) {
            return $this->numberingLevel['default'];
        } else {
            return $this->numberingLevel[$type];
        }
    }

    /**
     * Get the enumeration postfix for the given element type
     * @param string $type (optional)
     * @return string
     */
    public function getNumberingPostfix($type = '')
    {
        if (empty($type) || !array_key_exists($type, $this->numberingPostfix)) {
            return $this->numberingPostfix['default'];
        } else {
            return $this->numberingPostfix[$type];
        }
    }

    /**
     * Get the enumeration prefix for the given element type
     * @param string $type (optional)
     * @return string
     */
    public function getNumberingPrefix($type = '')
    {
        if (empty($type) || !array_key_exists($type, $this->numberingPrefix)) {
            return $this->numberingPrefix['default'];
        } else {
            return $this->numberingPrefix[$type];
        }
    }

    /**
     * Get the numbering separator for the given element type
     * @param string $type (optional)
     * @return string
     */
    public function getNumberingSeparator($type = '')
    {
        if (empty($type) || !array_key_exists($type, $this->numberingSeparator)) {
            return $this->numberingSeparator['default'];
        } else {
            return $this->numberingSeparator[$type];
        }
    }

    /**
     * Get the starting index for the given element type
     * @param string $type (optional)
     * @return int
     */
    public function getStartIndex($type = '')
    {
        if (empty($type) || !array_key_exists($type, $this->startIndex)) {
            return $this->startIndex['default'];
        } else {
            return $this->startIndex[$type];
        }
    }

    /**
     * Get the default page margins in user-units for the specified direction.
     * If the direction is omitted or not found, a default value is returned.
     * @param string $dir (optional)
     *  page margin direction, usual keys: 'top', 'bottom', 'left', 'right'
     * @return float
     */
    public function getPageMargins($dir = '')
    {
        if (empty($dir) || !array_key_exists($dir, $this->pageMargins)) {
            return $this->pageMargins['default'];
        } else {
            return $this->pageMargins[$dir];
        }
    }

    /**
     * Set the default page margins for each given direction in user-units
     * @param array $margins
     *  array containg direction=>margin pairs
     */
    public function setPageMargins(array $margins)
    {
        //todo: sanity check: sum of margins must be <= pagedimensions
        $this->pageMargins = array_merge($this->pageMargins, $margins);
    }

    /**
     * Get the documents ElementFactory instance
     * @return ElementFactory
     */
    public function getElementFactory()
    {
        return $this->elementFactory;
    }

    /**
     * Set the documents ElementFactory instalce
     * @param ElementFactory $elementFactory
     */
    public function setElementFactory(ElementFactory $elementFactory)
    {
        $this->elementFactory = $elementFactory;
    }

    /**
     * Get the documents instance of the PDF object
     * @return PDF
     */
    public function getPDF()
    {
        if (empty($this->pdf)) {
            throw new RuntimeException('Trying to access PDF object before it is set');
        }
        return $this->pdf;
    }

    /**
     * Set the documents PDF instance
     * @param PDF $pdf
     */
    public function setPDF(PDF $pdf)
    {
        $this->pdf = $pdf;
    }

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
    public function setUnit($unit)
    {
        $this->unit = $unit;
    }

    /**
     * Set the default page size (in user-units)
     * @param array $pageSize
     *  keys: 'width', 'height'
     */
    public function setPageSize(array $pageSize)
    {
        $this->pageSize = $pageSize;
    }

    /**
     * Set the pages orientation
     * @param string $orientation
     *  values: 'P' for portrait mode (default) or 'L' for landscape mode
     */
    public function setOrientation($orientation)
    {
        $this->orientation = $orientation;
    }

    /**
     * Set the numbering format for the given element types
     * @param array $numberingFormat
     *  Array containig elementtype=>numberingformat pairs for each element-type
     */
    public function setNumberingFormat(array $numberingFormat)
    {
        $this->numberingFormat = array_merge(
            $this->numberingFormat,
            $numberingFormat
        );
    }

    /**
     * Set the numbering level for the given element types
     * @param array $numberingLevel
     *  Array containig elementtype=>numberinglevel pairs for each element-type
     */
    public function setNumberingLevel(array $numberingLevel)
    {
        $this->numberingLevel = array_merge(
            $this->numberingLevel,
            $numberingLevel
        );
    }

    /**
     * Set the numbering prefix for the given element types
     * @param string $numberingPrefix
     *  Array containig elementtype=>numberingprefix pairs for each element-type
     */
    public function setNumberingPrefix(array $numberingPrefix)
    {
        $this->numberingPrefix = array_merge(
            $this->numberingPrefix,
            $numberingPrefix
        );
    }

    /**
     * Set the numbering postfix for the given element types
     * @param array $numberingPostfix
     *  Array containig elementtype=>numberingpostfix pairs for each element-type
     */
    public function setNumberingPostfix(array $numberingPostfix)
    {
        $this->numberingPostfix = array_merge(
            $this->numberingPostfix,
            $numberingPostfix
        );
    }

    /**
     * Set the numbering separator for the given element types
     * @param array $numberingSeparator
     *  Array containig elementtype=>numberingseparator pairs for each element-type
     */
    public function setNumberingSeparator(array $numberingSeparator)
    {
        $this->numberingSeparator = array_merge(
            $this->numberingSeparator,
            $numberingSeparator
        );
    }

    /**
     * Set the start index for the given element types
     * @param array $startIndex
     *  Array containig elementtype=>startindex pairs for each element-type
     */
    public function setStartIndex(array $startIndex)
    {
        $this->startIndex = array_merge($this->startIndex, $startIndex);
    }

    /**
     * todo: doc
     */
    public function getSize($startYposition = null)
    {
        //do nothing
    }

    /**
     * Get the default page format as string (e.g. A4)
     * @return string
     */
    public function getPageFormat()
    {
        return $this->pageFormat;
    }

    /**
     * Set the documents default page format.
     * for available formats see the TCPDF getPageSizeFromFormat() documentation
     * @see TCPDF_STATIC::getPageSizeFromFormat()
     * @param string $format
     *  page format (e.g. 'A4')
     */
    public function setPageFormat($format)
    {
        $this->pageFormat = $format;
    }

    /**
     * Add soft-hyphens (&shy;) to the words of the provided text according to
     * the defined hyphenation patterns.
     * @param string $text
     * @return string
     *  hyphenated version of the input string. HTML tags will remain unhyphenated
     * @see TCPDF::hyphenateText()
     */
    public function hypenateText($text)
    {
        $hyphenationOptions = $this->getHyphenationOptions();

        return $this->getPDF()->hyphenateText(
            $text,
            $this->getHyphenationPatterns(),
            $hyphenationOptions['dictionary'],
            $hyphenationOptions['leftMin'],
            $hyphenationOptions['rightMin'],
            $hyphenationOptions['charMin'],
            $hyphenationOptions['charMax']
        );
    }

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
    public function setHyphenationOptions(array $hyphenation)
    {
        foreach ($hyphenation as $key => $option) {
            if (array_key_exists($key, $this->hyphenationOptions)) {
                if ($option == ['']) {
                    $option = [];
                }
                $this->hyphenationOptions[$key] = $option;
            }
        }
    }

    /**
     * Get the documents hyphenation settings
     * @return array
     */
    public function getHyphenationOptions()
    {
        return $this->hyphenationOptions;
    }

    /**
     * Set the documents title
     * @param string $docTitle
     */
    public function setDocTitle($docTitle)
    {
        $this->docTitle = $docTitle;
        if ($this->pdf instanceof PDF) {
            $this->getPDF()->SetTitle($docTitle);
        }
    }

    /**
     * Get a list of all defined sources for the document
     * @return array
     *  Array containing references to Source objects
     */
    public function getSources()
    {
        return $this->sources;
    }

    /**
     * Get the documents citation style (not implemented)
     * @return string
     */
    public function getCitationStyle()
    {
        return $this->citationStyle;
    }

    /**
     * Set the documents sources
     * @param array $sources
     */
    public function setSources(array $sources)
    {
        //todo: validate array elements
        $this->sources = $sources;
    }

    /**
     *
     * @param string $style
     */
    public function setCitationStyle($style)
    {
        $this->citationStyle = $style;
    }

    /**
     * Add a soucre to the documents sources list
     * @param string $label
     *  reference identifier
     * @param array $settings
     * @return Source
     */
    public function addSource($label, array $settings = [])
    {
        $factory = $this->getElementFactory();
        $content = $factory->createElement('source', $settings);
        $source = $this->addContent($content);
        $this->sources[$label] = $source;
        return $source;
    }

    /**
     * Add sources to the documents sources list from a BibTex file
     * @param string $bibfile
     *  path to the BibTeX file
     * @throws RuntimeException
     */
    public function addBibTexSources($bibfile)
    {
        if (!is_readable($bibfile)) {
            throw new RuntimeException('File '.$bibfile.' is not readable');
        }
        $this->parseBibFile($bibfile);
    }

    /**
     * todo: doc
     * @param type $bibfile
     * @throws RuntimeException
     */
    protected function parseBibFile($bibfile)
    {
        $results = BibtexParser::parseFile($bibfile);
        if (!is_array($results)) {
            throw new RuntimeException('Parsing of BibTexFile failed');
        }
        foreach ($results as $source) {
            if (!empty($source['reference'])) {
                $src = $this->addSource($source['reference']);
                foreach ($source as $fieldName => $fieldValue) {
                    $methodName = 'setSource'.ucfirst($fieldName);
                    if (method_exists($src, $methodName)) {
                        $src->$methodName($fieldValue);
                    }
                }
            } else {
                trigger_error(
                    'Source does not contain required "reference" property',
                    E_USER_ERROR
                );
            }
        }
    }

    /**
     * todo: doc
     * @return int
     */
    public function generateOutput()
    {
        return 0;
    }

    /**
     * Get the hyphenation patterns for the document
     * @return array
     */
    public function getHyphenationPatterns()
    {
        if (empty($this->hyphenationPatterns)
            || !is_array($this->hyphenationPatterns)
        ) {
            $this->hyphenationPatterns = TCPDF_STATIC::getHyphenPatternsFromTEX(
                $this->getHyphenationOptions()['file']
            );
        }
        return $this->hyphenationPatterns;
    }

    /**
     * Get the pagenumber style for the given pagegroup
     * @param string $pageGroup (optional)
     *  pagegroup identifier. if it is unset or not found, a default will be used
     * @return string
     */
    public function getPageNumberStyle($pageGroup = 'default')
    {
        if (isset($this->pageNumberStyle[$pageGroup])) {
            return $this->pageNumberStyle[$pageGroup];
        } else {
            return $this->pageNumberStyle['default'];
        }
    }

    /**
     * Set the pagenumber style for the given pagegroups
     * @param array $pageNumberStyle
     *  Array containing pagegroup=>style pairs
     */
    public function setPageNumberStyle(array $pageNumberStyle)
    {
        $this->pageNumberStyle = array_merge(
            $this->pageNumberStyle,
            $pageNumberStyle
        );
    }

    /**
     * Get the start index for page numbers for the given pagegroup
     * @param string $pageGroup (optional)
     * @return int
     */
    public function getPageNumberStartValue($pageGroup = 'default')
    {
        if (isset($this->pageNumberStartValue[$pageGroup])) {
            return $this->pageNumberStartValue[$pageGroup];
        } else {
            return $this->pageNumberStartValue['default'];
        }
    }

    /**
     * Set the start index for the given pagegroups
     * @param array $pageNumberStartValue
     *  array containing pagegroup=>startindex pairs
     */
    public function setPageNumberStartValue(array $pageNumberStartValue)
    {
        $this->pageNumberStartValue = array_merge (
            $this->pageNumberStartValue,
            $pageNumberStartValue
        );
    }

    /**
     * Set the pagegroup for the current element.
     * This group is maintained by all following elements unless they specify a
     * new pagegroup
     * @param string $pageGroup
     */
    public function setPageGroup($pageGroup)
    {
        trigger_error(
            'The PageGroup Property can only be set for content, not the document.',
            E_USER_WARNING
        );
        parent::setPageGroup($pageGroup);
    }

    /**
     * Add a titlepage to de document
     * @param array $settings
     * @return TitlePage
     */
    public function addTitlePage(array $settings = [])
    {
        $factory = $this->getElementFactory();
        $content = $factory->createElement('titlePage', $settings);
        return $this->addContent($content);
    }
}
