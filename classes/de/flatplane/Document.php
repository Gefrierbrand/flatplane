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
 * This class represents the base document.
 */
class Document
{

    /**
     * @var string
     *  Name of Author
     */
    private $author;

    /**
     * @var string
     *  Document title
     */
    private $title;

    /**
     * @var string
     *  Description/Summary of the document
     */
    private $description;

    /**
     * @var string
     *  Subject of the document
     */
    private $subject;

    /**
     * @var string
     *  Comma-seperated list of Keywords
     */
    private $keywords;

    /**
     * @var int
     *  Number of pages
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
    protected $counter;

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
    public function addSection($title, $showInToc = true, $enumerate = true,
        $showInDocument = true)
    {
        if ($enumerate) {
            $this->counter->add();
        }
        $sec = new Section($this, $title, $showInToc, $enumerate, $showInDocument);
        $sec->setNumber($this->counter->getValue());

        $this->subSections[] = $sec;
        return $sec;
    }

    public function __construct($title = '', $author = '', $description = '',
        $subject = '', $keywords = '', $unit = 'mm')
    {
        $this->title = $title;
        $this->author = $author;
        $this->description = $description;
        $this->subject = $subject;
        $this->keywords = $keywords;
        $this->unit = $unit;
        //$this->creator = FP_TITLE.FP_VERSION;
        $this->counter = new Counter();
    }

    /**
     * This Method is used to access the children of a Document or class
     * @return array Returns empty array or array containing instances of Document
     */
    public function getSections()
    {
        return $this->subSections;
    }

    /**
     *
     * @see getSections() :Alias:
     * @return array Returns empty array or array containing instances of Document
     */
    public function getSubSections() // Alias of getSections()
    {
        return $this->getSections();
    }

    /**
     * @see getSections() :Alias:
     * @return array Returns empty array or array containing instances of Document
     */
    public function getChildren() // Alias of getSections()
    {
        return $this->getSections();
    }

    public function getFullNumber()
    {
        return [];
    }

    public function getCounter()
    {
        return $this->counter;
    }

    public function setCounter(Counter $counter)
    {
        $this->counter = $counter;
    }
}
?>