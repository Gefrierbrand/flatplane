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

use de\flatplane\documentContent\DocumentContent;
use de\flatplane\interfaces\DocumentContentElementInterface;
use de\flatplane\utilities\Counter;
use de\flatplane\utilities\Number;

//TODO: FIX ME

/**
 * Description of Content
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
trait ContentFunctions
{
    protected $number; // = array();
    protected $numberingLevel = -1;
    protected $numberingStyle = 'int';

    /**
     * @var array
     *  Array holding references to the content of the document like sections,
     *  text or formulas
     */
    protected $content;
    protected $counter = array();

    /**
     * @var mixed
     *  Set to true or false to completely allow or dissallow subcontent
     *  Set to an array containing the type names to allow specific subcontent types
     */
    protected $allowSubContent = true; // use specific types in an array or forbid completely by using false

    /**
     * TODO: update inline doc
     * @param DocumentContentElementInterface $content
     * @return DocumentContent|bool
     */
    public function addContent(DocumentContentElementInterface $content)
    {
        if (!$this->checkAllowedContent($content)) {
            trigger_error(
                "You can't add content of type {$content->getType()} to content" .
                " of type {$this->getType()}.",
                E_USER_WARNING
            );
            return false;
        }

        $content->setParent($this);
        $content->setLevel($this->getLevel()+1);

        //the number property is only set if the enumerate property is true
        if ($content->getEnumerate()) {
            $this->setCounterAndNumber($content);
        }

        //each content needs to know its parent to be able to reversely traverse
        //the document tree up to its root.

        return $this->content[] = $content;
    }

    protected function checkAllowedContent(DocumentContentElementInterface $content)
    {
        if ($this->allowSubContent === true) {
            return true;
        } elseif (in_array($content->getType(), $this->allowSubContent)) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * TODO: DOCUMENT INLINE
     * @param \de\flatplane\interfaces\DocumentContentElementInterface $content
     */
    protected function setCounterAndNumber(DocumentContentElementInterface $content)
    {
        $type = $content->getType();

        //check if a counter for the given type already exists and increment
        //its value, or create a new one for that type
        if (array_key_exists($type, $this->counter)) {
            $this->counter[$content->getType()]->add();
        } else {
            $document = $this->toRoot();
            if (isset($document->getSettings('startIndex')[$type])) {
                $startIndex = $document->getSettings('startIndex')[$type];
            } else {
                $startIndex = $document->getSettings('defaultStartIndex');
            }
            $this->addCounter(new Counter($startIndex), $type);
        }

        //set the Number as an instance of the Number object to have access
        //to advanced formating options like letters or roman numerals.
        $num = new Number($this->getCounter($type)->getValue());
        $num->setFormat($this->numberingStyle);

        if ($content->getNumberingLevel() != -1) {
            $parentnum = array_slice(
                $this->getNumber(),
                0,
                $content->getNumberingLevel()
            );
        } else {
            $parentnum = $this->getNumber();
        }

        array_push($parentnum, $num);

        $content->setNumber($parentnum);
    }

     /**
     * @return array
     *  Returns a (multilevel) Array containing references to PageElement instances
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @see getContent() :Alias:
     * @return array
     *  Returns empty array or array containing instances of Document
     */
    public function getChildren() // Alias of getSections()
    {
        return $this->getContent();
    }

    public function hasContent()
    {
        return !empty($this->content);
    }

    /**
    *
    * @param string $name
    * @return Counter
    */
    public function getCounter($name)
    {
        if (array_key_exists($name, $this->counter)) {
            return $this->counter[$name];
        } else {
            trigger_error('New Counter '.$name.' created', E_USER_WARNING);
            $startIndex = $this->toRoot()->getSettings()['startIndex'];
            return $this->addCounter(new Counter($startIndex), $name);
        }
    }

    protected function addCounter(Counter $counter, $name)
    {
        return $this->counter[$name] = $counter;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function setNumber(array $number)
    {
        $this->number = $number;
    }

    public function toRoot()
    {
        if ($this->getParent() !==null) {
            return $this->getParent()->toRoot();
        } else {
            return $this;
        }
    }

    public function getNumberingLevel()
    {
        return $this->numberingLevel;
    }

    public function getNumberingStyle()
    {
        return $this->numberingStyle;
    }

    public function getAllowSubContent()
    {
        return $this->allowSubContent;
    }

    public function setNumberingLevel($numberingLevel)
    {
        $this->numberingLevel = $numberingLevel;
    }

    public function setNumberingStyle($numberingStyle)
    {
        $this->numberingStyle = $numberingStyle;
    }

    public function setAllowSubContent($allowSubContent)
    {
        $this->allowSubContent = $allowSubContent;
    }
}
