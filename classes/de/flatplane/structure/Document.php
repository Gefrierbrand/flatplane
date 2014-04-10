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

use de\flatplane\settings\DocumentSettings;
use de\flatplane\utilities\Counter;

/**
 * This class represents the base document.
 * @author Nikolai Neff <admin@flatplane.de>
 */
class Document
{
    /**
     * @var DocumentSettings
     *  Holds an instance of the DocumentSettings configuration Object
     */
    private $settings;

    /**
     * @var int
     *  Number of pages; used for internal representation. //FIXME: Currently not used at all
     */
    private $pages;

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
     * @return Section
     */
    public function addSection(
        $title,
        $altTitle = '',
        $showInToc = true,
        $enumerate = true,
        $showInDocument = true
    ) {
        if ($enumerate) {
            if (array_key_exists('section', $this->counter)) {
                $this->counter['section']->add();
            } else {
                var_dump($this->settings);
                $startIndex = $this->settings->getStartIndex();
                //alternative:
                //$startIndex = $this->settings->getSetting('startIndex');
                $this->addCounter(new Counter($startIndex), 'section');
            }
        }
        $sec = new Section($this, $title, $altTitle, $showInToc, $enumerate, $showInDocument);
        $sec->setNumber($this->getCounter('section')->getValue());

        $this->subSections[] = $sec;
        return $sec;
    }

    /**
     *  Document Constructor
     *  @param DocumentSettings $settings
     *   Instance of the DocumentSettings configuration object or null to use defaults
     */
    public function __construct(DocumentSettings $settings = null)
    {
        if ($settings === null) {
            $settings = new DocumentSettings(); //use default values
        }
        $this->settings = $settings;
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
     * This method is used to access the children of a document or its subclasses
     * @return array
     *  Returns empty array or array containing instances of Document
     */
    public function getSections()
    {
        return $this->subSections;
    }

    /**
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

    /**
     * This method is called recursively by sections to get their complete branch
     * number. As the document is always the root, this method gets overridden by
     * the subclasses for the actual implemenation
     *
     * @see Section::getFullNumber()
     * @return array
     *  returns empty array
     */
    public function getFullNumber()
    {
        return [];
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
            //TODO: Maybe notice the user an new counter was created?
            return $this->counter[$name] = new Counter();
        }
    }

    public function addCounter(Counter $counter, $name)
    {
        $this->counter[$name] = $counter;
    }

    /*
    private function getSettings($key = null)
    {
        if ($key === null) {
            return $this->settings;
        } else {
            return $this->settings->getSetting($key);
        }
    }
    */

    public function setSettings(DocumentSettings $settings)
    {
        $this->settings = $settings;
    }

    public function getSettings()
    {
        return $this->settings;
    }

    public function toRoot()
    {
        return $this;
    }
}
