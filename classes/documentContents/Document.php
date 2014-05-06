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
use de\flatplane\interfaces\documentelements\DocumentInterface;
use de\flatplane\interfaces\StyleInterface;
use de\flatplane\interfaces\styles\DocumentStyleInterface;
use InvalidArgumentException;

/**
 * This class represents the base document.
 * @author Nikolai Neff <admin@flatplane.de>
 */
class Document extends AbstractDocumentContentElement implements DocumentInterface
{
    //TODO: DOC!
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

    /**
     * @var int
     *  Number of pages; used for internal representation.
     *  FIXME: Currently not used at all
     */
    private $pages;

    public function __construct(array $config, StyleInterface $style)
    {
        if (!($style instanceof DocumentStyleInterface)) {
            throw new InvalidArgumentException(
                'The documentStyle must be an instanceof DocumentStyleInterface'
            );
        }
        parent::__construct($config, $style);
    }


    public function __toString()
    {
        return (string) $this->getSettings('title');
    }

    public function __clone()
    {
        //currently: do nothing
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
        return $this;
    }

    public function setParent(DocumentElementInterface $parent)
    {
        //currently: do nothing
    }

    public function addLabel(DocumentElementInterface $instance)
    {
        $this->labels[$instance->getLabelName()] = $instance;
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

    public function setUnit($unit)
    {
        $this->unit = $unit;
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

    public function setNumberingFormat(array $format)
    {
        $this->numberingFormat = array_merge($this->numberingFormat, $format);
    }

    public function setNumberingLevel(array $level)
    {
        $this->numberingLevel = array_merge($this->numberingLevel, $level);
    }

    public function setNumberingPostfix(array $postfix)
    {
        $this->numberingPostfix = array_merge($this->numberingPostfix, $postfix);
    }

    public function setNumberingPrefix(array $prefix)
    {
        $this->numberingPrefix = array_merge($this->numberingPrefix, $prefix);
    }

    public function setNumberingSeparator(array $separator)
    {
        $this->numberingSeparator = array_merge(
            $this->numberingSeparator,
            $separator
        );
    }

    public function setStartIndex(array $startIndex)
    {
        $this->startIndex = array_merge($this->startIndex, $startIndex);
    }
}
