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

/**
 * Description of Number
 * TODO: NumberInterface
 * todo: test formatting with floats
 * @author Nikolai Neff <admin@flatplane.de>
 */
class Number
{
    protected $value;
    protected $format = 'int';

    public function __construct($value = 0)
    {
        //todo: cast value?
        $this->value = $value;
    }

    public function __toString()
    {
        return (string) $this->getFormattedValue();
    }

    public function getValue()
    {
        return $this->value;
    }

    /**
     *
     * @param string $format
     * @param int $numDecimals
     * @param string $dec_point
     * @param string $thousands_sep
     * @return string
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

    //todo: arbitrary number via param?
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

    public function getFormat()
    {
        return $this->format;
    }

    public function setFormat($format)
    {
        $this->format = $format;
    }
}
