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

use de\flatplane\interfaces\CounterInterface;
use de\flatplane\interfaces\DocumentElementInterface;
use de\flatplane\utilities\Counter;
use de\flatplane\utilities\Number;
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
     * @var array
     *  Named-key array holding instances of Counter
     */
    protected $counter = array();

    /**
     * Returns an existing Counter for the given name or creates a new one if
     * a counter with that name is not already present. This might create
     * unwanted side effects like wrong element-numbering and therefore also
     * triggers an error in that case.
     * @param string $name
     * @return CounterInterface
     */
    public function getCounter($name)
    {
        if (array_key_exists($name, $this->counter)) {
            return $this->counter[$name];
        } else {
            //todo: Exception instead of error here?
            trigger_error('New Counter '.$name.' created', E_USER_WARNING);
            return $this->addCounter(new Counter(), $name);
        }
    }

    /**
     * Returns all defined Counters for the current level as array
     * @return array
     */
    public function getCounterArray()
    {
        return $this->counter;
    }

    /**
     * Adds a new Counter with the given name to the current object. If a counter
     * with that name already exists it will be overwritten with the new counter.
     * @param CounterInterface $counter
     * @param string $name
     * @return Counter
     */
    public function addCounter(CounterInterface $counter, $name)
    {
        return $this->counter[$name] = $counter;
    }

    /**
     * This method delegates the calculation of the content-number to either its
     * own instance if the numberingLevel permits it, or to another object higher
     * up in the document tree. It then sets the number in the subcontent.
     * @param DocumentElementInterface $content
     * @throws OutOfBoundsException
     */
    protected function calculateNumber(DocumentElementInterface $content)
    {
        $root = $content->toRoot();
        //the numbering level is a document wide setting, so retrieve it from
        //the documents config
        $numberingLevel = $root->getNumberingLevel($content->getType());
        //check the contents numberingLevel settings (-1 for arbitrary depth)
        if ($numberingLevel == -1) {
            //increment the appropriate counters in the current depth and
            //get their value
            $counterValue = $this->checkLocalCounter($content)->getValue();
            //the parent part of the childs number is the current number
            $parentnum = $this->getNumbers();
        } elseif ($numberingLevel >= 0) {
            //increment the appropriate counters in the parent at the correct
            //depth and get their value
            $counterValue = $this->checkRemoteCounter($content)->getValue();

            //remove unneccesary parts from the current numbering scheme
            //array_slice(originalarray, offset, legth)
            $parentnum = array_slice($this->getNumbers(), 0, $numberingLevel);
        } else {
            throw new OutOfBoundsException(
                'The numberingLevel can\'t be smaller than -1'
            );
        }

        //set the Number as an instance of the Number object to have access
        //to advanced formating options like letters or roman numerals.
        $num = new Number($counterValue);
        $num->setFormat($root->getNumberingFormat($content->getType()));

        //append the new content number to the calculated parents
        $parentnum[] = $num;

        //set the contents numbering
        $content->setNumbers($parentnum);
    }

     /**
     * Checks if a counter for the content-type already exists on the current
     * level and increments its value, or creates a new one for that content-type
     * @param DocumentElementInterface $content
     * @return Counter
     *  Counter for the given content-type
     */
    public function checkLocalCounter(DocumentElementInterface $content)
    {
        $type = $content->getType();
        if (array_key_exists($type, $this->getCounterArray())) {
            $this->getCounter($type)->add();
        } else {
            $startIndex = $this->toRoot()->getStartIndex($type);
            $this->addCounter(new Counter($startIndex), $type);
        }
        return $this->getCounter($content->getType());
    }

    /**
     * Calls the checkLocalCounter() method at the appropriate level in the
     * document tree
     * @param DocumentElementInterface $content
     * @return Counter
     */
    protected function checkRemoteCounter(DocumentElementInterface $content)
    {
        $level = $content->toRoot()->getNumberingLevel($content->getType());
        if ($level < $this->getLevel()) {
            $parentAtLevel = $this->toParentAtLevel($level);
            return $parentAtLevel->checkLocalCounter($content);
        } else {
            return $this->checkLocalCounter($content);
        }
    }

    /**
     * Get all numbers for the current element and its parents up to the root
     * @return array
     *  Array containing objects implementing NumberInterface
     */
    public function getNumbers()
    {
        return $this->numbers;
    }

    /**
     * Set the numbers of the current element
     * @param array $numbers
     *  Array containing values of the numbers or objects implementing the
     *  NumberInterface
     */
    public function setNumbers(array $numbers)
    {
        foreach ($numbers as $key => $number) {
            if (!($number instanceof Number)) {
                $numbers[$key] = new Number($number);
            }
        }
        $this->numbers = $numbers;
    }

    /**
     * Get the numbers of the current object formatted as string according to
     * the elements settings
     * @return string
     */
    public function getFormattedNumbers()
    {
        $root = $this->toRoot();
        $type = $this->getType();

        //all default to null if setting is not found
        $prefix = $root->getNumberingPrefix($type);
        $separator = $root->getNumberingSeparator($type);
        $postfix = $root->getNumberingPostfix($type);

        $out = $prefix;

        foreach ($this->getNumbers() as $number) {
            $out .= $number->getFormattedValue();
            $out .= $separator;
        }

        $out = rtrim($out, $separator); //remove last separator
        $out .= $postfix;

        return $out;
    }
}
