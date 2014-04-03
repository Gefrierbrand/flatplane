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

namespace de\flatplane\utilities;

/**
 * Description of counter
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class Counter
{

    protected $value;
    protected $increment = 1;

    public function __construct($startValue = 0, $increment = 1)
    {
        $this->value = $startValue;
        $this->increment = $increment;
        $this->validate();
    }

    public function __toString()
    {
        return (string) $this->value;
    }

    public function add($increment = 1)
    {
        $this->validate();
        if (func_num_args() == 0) {
            $this->value += $this->increment;
        } else {
            $this->value += $increment;
        }
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getFormatedValue($format)
    {
        switch ($format) {
            case 'alpha':
                return $this->alpha('lower');
                break;

            case 'Alpha':
            case 'ALPHA':
                return $this->alpha('upper');
                break;

            case 'roman':
                return $this->roman('lower');
                break;

            case 'Roman':
            case 'ROMAN':
                return $this->roman('upper');
                break;

            default:
                return $this->value;
                break;
        }
    }

    public function setValue($val)
    {
        $this->value = $val;
        $this->validate();
    }

    public function getIncrement()
    {
        return $this->increment;
    }

    public function setIncrement($inc)
    {
        $this->increment = $inc;
        $this->validate();
    }

    public function resetValue()
    {
        $this->value = 0;
    }

    protected function validate() //TODO: gf schöne Fehlermeldungen
    {
        if (!is_numeric($this->increment)) {
            trigger_error(
                'Counter-Increment should be numeric, ' .
                gettype($this->increment) . ' was given.',
                E_USER_NOTICE
            );
        }
        if (!is_numeric($this->value)) {
            trigger_error(
                'Value should be numeric, ' .
                gettype($this->value) . ' was given.',
                E_USER_NOTICE
            );
        }
    }

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
}
