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
    use NumberingFunctions;
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
     * @return mixed
     */
    public function getAllowSubContent()
    {
        return $this->allowSubContent;
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
