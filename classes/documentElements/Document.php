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
    protected $description = '';
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

    public function addLabel(DocumentElementInterface $instance)
    {
        $this->labels[$instance->getLabel()] = $instance;
    }

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

    public function getType()
    {
        return $this->type;
    }

    public function getLabels()
    {
        return $this->labels;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function getDocTitle()
    {
        return $this->docTitle;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function getKeywords()
    {
        return $this->keywords;
    }

    public function getUnit()
    {
        return $this->unit;
    }

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
        $width = $this->getPdf()->getHTMLUnitToUnits($val[0], 1, 'pt');
        $height = $this->getPdf()->getHTMLUnitToUnits($val[1], 1, 'pt');
        return ['width' => $width, 'height' => $height];
    }

    public function getOrientation()
    {
        return $this->orientation;
    }

    public function getNumPages()
    {
        return $this->numPages;
    }

    public function setAuthor($author)
    {
        $this->author = $author;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    public function getNumberingFormat($type = '')
    {
        if (empty($type) || !array_key_exists($type, $this->numberingFormat)) {
            return $this->numberingFormat['default'];
        } else {
            return $this->numberingFormat[$type];
        }
    }

    public function getNumberingLevel($type = '')
    {
        if (empty($type) || !array_key_exists($type, $this->numberingLevel)) {
            return $this->numberingLevel['default'];
        } else {
            return $this->numberingLevel[$type];
        }
    }

    public function getNumberingPostfix($type = '')
    {
        if (empty($type) || !array_key_exists($type, $this->numberingPostfix)) {
            return $this->numberingPostfix['default'];
        } else {
            return $this->numberingPostfix[$type];
        }
    }

    public function getNumberingPrefix($type = '')
    {
        if (empty($type) || !array_key_exists($type, $this->numberingPrefix)) {
            return $this->numberingPrefix['default'];
        } else {
            return $this->numberingPrefix[$type];
        }
    }

    public function getNumberingSeparator($type = '')
    {
        if (empty($type) || !array_key_exists($type, $this->numberingSeparator)) {
            return $this->numberingSeparator['default'];
        } else {
            return $this->numberingSeparator[$type];
        }
    }

    public function getStartIndex($type = '')
    {
        if (empty($type) || !array_key_exists($type, $this->startIndex)) {
            return $this->startIndex['default'];
        } else {
            return $this->startIndex[$type];
        }
    }

    public function getPageMargins($dir = '')
    {
        if (empty($dir) || !array_key_exists($dir, $this->pageMargins)) {
            return $this->pageMargins['default'];
        } else {
            return $this->pageMargins[$dir];
        }
    }

    protected function setPageMargins(array $margins)
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
    public function getPdf()
    {
        if (empty($this->pdf)) {
            throw new RuntimeException('Trying to access PDF object before it is set');
        }
        return $this->pdf;
    }

    /**
     * @param PDF $pdf
     */
    public function setPdf(PDF $pdf)
    {
        $this->pdf = $pdf;
    }

    protected function setUnit($unit)
    {
        $this->unit = $unit;
    }

    protected function setPageSize(array $pageSize)
    {
        $this->pageSize = $pageSize;
    }

    protected function setOrientation($orientation)
    {
        $this->orientation = $orientation;
    }

    protected function setNumberingFormat($numberingFormat)
    {
        $this->numberingFormat = $numberingFormat;
    }

    protected function setNumberingLevel($numberingLevel)
    {
        $this->numberingLevel = $numberingLevel;
    }

    protected function setNumberingPrefix($numberingPrefix)
    {
        $this->numberingPrefix = $numberingPrefix;
    }

    protected function setNumberingPostfix($numberingPostfix)
    {
        $this->numberingPostfix = $numberingPostfix;
    }

    protected function setNumberingSeparator($numberingSeparator)
    {
        $this->numberingSeparator = $numberingSeparator;
    }

    protected function setStartIndex(array $startIndex)
    {
        $this->startIndex = array_merge($this->startIndex, $startIndex);
    }

    public function getSize()
    {
        //todo: implement me;
    }

    public function getPageFormat()
    {
        return $this->pageFormat;
    }

    protected function setPageFormat($format)
    {
        $this->pageFormat = $format;
    }

    public function hypenateText($text)
    {
        $hyphenationOptions = $this->getHyphenationOptions();

        return $this->getPdf()->hyphenateText(
            $text,
            $this->getHyphenationPatterns(),
            $hyphenationOptions['dictionary'],
            $hyphenationOptions['leftMin'],
            $hyphenationOptions['rightMin'],
            $hyphenationOptions['charMin'],
            $hyphenationOptions['charMax']
        );
    }

    protected function setHyphenationOptions(array $hyphenation)
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

    public function getHyphenationOptions()
    {
        return $this->hyphenationOptions;
    }

    public function getHyphenate()
    {
        return $this->hyphenate;
    }

    protected function setHyphenate($hyphenate)
    {
        $this->hyphenate = $hyphenate;
    }

    /**
     * @param string $docTitle
     */
    protected function setDocTitle($docTitle)
    {
        $this->docTitle = $docTitle;
    }

    public function cite($source, $extras = '')
    {
        if (array_key_exists($source, $this->getSources())) {
            $citeStyle = $this->getCitationStyle();
            $cite = $citeStyle['prefix'];
            $cite .= $this->getSources()[$source]->getFormattedNumbers();
            if (!empty($extras)) {
                $cite .= $citeStyle['separator'].' '.$extras;
            }
            $cite .= $citeStyle['postfix'];
        } else {
            trigger_error(
                'Source "'.$source.'" for citation not found',
                E_USER_NOTICE
            );
            $cite = '[??]'; //todo: use assumption settings
        }
        return $cite;
    }

    public function getSources()
    {
        return $this->sources;
    }

    public function getCitationStyle()
    {
        return $this->citationStyle;
    }

    protected function setSources(array $sources)
    {
        $this->sources = $sources;
    }

    protected function setCitationStyle($cite)
    {
        $this->citationStyle = $cite;
    }

    public function addSource($label, array $settings = [])
    {
        $factory = $this->getElementFactory();
        $content = $factory->createElement('source', $settings);
        $source = $this->addContent($content);
        $this->sources[$label] = $source;
        return $source;
    }

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

    public function generateOutput()
    {
        return;
    }

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

    public function setPageNumberStyle($pageNumberStyle)
    {
        $this->pageNumberStyle = $pageNumberStyle;
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

    public function setPageNumberStartValue($pageNumberStartValue)
    {
        $this->pageNumberStartValue = $pageNumberStartValue;
    }
}
