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

use de\flatplane\utilities\Counter;

/**
 * This class represents the base document.
 */
class Document
{
    const START_INDEX = 1; //Change this to start all numbering from a different index

    private $author;
    private $title;
    private $description;
    private $subject;
    private $keywords;

    /**
     * @var int
     *  Number of pages; used for internal representation. //FIXME: Currently not used at all
     */
    private $pages;

    /**
     * @var string
     *  Unit of Measurement to use troughout the document.
     *  Posible values:
     *  <ul>
     *   <li>pt: point</li>
     *   <li>mm: millimeter (default)</li>
     *   <li>cm: centimeter</li>
     *   <li>in: inch</li>
     *  </ul>
     */
    private $unit = 'mm';

    /**
     * @var array
     *  Array holding references to the sections or subsections of the document
     */
    protected $subSections;

    /**
     * @var Counter
     *  Holds an instance of Counter to enumerate the documents children
     */
    protected $counter = array();

    /**
     * FIXME: Beschreibung hinzufügen
     * @param string $title
     * 	The Title of the section to be displayed
     * @param bool $showInToc
     *  determines whether to show the section in the TableOfContents(TOC) or not
     * @param bool $enumerate
     *  determines whether the section will be automatically numbered
     * @return Section
     *  Returns new instance of section class
     */
    public function addSection(
        $title,
        $showInToc = true,
        $enumerate = true,
        $showInDocument = true
    ) {
        if ($enumerate) {
            if (array_key_exists('section', $this->counter)) {
                $this->counter['section']->add();
            } else {
                $this->addCounter(new Counter(SELF::START_INDEX), 'section');
            }
        }
        $sec = new Section($this, $title, $showInToc, $enumerate, $showInDocument);
        $sec->setNumber($this->getCounter('section')->getValue());

        $this->subSections[] = $sec;
        return $sec;
    }

    /**
     *
     * @param string $title
     *  Document title. (Is usually displayed in the PDF-Reader titlebar)
     * @param string $author
     *  Name of Author. (Usually displayed in the document properties)
     * @param string $description
     *  Description/Summary of the document
     * @param string $subject
     *  Subject of the document
     * @param string $keywords
     *  Comma-seperated list of Keywords
     * @param string $unit
     *  Unit of Measurement to use troughout the document.
     *  Posible values:
     *  <ul>
     *   <li>pt: point</li>
     *   <li>mm: millimeter (default)</li>
     *   <li>cm: centimeter</li>
     *   <li>in: inch</li>
     *  </ul>
     */
    public function __construct(
        $title = '',
        $author = '',
        $description = '',
        $subject = '',
        $keywords = '',
        $unit = 'mm'
    ) {
        $this->title = $title;
        $this->author = $author;
        $this->description = $description;
        $this->subject = $subject;
        $this->keywords = $keywords;
        $this->unit = $unit;
        //$this->creator = FP_TITLE.FP_VERSION;
    }

    /**
     * @return bool
     */
    public function hasChildren()
    {
        return !empty($this->subSections);
    }

    /**
     * @see hasChildren() :Alias:
     * @return bool
     */
    public function hasSections()
    {
        return $this->hasChildren();
    }

    /**
     * This Method is used to access the children of a Document or class
     * @return array
     *  Returns empty array or array containing instances of Document
     */
    public function getSections()
    {
        return $this->subSections;
    }

    /**
     *
     * @see getSections() :Alias:
     * @return array
     *  Returns empty array or array containing instances of Document
     */
    public function getSubSections() // Alias of getSections()
    {
        return $this->getSections();
    }

    /**
     * @see getSections() :Alias:
     * @return array
     *  Returns empty array or array containing instances of Document
     */
    public function getChildren() // Alias of getSections()
    {
        return $this->getSections();
    }

    public function getFullNumber()
    {
        return [];
    }

    public function getCounter($name)
    {
        if (array_key_exists($name, $this->counter)) {
            return $this->counter[$name];
        } else {
            //TODO: Maybe notice the user an new counter was created?
            return $this->counter[$name] = new Counter();
        }
    }

    public function addCounter(Counter $counter, $name)
    {
        $this->counter[$name] = $counter;
    }
}
