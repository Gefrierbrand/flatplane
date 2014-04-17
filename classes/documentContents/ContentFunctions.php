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
use de\flatplane\structure\Document;
use de\flatplane\utilities\Counter;
use de\flatplane\utilities\Number;

/**
 * This trait provides functionality to the Document and DocumentContentElement
 * classes for dealing with content elements like adding and numbering items.
 * @internal
 *  This might get extended to be able to move or delete content elements,
 *  which is currently not needed.
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
trait ContentFunctions
{
    /**
     * @var array
     *  Array containing instances of the number object representing a counted value
     */
    protected $numbers = array();
    protected $numberingLevel = -1;
    protected $numberingStyle = 'int';

    /**
     * @var array
     *  Array holding references to the content of the document like sections,
     *  text or formulas
     */
    protected $content;

    /**
     * @var array
     *  Named-key array holding instances of Counter
     */
    protected $counter = array();

    /**
     * @var mixed
     *  Set to true or false to completely allow or dissallow subcontent
     *  Set to an array containing the type names to allow specific content types
     */
    protected $allowSubContent = true;

    /**
     * @var int
     *  integer representing the depth of the current object inside the Document
     *  tree, starting at 0 for the root (Document).
     */
    protected $level = 0;

    /**
     * This method is used to add content to the Document or other content.
     * It checks if the given, to-be added content-type is allowed for the current
     * object and returns false on failure or a reference to the added content.
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

        //set the contents parent to the current instance to be able to reversely
        //traverse the document tree
        $content->setParent($this);

        //the subcontents level is always one greater than the current level
        $content->setLevel($this->getLevel()+1);

        //the number property is only set if the enumerate property is true
        if ($content->getEnumerate()) {
            $this->calculateNumber($content);
        }

        return $this->content[] = $content;
    }

    /**
     * Determines if the given content may be added to the current object
     * @param DocumentContentElementInterface $content
     * @return bool
     */
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
     * This method delegates the calculation of the content-number to either its
     * own instance if the numberingLevel permits it, or to another object higher
     * up in the document tree. It then sets the number in the subcontent.
     * @param DocumentContentElementInterface $content
     * @throws OutOfBoundsException
     */
    protected function calculateNumber(DocumentContentElementInterface $content)
    {
        //check the contents numberingLevel settings (-1 for arbitrary depth)
        if ($content->getNumberingLevel() == -1) {
            //increment the appropriate counters in the current depth and
            //get their value
            $counterValue = $this->checkLocalCounter($content)->getValue();
            //the parent part of the childs number is the current number
            $parentnum = $this->getNumbers();
        } else if ($content->getNumberingLevel() >= 0) {
            //increment the appropriate counters in the parent at the correct
            //depth and get their value
            $counterValue = $this->checkRemoteCounter($content)->getValue();

            //remove unneccesary parts from the current numbering scheme
            $parentnum = array_slice(
                $this->getNumbers(),
                0,
                $content->getNumberingLevel()
            );
        } else {
            throw new \OutOfBoundsException(
                'The numberingLevel can\'t be smaller than -1'
            );
        }

        //set the Number as an instance of the Number object to have access
        //to advanced formating options like letters or roman numerals.
        $num = new Number($counterValue);
        $num->setFormat($this->numberingStyle);

        //append the new content number to the calculated parents
        array_push($parentnum, $num);
        $content->setNumbers($parentnum);
    }

    /**
     * Checks if a counter for the content-type already exists and increments
     * its value, or creates a new one for that type
     * @param DocumentContentElementInterface $content
     * @return Counter Counter for the given content-type
     */
    public function checkLocalCounter(DocumentContentElementInterface $content)
    {
        $type = $content->getType();
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
        return $this->counter[$content->getType()];
    }

    /**
     * Calls the checkLocalCounter() method at the appropriate level in the
     * document tree
     * @param DocumentContentElementInterface $content
     * @return Counter
     */
    protected function checkRemoteCounter(DocumentContentElementInterface $content)
    {
        $level = $content->getNumberingLevel();
        if ($level < $this->level) {
            $parentAtLevel = $this->toParentAtLevel($level);
            return $parentAtLevel->checkLocalCounter($content);
        } else {
            return $this->checkLocalCounter($content);
        }
    }

    /**
    * @return array
    *  Returns a (multilevel) Array containing references to
    *  DocumentContentElement instances
    */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @see getContent() :Alias:
     * @return array
     */
    public function getChildren() // Alias of getSections()
    {
        return $this->getContent();
    }

    /**
     * @return bool
     */
    public function hasContent()
    {
        return !empty($this->content);
    }

    /**
     * Returns an existing Counter for the given type or creates a new one if
     * a counter for that type is not already present. This might create
     * unwanted side effects like wrong element-numbering and therefore also
     * triggers an error in that case.
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

    /**
     * Adds a new Counter for the given type to the current object
     * @param Counter $counter
     * @param string $name
     * @return Counter
     */
    protected function addCounter(Counter $counter, $name)
    {
        return $this->counter[$name] = $counter;
    }

    /**
     *
     * @return array
     */
    public function getNumbers()
    {
        return $this->numbers;
    }

    public function setNumbers(array $number)
    {
        $this->numbers = $number;
    }

    /**
     * This method calls itself recursively until the root Document is reached
     * @return DocumentContentStructure
     */
    public function toRoot()
    {
        if ($this->getParent() !==null) {
            return $this->getParent()->toRoot();
        } else {
            return $this;
        }
    }

    /**
     * This method travels recursively upwards in the document tree until the
     * given depth from $level is reached an returns a reference to the reached
     * object
     * @param int $level
     * @return DocumentContentStructure
     */
    public function toParentAtLevel($level)
    {
        if ($level <0) {
            trigger_error('Level can\'t be smaller than 0.', E_USER_NOTICE);
            $level = 0;
        }

        if ($this->level == $level) {
            return $this;
        } else {
            return $this->getParent()->toParentAtLevel($level);
        }
    }

    /**
     *
     * @return int
     */
    public function getNumberingLevel()
    {
        if ($this->numberingLevel < -1) {
            trigger_error('Numering Level can\'t be smaller than -1', E_USER_NOTICE);
            $this->numberingLevel = -1;
        }
        return $this->numberingLevel;
    }

    /**
     *
     * @return string
     */
    public function getNumberingStyle()
    {
        return $this->numberingStyle;
    }

    /**
     *
     * @return mixed
     */
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

    /**
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    public function setLevel($level)
    {
        $this->level = $level;
    }
}
