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
use de\flatplane\utilities\PDF;
use de\flatplane\utilities\StaticPDF;
use RuntimeException;

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


    public function __toString()
    {
        return (string) $this->getSettings('title');
    }

    /**
     * @return Document
     */
    public function toRoot()
    {
        return $this;
    }

    /**
     *
     * @param \de\flatplane\interfaces\DocumentElementInterface $instance
     */
    public function addLabel(DocumentElementInterface $instance)
    {
        $this->labels[$instance->getLabel()] = $instance;
    }

    /**
     *
     * @param \de\flatplane\interfaces\DocumentElementInterface $parent
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
     *
     * @return array
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     *
     * @return string
     */
    public function getDocTitle()
    {
        return $this->docTitle;
    }

    /**
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     *
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     *
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     *
     * @return array
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
     *
     * @return string
     */
    public function getOrientation()
    {
        return $this->orientation;
    }

    /**
     *
     * @return int
     */
    public function getNumPages()
    {
        //todo: implement total number of pages
        return $this->numPages;
    }

    /**
     *
     * @param string $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     *
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     *
     * @param string $keywords
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     *
     * @param string $type
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
     *
     * @param string $type
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
     *
     * @param string $type
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
     *
     * @param string $type
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
     *
     * @param string $type
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
     *
     * @param string $type
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
     * @todo: rename and add function to get all margins
     * @param string $dir
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
     *
     * @param array $margins
     */
    public function setPageMargins(array $margins)
    {
        //todo: sanity check: sum of margins must be <= pagedimensions
        $this->pageMargins = array_merge($this->pageMargins, $margins);
    }

    /**
     * @return ElementFactory
     */
    public function getElementFactory()
    {
        return $this->elementFactory;
    }

    /**
     * @param ElementFactory $elementFactory
     */
    public function setElementFactory(ElementFactory $elementFactory)
    {
        $this->elementFactory = $elementFactory;
    }

    /**
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
     * @param PDF $pdf
     */
    public function setPDF(PDF $pdf)
    {
        $this->pdf = $pdf;
    }

    /**
     *
     * @param string $unit
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;
    }

    /**
     *
     * @param array $pageSize
     */
    public function setPageSize(array $pageSize)
    {
        $this->pageSize = $pageSize;
    }

    /**
     *
     * @param string $orientation
     */
    public function setOrientation($orientation)
    {
        $this->orientation = $orientation;
    }

    /**
     *
     * @param string $numberingFormat
     */
    public function setNumberingFormat(array $numberingFormat)
    {
        $this->numberingFormat = array_merge(
            $this->numberingFormat,
            $numberingFormat
        );
    }

    /**
     *
     * @param int $numberingLevel
     */
    public function setNumberingLevel(array $numberingLevel)
    {
        $this->numberingLevel = array_merge(
            $this->numberingLevel,
            $numberingLevel
        );
    }

    /**
     *
     * @param string $numberingPrefix
     */
    public function setNumberingPrefix(array $numberingPrefix)
    {
        $this->numberingPrefix = array_merge(
            $this->numberingPrefix,
            $numberingPrefix
        );
    }

    /**
     *
     * @param string $numberingPostfix
     */
    public function setNumberingPostfix(array $numberingPostfix)
    {
        $this->numberingPostfix = array_merge(
            $this->numberingPostfix,
            $numberingPostfix
        );
    }

    /**
     *
     * @param string $numberingSeparator
     */
    public function setNumberingSeparator(array $numberingSeparator)
    {
        $this->numberingSeparator = array_merge(
            $this->numberingSeparator,
            $numberingSeparator
        );
    }

    /**
     *
     * @param int $startIndex
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
     *
     * @return string
     */
    public function getPageFormat()
    {
        return $this->pageFormat;
    }

    /**
     *
     * @param string $format
     */
    public function setPageFormat($format)
    {
        $this->pageFormat = $format;
    }

    /**
     *
     * @param string $text
     * @return string
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
     *
     * @param array $hyphenation
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
     *
     * @return array
     */
    public function getHyphenationOptions()
    {
        return $this->hyphenationOptions;
    }

    /**
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
     *
     * @return array
     */
    public function getSources()
    {
        return $this->sources;
    }

    /**
     *
     * @return string
     */
    public function getCitationStyle()
    {
        return $this->citationStyle;
    }

    /**
     *
     * @param array $sources
     */
    public function setSources(array $sources)
    {
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
     *
     * @param string $label
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
     *
     * @param string $bibfile
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
     *
     * @return array
     */
    public function getHyphenationPatterns()
    {
        if (empty($this->hyphenationPatterns)
            || !is_array($this->hyphenationPatterns)
        ) {
            $this->hyphenationPatterns = \TCPDF_STATIC::getHyphenPatternsFromTEX(
                $this->getHyphenationOptions()['file']
            );
        }
        return $this->hyphenationPatterns;
    }

    /**
     * todo: doc, error handling
     * @param string $pageGroup
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
     * todo: pagegroup
     * @param string $pageNumberStyle
     */
    public function setPageNumberStyle(array $pageNumberStyle)
    {
        $this->pageNumberStyle = array_merge(
            $this->pageNumberStyle,
            $pageNumberStyle
        );
    }

    /**
     * todo: doc, error handling
     * @param string $pageGroup
     * @return int|float
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
     *
     * @param int $pageNumberStartValue
     */
    public function setPageNumberStartValue($pageNumberStartValue)
    {
        $this->pageNumberStartValue = $pageNumberStartValue;
    }

    public function setPageGroup($pageGroup)
    {
        trigger_error(
            'The PageGroup Property can only be set for content, not the document.',
            E_USER_WARNING
        );
        parent::setPageGroup($pageGroup);
    }

    public function addTitlePage(array $settings = [])
    {
        $factory = $this->getElementFactory();
        $content = $factory->createElement('titlePage', $settings);
        return $this->addContent($content);
    }
}
