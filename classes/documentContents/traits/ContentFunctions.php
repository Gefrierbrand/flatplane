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
use de\flatplane\interfaces\DocumentElementInterface;
use RuntimeException;

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
     * @var int
     *  integer representing the depth of the current object inside the Document
     *  tree, starting at 0 for the root (Document).
     */
    protected $level = 0;

    /**
     * This method is used to add content to the Document or other content.
     * It checks if the given, to-be-added, content-type is allowed for the
     * current object and returns false on failure or a reference to the
     * added content.
     * TODO: doc
     *
     * @param string $type
     * @param array $settings
     * @param string $position (optional)
     *  String indicating the position where the new content will be appended to
     *  existing content. use 'first' for the beginning. defaults to 'last'
     * @return DocumentElementInterface
     *  returns a reference to the just added content instance
     */
    public function addContent($type, array $settings = [], $position = 'last')
    {
        $factory = $this->toRoot()->getElementFactory();
        $content = $factory->createElement($type, $settings);

        if (!$this->checkAllowedContent($content)) {
            throw new RuntimeException(
                "You can't add content of type {$content->getType()} to content".
                " of type {$this->getType()}."
            );
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

        if ($content->getLabel()) {
            $this->toRoot()->addLabel($content);
        }

        if ($position == 'first') {
            //add content as first array entry
            array_unshift($this->content, $content);
            return $this->content[0];
        } else {
            //append content as last array entry
            return $this->content[] = $content;
        }
    }

    /**
     * Determines if the given content may be added to the current object
     * @param DocumentElementInterface $content
     * @return bool
     */
    protected function checkAllowedContent(DocumentElementInterface $content)
    {
        if (is_array($this->getAllowSubContent())) {
            return in_array(
                $content->getType(),
                $this->getAllowSubContent()
            );
        } else {
            return (bool) $this->getAllowSubContent();
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
     * @return bool
     */
    public function hasContent()
    {
        return !empty($this->content);
    }

    /**
     * This method calls itself recursively until the root Document is reached
     * @return Document
     */
    public function toRoot()
    {
        if ($this->getParent() !==null) {
            $root = $this->getParent()->toRoot();
        } else {
            $root = $this;
        }
        if (!($root instanceof Document)) {
            throw new \RuntimeException(
                'toRoot() did not return an instance of Document'
            );
        }
        return $root;
    }

    /**
     * This method travels recursively upwards in the document tree until the
     * given depth from $level is reached an returns a reference to the reached
     * object
     * @param int $level
     * @return DocumentContentStructureInterface
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
     * @return mixed
     *  returns bool or allowed subcontent
     */
    public function getAllowSubContent()
    {
        if (is_array($this->getAllowSubContent())) {
            return $this->getAllowSubContent();
        } else {
            return (bool) $this->getAllowSubContent();
        }
    }

    /**
     * Allow or disallow subcontent of the current element
     * @param mixed $allowSubContent
     *  set to a bool to enable/disable subcontent completeley or use an array
     *  containting the names of allowed subcontent-types
     */
//    public function setAllowSubContent($allowSubContent)
//    {
//        if (!empty($this->getContent())) {
//            trigger_error(
//                'setAllowSubContent should not be called after adding content',
//                E_USER_NOTICE
//            );
//        }
//        $this->setAllowSubContent($allowSubContent);
//    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param int $level
     *  Set the depth inside the document tree
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * @return bool
     */
    public function getIsSplitable()
    {
        return $this->getIsSplitable();
    }
}