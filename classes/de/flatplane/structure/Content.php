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
 * Description of Content
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
trait Content
{
    /**
     * @var array
     *  Array holding references to the content of the document like sections,
     *  text or formulas
     */
    protected $content;
    protected $counter = array();

    /**
     *
     * @param \de\flatplane\pageelements\PageElement $content
     * @return \de\flatplane\pageelements\PageElement
     */
    public function addContent(PageElement $content)
    {
        /* every content gets a number depending on its type and level inside the
         * document tree. therefore the nummeration starts new in each subsection
         * and so on. The display of these numbers in another format (e.g. numeration
         * for the complete document) is handled by the display layer in the
         * corresponding iterators and in the getFullNumber() Method*/

        //the number property is only set if the enumerate property is true
        if ($content->getEnumerate()) {
            $document = $this->toRoot();

            //check if a counter for the given type already exists and increment
            //its value, or create a new one for that type
            $type = $content->getType();
            if (array_key_exists($type, $this->counter)) {
                $this->counter[$content->getType()]->add();
            } else {
                if (isset($document->getSettings('startIndex')[$type])) {
                    $startIndex = $document->getSettings('startIndex')[$type];
                } else {
                    $startIndex = $document->getSettings('defaultStartIndex');
                }
                $this->addCounter(new Counter($startIndex), $type);
            }

            //set the Number as an instance of the Number object to have access
            //to advanced formating options like letters or roman numerals.
            $content->setNumber(new Number($this->getCounter($type)->getValue()));
        }

        //each content needs to know its parent to be able to reversely traverse
        //the document tree to its root.
        $content->setParent($this);
        return $this->content[] = $content;
    }

    /**
     * @return array
     *  Returns a (multilevel) Array containing references to PageElement instances
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @see getContent() :Alias:
     * @return array
     *  Returns empty array or array containing instances of Document
     */
    public function getChildren() // Alias of getSections()
    {
        return $this->getContent();
    }

    public function hasContent()
    {
        return !empty($this->content);
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
            trigger_error('New Counter '.$name.' created', E_USER_WARNING);
            $startIndex = $this->toRoot()->getSettings()['startIndex'];
            return $this->addCounter(new Counter($startIndex), $name);
        }
    }

    protected function addCounter(Counter $counter, $name)
    {
        return $this->counter[$name] = $counter;
    }
}
