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

use de\flatplane\interfaces\DocumentContentElementInterface;
use de\flatplane\structure\Document;
use InvalidArgumentException;

/**
 * Abstract class for all page elements like sections, text, images, formulas, ...
 * Provides basic common functionality.
 * @author Nikolai Neff <admin@flatplane.de>
 */
abstract class DocumentContentElement implements DocumentContentElementInterface
{

    //import functionality horizontally from the trait ContentFunctions
    //(reduces codelength & reuse in Document)
    use ContentFunctions;

    protected $parent = null;
    protected $type = 'PageElement';

    protected $title;
    protected $altTitle;
    protected $caption;
    protected $showInIndex = true;
    protected $enumerate = true;

    public function __toString()
    {
        //TODO: remove numbering?
        $num = $this->getNumbers();
        $numStr = implode('.', $num);

        return (string) $numStr. ' '. $this->title;
    }

    public function getChildren() // Alias of getSections()
    {
        return $this->getContent();
    }

    public function hasContent()
    {
        return !empty($this->content);
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getSize()
    {
        //todo: IMPLEMENT : probably best in subclasses / content! //maybe as abstract?
    }

    public function getPage()
    {
        //TODO: Implement me
    }

    public function getEnumerate()
    {
        return $this->enumerate;
    }

    public function getShowInIndex()
    {
        return $this->showInIndex;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getAltTitle()
    {
        if (isset($this->altTitle)) {
            return $this->altTitle;
        } else {
            return $this->title;
        }
    }

    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * Sets the elements parent to another PageElement or the Document
     * @param Document|DocumentContentElement $parent
     * @throws InvalidArgumentException
     */
    public function setParent($parent)
    {
        if ($parent instanceof Document || $parent instanceof DocumentContentElementInterface) {
            $this->parent = $parent;
        } else {
            throw new InvalidArgumentException(
                'The parent of a PageElement must be another PageElement or the ' .
                'Document. ' . gettype($parent) . ' was given.'
            );
        }
    }

    public function setType($type)
    {
        if (!is_array($type)) {
            $type = [$type];
        }
        $this->type = $type;
    }

    public function setEnumerate($enumerate)
    {
        if ($this->parent !== null) {
            trigger_error(
                'setEnumerate() should not be called after adding the element'.
                ' as content',
                E_USER_WARNING
            );
        }
        $this->enumerate = $enumerate;
    }

    public function setShowInIndex($showInIndex)
    {
        if ($this->parent !== null) {
            trigger_error(
                'setShowInIndex() should not be called after adding the element'.
                ' as content',
                E_USER_WARNING
            );
        }
        $this->showInIndex = $showInIndex;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setAltTitle($altTitle)
    {
        $this->altTitle = $altTitle;
    }

    public function setCaption($caption)
    {
        $this->caption = $caption;
    }
}
