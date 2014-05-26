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

namespace de\flatplane\documentContents\traits;

use de\flatplane\documentContents\Document;
use de\flatplane\documentContents\ElementFactory;
use de\flatplane\utilities\PDF;

/**
 * Description of DocumentGetterSetter
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
trait DocumentGetterSetter
{
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

    protected function setStartIndex(array $startIndex)
    {
        $this->startIndex = array_merge($this->startIndex, $startIndex);
    }

    public function getSize()
    {
        //todo: implement me;
    }
}
