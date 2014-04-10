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

namespace de\flatplane\settings;

/**
 * Description of DocumentSettings
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class DocumentSettings
{
    /*
     * Default values for various settings
     */

    /**
     * @var int
     *  Determines which number to use as startvalue for counters
     *  (e.g. Sections, formulas), default = 1
     */
    private $startIndex = 1;

    /**
     * @var array
     *  Sets the counting modes for various counters in an associative array:
     *  <ul>
     *   <li>level: determins at which depth of the document structure a new counter
     *       is started. Example: If you set this to 1 for the formula counter, then
     *       each section will start a new numeration for its contained formulas.
     *       if set to 2, every subsection will start a new numeration and so on.</li>
     *   <li>format: sets the default output style.
     *       Possible values are:
     *       <ul>
     *          <li>int: (default) The counter value is displayed as (signed) Numeral
     *              (wich might be a float depending on size and increment)</li>
     *          <li>alpha: The value is displayed as letters. (use ALPHA for
     *              capitalised output)</li>
     *          <li>roman: Use roman numbers as outputformat. (ROMAN for upper-case
     *              output)</li>
     *      </ul>
     *  </ul>
     */
    private $counterModes = array(
                                'section'=>['level'=>0,'format'=>'int'],
                                'formula'=>['level'=>1,'format'=>'int']
                            );

    private $author = '';
    private $title = '';
    private $description;
    private $subject;
    private $keywords;

    //private $generator;

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
     * Todo: Document me!
     * @param string $key
     * @return mixed
     */
    public function getSetting($key)
    {
        $callback = 'get'.ucfirst($key);
        if (method_exists($this, $callback)) {
            return call_user_func($callback);
        } elseif (array_key_exists($key, get_object_vars($this))) {
            return get_object_vars($this)[$key];
        } else {
            trigger_error('Setting '.$key.' not found', 'E_USER_NOTICE');
            return null;
        }
    }

    public function getStartIndex()
    {
        return $this->startIndex;
    }

    public function getCounterModes()
    {
        return $this->counterModes;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function getKeywords()
    {
        return $this->keywords;
    }

    public function getUnit()
    {
        return $this->unit;
    }

    public function setStartIndex($startIndex)
    {
        $this->startIndex = $startIndex;
    }

    public function setCounterModes($counterModes)
    {
        $this->counterModes = $counterModes;
    }

    public function setAuthor($author)
    {
        $this->author = $author;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    public function setUnit($unit)
    {
        $this->unit = $unit;
    }
}
