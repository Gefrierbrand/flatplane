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

namespace de\flatplane\documentElements\traits;

use de\flatplane\documentElements\Document;
use de\flatplane\documentElements\Formula;
use de\flatplane\documentElements\Image;
use de\flatplane\documentElements\ListOfContents;
use de\flatplane\documentElements\Section;
use de\flatplane\documentElements\Table;
use de\flatplane\documentElements\Text;
use de\flatplane\interfaces\DocumentElementInterface;
use de\flatplane\interfaces\documentElements\DocumentInterface;
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
     * @var array
     *  Named-key array holding instances of Counter
     */
    protected $counter = array();

    /**
     * @var int
     *  integer representing the depth of the current object inside the Document
     *  tree, starting at -1 for the root (Document). Don't confuse this with the
     *  maxDepth "-1" of iterators which mean 'unlimited'
     */
    protected $level = -1;

    /**
     * This method is used to add content to the Document or other content.
     * It checks if the given, to-be-added, content-type is allowed for the
     * current object and returns false on failure or a reference to the
     * added content.
     *
     * TODO: doc
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

    public function addElement($type, array $settings)
    {
        $factory = $this->toRoot()->getElementFactory();
        $content = $factory->createElement($type, $settings);
        return $this->addContent($content);
    }

    /**
     * @param string $title
     * @param array $settings
     * @return Section
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
     * @param string $code
     * @param array $settings
     * @return Formula
     */
    public function addFormula($code, array $settings = [])
    {
        $factory = $this->toRoot()->getElementFactory();
        $settings['code'] = $code;
        $content = $factory->createElement('formula', $settings);
        return $this->addContent($content);
    }

    /**
     * @param array $data
     * @param array $settings
     * @return Table
     */
    public function addTable(array $data, array $settings = [])
    {
        $factory = $this->toRoot()->getElementFactory();
        $settings['data'] = $data;
        $content = $factory->createElement('table', $settings);
        return $this->addContent($content);
    }

    /**
     * @param string $path
     * @param array $settings
     * @return Text
     */
    public function addText($path, array $settings = [])
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
     * @param string $path
     * @param array $settings
     * @return Image
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
