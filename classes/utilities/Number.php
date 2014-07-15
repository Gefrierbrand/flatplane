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

namespace de\flatplane\utilities;

use de\flatplane\interfaces\NumberInterface;

/**
 * Description of Number
 * TODO: NumberInterface
 * todo: test formatting with floats
 * @author Nikolai Neff <admin@flatplane.de>
 */
class Number implements NumberInterface
{
    protected $value;
    protected $format = 'int';

    /**
     * Create a new number object
     * @param int|float $value (optional)
     */
    public function __construct($value = 0)
    {
        //todo: validate value
        $this->value = $value;
    }

    /**
     * Get a string representation of the current object
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getFormattedValue();
    }

    /**
     * Get the current value of the number
     * @return int|float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set the current value of the number
     * @param int|float $value
     */
    public function setValue($value)
    {
        //todo: validate value
        $this->value = $value;
    }

    /**
     * Get a formatted representation of the Numbers value as string.
     * @param string $format (optional)
     *  valid options: 'alpha', 'roman', 'float', 'int'
     *  The casing determines the appearance of the numbers for 'alpha' and 'roman'
     *  If omitted, the Numbers default format will be used
     * @param int $numDecimals
     *  number of decimals to display in 'float' mode
     * @param string $dec_point
     *  decimal separation character
     * @param string $thousands_sep
     *  thouseds separation character
     * @return string
     *  formatted number
     * @see Number::roman()
     * @see Number::alpha()
     */
    public function getFormattedValue(
        $format = null,
        $numDecimals = 2,
        $dec_point = '.',
        $thousands_sep = ''
    ) {
        if ($format === null) {
            $format = $this->format;
        }

        switch ($format) {
            case 'alpha':
                $retVal = $this->alpha('lower');
                break;

            case 'Alpha':
            case 'ALPHA':
                $retVal = $this->alpha('upper');
                break;

            case 'roman':
                $retVal = $this->roman('lower');
                break;

            case 'Roman':
            case 'ROMAN':
                $retVal = $this->roman('upper');
                break;

            case 'float':
                $retVal = number_format(
                    (float) $this->value,
                    $numDecimals,
                    $dec_point,
                    $thousands_sep
                );
                break;

            //case 'int':
            //case 'Int':
            //case 'INT':
            default:
                $retVal = (string) $this->value;
                break;
        }

        return $retVal;
    }

    /**
     * Get the Numbers current value repesented as roman numerals
     * @param string $case (optional)
     *  set to 'upper' to use uppercase roman numbers.
     * @return string
     */
    public function roman($case = 'upper')
    {
        if ($this->value == 0) {
            return 0;
        }

        $result = '';
        $tempNum = $this->value;
        if ($tempNum < 0) {
            $tempNum = abs($tempNum);
            $result .='-';
        }

        $chars = array('M' => 1000,
            'CM' => 900,
            'D' => 500,
            'CD' => 400,
            'C' => 100,
            'XC' => 90,
            'L' => 50,
            'XL' => 40,
            'X' => 10,
            'IX' => 9,
            'V' => 5,
            'IV' => 4,
            'I' => 1);

        foreach ($chars as $roman => $value) {
            $numMatches = floor($tempNum / $value);

            $result .= str_repeat($roman, $numMatches);

            $tempNum %= $value;
        }

        if ($case == 'lower') {
            $result = strtolower($result);
        }
        return $result;
    }

    /**
     * Get the Numbers current value repesented as charactes [a-z]
     * @param string $mode
     *  set to 'upper' to use uppercase letters
     * @return string
     */
    public function alpha($mode = 'upper')
    {
        if ($this->value == 0) {
            return 0;
        }

        $result = '';
        $tempNum = $this->value - 1;
        if ($tempNum < 0) {
            $tempNum = abs($tempNum) - 2; //2 twice off-by-one due to abs()
            $sign = '-';
        } else {
            $sign = '';
        }

        for ($i = 1; $tempNum >= 0; $i++) {
            $index = ($tempNum % pow(26, $i)) / pow(26, $i - 1);
            $result = chr(0x41 + $index) . $result;
            $tempNum -= pow(26, $i);
        }

        $result = $sign . $result;
        if ($mode == 'lower') {
            $result = strtolower($result);
        }

        return $result;
    }

    /**
     * Get the default number formatting format
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Set the default number formatting format
     * @param string $format
     */
    public function setFormat($format)
    {
        $this->format = $format;
    }
}
