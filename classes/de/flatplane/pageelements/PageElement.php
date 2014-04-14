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

namespace de\flatplane\pageelements;

use de\flatplane\interfaces\PageElementInterface;
use de\flatplane\structure\Document;
use de\flatplane\utilities\Number;
use InvalidArgumentException;

/**
 * Abstract class for all page elements like sections, text, images, formulas, ...
 * Provides basic common functionality.
 * @author Nikolai Neff <admin@flatplane.de>
 */
abstract class PageElement implements PageElementInterface
{
    //import functionality horizontally from the trait Content
    use \de\flatplane\structure\Content;

    protected $parent;
    protected $type = 'PageElement';
    protected $number;
    protected $numberingStyle = '-1.#'; //FIXME? ->getter/setter, usage, ...

    protected $title;
    protected $altTitle;
    protected $caption;

    protected $showInIndex;
    protected $enumerate;


    // GETTER:
    // STRUCTURE
    //
    public function getParent()
    {
        return $this->parent;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function getFullNumber()
    {
        if ($this->getParent()) {
            $arr = $this->getParent()->getFullNumber();
            $arr[] = $this->number;
            return $arr;
        } else {
            if (!$this->getNumber()) {
                return [];
            } else {
                return $this->number;
            }
        }
    }

    public function getLevel()
    {
        //TODO: Implement me
    }

    // GETTER:
    // FORMAT
    //
    public function getSize()
    {
        //todo: IMPLEMENT : probably best in subclasses / content! //maybe as abstract?
    }

    public function getPage()
    {
         //TODO: Implement me
    }

    // GETTER:
    // CONTENT
    //
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
        return $this->altTitle;
    }

    public function getCaption()
    {
        return $this->caption;
    }

    public function getContent()
    {
        return $this->content;
    }


    // SETTER:
    // STRUCTURE
    //
    public function setParent($parent)
    {
        if ($parent instanceof Document || $parent instanceof PageElement) {
            $this->parent = $parent;
        } else {
            throw new InvalidArgumentException(
                'The parent of a PageElement must be another PageElement or the '.
                'Document. '.gettype($parent).' was given.'
            );
        }
    }
    public function setType($type)
    {
        $this->type = $type;
    }

    public function setNumber(Number $number)
    {
        $this->number = $number;
    }

    // SETTER:
    // CONTENT
    //
    public function setEnumerate($enumerate)
    {
        $this->enumerate = $enumerate;
    }

    public function setShowInIndex($showInIndex)
    {
        $this->showInIndex = $showInIndex;
    }

    public function setTitle($title)
    {
        $this->title=$title;
    }

    public function setAltTitle($altTitle)
    {
        $this->altTitle=$altTitle;
    }

    public function setCaption($caption)
    {
        $this->caption = $caption;
    }
}
