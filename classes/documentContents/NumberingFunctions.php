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

namespace de\flatplane\documentContents;

use de\flatplane\interfaces\DocumentContentElementInterface;
use de\flatplane\utilities\Counter;
use de\flatplane\utilities\Number;
use de\flatplane\utilities\Config;
use OutOfBoundsException;

/**
 * Description of NumberingFunctions
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
trait NumberingFunctions
{
    /**
    * @var array
    *  Array containing instances of the number object representing a counted value
    */
    protected $numbers = array();

    /**
     * Returns an existing Counter for the given type or creates a new one if
     * a counter for that type is not already present. This might create
     * unwanted side effects like wrong element-numbering and therefore also
     * triggers an error in that case.
     * @param string $name
     * @return Counter
     */
    public function getCounter($name)
    {
        if (array_key_exists($name, $this->counter)) {
            return $this->counter[$name];
        } else {
            //todo: proper errors?
            trigger_error('New Counter '.$name.' created', E_USER_WARNING);
            return $this->addCounter(new Counter(), $name);
        }
    }

    /**
     * Adds a new Counter for the given type to the current object
     * @param Counter $counter
     * @param string $name
     * @return Counter
     */
    protected function addCounter(Counter $counter, $name)
    {
        return $this->counter[$name] = $counter;
    }

    /**
     * This method delegates the calculation of the content-number to either its
     * own instance if the numberingLevel permits it, or to another object higher
     * up in the document tree. It then sets the number in the subcontent.
     * @param DocumentContentElementInterface $content
     * @throws OutOfBoundsException
     */
    protected function calculateNumber(DocumentContentElementInterface $content)
    {
        $numberingLevel = Config::getSettings(
            'numberingLevel',
            $content->getType()
        );
        //check the contents numberingLevel settings (-1 for arbitrary depth)
        if ($numberingLevel == -1) {
            //increment the appropriate counters in the current depth and
            //get their value
            $counterValue = $this->checkLocalCounter($content)->getValue();
            //the parent part of the childs number is the current number
            $parentnum = $this->getNumbers();
        } else if ($numberingLevel >= 0) {
            //increment the appropriate counters in the parent at the correct
            //depth and get their value
            $counterValue = $this->checkRemoteCounter($content)->getValue();

            //remove unneccesary parts from the current numbering scheme
            $parentnum = array_slice($this->getNumbers(), 0, $numberingLevel);
        } else {
            throw new OutOfBoundsException(
                'The numberingLevel can\'t be smaller than -1'
            );
        }

        //set the Number as an instance of the Number object to have access
        //to advanced formating options like letters or roman numerals.
        $num = new Number($counterValue);
        $num->setFormat(
            Config::getSettings('numberingFormat', $content->getType())
        );

        //append the new content number to the calculated parents
        $parentnum[] = $num;

        //set the contents numbering
        $content->setNumbers($parentnum);
    }

    /**
     * Checks if a counter for the content-type already exists and increments
     * its value, or creates a new one for that type
     * @param DocumentContentElementInterface $content
     * @return Counter Counter for the given content-type
     */
    public function checkLocalCounter(DocumentContentElementInterface $content)
    {
        $type = $content->getType();
        if (array_key_exists($type, $this->counter)) {
            $this->counter[$content->getType()]->add();
        } else {
            $startIndex = Config::getSettings('startIndex', $type);
            $this->addCounter(new Counter($startIndex), $type);
        }
        return $this->counter[$content->getType()];
    }

    /**
     * Calls the checkLocalCounter() method at the appropriate level in the
     * document tree
     * @param DocumentContentElementInterface $content
     * @return Counter
     */
    protected function checkRemoteCounter(DocumentContentElementInterface $content)
    {
        $level = Config::getSettings('numberingLevel', $content->getType());
        if ($level < $this->level) {
            $parentAtLevel = $this->toParentAtLevel($level);
            return $parentAtLevel->checkLocalCounter($content);
        } else {
            return $this->checkLocalCounter($content);
        }
    }

    /**
     *
     * @return array
     */
    public function getNumbers()
    {
        return $this->numbers;
    }

    public function setNumbers(array $numbers)
    {
        $this->numbers = $numbers;
    }

    /**
     *
     * @return string
     */
    public function getFormattedNumbers()
    {
        $prefix = Config::getSettings('numberingPrefix', $this->getType());
        $separator = Config::getSettings('numberingSeparator', $this->getType());
        $postfix = Config::getSettings('numberingPostfix', $this->getType());

        $out = $prefix;

        foreach ($this->numbers as $number) {
            $out .= $number->getFormattedValue();
            $out .= $separator;
        }

        $out = rtrim($out,$separator); //remove last separator
        $out .= $postfix;

        return $out;
    }
}
