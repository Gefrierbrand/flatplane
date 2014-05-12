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
use de\flatplane\interfaces\documentElements\DocumentInterface;
use RuntimeException;

//todo: count unresolved references
/**
 * This class represents the base document.
 * @author Nikolai Neff <admin@flatplane.de>
 */
class Document extends AbstractDocumentContentElement implements DocumentInterface
{
    /**
     * @var int
     *  type of the element
     */
    protected $type='document';
    protected $labels = [];
    protected $isSplitable = true;

    protected $author =  '';
    protected $title = '';
    protected $description = '';
    protected $subject = '';
    protected $keywords = '';
    protected $unit = 'mm';
    protected $pageSize = 'A4';
    protected $orientation = 'P';

    protected $numberingFormat = ['default' => 'int'];
    protected $numberingLevel = ['default' => -1];
    protected $numberingPrefix = ['default' => ''];
    protected $numberingPostfix = ['default' => ''];
    protected $numberingSeparator = ['default' => '.'];
    protected $startIndex = ['default' => 1];

    protected $validLabelTypes = ['page', 'title', 'number'];
    protected $unresolvedReferenceMarker = '?';
    protected $assumedPageNumberWidth = 3;
    protected $assumedStructureNumberWidth = 4;
    protected $assumedTitleWidth = 20;


    /**
     * @var ElementFactory
     */
    protected $elementFactory;

    /**
     * @var int
     *  Number of pages; used for internal representation.
     *  FIXME: Currently not used at all
     */
    private $pages;

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
     * @return Document
     */
    public function getParent()
    {
        return null;
    }

    public function setParent(DocumentElementInterface $parent)
    {
        throw new RuntimeException('You can\'t set a parent for the document');
    }

    public function addLabel(DocumentElementInterface $instance)
    {
        $this->labels[$instance->getLabel()] = $instance;
    }

    public function getReference($label, $type = 'number')
    {
        if (!in_array($type, $this->validLabelTypes)) {
            trigger_error(
                "$type is not a valid label type. Defaulting to Number",
                E_USER_WARNING
            );
            $type = 'number';
        }
        if (array_key_exists($label, $this->getLabels())) {
            return $this->getReferenceValue($this->getLabels()[$label], $type);
        } else {
            return $this->getDefaultReferenceValue($type);
        }
    }

    protected function getReferenceValue(DocumentElementInterface $instance, $type)
    {
        switch ($type) {
            case 'number':
                return $instance->getFormattedNumbers();
            case 'title':
                return $instance->getTitle();
            case 'page':
                return $instance->getPage();
            default:
                trigger_error('Invalid reference type, defaulting to number');
                return $instance->getFormattedNumbers();
        }
    }

    protected function getDefaultReferenceValue($type)
    {
        switch ($type) {
            case 'number':
                $width = $this->getAssumedStructureNumberWidth();
                //add num-1 to the width to account for number separation
                $width += ($width-1);
                return str_repeat($this->getUnresolvedReferenceMarker(), $width);
            case 'title':
                $width = $this->getAssumedTitleWidth();
                return str_repeat($this->getUnresolvedReferenceMarker(), $width);
            case 'page':
                $width = $this->getAssumedPageNumberWidth();
                return str_repeat($this->getUnresolvedReferenceMarker(), $width);
            default:
                trigger_error('Invalid reference type, defaulting to number');
                $width = $this->getAssumedStructureNumberWidth();
                //add num-1 to the width to account for number separation
                $width += ($width-1);
                return str_repeat($this->getUnresolvedReferenceMarker(), $width);
        }
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

    public function getTitle()
    {
        return $this->title;
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
        return $this->pageSize;
    }

    public function getOrientation()
    {
        return $this->orientation;
    }

    public function getPages()
    {
        return $this->pages;
    }

    public function setAuthor($author)
    {
        $this->author = $author;
    }

    public function setTitle($title)
    {
        $this->title = $title;
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

    public function getElementFactory()
    {
        return $this->elementFactory;
    }

    public function setElementFactory(ElementFactory $elementFactory)
    {
        $this->elementFactory = $elementFactory;
    }

    protected function setUnit($unit)
    {
        $this->unit = $unit;
    }

    protected function setPageSize($pageSize)
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

    protected function setStartIndex($startIndex)
    {
        $this->startIndex = $startIndex;
    }
    protected function setUnresolvedReferenceMarker($marker)
    {
        $this->unresolvedReferenceMarker = $marker;
    }

    protected function setAssumedPageNumberWidth($assumedPageNumberWidth)
    {
        $this->assumedPageNumberWidth = (int) $assumedPageNumberWidth;
    }

    protected function setAssumedStructureNumberWidth($assumedStructureNumberWidth)
    {
        $this->assumedStructureNumberWidth = (int) $assumedStructureNumberWidth;
    }

    protected function setAssumedTitleWidth($assumedTitleWidth)
    {
        $this->assumedTitleWidth = (int) $assumedTitleWidth;
    }

    public function getAssumedPageNumberWidth()
    {
        return $this->assumedPageNumberWidth;
    }

    public function getAssumedStructureNumberWidth()
    {
        return $this->assumedStructureNumberWidth;
    }

    public function getAssumedTitleWidth()
    {
        return $this->assumedTitleWidth;
    }

    public function getUnresolvedReferenceMarker()
    {
        return $this->unresolvedReferenceMarker;
    }

    public function getSize()
    {
        //todo: implement me;
    }
}
