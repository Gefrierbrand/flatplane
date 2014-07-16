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

namespace de\flatplane\documentElements\traits;

use de\flatplane\documentElements\Code;
use de\flatplane\documentElements\ListOfContents;
use de\flatplane\interfaces\DocumentElementInterface;
use de\flatplane\interfaces\documentElements\DocumentInterface;
use de\flatplane\interfaces\documentElements\FormulaInterface;
use de\flatplane\interfaces\documentElements\ImageInterface;
use de\flatplane\interfaces\documentElements\TableInterface;
use de\flatplane\interfaces\documentElements\TextInterface;
use de\flatplane\interfaces\documentElements\SectionInterface;
use RuntimeException;

/**
 * This trait provides functionality to the Document and DocumentContentElement
 * classes for dealing with content elements like adding and numbering items.
 * @internal
 *  This might get extended to be able to move or delete content elements,
 *  which is currently not needed.
 *
 * todo: update doc
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
     * @var int
     *  integer representing the depth of the current object inside the document
     *  tree, starting at 0 for the root (Document).
     */
    protected $level = 0;

    /**
     * This method is used to add content to the Document or other content.
     * It checks if the given, to-be-added, content-type is allowed for the
     * current object and returns false on failure or a reference to the
     * added content.
     *
     * @param DocumentElementInterface $content
     * @param string $position (optional)
     *  String indicating the position where the new content will be appended to
     *  existing content. use 'first' for the beginning. defaults to 'last'
     * @return DocumentElementInterface
     *  returns a reference to the just added content instance
     */
    protected function addContent(DocumentElementInterface $content, $position = 'last')
    {
        if (!$this->checkAllowedContent($content)) {
            throw new RuntimeException(
                "You can't add content of type {$content->getType()} to content".
                " of type {$this->getType()}."
            );
        }
        //set the contents parent to the current instance to be able to reversely
        //traverse the document tree
        $content->setParent($this);

        //hyphenate Title / altTitle of the content (if needed)
        $content->hyphenateTitle();

        //the subcontents level is always one greater than the current level
        $content->setLevel($this->getLevel()+1);

        //the number property is only set if the enumerate property is true
        if ($content->getEnumerate()) {
            $this->calculateNumber($content);
        }

        //add a label to the document-wide label list, if the content requires it
        if ($content->getLabel()) {
            $this->toRoot()->addLabel($content);
        }

        if ($position == 'first') {
            //add content as first array entry
            if (!is_array($this->content)) {
                $this->content = [];
            }
            array_unshift($this->content, $content);
            return $this->content[0];
        } else {
            //append content as last array entry
            return $this->content[] = $content;
        }
    }

    /**
     * Adds an arbitrary element of type $type to the current content
     * @param string $type
     * @param array $settings
     * @return DocumentElementInterface
     *  Content object implementing DocumentElementInterface with its parent set
     *  to the current object
     */
    public function addElement($type, array $settings = [])
    {
        $factory = $this->toRoot()->getElementFactory();
        $content = $factory->createElement($type, $settings);
        return $this->addContent($content);
    }

    /**
     * Adds a new Section to the current element
     * @param string $title
     * @param array $settings
     * @return SectionInterface
     *  Content object implementing SectionInterface
     */
    public function addSection($title, array $settings = [])
    {
        $factory = $this->toRoot()->getElementFactory();
        $settings['title'] = $title;
        $content = $factory->createElement('section', $settings);
        return $this->addContent($content);
    }

    /**
     * @param array $displayTypes (optional)
     * @param array $settings (optional)
     * @return ListOfContents
     */
    public function addList(array $displayTypes = ['section'], array $settings = [])
    {
        $factory = $this->toRoot()->getElementFactory();
        $settings['displayTypes'] = $displayTypes;
        $content = $factory->createElement('list', $settings);
        return $this->addContent($content);
    }

    /**
     * Adds a new formula to the current element
     * @param string $code
     *  TeX or MathMl description of the formula
     * @param array $settings
     * @return FormulaInterface
     */
    public function addFormula($code, array $settings = [])
    {
        $factory = $this->toRoot()->getElementFactory();
        $settings['code'] = $code;
        $content = $factory->createElement('formula', $settings);
        return $this->addContent($content);
    }

    /**
     * Adds Text (including references or footnotes) from a file
     * @param string $path
     * @param array $settings
     * @return TextInterface
     */
    public function addTextFile($path, array $settings = [])
    {
        if (!is_readable($path)) {
            throw new RuntimeException('File '.$path.' is not readable');
        }
        $factory = $this->toRoot()->getElementFactory();
        $settings['path'] = $path;
        $content = $factory->createElement('text', $settings);
        return $this->addContent($content);
    }

    /**
     * Adds Text (without references or footnotes) from a string
     * @param string $text
     * @param array $settings
     * @return TextInterface
     */
    public function addText($text, array $settings = [])
    {
        $factory = $this->toRoot()->getElementFactory();
        $settings['text'] = $text;
        $settings['parse'] = false;
        $content = $factory->createElement('text', $settings);
        return $this->addContent($content);
    }

    /**
     * Adds a new Table to the current element
     * @param string $code
     *  HTML representation of the table
     * @param array $settings
     * @return TableInterface
     */
    public function addTable($code, array $settings = [])
    {
        $factory = $this->toRoot()->getElementFactory();
        $settings['text'] = $code;
        $content = $factory->createElement('table', $settings);
        return $this->addContent($content);
    }

    /**
     * Adds highlighted PHP Code as text from a file
     * @param string $path
     * @param array $settings
     * @return Code
     */
    public function addCodeFile($path, array $settings = [])
    {
        if (!is_readable($path)) {
            throw new RuntimeException('File '.$path.' is not readable');
        }
        $factory = $this->toRoot()->getElementFactory();
        $settings['path'] = $path;
        $content = $factory->createElement('code', $settings);
        return $this->addContent($content);
    }

    /**
     * Adds an image to the current element
     * @param string $path
     * @param array $settings
     * @return ImageInterface
     * @throws RuntimeException
     */
    public function addImage($path, array $settings = [])
    {
        if (!is_readable($path)) {
            throw new RuntimeException('File '.$path.' is not readable');
        }
        $factory = $this->toRoot()->getElementFactory();
        $settings['path'] = $path;
        $content = $factory->createElement('image', $settings);
        return $this->addContent($content);
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
    *  Returns a multilevel array containing references to
    *  DocumentContentElement instances
    */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Indicates whether the current object cointains other content objects
     * @return bool
     */
    public function hasContent()
    {
        return !empty($this->content);
    }

    /**
     * This method calls itself recursively until the root node (Document)
     * is reached
     * @return DocumentInterface
     */
    public function toRoot()
    {
        if ($this->getParent() !== null) {
            $root = $this->getParent()->toRoot();
        } else {
            $root = $this;
        }
        if (!($root instanceof DocumentInterface)) {
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
     * @return DocumentElementInterface
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
     * Gets the level (=depth) of the current element inside the document tree
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
