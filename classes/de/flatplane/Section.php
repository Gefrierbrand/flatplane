<?php

/*
 * Copyright (C) 2014 Nikolai Neff <admin@flatplane.de>.
 *
 * This file is part of Flatplane.
 *
 * Flatplane is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Flatplane is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace de\flatplane;

/**
 * Description of section
 *
 * @author niko
 */
class Section extends Document
{

    protected $title;
    protected $shortTitle;
    protected $showInToc = true;
    protected $showInDocument = true;
    protected $enumerate = true;
    protected $parent = null;
    protected $number;
    protected $content = array();

    /**
     * This method is used to initialice an new Section. The Section class
     * extends the Document class to be able to generate a complete tree,
     * however their purpose is different and the parent constructor
     * ist intentionally <b>not</b> called.
     *
     * FIXME: Maybe implement this better using traits.
     *
     * @param Document $parent
     *  Instance of Document
     * @param string $title
     *  The Title of the section to be displayed in the Document
     * @param string $shortTitle
     *  The Title of the section to be displayed in the TOC
     * @param bool $showInToc
     *  Determines whether to show the section in the TableOfContents(TOC) or not
     * @param bool $enumerate
     *  Determines whether the section will be automatically numbered
     * @param bool $showInDocument
     *  Determines whether the section will be shown in the document.
     *  Set this to false if you whish to add an entry to just the TOC.
     */
    public function __construct(
        Document $parent,
        $title,
        $showInToc = true,
        $enumerate = true,
        $showInDocument = true
    ) {
        $this->parent = $parent;
        $this->title = $title;
        $this->showInToc = $showInToc;
        $this->enumerate = $enumerate;
        $this->showInDocument = $showInDocument;
        $this->counter = new Counter();
    }

    public function __toString()
    {
        return $this->title;
    }
    /**
     * @see Document::addSection() :Alias:
     * @param string $title
     * 	The Title of the section to be displayed
     * @param bool $showInToc
     *  determines whether to show the section in the TableOfContents(TOC) or not
     * @param bool $enumerate
     *  determines whether the section will be automatically numbered
     * @return Section
     *  Returns new instance of section class
     */
    public function addSubSection(
        $title,
        $showInToc = true,
        $enumerate = true,
        $showInDocument = true
    ) {
        return $this->addSection($title, $showInToc, $enumerate, $showInDocument);
    }

    public function hasChildren()
    {
        return !empty($this->subSections);
    }

    public function addContent($content)
    {
        $this->content[] = $content;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setShortTitle($title)
    {
        $this->shortTitle = $title;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function setNumber($number)
    {
        $this->number = $number;
    }

    public function setParent(Section $parent)
    {
        $this->parent = $parent;
    }

    public function setShowInToc($showInToc)
    {
        $this->showInToc = $showInToc;
    }

    public function setEnumerate($enumerate)
    {
        $this->enumerate = $enumerate;
    }

    public function setShowInDocument($showInDocument)
    {
        $this->showInDocument = $showInDocument;
    }

    public function getShowInDocument()
    {
        return $this->showInDocument;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getShortTitle()
    {
        return $this->shortTitle;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getEnumerate()
    {
        return $this->enumerate;
    }

    public function getParent()
    {
        return $this->parent;
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
            return [$this->getNumber()];
        }
    }

    public function getShowInToc()
    {
        return $this->showInToc;
    }
}
