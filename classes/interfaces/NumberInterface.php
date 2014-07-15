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

namespace de\flatplane\interfaces;

/**
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
interface NumberInterface
{
    /**
     * Create a new number object
     * @param int|float $value (optional)
     */
    public function __construct($value = 0);

    /**
     * get a string representation of the current object
     * @return string
     */
    public function __toString();

    /**
     * Get the current value of the number
     * @return int|float
     */
    public function getValue();

    public function setValue($value);

    /**
     * Get a formatted representation of the Numbers value as string.
     * @param string $format (optional)
     *  valid options: 'alpha', 'roman', 'float', 'int' (any casing)
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
    );

    /**
     * Get the default number formatting format
     * @return string
     */
    public function getFormat();

    /**
     * Set the default number formatting format
     * @param string $format
     */
    public function setFormat($format);

    /**
     * Get the Numbers current value repesented as roman numerals
     * @param string $case (optional)
     *  set to 'upper' to use uppercase roman numbers.
     * @return string
     */
    public function roman($case = 'upper');

    /**
     * Get the Numbers current value repesented as charactes [a-z]
     * @param string $mode
     *  set to 'upper' to use uppercase letters
     * @return string
     */
    public function alpha($mode = 'upper');
}
