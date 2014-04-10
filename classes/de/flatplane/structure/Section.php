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

namespace de\flatplane\structure;

use de\flatplane\pageelements\PageElement;
use de\flatplane\utilities\Counter;
use de\flatplane\utilities\Number;

/**
 * Description of section
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class Section extends Document
{
    protected $title;
    protected $altTitle;
    protected $showInToc = true;
    protected $showInDocument = true;
    protected $enumerate = true;
    protected $parent = null;
    protected $number;
    protected $content = array();

    /**
     * This method is used to initialise a new Section. The Section class
     * extends the Document class to be able to generate a complete tree,
     * however their purpose is different and the parent constructor
     * ist intentionally <b>not</b> called.
     *
     * @internal FIXME: Maybe implement this better using traits.
     *
     * @param Document $parent
     *  Instance of Document
     * @param string $title
     *  The title of the section to be displayed in the document
     * @param string $altTitle
     *  An alternative (shorter) title to be used in the TableOfContents(TOC)
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
        $altTitle = '',
        $showInToc = true,
        $enumerate = true,
        $showInDocument = true
    ) {
        $this->parent = $parent;
        $this->title = $title;
        $this->showInToc = $showInToc;
        $this->enumerate = $enumerate;
        $this->showInDocument = $showInDocument;
        if ($altTitle == '') {
            $this->altTitle = $title;
        } else {
            $this->altTitle = $altTitle;
        }
    }

    public function __toString()
    {
        return $this->title;
    }

    /**
     * This method overwrited Document::addSection() and relays the call to addSubsection()
     * @see addSubSection()
     * @param string $title
     * @param string $altTitle
     * @param bool $showInToc
     * @param bool $enumerate
     * @param bool $showInDocument
     * @return Section
     */
    public function addSection(
        $title,
        $altTitle = '',
        $showInToc = true,
        $enumerate = true,
        $showInDocument = true
    ) {
        trigger_error('addSection() sould not be used for SubSections. Use addSubSection() on the section object instead', 'E_USER_NOTICE');
        return $this->addSubSection(
            $title,
            $altTitle,
            $showInToc,
            $enumerate,
            $showInDocument
        );
    }

    /**
     * This method adds SubSections or SubSubSections (or any other depth) to the
     * parent section.
     *
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
        $altTitle = '',
        $showInToc = true,
        $enumerate = true,
        $showInDocument = true
    ) {
        $counterValue = null;
        if ($enumerate) {
            if (array_key_exists('section', $this->counter)) {
                $this->counter['section']->add();
            } else {
                $startIndex = $this->toRoot()->getSettings()->getStartIndex();
                $this->addCounter(new Counter($startIndex), 'section');
            }
            $counterValue = $this->getCounter('section')->getValue();
        }
        $sec = new Section($this, $title, $altTitle, $showInToc, $enumerate, $showInDocument);
        $sec->setNumber(new Number($counterValue));
        $this->subSections[] = $sec;
        return $sec;
    }

    public function addContent(PageElement $content)
    {
        //TODO: fix code dupilcation
        if ($content->getEnumerate()) {
            if (array_key_exists($content->getType(), $this->counter)) {
                $this->counter[$content->getType()]->add();
            } else {
                $startIndex = $this->toRoot()->getSettings()->getStartIndex();
                $this->addCounter(new Counter($startIndex), $content->getType());
            }
            $content->setNumber(new Number($this->getCounter($content->getType())->getValue()));
        }

        /*
         * every content gets a number depending on its type and level inside the
         * document tree. therefore the nummeration starts new in each subsection
         * and so on. The display of these numbers in another format (e.g. numeration
         * for the complete document) is handled by the display layer in the
         * corresponding iterators*/


        $content->setParent($this);
        return $this->content[] = $content;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setAltTitle($title)
    {
        $this->altTitle = $title;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function setNumber(Number $number)
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

    public function getAltTitle()
    {
        return $this->altTitle;
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

    public function toRoot()
    {
        return $this->parent->toRoot();
    }
}
